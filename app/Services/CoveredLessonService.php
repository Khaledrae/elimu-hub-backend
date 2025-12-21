<?php

namespace App\Services;

use App\Models\CoveredLesson;
use App\Models\Lesson;
use Illuminate\Support\Facades\DB;

class CoveredLessonService
{
    //     public function markAsInProgress($studentId, $lessonId)
    //     {
    //         $lesson = Lesson::findOrFail($lessonId);

    //         return CoveredLesson::updateOrCreate(
    //             [
    //                 'student_id' => $studentId,
    //                 'lesson_id' => $lessonId,
    //             ],
    //             [
    //                 'course_id' => $lesson->course_id, // Fetch from lesson
    //                 'class_id' => $lesson->class_id,   // Fetch from lesson
    //                 'status' => 'in-progress',
    //                 'started_at' => now(),
    //                 'attempts' => DB::raw('IFNULL(attempts, 0) + 1'),
    //             ]
    //         );
    //     }

    public function markAsInProgress($studentId, $lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);

        // Check if record exists
        $coveredLesson = CoveredLesson::where('student_id', $studentId)
            ->where('lesson_id', $lessonId)
            ->first();

        if ($coveredLesson) {
            // Update existing
            $coveredLesson->update([
                'status' => 'in-progress',
                'started_at' => now(),
                'attempts' => $coveredLesson->attempts + 1,
            ]);
        } else {
            // Create new
            $coveredLesson = CoveredLesson::create([
                'student_id' => $studentId,
                'lesson_id' => $lessonId,
                'course_id' => $lesson->course_id,
                'class_id' => $lesson->class_id,
                'status' => 'in-progress',
                'started_at' => now(),
                'attempts' => 1,
            ]);
        }

        return $coveredLesson;
    }
    public function markCompleted($studentId, $lessonId)
    {
        $lesson = CoveredLesson::updateOrCreate(
            ['student_id' => $studentId, 'lesson_id' => $lessonId],
            ['status' => 'completed']
        );

        return $lesson;
    }

    public function getStudentProgress($studentId, $courseId)
    {
        $totalLessons = Lesson::where('course_id', $courseId)->count();
        $completedLessons = CoveredLesson::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('status', 'completed')
            ->count();

        $percent = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

        return $percent;
    }

    public function getRecentLessons($studentId, $limit = 5)
    {
        return CoveredLesson::with('lesson')
            ->where('student_id', $studentId)
            ->orderByDesc('updated_at')
            ->take($limit)
            ->get();
    }

    public function getLastLesson($studentId)
    {
        return CoveredLesson::with('lesson')
            ->where('student_id', $studentId)
            ->orderByDesc('updated_at')
            ->first();
    }
}
