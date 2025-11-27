<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index()
    {
        return response()->json(
            Lesson::with(['course', 'class', 'teacher'])->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'class_id' => 'nullable|exists:classes,id',
            'teacher_id' => 'nullable|exists:teachers,user_id',
            'title' => 'required|string|max:255',
            'content_type' => 'required|in:text,video,document',
            'content' => 'nullable|string',
            'video_url' => 'nullable|string',
            'document_path' => 'nullable|string',
            'order' => 'nullable|integer',
            'status' => 'nullable|in:draft,published',
        ]);

        $lesson = Lesson::create($data);

        return response()->json($lesson, 201);
    }

    public function show($id)
    {
        return response()->json(
            Lesson::with(['course', 'class', 'teacher'])->findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->update($request->only([
            'title',
            'content',
            'video_url',
            'document_path',
            'order',
            'status'
        ]));
        return response()->json($lesson);
    }

    public function destroy($id)
    {
        Lesson::findOrFail($id)->delete();
        return response()->json(['message' => 'Lesson deleted successfully']);
    }
    public function byCourse($id)
    {
        $lessons = Lesson::where('course_id', $id)
            ->with(['teacher', 'class'])
            ->orderBy('order')
            ->get();

        return response()->json($lessons);
    }
}
