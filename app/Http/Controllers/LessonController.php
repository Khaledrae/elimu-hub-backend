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

    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'course_id' => 'required|exists:courses,id',
    //         'class_id' => 'nullable|exists:classes,id',
    //         'teacher_id' => 'nullable|exists:teachers,user_id',
    //         'title' => 'required|string|max:255',
    //         'content_type' => 'required|in:text,video,document',
    //         'content' => 'nullable|string',
    //         'description' => 'nullable|string',
    //         'video_url' => 'nullable|string',
    //         'document_path' => 'nullable|string',
    //         'order' => 'nullable|integer',
    //         'status' => 'nullable|in:draft,published',
    //     ]);
    //     // If order is not provided, set it to the next available order
    //     if (!isset($data['order'])) {
    //         $maxOrder = Lesson::where('course_id', $data['course_id'])
    //             ->where('class_id', $data['class_id'])
    //             ->max('order');
    //         $data['order'] = $maxOrder ? $maxOrder + 1 : 1;
    //     }

    //     $lesson = Lesson::create($data);

    //     return response()->json($lesson->load(['course', 'class', 'teacher']), 201);
    // }
    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'class_id' => 'nullable|exists:classes,id',
            'teacher_id' => 'nullable|exists:teachers,user_id',
            'title' => 'required|string|max:255',
            'content_type' => 'required|in:text,video,document',
            'content' => 'nullable|string',
            'description' => 'nullable|string',
            'video_url' => 'nullable|string|url',
            'document_path' => 'nullable|string',
            'order' => 'required|integer|min:1',
            'status' => 'nullable|in:active,inactive,draft,published',
        ]);

        // Get existing lessons for this course and class
        $existingLessons = Lesson::where('course_id', $data['course_id'])
            ->where('class_id', $data['class_id'])
            ->orderBy('order')
            ->get();

        $targetOrder = $data['order'];

        // If there are no existing lessons or target order is greater than max order + 1
        if ($existingLessons->isEmpty() || $targetOrder > $existingLessons->count() + 1) {
            // Just create the lesson with the given order
            $data['order'] = $targetOrder;
        } else {
            // Shift all lessons from target order onward
            foreach ($existingLessons as $lesson) {
                if ($lesson->order >= $targetOrder) {
                    $lesson->order += 1;
                    $lesson->save();
                }
            }
        }

        $lesson = Lesson::create($data);

        return response()->json($lesson->load(['course', 'class', 'teacher']), 201);
    }

    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        $data = $request->validate([
            'course_id' => 'sometimes|exists:courses,id',
            'class_id' => 'nullable|exists:classes,id',
            'teacher_id' => 'nullable|exists:teachers,user_id',
            'title' => 'sometimes|string|max:255',
            'content_type' => 'sometimes|in:text,video,document',
            'content' => 'nullable|string',
            'description' => 'nullable|string',
            'video_url' => 'nullable|string|url',
            'document_path' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
            'status' => 'nullable|in:active,inactive,draft,published',
        ]);

        // Handle order change if provided and different from current
        if (isset($data['order']) && $data['order'] != $lesson->order) {
            $existingLessons = Lesson::where('course_id', $lesson->course_id)
                ->where('class_id', $lesson->class_id)
                ->where('id', '!=', $lesson->id)
                ->orderBy('order')
                ->get();

            $oldOrder = $lesson->order;
            $newOrder = $data['order'];

            if ($newOrder > $oldOrder) {
                // Moving lesson down in order (e.g., from 2 to 5)
                foreach ($existingLessons as $existingLesson) {
                    if ($existingLesson->order > $oldOrder && $existingLesson->order <= $newOrder) {
                        $existingLesson->order -= 1;
                        $existingLesson->save();
                    }
                }
            } else {
                // Moving lesson up in order (e.g., from 5 to 2)
                foreach ($existingLessons as $existingLesson) {
                    if ($existingLesson->order >= $newOrder && $existingLesson->order < $oldOrder) {
                        $existingLesson->order += 1;
                        $existingLesson->save();
                    }
                }
            }
        }

        $lesson->update($data);

        return response()->json($lesson->load(['course', 'class', 'teacher']));
    }

    // Add a method to get ordered lessons for dropdown
    public function getOrderOptions($courseId, $classId)
    {
        $lessons = Lesson::where('course_id', $courseId)
            ->where('class_id', $classId)
            ->orderBy('order')
            ->get(['id', 'title', 'order']);

        // Generate position options
        $positions = [];

        // Add "First" position
        $positions[] = [
            'value' => 1,
            'label' => 'First',
            'description' => 'Will be placed at the beginning'
        ];

        // Add positions between existing lessons
        for ($i = 0; $i < count($lessons); $i++) {
            $currentOrder = $i + 1;
            $nextOrder = $currentOrder + 1;

            $positions[] = [
                'value' => $nextOrder,
                'label' => "After: " . $lessons[$i]->title,
                'description' => "Current position: " . $currentOrder
            ];
        }

        // If there are no lessons, only show "First"
        if (count($lessons) === 0) {
            return response()->json([
                'positions' => $positions,
                'currentLessons' => []
            ]);
        }

        return response()->json([
            'positions' => $positions,
            'currentLessons' => $lessons
        ]);
    }

    public function show($id)
    {
        return response()->json(
            Lesson::with(['course', 'class', 'teacher'])->findOrFail($id)
        );
    }

    // public function update(Request $request, $id)
    // {
    //     $lesson = Lesson::findOrFail($id);

    //     $data = $request->validate([
    //         'course_id' => 'sometimes|exists:courses,id',
    //         'class_id' => 'nullable|exists:classes,id',
    //         'teacher_id' => 'nullable|exists:teachers,user_id',
    //         'title' => 'sometimes|string|max:255',
    //         'content_type' => 'sometimes|in:text,video,document',
    //         'content' => 'nullable|string',
    //         'description' => 'nullable|string',
    //         'video_url' => 'nullable|string|url',
    //         'document_path' => 'nullable|string',
    //         'order' => 'nullable|integer',
    //         'status' => 'nullable|in:active,inactive,draft,published',
    //     ]);

    //     $lesson->update($data);

    //     return response()->json($lesson->load(['course', 'class', 'teacher']));
    // }

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
    public function byCourseAndClass($courseId, $classId)
    {
        $lessons = Lesson::where('course_id', $courseId)
            ->where('class_id', $classId)
            ->with(['teacher', 'class'])
            ->orderBy('order')
            ->get();

        return response()->json($lessons);
    }
    // Get next lesson in sequence
    public function getNextLesson($currentLessonId)
    {
        $currentLesson = Lesson::findOrFail($currentLessonId);

        $nextLesson = Lesson::where('course_id', $currentLesson->course_id)
            ->where('class_id', $currentLesson->class_id)
            ->where('order', '>', $currentLesson->order)
            ->where('status', 'active')
            ->orderBy('order')
            ->first();

        if (!$nextLesson) {
            return response()->json(['message' => 'No next lesson found'], 404);
        }

        return response()->json($nextLesson->load(['course', 'class', 'teacher']));
    }
}
