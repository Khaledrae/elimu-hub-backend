<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\StudentResponse;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentResponseController extends Controller
{
    public function submit(Request $request, $assessmentId)
    {
        // Check what's being passed
        logger("Assessment ID: " . $assessmentId);

        $assessment = Assessment::find($assessmentId);
        logger("Assessment found: " . ($assessment ? 'yes' : 'no'));

        if (!$assessment) {
            return response()->json([
                'error' => 'Assessment not found',
                'requested_id' => $assessmentId
            ], 404);
        }
        $validated = $request->validate([
            'responses' => 'required|array',
            'responses.*.question_id' => 'required|exists:questions,id',
            'responses.*.selected_option' => 'required|string|in:A,B,C,D',
        ]);

        $studentId = Auth::user()->student->id;

        DB::transaction(function () use ($validated, $studentId, $assessmentId) {
            foreach ($validated['responses'] as $response) {
                $question = Question::find($response['question_id']);

                $isCorrect = strtoupper($response['selected_option']) === $question->correct_option;
                $marks = $isCorrect ? $question->marks : 0;

                StudentResponse::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'assessment_id' => $assessmentId,
                        'question_id' => $response['question_id'],
                    ],
                    [
                        'selected_option' => strtoupper($response['selected_option']),
                        'is_correct' => $isCorrect,
                        'marks_awarded' => $marks,
                    ]
                );
            }
        });

        return response()->json([
            'message' => 'Responses submitted successfully!',
        ]);
    }

    public function results($assessmentId)
    {
        $studentId = Auth::user()->student->id;

        $responses = StudentResponse::with('question')
            ->where('student_id', $studentId)
            ->where('assessment_id', $assessmentId)
            ->get();

        $totalMarks = $responses->sum('marks_awarded');
        $possibleMarks = $responses->sum(fn($r) => $r->question->marks);

        return response()->json([
            'assessment_id' => $assessmentId,
            'total_marks' => $totalMarks,
            'possible_marks' => $possibleMarks,
            'score_percentage' => round(($totalMarks / max($possibleMarks, 1)) * 100, 2),
            'responses' => $responses,
        ]);
    }
}
