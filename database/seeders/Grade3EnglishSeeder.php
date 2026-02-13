<?php

namespace Database\Seeders;

use App\Models\Assessment;
use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Teacher;
use App\Models\ClassModel;
use App\Models\Course;

class Grade3EnglishSeeder extends Seeder
{
    public function run(): void
    {
        $class  = ClassModel::where('name', 'Grade 3')->firstOrFail();
        $course = Course::where('title', 'English')->firstOrFail();
        $teacher = Teacher::where('subject_specialization', 'English')->first();

        $teacherUserId = $teacher?->user_id;

        $lessons = [
            [
                'title' => 'Greetings and Polite Expressions',
                'description' => 'Using polite language in daily communication.',
                'content' => 'Learners practice saying please, thank you, excuse me, and sorry.',
                'questions' => [
                    [
                        'text' => 'Which word is polite?',
                        'options' => ['Please', 'Shout', 'Run', 'Jump'],
                        'answer' => 'A',
                        'marks' => 1,
                        'blooms' => 'remember',
                    ],
                    [
                        'text' => 'When should we say thank you?',
                        'options' => ['When helped', 'When angry', 'When sleeping', 'When running'],
                        'answer' => 'A',
                        'marks' => 1,
                        'blooms' => 'understand',
                    ],
                ],
            ],
            [
                'title' => 'Reading Simple Sentences',
                'description' => 'Reading and understanding short sentences.',
                'content' => 'Learners read sentences aloud and answer questions.',
                'questions' => [
                    [
                        'text' => 'Which sentence is correct?',
                        'options' => [
                            'The dog run fast.',
                            'The dog runs fast.',
                            'Dog the fast runs.',
                            'Run dog fast.'
                        ],
                        'answer' => 'B',
                        'marks' => 2,
                        'blooms' => 'apply',
                    ],
                ],
            ],
            [
                'title' => 'Nouns and Naming Words',
                'description' => 'Identifying nouns in sentences.',
                'content' => 'Nouns are words that name people, places, animals, or things.',
                'questions' => [
                    [
                        'text' => 'Which word is a noun?',
                        'options' => ['Teacher', 'Run', 'Quickly', 'Blue'],
                        'answer' => 'A',
                        'marks' => 1,
                        'blooms' => 'remember',
                    ],
                ],
            ],
            [
                'title' => 'Verbs and Action Words',
                'description' => 'Learning action words.',
                'content' => 'Verbs show action in a sentence.',
                'questions' => [
                    [
                        'text' => 'Which word shows action?',
                        'options' => ['Jump', 'Table', 'Happy', 'Blue'],
                        'answer' => 'A',
                        'marks' => 1,
                        'blooms' => 'remember',
                    ],
                ],
            ],
            [
                'title' => 'Adjectives and Describing Words',
                'description' => 'Words that describe nouns.',
                'content' => 'Adjectives describe how something looks, feels, or sounds.',
                'questions' => [
                    [
                        'text' => 'Which word describes a cat?',
                        'options' => ['Soft', 'Run', 'Jump', 'Eat'],
                        'answer' => 'A',
                        'marks' => 1,
                        'blooms' => 'understand',
                    ],
                ],
            ],
            [
                'title' => 'Singular and Plural Nouns',
                'description' => 'Understanding one and many.',
                'content' => 'Singular means one, plural means more than one.',
                'questions' => [
                    [
                        'text' => 'What is the plural of book?',
                        'options' => ['Books', 'Bookes', 'Book', 'Booking'],
                        'answer' => 'A',
                        'marks' => 1,
                        'blooms' => 'apply',
                    ],
                ],
            ],
            [
                'title' => 'Sentence Construction',
                'description' => 'Writing correct sentences.',
                'content' => 'Sentences must have a subject and a verb.',
                'questions' => [
                    [
                        'text' => 'Which is a complete sentence?',
                        'options' => [
                            'Running fast',
                            'The boy runs.',
                            'Fast running boy',
                            'Runs the boy fast'
                        ],
                        'answer' => 'B',
                        'marks' => 2,
                        'blooms' => 'analyze',
                    ],
                ],
            ],
            [
                'title' => 'Capital Letters and Full Stops',
                'description' => 'Correct use of punctuation.',
                'content' => 'Sentences begin with capital letters and end with full stops.',
                'questions' => [
                    [
                        'text' => 'Which sentence is correct?',
                        'options' => [
                            'the boy runs.',
                            'The boy runs',
                            'The boy runs.',
                            'the boy runs'
                        ],
                        'answer' => 'C',
                        'marks' => 2,
                        'blooms' => 'apply',
                    ],
                ],
            ],
        ];

        $order = 1;

        foreach ($lessons as $lessonData) {
            $lesson = Lesson::create([
                'class_id'    => $class->id,
                'course_id'   => $course->id,
                'teacher_id'  => $teacherUserId,
                'title'       => $lessonData['title'],
                'description' => $lessonData['description'],
                'content_type' => 'text',
                'content'     => $lessonData['content'],
                'order'       => $order++,
                'status'      => 'published',
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
                    'set_by'        => $teacherUserId,
                    'question_text' => $q['text'],
                    'marks'         => $q['marks'],
                    'option_a'      => $q['options'][0],
                    'option_b'      => $q['options'][1],
                    'option_c'      => $q['options'][2],
                    'option_d'      => $q['options'][3],
                    'correct_option' => $q['answer'],
                    //'blooms_level'  => $q['blooms'] ?? 'remember',
                ]);
            }
        }
    }
}
