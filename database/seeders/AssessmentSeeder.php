<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Assessment;
use App\Models\Lesson;
use App\Models\Teacher;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $subject = 'English';
        $teacher = Teacher::where('subject_specialization', $subject)->first();
        $user_id = $teacher ? $teacher->id : null;

        $lessonTitle = "Introduction to English Letters";
        $lesson = Lesson::where('title', $lessonTitle)->first();
        $lessonId = $lesson ? $lesson->id : null;

        $assessment = Assessment::create([
            'lesson_id'       => $lessonId,             // Adjust if needed
            'teacher_id'      => $user_id,
            'title'           => 'English Basics Quiz – Alphabet & Sounds',
            'instructions'    => 'Answer all questions. Choose the best option.',
            'type'            => 'quiz',
            'total_marks'     => 10,
            'duration_minutes' => 10,
            'status'          => 'published',
        ]);

        // Store the id for question seeding
        file_put_contents(storage_path('app/assessment_id.txt'), $assessment->id);
    }
}
