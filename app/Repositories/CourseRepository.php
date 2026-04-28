<?php

namespace App\Repositories;

use App\Models\Course;
use App\Repositories\Contracts\CourseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseRepository implements CourseRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Course::paginate($perPage);
    }

    public function findById(int $id): ?Course
    {
        return Course::find($id);
    }

    public function create(array $data): Course
    {
        return Course::create($data);
    }

    public function update(Course $course, array $data): Course
    {
        $course->update($data);
        return $course;
    }

    public function delete(Course $course): bool
    {
        return $course->delete();
    }
}
