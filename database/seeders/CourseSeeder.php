<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $courses = [
            'Mathematics', 'English', 'Kiswahili', 'Science & Technology',
            'Social Studies', 'CRE', 'Music', 'Art & Craft',
            'Physical Education', 'Agriculture', 'Home Science',
            'Integrated Science', 'Pre-Technical Studies', 'Business Studies',
            'Computer Science', 'Life Skills', 'Visual Arts',
            'Performing Arts', 'Religious Education'
        ];

        foreach ($courses as $title) {
            
            $teacher = Teacher::where('subject_specialization', $title)->first();

            Course::firstOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'description' => $title . ' course under CBC curriculum.',
                    'teacher_id' => $teacher ? $teacher->user_id : null,
                    'thumbnail' => 'default.png',
                    'level' => 'CBC',
                    'status' => 'active',
                ]
            );
        }
    }
}
