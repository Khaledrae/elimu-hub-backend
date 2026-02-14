<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\Course;
use App\Models\ClassModel;
use App\Models\Teacher;

class Grade2EnglishSeeder extends Seeder
{
    public function run(): void
    {
        $grade   = 'Grade 2';
        $subject = 'English';

        $class   = ClassModel::where('name', $grade)->firstOrFail();
        $course  = Course::where('title', $subject)->firstOrFail();
        $teacher = Teacher::where('subject_specialization', $subject)->first();

        $teacherUserId = $teacher?->user_id;

        $themes = [

            // 1. Listening and Speaking
            [
                'title' => 'Listening and Speaking',
                'description' => 'Listening carefully and speaking clearly.',
                'content' => 'We listen when others speak and respond politely.',
                'order' => 1,
                'questions' => [
                    [
                        'question' => 'What should we do when someone is speaking?',
                        'options'  => ['Listen', 'Shout', 'Run', 'Sleep'],
                        'answer'   => 'A',
                    ],
                    [
                        'question' => 'How should we speak to others?',
                        'options'  => ['Rudely', 'Politely', 'Angrily', 'Loudly'],
                        'answer'   => 'B',
                    ],
                ],
            ],

            // 2. Greetings and Polite Language
            [
                'title' => 'Greetings and Polite Language',
                'description' => 'Using polite words every day.',
                'content' => 'We say please, thank you and excuse me.',
                'order' => 2,
                'questions' => [
                    [
                        'question' => 'Which word shows politeness?',
                        'options'  => ['Please', 'Go', 'Stop', 'Run'],
                        'answer'   => 'A',
                    ],
                    [
                        'question' => 'What do we say after receiving help?',
                        'options'  => ['Sorry', 'Thank you', 'Hello', 'Goodbye'],
                        'answer'   => 'B',
                    ],
                ],
            ],

            // 3. Reading Simple Stories
            [
                'title' => 'Reading Simple Stories',
                'description' => 'Reading short and simple stories.',
                'content' => 'Stories help us learn new words.',
                'order' => 3,
                'questions' => [
                    [
                        'question' => 'What do we read in a story?',
                        'options'  => ['Words', 'Numbers', 'Pictures only', 'Games'],
                        'answer'   => 'A',
                    ],
                    [
                        'question' => 'Stories can teach us?',
                        'options'  => ['Bad habits', 'Lessons', 'Noise', 'Fear'],
                        'answer'   => 'B',
                    ],
                ],
            ],

            // 4. Alphabetical Order
            [
                'title' => 'Alphabetical Order',
                'description' => 'Arranging words in alphabetical order.',
                'content' => 'A comes before B in the alphabet.',
                'order' => 4,
                'questions' => [
                    [
                        'question' => 'Which letter comes first?',
                        'options'  => ['A', 'C', 'D', 'B'],
                        'answer'   => 'A',
                    ],
                    [
                        'question' => 'Which word comes first alphabetically?',
                        'options'  => ['Apple', 'Ball', 'Cat', 'Dog'],
                        'answer'   => 'A',
                    ],
                ],
            ],

            // 5. Nouns
            [
                'title' => 'Nouns',
                'description' => 'Naming people, places and things.',
                'content' => 'A noun is a name of a person, place or thing.',
                'order' => 5,
                'questions' => [
                    [
                        'question' => 'Which word is a noun?',
                        'options'  => ['Teacher', 'Run', 'Quickly', 'Blue'],
                        'answer'   => 'A',
                    ],
                    [
                        'question' => 'Which one is a place?',
                        'options'  => ['School', 'Jump', 'Sing', 'Happy'],
                        'answer'   => 'A',
                    ],
                ],
            ],

            // 6. Verbs
            [
                'title' => 'Verbs',
                'description' => 'Action words.',
                'content' => 'Verbs tell us what someone is doing.',
                'order' => 6,
                'questions' => [
                    [
                        'question' => 'Which word shows action?',
                        'options'  => ['Run', 'Chair', 'Blue', 'Slow'],
                        'answer'   => 'A',
                    ],
                    [
                        'question' => 'Which is NOT a verb?',
                        'options'  => ['Jump', 'Eat', 'Sleep', 'Table'],
                        'answer'   => 'D',
                    ],
                ],
            ],

            // 7. Adjectives
            [
                'title' => 'Adjectives',
                'description' => 'Describing words.',
                'content' => 'Adjectives describe nouns.',
                'order' => 7,
                'questions' => [
                    [
                        'question' => 'Which word describes a dog?',
                        'options'  => ['Big', 'Run', 'Eat', 'Jump'],
                        'answer'   => 'A',
                    ],
                    [
                        'question' => 'Which word is an adjective?',
                        'options'  => ['Happy', 'Sing', 'Walk', 'Play'],
                        'answer'   => 'A',
                    ],
                ],
            ],

            // 8. Sentence Construction
            [
                'title' => 'Sentence Construction',
                'description' => 'Making correct sentences.',
                'content' => 'A sentence has meaning and ends with a full stop.',
                'order' => 8,
                'questions' => [
                    [
                        'question' => 'Which is a correct sentence?',
                        'options'  => [
                            'I like school.',
                            'Like school I',
                            'School like',
                            'I school'
                        ],
                        'answer' => 'A',
                    ],
                    [
                        'question' => 'What ends a sentence?',
                        'options'  => ['Comma', 'Question mark', 'Full stop', 'Dash'],
                        'answer'   => 'C',
                    ],
                ],
            ],

            // 9. Punctuation
            [
                'title' => 'Punctuation',
                'description' => 'Using punctuation marks.',
                'content' => 'Punctuation helps us understand sentences.',
                'order' => 9,
                'questions' => [
                    [
                        'question' => 'Which mark ends a statement?',
                        'options'  => ['Full stop', 'Question mark', 'Comma', 'Exclamation'],
                        'answer'   => 'A',
                    ],
                    [
                        'question' => 'Which mark is used for questions?',
                        'options'  => ['Comma', 'Full stop', 'Question mark', 'Dash'],
                        'answer'   => 'C',
                    ],
                ],
            ],

            // 10. Comprehension
            [
                'title' => 'Comprehension',
                'description' => 'Understanding what we read.',
                'content' => 'We answer questions about a story.',
                'order' => 10,
                'questions' => [
                    [
                        'question' => 'Why do we read stories?',
                        'options'  => ['To sleep', 'To understand', 'To shout', 'To run'],
                        'answer'   => 'B',
                    ],
                    [
                        'question' => 'What do we do after reading?',
                        'options'  => ['Forget', 'Answer questions', 'Cry', 'Sleep'],
                        'answer'   => 'B',
                    ],
                ],
            ],
        ];

        foreach ($themes as $theme) {

            $lesson = Lesson::create([
                'class_id'     => $class->id,
                'course_id'    => $course->id,
                'teacher_id'   => $teacherUserId,
                'title'        => $theme['title'],
                'description'  => $theme['description'],
                'content_type' => 'text',
                'content'      => $theme['content'],
                'order'        => $theme['order'],
                'status'       => 'published',
            ]);

            $assessment = Assessment::create([
                'lesson_id'       => $lesson->id,
                'teacher_id'      => $teacherUserId,
                'title'           => $theme['title'].' Assessment',
                'instructions'    => 'Answer all questions.',
                'type'            => 'quiz',
                'total_marks'     => count($theme['questions']) * 2,
                'duration_minutes'=> 10,
                'status'          => 'published',
            ]);

            foreach ($theme['questions'] as $q) {
                Question::create([
                    'assessment_id' => $assessment->id,
                    'set_by'        => $teacherUserId,
                    'question_text'=> $q['question'],
                    'marks'         => 2,
                    'option_a'      => $q['options'][0],
                    'option_b'      => $q['options'][1],
                    'option_c'      => $q['options'][2] ?? null,
                    'option_d'      => $q['options'][3] ?? null,
                    'correct_option'=> $q['answer'],
                ]);
            }
        }
    }
}
