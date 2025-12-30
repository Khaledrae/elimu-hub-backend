<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Attempt;
use App\Models\CoveredLesson;
use App\Models\StudentResponse;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentResponseController extends Controller
{
    public function startAttempt($assessmentId)
    {
        $studentId = Auth::user()->student->id;
        $assessment = Assessment::findOrFail($assessmentId);

        // Check if there's already an active attempt
        $existingAttempt = Attempt::where('student_id', $studentId)
            ->where('assessment_id', $assessmentId)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            return response()->json([
                'message' => 'You already have an active attempt',
                'attempt' => $existingAttempt
            ], 200);
        }
        $nextAttemptNumber = Attempt::where('student_id', $studentId)
            ->where('assessment_id', $assessmentId)
            ->max('attempt_number') + 1;

        $attempt = Attempt::create([
            'student_id' => $studentId,
            'assessment_id' => $assessmentId,
            'attempt_number' => $nextAttemptNumber,
            'started_at' => now(),
            'status' => 'in_progress',
            'total_marks_possible' => $assessment->total_marks,
        ]);

        return response()->json([
            'message' => 'Attempt started successfully',
            'attempt' => $attempt
        ], 201);
    }



    // Submit responses
    public function submit(Request $request, $assessmentId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $userId = Auth::id();
        $studentId = Auth::user()->student->id;

        $validated = $request->validate([
            'attempt_id' => 'required|exists:attempts,id',
            'responses' => 'required|array',
            'responses.*.question_id' => 'required|exists:questions,id',
            'responses.*.selected_option' => 'required|string|in:A,B,C,D',
        ]);
        // Log::debug([
        //     'student_id' => $studentId,
        //     'assessment_id' => $assessmentId,
        //     'attempt_id' => $validated['attempt_id'],
        //     'responses_count' => count($validated['responses'])
        // ]);
        // Verify the attempt belongs to this student and assessment
        $attempt = Attempt::where('id', $validated['attempt_id'])
            ->where('student_id', $studentId)
            ->where('assessment_id', $assessmentId)
            ->where('status', 'in_progress')
            ->firstOrFail();

        $totalMarks = 0;

        DB::transaction(function () use ($validated, $userId, $studentId, $assessmentId, &$totalMarks, $attempt, $assessment) {
            foreach ($validated['responses'] as $response) {
                $question = Question::find($response['question_id']);

                $isCorrect = strtoupper($response['selected_option']) === $question->correct_option;
                $marks = $isCorrect ? $question->marks : 0;
                $totalMarks += $marks;

                StudentResponse::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'assessment_id' => $assessmentId,
                        'attempt_id' => $attempt->id,
                        'question_id' => $response['question_id'],
                    ],
                    [
                        'selected_option' => strtoupper($response['selected_option']),
                        'is_correct' => $isCorrect,
                        'marks_awarded' => $marks,
                    ]
                );
            }

            // Update attempt
            $scorePercentage = ($totalMarks / max($attempt->total_marks_possible, 1)) * 100;
            $attempt->update([
                'submitted_at' => now(),
                'total_marks_scored' => $totalMarks,
                'score_percentage' => $scorePercentage,
                'status' => 'submitted',
            ]);

            // Auto-complete the lesson based on quiz results
            $this->completeLesson($userId, $assessment->lesson_id, $scorePercentage);
        });

        return response()->json([
            'message' => 'Responses submitted successfully!',
            'attempt_id' => $attempt->id,
            'total_marks' => $totalMarks,
            'score_percentage' => $attempt->fresh()->score_percentage,
        ]);
    }

    // Auto-complete lesson based on quiz score
    private function completeLesson($studentId, $lessonId, $scorePercentage)
    {
        $coveredLesson = CoveredLesson::where('student_id', $studentId)
            ->where('lesson_id', $lessonId)
            ->first();

        if (!$coveredLesson) {
            // If lesson wasn't started, create it now
            $coveredLesson = CoveredLesson::create([
                'student_id' => $studentId,
                'lesson_id' => $lessonId,
                'status' => 'in-progress',
                'started_at' => now(),
            ]);
        }

        // Only update if not already completed or if new score is better
        $passedThreshold = 70; // Default passing score
        $passed = $scorePercentage >= $passedThreshold;

        // Update lesson completion status
        $coveredLesson->update([
            'status' => $passed ? 'completed' : 'failed',
            'score' => $scorePercentage,
            'completed_at' => now(),
        ]);

        return $coveredLesson;
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
