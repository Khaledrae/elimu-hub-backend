<?php

namespace Database\Seeders;

use App\Models\Assessment;
use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Teacher;
use App\Models\ClassModel;
use App\Models\Course;

class Grade6EnglishSeeder extends Seeder
{
    public function run(): void
    {
        $class  = ClassModel::where('name', 'Grade 6')->firstOrFail();
        $course = Course::where('title', 'English')->firstOrFail();
        $teacher = Teacher::where('subject_specialization', 'English')->first();
        $teacherUserId = $teacher?->user_id;

        $lessons = [
            [
                'title' => 'Listening for Information',
                'description' => 'Listening carefully to get specific details.',
                'content' => 'Learners listen to spoken texts and identify key points.',
                'questions' => [
                    [
                        'text' => 'Why is listening carefully important?',
                        'options' => [
                            'To speak first',
                            'To understand information',
                            'To finish quickly',
                            'To avoid writing'
                        ],
                        'answer' => 'B',
                        'marks' => 2,
                        'blooms' => 'understand',
                    ],
                ],
            ],
            [
                'title' => 'Reading Comprehension',
                'description' => 'Understanding and interpreting passages.',
                'content' => 'Learners read passages and answer questions.',
                'questions' => [
                    [
                        'text' => 'What is an inference?',
                        'options' => [
                            'A direct answer from the text',
                            'A guess using clues from the text',
                            'Reading aloud',
                            'Copying sentences'
                        ],
                        'answer' => 'B',
                        'marks' => 2,
                        'blooms' => 'analyze',
                    ],
                ],
            ],
            [
                'title' => 'Summarising',
                'description' => 'Writing short summaries.',
                'content' => 'A summary contains only important points.',
                'questions' => [
                    [
                        'text' => 'What should a good summary include?',
                        'options' => [
                            'Every detail',
                            'Main ideas only',
                            'Personal opinions',
                            'Pictures'
                        ],
                        'answer' => 'B',
                        'marks' => 2,
                        'blooms' => 'analyze',
                    ],
                ],
            ],
            [
                'title' => 'Tenses Review',
                'description' => 'Using correct verb tenses.',
                'content' => 'Verbs show time of action.',
                'questions' => [
                    [
                        'text' => 'Which sentence is in the past tense?',
                        'options' => [
                            'She runs fast',
                            'She is running',
                            'She ran fast',
                            'She will run'
                        ],
                        'answer' => 'C',
                        'marks' => 1,
                        'blooms' => 'apply',
                    ],
                ],
            ],
            [
                'title' => 'Reported Speech',
                'description' => 'Changing direct speech to reported speech.',
                'content' => 'Reported speech tells what someone said.',
                'questions' => [
                    [
                        'text' => 'Which is reported speech?',
                        'options' => [
                            '"I am tired," he said.',
                            'He said that he was tired.',
                            'I am tired.',
                            'He is tired.'
                        ],
                        'answer' => 'B',
                        'marks' => 2,
                        'blooms' => 'apply',
                    ],
                ],
            ],
            [
                'title' => 'Conjunctions and Clauses',
                'description' => 'Joining ideas correctly.',
                'content' => 'Conjunctions connect clauses.',
                'questions' => [
                    [
                        'text' => 'Which word joins two sentences?',
                        'options' => ['Because', 'Quick', 'Jump', 'Happy'],
                        'answer' => 'A',
                        'marks' => 1,
                        'blooms' => 'remember',
                    ],
                ],
            ],
            [
                'title' => 'Formal Letter Writing',
                'description' => 'Writing formal letters.',
                'content' => 'Formal letters use polite language.',
                'questions' => [
                    [
                        'text' => 'Which is used in a formal letter?',
                        'options' => [
                            'Slang',
                            'Polite language',
                            'Nicknames',
                            'Jokes'
                        ],
                        'answer' => 'B',
                        'marks' => 2,
                        'blooms' => 'understand',
                    ],
                ],
            ],
            [
                'title' => 'Dialogue Writing',
                'description' => 'Writing conversations.',
                'content' => 'Dialogue shows what people say.',
                'questions' => [
                    [
                        'text' => 'What punctuation is used in dialogue?',
                        'options' => [
                            'Quotation marks',
                            'Full stops only',
                            'Brackets',
                            'Commas only'
                        ],
                        'answer' => 'A',
                        'marks' => 1,
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
                    'assessment_id' => $assessment->id, // attach later if needed
                    'set_by' => $teacherUserId,
                    'question_text' => $q['text'],
                    'marks' => $q['marks'],
                    'option_a' => $q['options'][0],
                    'option_b' => $q['options'][1],
                    'option_c' => $q['options'][2],
                    'option_d' => $q['options'][3],
                    'correct_option' => $q['answer'],
                    'blooms_level' => $q['blooms'] ?? 'remember',
                ]);
            }
        }
    }
}
