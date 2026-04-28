<?php

namespace App\Services;

use App\Constants\CourseConstants;
use App\Models\Course;
use App\Repositories\Contracts\CourseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseService
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository
    ) {}

    public function getPaginatedCourses(int $perPage = 15): LengthAwarePaginator
    {
        $perPage = min($perPage, 100);
        return $this->courseRepository->paginate($perPage);
    }

    public function getCourseById(int $id): Course
    {
        $course = $this->courseRepository->findById($id);

        if (!$course) {
            throw new \Exception(CourseConstants::ERROR_COURSE_NOT_FOUND, 404);
        }

        return $course;
    }

    public function createCourse(array $data): Course
    {
        return $this->courseRepository->create($data);
    }

    public function updateCourse(int $id, array $data): Course
    {
        $course = $this->getCourseById($id);
        return $this->courseRepository->update($course, $data);
    }

    public function deleteCourse(int $id): void
    {
        $course = $this->getCourseById($id);
        $this->courseRepository->delete($course);
    }
}
