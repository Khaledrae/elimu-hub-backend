<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class ClassModelController extends Controller
{
    public function index()
    {
        return response()->json(ClassModel::with(['manager', 'courses'])->get());
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
        $class = ClassModel::with(['manager', 'courses'])->findOrFail($id);
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
