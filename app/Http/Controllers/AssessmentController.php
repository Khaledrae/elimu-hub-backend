<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AssessmentController extends Controller
{
    public function index()
    {
        return Assessment::with(['lesson', 'teacher.user'])->paginate(10);
    }
    // Get assessment by lesson_id
    public function getByLesson($lessonId)
    {
        $assessment = Assessment::with(['lesson', 'teacher.user', 'questions'])
            ->where('lesson_id', $lessonId)
            ->first();
        
        if (!$assessment) {
            return response()->json([
                'message' => 'No assessment found for this lesson'
            ], 404);
        }
        
        return response()->json($assessment);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'title' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'type' => 'required|in:quiz,assignment,exam',
            'total_marks' => 'required|integer|min:1',
            'duration_minutes' => 'nullable|integer|min:1',
        ]);

        $assessment = Assessment::create($validated);

        return response()->json($assessment->load(['lesson', 'teacher.user']), 201);
    }
    public function show($id)
    {
        return Assessment::with(['lesson', 'teacher.user', 'questions'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $assessment = Assessment::findOrFail($id);
        $assessment->update($request->only([
            'title',
            'instructions',
            'type',
            'total_marks',
            'duration_minutes',
            'status'
        ]));

        return response()->json($assessment->fresh(['lesson', 'teacher.user']));
    }

    public function destroy($id)
    {
        Assessment::findOrFail($id)->delete();
        return response()->json(['message' => 'Assessment deleted successfully']);
    }
}
