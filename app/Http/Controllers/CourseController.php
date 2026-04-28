<?php

namespace App\Http\Controllers;

use App\Constants\CourseConstants;
use App\Http\Requests\CourseRequest;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(
        private CourseService $courseService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $courses = $this->courseService->getPaginatedCourses($perPage);

        return response()->json([
            'data' => $courses->items(),
            'pagination' => [
                'total' => $courses->total(),
                'per_page' => $courses->perPage(),
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
            ],
        ], 200);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $course = $this->courseService->getCourseById($id);

            return response()->json([
                'data' => $course,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    public function store(CourseRequest $request): JsonResponse
    {
        try {
            $course = $this->courseService->createCourse($request->validated());

            return response()->json([
                'data' => $course,
                'message' => CourseConstants::MESSAGE_COURSE_CREATED,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(CourseRequest $request, int $id): JsonResponse
    {
        try {
            $course = $this->courseService->updateCourse($id, $request->validated());

            return response()->json([
                'data' => $course,
                'message' => CourseConstants::MESSAGE_COURSE_UPDATED,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->courseService->deleteCourse($id);

            return response()->json([
                'message' => CourseConstants::MESSAGE_COURSE_DELETED,
            ], 204);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }
}