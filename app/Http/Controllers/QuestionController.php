<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    // List all questions (optionally by assessment)
    public function index(Request $request)
    {
        $assessmentId = $request->query('assessment_id');

        $query = Question::with(['teacher.user', 'assessment']);
        if ($assessmentId) {
            $query->where('assessment_id', $assessmentId);
        }

        return response()->json($query->paginate(10));
    }

    // Store a new question
    public function store(Request $request)
    {
        $validated = $request->validate([
            'assessment_id' => 'required|exists:assessments,id',
            'question_text' => 'required|string',
            'marks' => 'nullable|integer|min:1',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'nullable|string',
            'option_d' => 'nullable|string',
            'correct_option' => 'required|in:A,B,C,D',
        ]);

        $validated['set_by'] = auth()->id();

        $question = Question::create($validated);

        return response()->json([
            'message' => 'Question created successfully',
            'data' => $question->load(['assessment', 'teacher.user']),
        ]);
    }

    // Get a single question
    public function show($id)
    {
        $question = Question::with(['assessment', 'teacher.user'])->findOrFail($id);
        return response()->json($question);
    }

    // Update question
    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $validated = $request->validate([
            'question_text' => 'sometimes|string',
            'marks' => 'sometimes|integer|min:1',
            'option_a' => 'sometimes|string',
            'option_b' => 'sometimes|string',
            'option_c' => 'nullable|string',
            'option_d' => 'nullable|string',
            'correct_option' => 'sometimes|in:A,B,C,D',
        ]);

        $question->update($validated);

        return response()->json([
            'message' => 'Question updated successfully',
            'data' => $question->fresh(),
        ]);
    }

    // Delete question
    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        return response()->json(['message' => 'Question deleted successfully']);
    }
}
