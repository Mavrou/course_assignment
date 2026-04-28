<?php

namespace App\Http\Requests;

use App\Constants\CourseConstants;
use App\Enums\CourseStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            CourseConstants::FIELD_COURSE_TITLE => 'required|string|max:255',
            CourseConstants::FIELD_COURSE_DESCRIPTION => 'nullable|string',
            CourseConstants::FIELD_STATUS => [
                'required',
                Rule::enum(CourseStatus::class),
            ],
            CourseConstants::FIELD_IS_PREMIUM => 'required|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            CourseConstants::FIELD_COURSE_TITLE => 'title',
            CourseConstants::FIELD_COURSE_DESCRIPTION => 'description',
            CourseConstants::FIELD_STATUS => 'status',
            CourseConstants::FIELD_IS_PREMIUM => 'is_premium',
        ];
    }
}
