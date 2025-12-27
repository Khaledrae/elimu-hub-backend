<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassModelResource;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class ClassModelController extends Controller
{
    public function index()
    {
        $classes = ClassModel::with(['manager', 'courses', 'students', 'lessons'])->get();
        return ClassModelResource::collection($classes);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'level_group' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        $class = ClassModel::create($data);

        return response()->json($class, 201);
    }

    public function show($id)
    {
        $class = ClassModel::with(['manager', 'courses.teacher.user', 'students.user', 'lessons.course', 'lessons.teacher.user'])
            ->findOrFail($id);
        return response()->json($class);
    }

    public function update(Request $request, $id)
    {
        $class = ClassModel::findOrFail($id);
        $class->update($request->only(['name', 'level_group', 'manager_id']));
        return response()->json($class);
    }

    public function destroy($id)
    {
        ClassModel::findOrFail($id)->delete();
        return response()->json(['message' => 'Class deleted successfully']);
    }
    /*
    public function courses($id)
    {
        $class = ClassModel::with(['courses.teacher'])->findOrFail($id);

        return response()->json([
            'class' => [
                'id' => $class->id,
                'name' => $class->name,
                'level_group' => $class->level_group,
            ],
            'courses' => $class->courses->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'slug' => $course->slug,
                    'level' => $course->level,
                    'teacher' => optional($course->teacher->user)->name,
                    'status' => $course->status,
                ];
            }),
        ]);
    }*/
    public function courses($id)
    {
        $class = ClassModel::with(['courses.teacher.user', 'courses' => function ($q) {
            $q->withCount('lessons');
        }])->findOrFail($id);

        return response()->json([
            'class' => [
                'id' => $class->id,
                'name' => $class->name,
                'level_group' => $class->level_group,
            ],
            'courses' => $class->courses->map(function ($course) {
                $teacher = optional($course->teacher);
                $user = optional($teacher->user);

                $teacherName = $user->first_name && $user->last_name
                    ? $user->first_name . ' ' . $user->last_name
                    : null;

                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'slug' => $course->slug,
                    'level' => $course->level,
                    'teacher' => $teacherName,
                    'status' => $course->status,
                    'lessons_count' => $course->lessons_count,
                ];
            }),
        ]);
    }
    public function getLessons($id)
    {
        $class = ClassModel::with(['lessons.course', 'lessons.teacher.user'])->findOrFail($id);

        return response()->json([
            'class' => [
                'id' => $class->id,
                'name' => $class->name,
                'level_group' => $class->level_group,
            ],
            'lessons' => $class->lessons->map(function ($lesson) {
                $teacherName = null;

                if ($lesson->teacher && $lesson->teacher->user) {
                    $teacherName = $lesson->teacher->user->first_name . ' ' . $lesson->teacher->user->last_name;
                }

                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'content' => $lesson->content,
                    'video_url' => $lesson->video_url,
                    'document_path' => $lesson->document_path,
                    'order' => $lesson->order,
                    'status' => $lesson->status,
                    'teacher' => $teacherName,
                    'course' => $lesson->course ? [
                        'id' => $lesson->course->id,
                        'title' => $lesson->course->title,
                    ] : null,
                    'created_at' => $lesson->created_at,
                    'updated_at' => $lesson->updated_at,
                ];
            }),
        ]);
    }
    public function assignCourses(Request $request, $classId)
    {
        $class = ClassModel::findOrFail($classId);
        $courseIds = $request->input('course_ids', []);

        $class->courses()->syncWithoutDetaching($courseIds);

        return response()->json([
            'message' => 'Courses assigned successfully',
            'class' => $class->load('courses')
        ]);
    }
}
