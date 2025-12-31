<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\CoveredLesson;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Course;
use App\Models\Student;
use App\Services\CoveredLessonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoveredLessonController extends Controller
{
    protected CoveredLessonService $coveredLessonService;

    public function __construct(CoveredLessonService $coveredLessonService)
    {
        $this->coveredLessonService = $coveredLessonService;
    }

    // Get student's covered lessons with pagination and filters
    public function index(Request $request, $studentId)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:in-progress,completed,failed',
            'course_id' => 'sometimes|exists:courses,id',
            'class_id' => 'sometimes|exists:classes,id',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);

        $query = CoveredLesson::with(['lesson', 'course', 'class'])
            ->where('student_id', $studentId);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $validated['status']);
        }

        if ($request->has('course_id')) {
            $query->where('course_id', $validated['course_id']);
        }

        if ($request->has('class_id')) {
            $query->where('class_id', $validated['class_id']);
        }

        if ($request->has('date_from')) {
            $query->whereDate('updated_at', '>=', $validated['date_from']);
        }

        if ($request->has('date_to')) {
            $query->whereDate('updated_at', '<=', $validated['date_to']);
        }

        // Sort by most recent
        $query->orderBy('updated_at', 'desc');

        $perPage = $validated['per_page'] ?? 20;
        return response()->json($query->paginate($perPage));
    }

    // Start a lesson (mark as in-progress)
    // public function startLesson(Request $request, $studentId)
    // {
    //     $validated = $request->validate([
    //         'lesson_id' => 'required|exists:lessons,id',
    //         'course_id' => 'sometimes|exists:courses,id',
    //         'class_id' => 'sometimes|exists:classes,id',
    //     ]);

    //     // Get lesson to get course_id if not provided
    //     $lesson = Lesson::findOrFail($validated['lesson_id']);

    //     $coveredLesson = CoveredLesson::updateOrCreate(
    //         [
    //             'student_id' => $studentId,
    //             'lesson_id' => $validated['lesson_id'],
    //         ],
    //         [
    //             'course_id' => $validated['course_id'] ?? $lesson->course_id,
    //             'class_id' => $validated['class_id'] ?? null,
    //             'status' => 'in-progress',
    //             'started_at' => now(),
    //             'attempts' => DB::raw('attempts + 1'),
    //         ]
    //     );

    //     return response()->json([
    //         'message' => 'Lesson started',
    //         'data' => $coveredLesson->load('lesson')
    //     ], 201);
    // }

    public function startLesson(Request $request, $studentId)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
        ]);

        if ($request->has('course_id') || $request->has('class_id')) {
            $lesson = Lesson::findOrFail($validated['lesson_id']);

            if ($request->has('course_id') && $lesson->course_id != $validated['course_id']) {
                return response()->json([
                    'message' => 'Course ID does not match lesson\'s course',
                    'lesson_course_id' => $lesson->course_id
                ], 422);
            }

            if ($request->has('class_id') && $lesson->class_id != $validated['class_id']) {
                return response()->json([
                    'message' => 'Class ID does not match lesson\'s class',
                    'lesson_class_id' => $lesson->class_id
                ], 422);
            }
        }

        $coveredLesson = $this->coveredLessonService->markAsInProgress(
            $studentId,
            $validated['lesson_id']
        );

        return response()->json([
            'message' => 'Lesson started',
            'data' => $coveredLesson->load('lesson')
        ], 201);
    }

    // Complete a lesson
    public function completeLesson(Request $request, $studentId, $lessonId)
    {
        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'time_spent' => 'sometimes|integer|min:0', // in seconds
            'passed_threshold' => 'sometimes|numeric|min:0|max:100', // passing score threshold
        ]);

        $coveredLesson = CoveredLesson::where('student_id', $studentId)
            ->where('lesson_id', $lessonId)
            ->first();

        if (!$coveredLesson) {
            return response()->json([
                'message' => 'Cannot complete lesson that hasn\'t been started.',
                'required_action' => 'Start the lesson first',
                'endpoint' => 'POST /students/' . $studentId . '/covered-lessons/start',
                'payload_example' => ['lesson_id' => $lessonId]
            ], 400);
        }
        $passedThreshold = $validated['passed_threshold'] ?? 70; // Default 70% pass
        $passed = $validated['score'] >= $passedThreshold;

        $coveredLesson->update([
            'status' => $passed ? 'completed' : 'failed',
            'score' => $validated['score'],
            'time_spent' => $validated['time_spent'] ?? $coveredLesson->time_spent,
            'completed_at' => now(),
        ]);

        return response()->json([
            'message' => $passed ? 'Lesson completed successfully!' : 'Lesson failed. Please try again.',
            'data' => $coveredLesson->fresh(['lesson', 'course']),
            'passed' => $passed,
        ]);
    }

    // Get last lesson for student
    public function recentLesson($studentId)
    {
        $lastLesson = CoveredLesson::with(['lesson', 'course'])
            ->where('student_id', $studentId)
            ->orderBy('updated_at', 'desc')
            ->get();

        if (!$lastLesson) {
            return response()->json([
                'message' => 'No lessons started yet',
                'data' => null
            ]);
        }

        return response()->json([
            'data' => $lastLesson
        ]);
    }
    public function courseRecentLessons($studentId, $courseId)
    {
        $lastLesson = CoveredLesson::with(['lesson', 'course'])
            ->where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->orderBy('updated_at', 'desc')
            ->get();

        if (!$lastLesson) {
            return response()->json([
                'message' => 'No lessons started yet',
                'data' => null
            ]);
        }

        return response()->json([
            'data' => $lastLesson
        ]);
    }
    public function lastLesson($studentId)
    {
        $lastLesson = CoveredLesson::with(['lesson', 'course'])
            ->where('student_id', $studentId)
            ->orderBy('updated_at', 'desc')
            ->first();

        if (!$lastLesson) {
            return response()->json([
                'message' => 'No lessons started yet',
                'data' => null
            ]);
        }

        return response()->json([
            'data' => $lastLesson
        ]);
    }

    // Get course progress for student
    public function progress($courseId, $studentId)
    {
        $course = Course::findOrFail($courseId);

        $totalLessons = $course->lessons()->count();
        $completedLessons = CoveredLesson::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('status', 'completed')
            ->count();

        $inProgressLessons = CoveredLesson::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('status', 'in-progress')
            ->count();

        $failedLessons = CoveredLesson::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('status', 'failed')
            ->count();

        $averageScore = CoveredLesson::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('status', 'completed')
            ->avg('score') ?? 0;

        $progressPercentage = $totalLessons > 0
            ? round(($completedLessons / $totalLessons) * 100, 2)
            : 0;

        return response()->json([
            'course' => $course->only(['id', 'title', 'description']),
            'progress' => [
                'total_lessons' => $totalLessons,
                'completed' => $completedLessons,
                'in_progress' => $inProgressLessons,
                'failed' => $failedLessons,
                'not_started' => $totalLessons - ($completedLessons + $inProgressLessons + $failedLessons),
                'percentage' => $progressPercentage,
                'average_score' => round($averageScore, 2),
            ],
            'recent_lessons' => CoveredLesson::with('lesson')
                ->where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get()
        ]);
    }

    // Get overall student progress across all courses
    // public function overallProgress($studentId)
    // {
    //     $progress = CoveredLesson::selectRaw('
    //             COUNT(*) as total_covered,
    //             SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
    //             SUM(CASE WHEN status = "in-progress" THEN 1 ELSE 0 END) as in_progress,
    //             SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed,
    //             AVG(CASE WHEN status = "completed" THEN score ELSE NULL END) as average_score
    //         ')
    //         ->where('student_id', $studentId)
    //         ->first();

    //     $student = Student::where('user_id', $studentId)->first();
    //     $studentClassId = $student->grade_level;

    //     $totalLessons = $studentClassId
    //         ? Lesson::where('class_id', $studentClassId)->count()
    //         : Lesson::count();
    //     // Course-by-course breakdown
    //     $courseBreakdown = CoveredLesson::selectRaw('
    //         courses.id,
    //         courses.title,
    //         courses.code,
    //         COUNT(covered_lessons.id) as covered_lessons,
    //         SUM(CASE WHEN covered_lessons.status = "completed" THEN 1 ELSE 0 END) as completed,
    //         AVG(CASE WHEN covered_lessons.status = "completed" THEN covered_lessons.score ELSE NULL END) as average_score
    //     ')
    //         ->join('courses', 'covered_lessons.course_id', '=', 'courses.id')
    //         ->where('covered_lessons.student_id', $studentId)
    //         ->groupBy('courses.id', 'courses.title', 'courses.code')
    //         ->orderBy('courses.title')
    //         ->get()
    //         ->map(function ($course) use ($studentClassId) {
    //             // Get total lessons for this course (filtered by class if applicable)
    //             $totalCourseLessons = $studentClassId
    //                 ? Lesson::where('course_id', $course->id)
    //                 ->where('class_id', $studentClassId)
    //                 ->count()
    //                 : Lesson::where('course_id', $course->id)->count();

    //             return [
    //                 'course' => [
    //                     'id' => $course->id,
    //                     'name' => $course->name,
    //                     'code' => $course->code,
    //                 ],
    //                 'progress' => [
    //                     'completed' => (int) $course->completed,
    //                     'total' => $totalCourseLessons,
    //                     'percentage' => $totalCourseLessons > 0
    //                         ? round(($course->completed / $totalCourseLessons) * 100, 2)
    //                         : 0,
    //                     'average_score' => round($course->average_score ?? 0, 2),
    //                     'covered' => (int) $course->covered_lessons,
    //                 ],
    //             ];
    //         });
    //     return response()->json([
    //         'total_lessons' => $totalLessons,
    //         'covered_lessons' => (int) $progress->total_covered,
    //         'completed' => (int) $progress->completed,
    //         'in_progress' => (int) $progress->in_progress,
    //         'failed' => (int) $progress->failed,
    //         'completion_rate' => $totalLessons > 0
    //             ? round(($progress->total_covered / $totalLessons) * 100, 2)
    //             : 0,
    //         'average_score' => round($progress->average_score ?? 0, 2),
    //         'streak' => $this->calculateLearningStreak($studentId),
    //         'breakdown_by_course' => $courseBreakdown,
    //     ]);
    // }
    public function overallProgress($studentId)
    {
        $student = Student::with('class')->where('user_id', $studentId)->first();

        // Get covered lessons with course
        $coveredLessons = CoveredLesson::with('course')
            ->where('student_id', $studentId)
            ->get();

        // Overall stats
        $progress = [
            'total_covered' => $coveredLessons->count(),
            'completed' => $coveredLessons->where('status', 'completed')->count(),
            'in_progress' => $coveredLessons->where('status', 'in-progress')->count(),
            'failed' => $coveredLessons->where('status', 'failed')->count(),
            'average_score' => $coveredLessons->where('status', 'completed')->avg('score'),
        ];

        // Get total lessons for student's class
        $totalLessons = $student->grade_level
            ? Lesson::where('class_id', $student->grade_level)->count()
            : Lesson::count();

         // Get total courses for student's class
        $totalCourses = $student->grade_level
            ? Course::whereHas('lessons', function($query) use ($student) {
                $query->where('class_id', $student->grade_level);
              })->count()
            : Course::count();

        // Course breakdown
        $courseBreakdown = [];
        $groupedByCourse = $coveredLessons->groupBy('course_id');

        foreach ($groupedByCourse as $courseId => $courseLessons) {
            $course = Course::find($courseId);

            if (!$course) continue;

            // Get total lessons for this course (in student's class)
            $courseLessonQuery = Lesson::where('course_id', $courseId);
            if ($student->grade_level) {
                $courseLessonQuery->where('class_id', $student->grade_level);
            }
            $totalCourseLessons = $courseLessonQuery->count();

            $completedCount = $courseLessons->where('status', 'completed')->count();
            $avgScore = $courseLessons->where('status', 'completed')->avg('score');

            $courseBreakdown[] = [
                'course' => [
                    'id' => $course->id,
                    'title' => $course->title,
                    'slug' => $course->slug,
                    'code' => $course->code ?? null,
                ],
                'progress' => [
                    'completed' => $completedCount,
                    'total' => $totalCourseLessons,
                    'percentage' => $totalCourseLessons > 0
                        ? round(($completedCount / $totalCourseLessons) * 100, 2)
                        : 0,
                    'average_score' => round($avgScore ?? 0, 2),
                    'covered' => $courseLessons->count(),
                ],
            ];
        }

        return response()->json([
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'class' => $student->class?->only(['id', 'name']),
            ],
            'total_lessons' => $totalLessons,
            'total_courses' => $totalCourses,
            'covered_lessons' => $progress['total_covered'],
            'completed' => $progress['completed'],
            'in_progress' => $progress['in_progress'],
            'failed' => $progress['failed'],
            'completion_rate' => $totalLessons > 0
                ? round(($progress['total_covered'] / $totalLessons) * 100, 2)
                : 0,
            'average_score' => round($progress['average_score'] ?? 0, 2),
            'streak' => $this->calculateLearningStreak($studentId),
            'breakdown_by_course' => $courseBreakdown,
        ]);
    }


    // Get recent activities for a student
    public function recentActivities(Request $request, $studentId)
    {
        $limit = $request->query('limit', 10);
        
        $student = Student::where('user_id', $studentId)->first();
        
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $coveredLessons = CoveredLesson::with(['lesson', 'course'])
            ->where('student_id', $student->id)
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        $activities = $coveredLessons->map(function($covered) {
            $type = 'lesson_started';
            $description = "Started learning";
            
            if ($covered->status === 'completed') {
                $type = 'lesson_completed';
                $description = "Completed with score: {$covered->score}%";
            } elseif ($covered->status === 'failed') {
                $type = 'lesson_failed';
                $description = "Attempted but didn't pass (Score: {$covered->score}%)";
            } elseif ($covered->status === 'in-progress') {
                $type = 'lesson_started';
                $description = "Currently learning";
            }

            return [
                'id' => $covered->id,
                'type' => $type,
                'title' => $covered->lesson?->title ?? 'Lesson',
                'description' => $description,
                'date' => $covered->updated_at->toISOString(),
                'course_name' => $covered->course?->title ?? 'Course',
                'score' => $covered->score,
                'status' => $covered->status,
            ];
        });

        return response()->json($activities);
    }

    // Get pending assessments for a student
    public function pendingAssessments($studentId)
    {
        $student = Student::where('user_id', $studentId)->first();
        
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        // Get lessons that are in-progress
        $inProgressLessons = CoveredLesson::where('student_id', $student->user_id)
            ->where('status', 'in-progress')
            ->pluck('lesson_id');

        // Get assessments for these lessons
        $pendingAssessments = Assessment::with(['lesson.course'])
            ->whereIn('lesson_id', $inProgressLessons)
            ->where('status', 'published')
            ->get()
            ->map(function($assessment) {
                return [
                    'id' => $assessment->id,
                    'title' => $assessment->title,
                    'type' => $assessment->type,
                    'total_marks' => $assessment->total_marks,
                    'duration_minutes' => $assessment->duration_minutes,
                    'lesson' => [
                        'id' => $assessment->lesson->id,
                        'title' => $assessment->lesson->title,
                    ],
                    'course' => [
                        'id' => $assessment->lesson->course->id,
                        'title' => $assessment->lesson->course->title,
                    ],
                ];
            });

        return response()->json([
            'count' => $pendingAssessments->count(),
            'assessments' => $pendingAssessments,
        ]);
    }
    // Calculate learning streak
    private function calculateLearningStreak($studentId): int
    {
        $dates = CoveredLesson::where('student_id', $studentId)
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->pluck('created_at')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->unique()
            ->sort()
            ->values();

        $streak = 0;
        $currentDate = now()->format('Y-m-d');
        $checkDate = $currentDate;

        while ($dates->contains($checkDate)) {
            $streak++;
            $checkDate = date('Y-m-d', strtotime($checkDate . ' -1 day'));
        }

        return $streak;
    }
}
