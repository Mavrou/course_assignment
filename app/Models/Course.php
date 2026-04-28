<?php

namespace App\Models;

use App\Constants\CourseConstants;
use App\Enums\CourseStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = CourseConstants::TABLE_COURSES;

    protected $fillable = [
        CourseConstants::FIELD_COURSE_TITLE,
        CourseConstants::FIELD_COURSE_DESCRIPTION,
        CourseConstants::FIELD_STATUS,
        CourseConstants::FIELD_IS_PREMIUM,
    ];

    protected $casts = [
        CourseConstants::FIELD_STATUS => CourseStatus::class,
        CourseConstants::FIELD_IS_PREMIUM => 'boolean',
        CourseConstants::FIELD_CREATED_AT => 'datetime',
        CourseConstants::FIELD_DELETED_AT => 'datetime',
    ];
}
