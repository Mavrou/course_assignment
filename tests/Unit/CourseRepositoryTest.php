<?php

use App\Constants\CourseConstants;
use App\Enums\CourseStatus;
use App\Models\Course;
use App\Repositories\Contracts\CourseRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('CourseRepository', function () {
    function getRepository() {
        return app(CourseRepositoryInterface::class);
    }

    describe('paginate', function () {
        test('paginates courses with default per_page', function () {
            Course::factory()->count(20)->create();
            $result = getRepository()->paginate();
            expect($result->perPage())->toBe(15);
            expect($result->items())->toHaveCount(15);
            expect($result->total())->toBe(20);
        });

        test('paginates with custom per_page', function () {
            Course::factory()->count(30)->create();
            $result = getRepository()->paginate(10);
            expect($result->perPage())->toBe(10);
            expect($result->items())->toHaveCount(10);
        });

        test('returns empty pagination when no courses', function () {
            $result = getRepository()->paginate(15);
            expect($result->items())->toBeEmpty();
            expect($result->total())->toBe(0);
        });
    });

    describe('findById', function () {
        test('finds a course by id', function () {
            $course = Course::factory()->create();
            $result = getRepository()->findById($course->id);
            expect($result)->not->toBeNull();
            expect($result->id)->toBe($course->id);
        });

        test('returns null when course not found', function () {
            $result = getRepository()->findById(9999);
            expect($result)->toBeNull();
        });

        test('returns course with all attributes', function () {
            $course = Course::factory()->create([
                'course_title' => 'Test Course',
                'course_description' => 'Test Description',
                'is_premium' => true,
            ]);
            $result = getRepository()->findById($course->id);
            expect($result->course_title)->toBe('Test Course');
            expect($result->course_description)->toBe('Test Description');
            expect($result->is_premium)->toBeTrue();
        });
    });

    describe('create', function () {
        test('creates a new course', function () {
            $data = [
                'course_title' => 'New Course',
                'course_description' => 'New Description',
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => false,
            ];
            $result = getRepository()->create($data);
            expect($result)->toBeInstanceOf(Course::class);
            expect($result->course_title)->toBe('New Course');
            expect(Course::count())->toBe(1);
        });

        test('stores course with all fields', function () {
            $data = [
                'course_title' => 'Complete Course',
                'course_description' => 'Full description',
                'status' => CourseStatus::PENDING->value,
                'is_premium' => true,
            ];
            $result = getRepository()->create($data);
            $stored = Course::find($result->id);
            expect($stored->course_title)->toBe('Complete Course');
            expect($stored->course_description)->toBe('Full description');
            expect($stored->status)->toBe(CourseStatus::PENDING);
            expect($stored->is_premium)->toBeTrue();
        });

        test('handles nullable description', function () {
            $data = [
                'course_title' => 'No Desc',
                'course_description' => null,
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => false,
            ];
            $result = getRepository()->create($data);
            expect($result->course_description)->toBeNull();
        });
    });

    describe('update', function () {
        test('updates a course', function () {
            $course = Course::factory()->create();
            $updateData = [
                'course_title' => 'Updated Title',
                'course_description' => 'Updated Description',
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => true,
            ];
            $result = getRepository()->update($course, $updateData);
            expect($result->id)->toBe($course->id);
            expect($result->course_title)->toBe('Updated Title');
            $stored = Course::find($course->id);
            expect($stored->course_title)->toBe('Updated Title');
        });

        test('preserves original id', function () {
            $course = Course::factory()->create();
            $originalId = $course->id;
            getRepository()->update($course, [
                'course_title' => 'New Title',
                'course_description' => $course->course_description,
                'status' => $course->status->value,
                'is_premium' => $course->is_premium,
            ]);
            expect(Course::find($originalId))->not->toBeNull();
        });

        test('updates only specified fields', function () {
            $course = Course::factory()->create([
                'course_title' => 'Original',
                'course_description' => 'Description',
            ]);
            getRepository()->update($course, [
                'course_title' => 'Changed',
                'course_description' => 'Description',
                'status' => $course->status->value,
                'is_premium' => $course->is_premium,
            ]);
            $updated = Course::find($course->id);
            expect($updated->course_title)->toBe('Changed');
            expect($updated->course_description)->toBe('Description');
        });
    });

    describe('delete', function () {
        test('deletes a course', function () {
            $course = Course::factory()->create();
            $result = getRepository()->delete($course);
            expect($result)->toBeTrue();
            expect(Course::find($course->id))->toBeNull();
        });

        test('soft deletes the course', function () {
            $course = Course::factory()->create();
            getRepository()->delete($course);
            $deletedCourse = Course::withTrashed()->find($course->id);
            expect($deletedCourse->trashed())->toBeTrue();
        });

        test('returns true on successful delete', function () {
            $course = Course::factory()->create();
            $result = getRepository()->delete($course);
            expect($result)->toBeTrue();
        });
    });

    describe('edge cases', function () {
        test('handles multiple operations in sequence', function () {
            $data1 = [
                'course_title' => 'Course 1',
                'course_description' => 'First',
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => false,
            ];
            $course1 = getRepository()->create($data1);

            $data2 = [
                'course_title' => 'Course 2',
                'course_description' => 'Second',
                'status' => CourseStatus::PENDING->value,
                'is_premium' => true,
            ];
            $course2 = getRepository()->create($data2);
            expect(Course::count())->toBe(2);

            getRepository()->delete($course1);
            expect(Course::count())->toBe(1);
            expect(Course::first()->id)->toBe($course2->id);
        });
    });
});
