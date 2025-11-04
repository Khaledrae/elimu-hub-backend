<?php

namespace App\Services;

use App\Models\Course;

class CourseService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function createCourse(array $data): Course
    {
        return Course::create($data);
    }

    public function updateCourse(Course $course, array $data): Course
    {
        $course->update($data);
        return $course;
    }

    public function deleteCourse(Course $course): bool
    {
        return $course->delete();
    }
}
