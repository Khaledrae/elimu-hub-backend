<?php

namespace Database\Seeders;

use App\Models\ClassModel;
use App\Models\Course;
use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\Teacher;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        $grade = "Grade 1";
        $class = ClassModel::where('name', $grade)->first();
        $classId = $class->id; 

        $subject = 'English';
        $course = Course::where('title', $subject)->first();
        $courseId = $course->id; 

        $teacher = Teacher::where('subject_specialization', $subject)->first();
        $teacherId = $teacher ? $teacher->id : null;
        

        $lessons = [
            [
                'title' => 'Introduction to English Letters',
                'description' => 'Overview of basic English alphabet for Grade 1 learners.',
                'content_type' => 'video',
                'content' => null,
                'video_url' => 'https://www.youtube.com/watch?v=drlIUqRYM-w',
                'order' => 1,
            ],
            [
                'title' => 'Vowels and Consonants',
                'description' => 'Explains vowels and consonants in simple language.',
                'content_type' => 'text',
                'content' => 'In English, we have vowels (A, E, I, O, U) and consonants (all other letters). Vowels help us form sounds.',
                'video_url' => null,
                'order' => 2,
            ],
            [
                'title' => 'Basic English Words',
                'description' => 'Learn simple words using basic phonics.',
                'content_type' => 'video',
                'content' => null,
                'video_url' => 'https://www.youtube.com/watch?v=bkPIMvXKNRc',
                'order' => 3,
            ],
            [
                'title' => 'Reading Simple Sentences',
                'description' => 'Introduction to reading short sentences.',
                'content_type' => 'text',
                'content' => 'Example sentences: "I am a boy.", "The cat is big.", "We go to school."',
                'video_url' => null,
                'order' => 4,
            ],
        ];

        foreach ($lessons as $lesson) {
            Lesson::create([
                'class_id' => $classId,
                'course_id' => $courseId,
                'teacher_id' => $teacherId,
                'title' => $lesson['title'],
                'description' => $lesson['description'],
                'content_type' => $lesson['content_type'],
                'content' => $lesson['content'],
                'video_url' => $lesson['video_url'],
                'order' => $lesson['order'],
                'status' => 'published',
            ]);
        }
    }
}
