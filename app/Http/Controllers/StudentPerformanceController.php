<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StudentResponse;
use App\Models\StudentPerformance;
use Illuminate\Support\Facades\Auth;

class StudentPerformanceController extends Controller
{
    public function summary()
    {
        $studentId = Auth::user()->student->id;

        $performances = StudentResponse::with(['question', 'assessment.course'])
            ->where('student_id', $studentId)
            ->get()
            ->groupBy(fn($r) => $r->assessment->course->id);

        $summary = $performances->map(function ($responses, $courseId) use ($studentId) {
            $courseTitle = $responses->first()->assessment->course->title ?? 'Unknown';
            $totalMarks = $responses->sum('marks_awarded');
            $possibleMarks = $responses->sum(fn($r) => $r->question->marks);
            $averageScore = round(($totalMarks / max($possibleMarks, 1)) * 100, 2);

            // Cache/update record
            StudentPerformance::updateOrCreate(
                ['student_id' => $studentId, 'course_id' => $courseId],
                [
                    'total_assessments' => $responses->pluck('assessment_id')->unique()->count(),
                    'total_marks' => $totalMarks,
                    'possible_marks' => $possibleMarks,
                    'average_score' => $averageScore,
                ]
            );

            return [
                'course_id' => $courseId,
                'course_title' => $courseTitle,
                'total_marks' => $totalMarks,
                'possible_marks' => $possibleMarks,
                'average_score' => $averageScore,
            ];
        })->values();

        return response()->json([
            'student_id' => $studentId,
            'performance_summary' => $summary,
        ]);
    }
}
