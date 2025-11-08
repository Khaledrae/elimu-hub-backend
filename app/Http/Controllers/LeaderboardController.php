<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StudentResponse;
use App\Models\StudentPerformance;
use App\Models\ClassModel;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    /**
     * Get leaderboard for a class (based on overall performance)
     */
    public function classLeaderboard($classId)
    {
        $class = ClassModel::with('lessons.course')->findOrFail($classId);

        $results = StudentResponse::select(
                'student_id',
                DB::raw('SUM(marks_awarded) as total_marks'),
                DB::raw('SUM(questions.marks) as possible_marks')
            )
            ->join('questions', 'student_responses.question_id', '=', 'questions.id')
            ->join('assessments', 'student_responses.assessment_id', '=', 'assessments.id')
            ->join('courses', 'assessments.course_id', '=', 'courses.id')
            ->join('class_course', 'courses.id', '=', 'class_course.course_id')
            ->where('class_course.class_id', $classId)
            ->groupBy('student_id')
            ->orderByDesc('total_marks')
            ->take(10)
            ->get()
            ->map(function ($item) {
                $student = $item->student ?? null;

                return [
                    'student_id' => $item->student_id,
                    'name' => optional($student->user)->name,
                    'total_marks' => (int) $item->total_marks,
                    'possible_marks' => (int) $item->possible_marks,
                    'average_score' => round(($item->total_marks / max($item->possible_marks, 1)) * 100, 2),
                ];
            });

        return response()->json([
            'class' => [
                'id' => $class->id,
                'name' => $class->name,
                'level_group' => $class->level_group,
            ],
            'leaderboard' => $results,
        ]);
    }

    /**
     * Get leaderboard for a specific course
     */
    public function courseLeaderboard($courseId)
    {
        $course = Course::findOrFail($courseId);

        $results = StudentResponse::select(
                'student_id',
                DB::raw('SUM(marks_awarded) as total_marks'),
                DB::raw('SUM(questions.marks) as possible_marks')
            )
            ->join('questions', 'student_responses.question_id', '=', 'questions.id')
            ->join('assessments', 'student_responses.assessment_id', '=', 'assessments.id')
            ->where('assessments.course_id', $courseId)
            ->groupBy('student_id')
            ->orderByDesc('total_marks')
            ->take(10)
            ->get()
            ->map(function ($item) {
                $student = $item->student ?? null;

                return [
                    'student_id' => $item->student_id,
                    'name' => optional($student->user)->name,
                    'total_marks' => (int) $item->total_marks,
                    'possible_marks' => (int) $item->possible_marks,
                    'average_score' => round(($item->total_marks / max($item->possible_marks, 1)) * 100, 2),
                ];
            });

        return response()->json([
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'level' => $course->level,
            ],
            'leaderboard' => $results,
        ]);
    }
}

