<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
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
}

