<?php

use App\Constants\CourseConstants;
use App\Enums\CourseStatus;
use App\Http\Requests\CourseRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

describe('CourseRequest Validation', function () {
    describe('create course validation', function () {
        test('passes with valid data', function () {
            $request = new CourseRequest();
            $request->merge([
                'course_title' => 'Test Course',
                'course_description' => 'Test Description',
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => false,
            ]);

            $rules = $request->rules();
            $validator = validator($request->all(), $rules);

            expect($validator->passes())->toBeTrue();
        });

        test('requires course_title', function () {
            $request = new CourseRequest();
            $request->merge([
                'course_description' => 'Test',
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => false,
            ]);

            $rules = $request->rules();
            $validator = validator($request->all(), $rules);

            expect($validator->fails())->toBeTrue();
            expect($validator->errors()->has('course_title'))->toBeTrue();
        });

        test('requires status', function () {
            $request = new CourseRequest();
            $request->merge([
                'course_title' => 'Test',
                'course_description' => 'Test',
                'is_premium' => false,
            ]);

            $rules = $request->rules();
            $validator = validator($request->all(), $rules);

            expect($validator->fails())->toBeTrue();
            expect($validator->errors()->has('status'))->toBeTrue();
        });

        test('requires is_premium', function () {
            $request = new CourseRequest();
            $request->merge([
                'course_title' => 'Test',
                'course_description' => 'Test',
                'status' => CourseStatus::PUBLISHED->value,
            ]);

            $rules = $request->rules();
            $validator = validator($request->all(), $rules);

            expect($validator->fails())->toBeTrue();
            expect($validator->errors()->has('is_premium'))->toBeTrue();
        });

        test('allows null description', function () {
            $request = new CourseRequest();
            $request->merge([
                'course_title' => 'Test',
                'course_description' => null,
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => false,
            ]);

            $rules = $request->rules();
            $validator = validator($request->all(), $rules);

            expect($validator->passes())->toBeTrue();
        });

        test('validates status enum', function () {
            $request = new CourseRequest();
            $request->merge([
                'course_title' => 'Test',
                'course_description' => 'Test',
                'status' => 'invalid_status',
                'is_premium' => false,
            ]);

            $rules = $request->rules();
            $validator = validator($request->all(), $rules);

            expect($validator->fails())->toBeTrue();
            expect($validator->errors()->has('status'))->toBeTrue();
        });

        test('validates is_premium is boolean', function () {
            $request = new CourseRequest();
            $request->merge([
                'course_title' => 'Test',
                'course_description' => 'Test',
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => 'not_boolean',
            ]);

            $rules = $request->rules();
            $validator = validator($request->all(), $rules);

            expect($validator->fails())->toBeTrue();
            expect($validator->errors()->has('is_premium'))->toBeTrue();
        });

        test('validates title max length', function () {
            $request = new CourseRequest();
            $request->merge([
                'course_title' => str_repeat('a', 256),
                'course_description' => 'Test',
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => false,
            ]);

            $rules = $request->rules();
            $validator = validator($request->all(), $rules);

            expect($validator->fails())->toBeTrue();
            expect($validator->errors()->has('course_title'))->toBeTrue();
        });

        test('accepts title at max length boundary', function () {
            $request = new CourseRequest();
            $request->merge([
                'course_title' => str_repeat('a', 255),
                'course_description' => 'Test',
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => false,
            ]);

            $rules = $request->rules();
            $validator = validator($request->all(), $rules);

            expect($validator->passes())->toBeTrue();
        });

        test('validates title is string', function () {
            $request = new CourseRequest();
            $request->merge([
                'course_title' => 12345,
                'course_description' => 'Test',
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => false,
            ]);

            $rules = $request->rules();
            $validator = validator($request->all(), $rules);

            expect($validator->fails())->toBeTrue();
        });

        test('validates description is string when provided', function () {
            $request = new CourseRequest();
            $request->merge([
                'course_title' => 'Test',
                'course_description' => 12345,
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => false,
            ]);

            $rules = $request->rules();
            $validator = validator($request->all(), $rules);

            expect($validator->fails())->toBeTrue();
        });
    });

    describe('authorization', function () {
        test('authorize returns true', function () {
            $request = new CourseRequest();

            expect($request->authorize())->toBeTrue();
        });
    });

    describe('attributes', function () {
        test('returns custom attribute names', function () {
            $request = new CourseRequest();
            $attributes = $request->attributes();

            expect($attributes[CourseConstants::FIELD_COURSE_TITLE])->toBe('title');
            expect($attributes[CourseConstants::FIELD_COURSE_DESCRIPTION])->toBe('description');
            expect($attributes[CourseConstants::FIELD_STATUS])->toBe('status');
            expect($attributes[CourseConstants::FIELD_IS_PREMIUM])->toBe('is_premium');
        });
    });

    describe('all course status values', function () {
        test('accepts published status', function () {
            $request = new CourseRequest();
            $request->merge([
                'course_title' => 'Test',
                'course_description' => 'Test',
                'status' => CourseStatus::PUBLISHED->value,
                'is_premium' => false,
            ]);

            $rules = $request->rules();
            $validator = validator($request->all(), $rules);

            expect($validator->passes())->toBeTrue();
        });

        test('accepts pending status', function () {
            $request = new CourseRequest();
            $request->merge([
                'course_title' => 'Test',
                'course_description' => 'Test',
                'status' => CourseStatus::PENDING->value,
                'is_premium' => false,
            ]);

            $rules = $request->rules();
            $validator = validator($request->all(), $rules);

            expect($validator->passes())->toBeTrue();
        });
    });
});
