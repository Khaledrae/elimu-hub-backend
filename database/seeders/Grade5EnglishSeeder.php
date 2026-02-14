<?php

namespace Database\Seeders;

use App\Models\Assessment;
use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Teacher;
use App\Models\ClassModel;
use App\Models\Course;

class Grade5EnglishSeeder extends Seeder
{
    public function run(): void
    {
        $class  = ClassModel::where('name', 'Grade 5')->firstOrFail();
        $course = Course::where('title', 'English')->firstOrFail();
        $teacher = Teacher::where('subject_specialization', 'English')->first();
        $teacherUserId = $teacher?->user_id;

        $lessons = [
            [
                'title' => 'Following Instructions',
                'description' => 'Listening and following steps.',
                'content' => 'Learners follow spoken instructions correctly.',
                'questions' => [
                    [
                        'text' => 'Why is it important to listen carefully?',
                        'options' => [
                            'To finish fast',
                            'To follow instructions correctly',
                            'To avoid reading',
                            'To speak loudly'
                        ],
                        'answer' => 'B',
                        'marks' => 2,
                        'blooms' => 'understand',
                    ],
                ],
            ],
            [
                'title' => 'Reading Comprehension',
                'description' => 'Understanding passages.',
                'content' => 'Learners answer questions based on passages.',
                'questions' => [
                    [
                        'text' => 'What does inference mean?',
                        'options' => [
                            'Reading loudly',
                            'Guessing using clues',
                            'Copying words',
                            'Skipping sentences'
                        ],
                        'answer' => 'B',
                        'marks' => 2,
                        'blooms' => 'analyze',
                    ],
                ],
            ],
            [
                'title' => 'Adverbs',
                'description' => 'Words that describe verbs.',
                'content' => 'Adverbs tell how, when, or where an action happens.',
                'questions' => [
                    [
                        'text' => 'Which word is an adverb?',
                        'options' => ['Quickly', 'Happy', 'Dog', 'Chair'],
                        'answer' => 'A',
                        'marks' => 1,
                        'blooms' => 'remember',
                    ],
                ],
            ],
            [
                'title' => 'Conjunctions',
                'description' => 'Joining sentences.',
                'content' => 'Conjunctions join words or sentences.',
                'questions' => [
                    [
                        'text' => 'Which word is a conjunction?',
                        'options' => ['And', 'Run', 'Blue', 'Under'],
                        'answer' => 'A',
                        'marks' => 1,
                        'blooms' => 'remember',
                    ],
                ],
            ],
            [
                'title' => 'Prepositions',
                'description' => 'Showing position.',
                'content' => 'Prepositions show where something is.',
                'questions' => [
                    [
                        'text' => 'Which word shows position?',
                        'options' => ['Under', 'Jump', 'Happy', 'Sing'],
                        'answer' => 'A',
                        'marks' => 1,
                        'blooms' => 'apply',
                    ],
                ],
            ],
            [
                'title' => 'Letter Writing',
                'description' => 'Writing informal letters.',
                'content' => 'Letters have addresses, greetings, and closings.',
                'questions' => [
                    [
                        'text' => 'What comes first in a letter?',
                        'options' => [
                            'Closing',
                            'Body',
                            'Address',
                            'Signature'
                        ],
                        'answer' => 'C',
                        'marks' => 2,
                        'blooms' => 'remember',
                    ],
                ],
            ],
        ];

        $order = 1;

        foreach ($lessons as $lessonData) {
            $lesson = Lesson::create([
                'class_id' => $class->id,
                'course_id' => $course->id,
                'teacher_id' => $teacherUserId,
                'title' => $lessonData['title'],
                'description' => $lessonData['description'],
                'content_type' => 'text',
                'content' => $lessonData['content'],
                'order' => $order++,
                'status' => 'published',
            ]);
            $assessment = Assessment::create([
                'lesson_id'       => $lesson->id,
                'teacher_id'      => $teacherUserId,
                'title'           => $lessonData['title'] . ' Assessment',
                'instructions'    => 'Answer all questions.',
                'type'            => 'quiz',
                'total_marks'     => count($lessonData['questions']) * 2,
                'duration_minutes' => 10,
                'status'          => 'published',
            ]);
            foreach ($lessonData['questions'] as $q) {
                Question::create([
                    'assessment_id' => $assessment->id,
                    'set_by' => $teacherUserId,
                    'question_text' => $q['text'],
                    'marks' => $q['marks'],
                    'option_a' => $q['options'][0],
                    'option_b' => $q['options'][1],
                    'option_c' => $q['options'][2],
                    'option_d' => $q['options'][3],
                    'correct_option' => $q['answer'],
                    //'blooms_level' => $q['blooms'] ?? 'remember',
                ]);
            }
        }
    }
}
