<?php

use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

/**
 * V1 API Routes - Course Endpoints
 * 
 * Base URL: /api/v1/courses
 */

Route::middleware('api')->group(function () {
    Route::apiResource('courses', CourseController::class);
});
