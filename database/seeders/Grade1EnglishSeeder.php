<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\Course;
use App\Models\ClassModel;
use App\Models\Teacher;

class Grade1EnglishSeeder extends Seeder
{
    public function run(): void
    {
        $grade   = 'Grade 1';
        $subject = 'English';

        $class   = ClassModel::where('name', $grade)->firstOrFail();
        $course  = Course::where('title', $subject)->firstOrFail();
        $teacher = Teacher::where('subject_specialization', $subject)->first();

        // IMPORTANT: always use teachers.user_id
        $teacherUserId = $teacher?->user_id;

        $themes = [
            // 1. Greetings
            [
                'title' => 'Greetings',
                'description' => 'Greeting people politely.',
                'content' => 'We say hello, good morning and good afternoon.',
                'order' => 1,
                'questions' => [
                    [
                        'question' => 'What do we say when we meet someone?',
                        'options'  => ['Hello', 'Sorry', 'Stop', 'Run'],
                        'answer'   => 'A',
                    ],
                    [
                        'question' => 'What do we say in the morning?',
                        'options'  => ['Good night', 'Good morning', 'Goodbye', 'Sorry'],
                        'answer'   => 'B',
                    ],
                ],
            ],

            // 2. My School
            [
                'title' => 'My School',
                'description' => 'Learning about school.',
                'content' => 'We learn and play at school.',
                'order' => 2,
                'questions' => [
                    [
                        'question' => 'Where do we learn?',
                        'options'  => ['Home', 'Market', 'School', 'Farm'],
                        'answer'   => 'C',
                    ],
                    [
                        'question' => 'Who teaches us at school?',
                        'options'  => ['Doctor', 'Teacher', 'Driver', 'Farmer'],
                        'answer'   => 'B',
                    ],
                ],
            ],

            // 3. My Family
            [
                'title' => 'My Family',
                'description' => 'Knowing family members.',
                'content' => 'Family members love and care for each other.',
                'order' => 3,
                'questions' => [
                    [
                        'question' => 'Who is your mother?',
                        'options'  => ['Aunt', 'Teacher', 'Mother', 'Friend'],
                        'answer'   => 'C',
                    ],
                    [
                        'question' => 'Family members live together.',
                        'options'  => ['True', 'False', 'Maybe', 'No'],
                        'answer'   => 'A',
                    ],
                ],
            ],

            // 4. My Home
            [
                'title' => 'My Home',
                'description' => 'Understanding home.',
                'content' => 'We live and sleep at home.',
                'order' => 4,
                'questions' => [
                    [
                        'question' => 'Where do we sleep?',
                        'options'  => ['School', 'Home', 'Shop', 'Road'],
                        'answer'   => 'B',
                    ],
                    [
                        'question' => 'Home keeps us safe.',
                        'options'  => ['Yes', 'No', 'Sometimes', 'Never'],
                        'answer'   => 'A',
                    ],
                ],
            ],

            // 5. Time
            [
                'title' => 'Time',
                'description' => 'Learning about time.',
                'content' => 'Morning, afternoon and evening.',
                'order' => 5,
                'questions' => [
                    [
                        'question' => 'When do we wake up?',
                        'options'  => ['Night', 'Morning', 'Evening', 'Afternoon'],
                        'answer'   => 'B',
                    ],
                    [
                        'question' => 'When do we sleep?',
                        'options'  => ['Morning', 'Afternoon', 'Night', 'Noon'],
                        'answer'   => 'C',
                    ],
                ],
            ],

            // 6. Weather and Environment
            [
                'title' => 'Weather and Environment',
                'description' => 'Knowing weather.',
                'content' => 'It can be sunny or rainy.',
                'order' => 6,
                'questions' => [
                    [
                        'question' => 'What falls when it rains?',
                        'options'  => ['Fire', 'Snow', 'Rain', 'Dust'],
                        'answer'   => 'C',
                    ],
                    [
                        'question' => 'We keep our environment clean.',
                        'options'  => ['Yes', 'No', 'Maybe', 'Never'],
                        'answer'   => 'A',
                    ],
                ],
            ],

            // 7. Hygiene
            [
                'title' => 'Hygiene',
                'description' => 'Keeping clean.',
                'content' => 'We wash our hands.',
                'order' => 7,
                'questions' => [
                    [
                        'question' => 'When do we wash our hands?',
                        'options'  => ['After toilet', 'Before dirt', 'After play', 'Never'],
                        'answer'   => 'A',
                    ],
                    [
                        'question' => 'Clean hands keep us healthy.',
                        'options'  => ['Yes', 'No', 'Maybe', 'Sometimes'],
                        'answer'   => 'A',
                    ],
                ],
            ],

            // 8. Parts of the Body
            [
                'title' => 'Parts of the Body',
                'description' => 'Knowing body parts.',
                'content' => 'We use our eyes to see.',
                'order' => 8,
                'questions' => [
                    [
                        'question' => 'Which part do we use to see?',
                        'options'  => ['Ear', 'Eye', 'Hand', 'Leg'],
                        'answer'   => 'B',
                    ],
                    [
                        'question' => 'Which part do we use to walk?',
                        'options'  => ['Head', 'Leg', 'Hand', 'Eye'],
                        'answer'   => 'B',
                    ],
                ],
            ],

            // 9. My Friends
            [
                'title' => 'My Friends',
                'description' => 'Learning about friends.',
                'content' => 'Friends help each other.',
                'order' => 9,
                'questions' => [
                    [
                        'question' => 'A friend is someone who helps us.',
                        'options'  => ['Enemy', 'Helper', 'Fighter', 'Stranger'],
                        'answer'   => 'B',
                    ],
                    [
                        'question' => 'We share with our friends.',
                        'options'  => ['Yes', 'No', 'Never', 'Sometimes'],
                        'answer'   => 'A',
                    ],
                ],
            ],

            // 10. Safety
            [
                'title' => 'Safety',
                'description' => 'Staying safe.',
                'content' => 'We avoid fire and sharp objects.',
                'order' => 10,
                'questions' => [
                    [
                        'question' => 'Fire is?',
                        'options'  => ['Safe', 'Dangerous', 'Fun', 'Soft'],
                        'answer'   => 'B',
                    ],
                    [
                        'question' => 'Who helps us when we are in danger?',
                        'options'  => ['Teacher', 'Police', 'Friend', 'All'],
                        'answer'   => 'D',
                    ],
                ],
            ],

            // 11–15 intentionally concise (same quality)
            [
                'title' => 'Community Leaders',
                'description' => 'Learning leaders.',
                'content' => 'Leaders help people.',
                'order' => 11,
                'questions' => [
                    ['question' => 'Who keeps law and order?', 'options' => ['Farmer','Police','Driver','Child'], 'answer' => 'B'],
                    ['question' => 'Leaders help people.', 'options' => ['Yes','No','Never','Sometimes'], 'answer' => 'A'],
                ],
            ],
            [
                'title' => 'Living Together',
                'description' => 'Respecting others.',
                'content' => 'We help and respect each other.',
                'order' => 12,
                'questions' => [
                    ['question' => 'We should be kind to others.', 'options' => ['Rude','Kind','Angry','Lazy'], 'answer' => 'B'],
                    ['question' => 'Sharing is good.', 'options' => ['Yes','No','Maybe','Never'], 'answer' => 'A'],
                ],
            ],
            [
                'title' => 'Technology',
                'description' => 'Using simple technology.',
                'content' => 'Phones help us talk.',
                'order' => 13,
                'questions' => [
                    ['question' => 'Which one is technology?', 'options' => ['Phone','Tree','Rock','Water'], 'answer' => 'A'],
                    ['question' => 'Technology helps us.', 'options' => ['Yes','No','Never','Sometimes'], 'answer' => 'A'],
                ],
            ],
            [
                'title' => 'Numbers in Language',
                'description' => 'Using numbers.',
                'content' => 'We count using numbers.',
                'order' => 14,
                'questions' => [
                    ['question' => 'Which is a number?', 'options' => ['One','Cat','Run','Big'], 'answer' => 'A'],
                    ['question' => 'We use numbers to count.', 'options' => ['Yes','No','Never','Maybe'], 'answer' => 'A'],
                ],
            ],
            [
                'title' => 'Conserving Resources',
                'description' => 'Saving resources.',
                'content' => 'We save water.',
                'order' => 15,
                'questions' => [
                    ['question' => 'What should we save?', 'options' => ['Water','Trash','Noise','Fire'], 'answer' => 'A'],
                    ['question' => 'Saving resources is good.', 'options' => ['Yes','No','Never','Sometimes'], 'answer' => 'A'],
                ],
            ],
        ];

        foreach ($themes as $theme) {
            $lesson = Lesson::create([
                'class_id' => $class->id,
                'course_id' => $course->id,
                'teacher_id' => $teacherUserId,
                'title' => $theme['title'],
                'description' => $theme['description'],
                'content_type' => 'text',
                'content' => $theme['content'],
                'order' => $theme['order'],
                'status' => 'published',
            ]);

            $assessment = Assessment::create([
                'lesson_id' => $lesson->id,
                'teacher_id' => $teacherUserId,
                'title' => $theme['title'] . ' Assessment',
                'instructions' => 'Answer all questions.',
                'type' => 'quiz',
                'total_marks' => 4,
                'duration_minutes' => 5,
                'status' => 'published',
            ]);

            foreach ($theme['questions'] as $q) {
                Question::create([
                    'assessment_id' => $assessment->id,
                    'set_by' => $teacherUserId,
                    'question_text' => $q['question'],
                    'marks' => 2,
                    'option_a' => $q['options'][0],
                    'option_b' => $q['options'][1],
                    'option_c' => $q['options'][2] ?? null,
                    'option_d' => $q['options'][3] ?? null,
                    'correct_option' => $q['answer'],
                ]);
            }
        }
    }
}
