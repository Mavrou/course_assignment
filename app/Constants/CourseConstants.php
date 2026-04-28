<?php

namespace App\Constants;

class CourseConstants
{
    // table names
    public const TABLE_COURSES = "courses";

    // field names
    public const FIELD_COURSE_ID = "course_id";
    public const FIELD_COURSE_TITLE = "course_title";
    public const FIELD_COURSE_DESCRIPTION = "course_description";
    public const FIELD_STATUS = "status";
    public const FIELD_IS_PREMIUM = "is_premium";
    public const FIELD_CREATED_AT = "created_at";
    public const FIELD_DELETED_AT = "deleted_at";

    // error messages
    public const ERROR_COURSE_NOT_FOUND = "Course not found";

    // success messages
    public const MESSAGE_COURSE_CREATED = "Course created successfully";
    public const MESSAGE_COURSE_UPDATED = "Course updated successfully";
    public const MESSAGE_COURSE_DELETED = "Course deleted successfully";
}