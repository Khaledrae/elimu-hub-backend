<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Models\ClassModel;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index()
    {
        return response()->json(Course::with('teacher')->latest()->paginate(10));
    }

    public function store(CourseRequest $request)
    {
        $course = $this->courseService->createCourse($request->validated());
        return response()->json($course, 201);
    }

    public function show(Course $course)
    {
        return response()->json($course->load('teacher'));
    }

    public function update(CourseRequest $request, Course $course)
    {
        $course = $this->courseService->updateCourse($course, $request->validated());
        return response()->json($course);
    }

    public function destroy(Course $course)
    {
        $this->courseService->deleteCourse($course);
        return response()->json(['message' => 'Course deleted']);
    }
    // In CourseController
    public function classes(Course $course)
    {
        $classes = $course->classes;

        return response()->json($classes->map(function ($class) {
            return [
                'id' => $class->id,
                'name' => $class->name,
                'level_group' => $class->level_group,
            ];
        }));
    }

    public function lessons(Course $course)
    {
        $lessons = $course->lessons()->orderBy('order')->get();

        return response()->json($lessons->map(function ($lesson) {
            return [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'description' => $lesson->content,
                'content_type' => $lesson->content_type ?? 'text',
                'order' => $lesson->order,
                'status' => $lesson->status,
                'created_at' => $lesson->created_at,
            ];
        }));
    }

    public function assessments(Course $course)
    {
        $assessments = $course->assessments;

        return response()->json($assessments->map(function ($assessment) {
            return [
                'id' => $assessment->id,
                'title' => $assessment->title,
                'type' => $assessment->type,
                'due_date' => $assessment->due_date,
                'status' => $assessment->status,
            ];
        }));
    }
    // Add this method to your existing CourseController

    public function getClasses($courseId)
    {
        $course = Course::findOrFail($courseId);

        // Get classes associated with this course through lessons
        $classes = ClassModel::join('lessons', 'classes.id', '=', 'lessons.class_id')
            ->where('lessons.course_id', $courseId)
            ->select('classes.*')
            ->distinct()
            ->get();

        return response()->json($classes);
    }
}
