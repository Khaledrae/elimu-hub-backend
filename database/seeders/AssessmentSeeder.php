<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Assessment;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $assessment = Assessment::create([
            'lesson_id'       => 1,             // Adjust if needed
            'teacher_id'      => 26,
            'title'           => 'English Basics Quiz – Alphabet & Sounds',
            'instructions'    => 'Answer all questions. Choose the best option.',
            'type'            => 'quiz',
            'total_marks'     => 10,
            'duration_minutes'=> 10,
            'status'          => 'active',
        ]);

        // Store the id for question seeding
        file_put_contents(storage_path('app/assessment_id.txt'), $assessment->id);
    }
}
