<?php

use App\Constants\CourseConstants;
use App\Enums\CourseStatus;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Course API Endpoints', function () {
    describe('GET /api/v1/courses', function () {
        test('returns all courses with pagination', function () {
            Course::factory()->count(5)->create();

            $response = $this->getJson('/api/v1/courses');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['id', 'course_title', 'course_description', 'status', 'is_premium', 'created_at', 'updated_at'],
                    ],
                    'pagination' => ['total', 'per_page', 'current_page', 'last_page'],
                ]);

            expect($response->json('data'))->toHaveCount(5);
        });

        test('returns paginated results with custom per_page', function () {
            Course::factory()->count(25)->create();

            $response = $this->getJson('/api/v1/courses?per_page=10');

            $response->assertStatus(200);
            expect($response->json('pagination.per_page'))->toBe(10);
            expect($response->json('data'))->toHaveCount(10);
        });

        test('enforces maximum per_page limit of 100', function () {
            Course::factory()->count(120)->create();

            $response = $this->getJson('/api/v1/courses?per_page=200');

            $response->assertStatus(200);
            expect($response->json('pagination.per_page'))->toBe(100);
        });

        test('returns empty data array when no courses exist', function () {
            $response = $this->getJson('/api/v1/courses');

            $response->assertStatus(200);
            expect($response->json('data'))->toBeEmpty();
            expect($response->json('pagination.total'))->toBe(0);
        });
    });

    describe('GET /api/v1/courses/{id}', function () {
        test('returns a single course', function () {
            $course = Course::factory()->create();

            $response = $this->getJson("/api/v1/courses/{$course->id}");

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => ['id', 'course_title', 'course_description', 'status', 'is_premium', 'created_at', 'updated_at'],
                ])
                ->assertJson([
                    'data' => [
                        'id' => $course->id,
                        'course_title' => $course->course_title,
                    ],
                ]);
        });

        test('returns 404 when course not found', function () {
            $response = $this->getJson('/api/v1/courses/9999');

            $response->assertStatus(404)
                ->assertJson([
                    'error' => CourseConstants::ERROR_COURSE_NOT_FOUND,
                ]);
        });
    });

    describe('POST /api/v1/courses', function () {
        test('creates a course with valid data', function () {
            $courseData = [
                'course_title' => 'Laravel Basics',
                'course_description' => 'Learn Laravel fundamentals',
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => false,
            ];

            $response = $this->postJson('/api/v1/courses', $courseData);

            $response->assertStatus(201)
                ->assertJson([
                    'message' => CourseConstants::MESSAGE_COURSE_CREATED,
                ])
                ->assertJsonStructure([
                    'data' => ['id', 'course_title', 'course_description', 'status', 'is_premium', 'created_at', 'updated_at'],
                ]);

            expect(Course::count())->toBe(1);
            expect(Course::first()->course_title)->toBe('Laravel Basics');
        });

        test('creates a premium course', function () {
            $courseData = [
                'course_title' => 'Advanced PHP',
                'course_description' => 'Deep dive into PHP',
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => true,
            ];

            $response = $this->postJson('/api/v1/courses', $courseData);

            $response->assertStatus(201);
            expect(Course::first()->is_premium)->toBeTrue();
        });

        test('fails without required title', function () {
            $courseData = [
                'course_description' => 'No title provided',
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => false,
            ];

            $response = $this->postJson('/api/v1/courses', $courseData);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('course_title');
        });

        test('fails with invalid status', function () {
            $courseData = [
                'course_title' => 'Test Course',
                'course_description' => 'Test',
                'status' => 'invalid_status',
                'is_premium' => false,
            ];

            $response = $this->postJson('/api/v1/courses', $courseData);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('status');
        });

        test('fails without is_premium field', function () {
            $courseData = [
                'course_title' => 'Test Course',
                'course_description' => 'Test',
                'status' => CourseStatus::PUBLISHED->value,
            ];

            $response = $this->postJson('/api/v1/courses', $courseData);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('is_premium');
        });

        test('accepts nullable description', function () {
            $courseData = [
                'course_title' => 'Test Course',
                'course_description' => null,
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => false,
            ];

            $response = $this->postJson('/api/v1/courses', $courseData);

            $response->assertStatus(201);
            expect(Course::first()->course_description)->toBeNull();
        });
    });

    describe('PUT /api/v1/courses/{id}', function () {
        test('updates a course successfully', function () {
            $course = Course::factory()->create();

            $updateData = [
                'course_title' => 'Updated Title',
                'course_description' => 'Updated description',
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => true,
            ];

            $response = $this->putJson("/api/v1/courses/{$course->id}", $updateData);

            $response->assertStatus(200)
                ->assertJson([
                    'message' => CourseConstants::MESSAGE_COURSE_UPDATED,
                    'data' => [
                        'id' => $course->id,
                        'course_title' => 'Updated Title',
                    ],
                ]);

            expect(Course::find($course->id)->course_title)->toBe('Updated Title');
        });

        test('updates is_premium flag', function () {
            $course = Course::factory()->create(['is_premium' => false]);

            $updateData = [
                'course_title' => $course->course_title,
                'course_description' => $course->course_description,
                'status' => $course->status->value,
                'is_premium' => true,
            ];

            $response = $this->putJson("/api/v1/courses/{$course->id}", $updateData);

            $response->assertStatus(200);
            expect(Course::find($course->id)->is_premium)->toBeTrue();
        });

        test('returns 404 when updating non-existent course', function () {
            $updateData = [
                'course_title' => 'Test',
                'course_description' => 'Test',
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => false,
            ];

            $response = $this->putJson('/api/v1/courses/9999', $updateData);

            $response->assertStatus(404)
                ->assertJson([
                    'error' => CourseConstants::ERROR_COURSE_NOT_FOUND,
                ]);
        });

        test('fails with invalid status on update', function () {
            $course = Course::factory()->create();

            $updateData = [
                'course_title' => $course->course_title,
                'course_description' => $course->course_description,
                'status' => 'bad_status',
                'is_premium' => false,
            ];

            $response = $this->putJson("/api/v1/courses/{$course->id}", $updateData);

            $response->assertStatus(422)
                ->assertJsonValidationErrors('status');
        });
    });

    describe('DELETE /api/v1/courses/{id}', function () {
        test('deletes a course successfully', function () {
            $course = Course::factory()->create();

            $response = $this->deleteJson("/api/v1/courses/{$course->id}");

            $response->assertStatus(204);

            expect(Course::find($course->id))->toBeNull();
        });

        test('returns 404 when deleting non-existent course', function () {
            $response = $this->deleteJson('/api/v1/courses/9999');

            $response->assertStatus(404)
                ->assertJson([
                    'error' => CourseConstants::ERROR_COURSE_NOT_FOUND,
                ]);
        });

        test('soft deletes the course', function () {
            $course = Course::factory()->create();

            $this->deleteJson("/api/v1/courses/{$course->id}");

            $deletedCourse = Course::withTrashed()->find($course->id);
            expect($deletedCourse->trashed())->toBeTrue();
        });
    });

    describe('HTTP Methods', function () {
        test('patch method successfully updates course', function () {
            $course = Course::factory()->create();

            $response = $this->patchJson("/api/v1/courses/{$course->id}", [
                'course_title' => 'Updated Title',
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => false,
            ]);

            $response->assertStatus(200);
            expect($response->json('data.course_title'))->toBe('Updated Title');
        });
    });
});
