<?php

use App\Constants\CourseConstants;
use App\Enums\CourseStatus;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('CourseService', function () {
    function getCourseService() {
        return app(CourseService::class);
    }

    describe('getPaginatedCourses', function () {
        test('returns paginated courses', function () {
            Course::factory()->count(20)->create();
            $result = getCourseService()->getPaginatedCourses(10);
            expect($result->items())->toHaveCount(10);
            expect($result->total())->toBe(20);
            expect($result->perPage())->toBe(10);
        });

        test('enforces maximum per_page of 100', function () {
            Course::factory()->count(150)->create();
            $result = getCourseService()->getPaginatedCourses(200);
            expect($result->perPage())->toBe(100);
        });

        test('returns empty when no courses exist', function () {
            $result = getCourseService()->getPaginatedCourses(15);
            expect($result->items())->toBeEmpty();
            expect($result->total())->toBe(0);
        });
    });

    describe('getCourseById', function () {
        test('returns a course by id', function () {
            $course = Course::factory()->create();
            $result = getCourseService()->getCourseById($course->id);
            expect($result->id)->toBe($course->id);
            expect($result->course_title)->toBe($course->course_title);
        });

        test('throws exception when course not found', function () {
            getCourseService()->getCourseById(9999);
        })->throws(Exception::class, CourseConstants::ERROR_COURSE_NOT_FOUND);

        test('throws exception with 404 code', function () {
            try {
                getCourseService()->getCourseById(9999);
            } catch (Exception $e) {
                expect($e->getCode())->toBe(404);
            }
        });
    });

    describe('createCourse', function () {
        test('creates a new course', function () {
            $data = [
                'course_title' => 'New Course',
                'course_description' => 'A new course',
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => false,
            ];
            $result = getCourseService()->createCourse($data);
            expect($result)->toBeInstanceOf(Course::class);
            expect($result->course_title)->toBe('New Course');
            expect(Course::count())->toBe(1);
        });

        test('creates a premium course', function () {
            $data = [
                'course_title' => 'Premium Course',
                'course_description' => 'Premium content',
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => true,
            ];
            $result = getCourseService()->createCourse($data);
            expect($result->is_premium)->toBeTrue();
        });

        test('preserves nullable description', function () {
            $data = [
                'course_title' => 'No Description Course',
                'course_description' => null,
                'status' => CourseStatus::PENDING->value,
                'is_premium' => false,
            ];
            $result = getCourseService()->createCourse($data);
            expect($result->course_description)->toBeNull();
        });
    });

    describe('updateCourse', function () {
        test('updates an existing course', function () {
            $course = Course::factory()->create();
            $updateData = [
                'course_title' => 'Updated Title',
                'course_description' => 'Updated Description',
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => true,
            ];
            $result = getCourseService()->updateCourse($course->id, $updateData);
            expect($result->id)->toBe($course->id);
            expect($result->course_title)->toBe('Updated Title');
            expect($result->is_premium)->toBeTrue();
        });

        test('throws exception when course does not exist', function () {
            getCourseService()->updateCourse(9999, []);
        })->throws(Exception::class, CourseConstants::ERROR_COURSE_NOT_FOUND);

        test('only updates specified fields', function () {
            $course = Course::factory()->create([
                'course_title' => 'Original Title',
                'course_description' => 'Original Description',
            ]);
            $updateData = [
                'course_title' => 'New Title',
                'course_description' => 'Original Description',
                'status' => $course->status->value,
                'is_premium' => $course->is_premium,
            ];
            $result = getCourseService()->updateCourse($course->id, $updateData);
            expect($result->course_title)->toBe('New Title');
            expect($result->course_description)->toBe('Original Description');
        });
    });

    describe('deleteCourse', function () {
        test('deletes a course', function () {
            $course = Course::factory()->create();
            getCourseService()->deleteCourse($course->id);
            expect(Course::find($course->id))->toBeNull();
        });

        test('throws exception when course does not exist', function () {
            getCourseService()->deleteCourse(9999);
        })->throws(Exception::class, CourseConstants::ERROR_COURSE_NOT_FOUND);

        test('soft deletes the course', function () {
            $course = Course::factory()->create();
            getCourseService()->deleteCourse($course->id);
            $deletedCourse = Course::withTrashed()->find($course->id);
            expect($deletedCourse->trashed())->toBeTrue();
        });
    });
});
