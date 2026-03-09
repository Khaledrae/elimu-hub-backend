<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\ClassModel;
use App\Models\Course;
use App\Models\Teacher;

class Grade1MathematicsSeeder extends Seeder
{
    public function run(): void
    {
        $grade   = 'Grade 1';
        $subject = 'Mathematics';

        $class   = ClassModel::where('name', $grade)->firstOrFail();
        $course  = Course::where('title', $subject)->firstOrFail();
        $teacher = Teacher::where('subject_specialization', $subject)->first();
        $teacherUserId = $teacher?->user_id;

        $lessons = [

            // 1. Counting
            [
                'title' => 'Counting Numbers 1–20',
                'description' => 'Counting numbers from 1 to 20 correctly.',
                'content_type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=D0Ajq682yrA',
                'questions' => [
                    [
                        'text' => 'What number comes after 5?',
                        'options' => ['4','6','7','8'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => 'How many numbers are there from 1 to 3?',
                        'options' => ['2','3','4','5'],
                        'answer' => 'B',
                    ],
                ],
            ],

            // 2. Writing Numbers
            [
                'title' => 'Writing Numbers 1–20',
                'description' => 'Writing numbers correctly.',
                'content_type' => 'text',
                'content' => 'We write numbers carefully and clearly from 1 to 20.',
                'questions' => [
                    [
                        'text' => 'Which is the correct number?',
                        'options' => ['E','Ten','10','Tn'],
                        'answer' => 'C',
                    ],
                    [
                        'text' => 'What number is this: 7?',
                        'options' => ['Seven','Six','Nine','Five'],
                        'answer' => 'A',
                    ],
                ],
            ],

            // 3. Comparing Numbers
            [
                'title' => 'Comparing Numbers (Greater and Smaller)',
                'description' => 'Comparing numbers using greater and smaller.',
                'content_type' => 'text',
                'content' => 'A bigger number is greater. A smaller number is less.',
                'questions' => [
                    [
                        'text' => 'Which number is greater between 3 and 8?',
                        'options' => ['3','8','Both','None'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => 'Which number is smaller between 2 and 5?',
                        'options' => ['5','2','Both','None'],
                        'answer' => 'B',
                    ],
                ],
            ],

            // 4. Addition
            [
                'title' => 'Addition Within 10',
                'description' => 'Adding numbers within 10.',
                'content_type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=mjlsSYLLOSE',
                'questions' => [
                    [
                        'text' => '2 + 3 =',
                        'options' => ['4','5','6','3'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => '1 + 4 =',
                        'options' => ['4','6','5','3'],
                        'answer' => 'C',
                    ],
                ],
            ],

            // 5. Subtraction
            [
                'title' => 'Subtraction Within 10',
                'description' => 'Subtracting numbers within 10.',
                'content_type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=9vKqVkMQHKk',
                'questions' => [
                    [
                        'text' => '5 - 2 =',
                        'options' => ['2','4','3','1'],
                        'answer' => 'C',
                    ],
                    [
                        'text' => '7 - 3 =',
                        'options' => ['5','3','4','2'],
                        'answer' => 'C',
                    ],
                ],
            ],

            // 6. Shapes
            [
                'title' => 'Shapes Around Us',
                'description' => 'Identifying common shapes.',
                'content_type' => 'text',
                'content' => 'Common shapes include circle, square, triangle and rectangle.',
                'questions' => [
                    [
                        'text' => 'Which shape is round?',
                        'options' => ['Square','Triangle','Circle','Rectangle'],
                        'answer' => 'C',
                    ],
                    [
                        'text' => 'How many sides does a triangle have?',
                        'options' => ['2','3','4','5'],
                        'answer' => 'B',
                    ],
                ],
            ],

            // 7. Patterns
            [
                'title' => 'Patterns',
                'description' => 'Recognizing and completing simple patterns.',
                'content_type' => 'text',
                'content' => 'Patterns repeat in a certain order.',
                'questions' => [
                    [
                        'text' => '2, 4, 2, 4, __',
                        'options' => ['6','2','4','8'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => 'Red, Blue, Red, Blue, __',
                        'options' => ['Green','Blue','Red','Yellow'],
                        'answer' => 'C',
                    ],
                ],
            ],

            // 8. Time
            [
                'title' => "Time (O'clock)",
                'description' => 'Reading time in hours.',
                'content_type' => 'text',
                'content' => 'When the long hand is at 12, we read the hour only.',
                'questions' => [
                    [
                        'text' => '3:00 is read as:',
                        'options' => ["Three o'clock","Three thirty","Four o'clock","Two o'clock"],
                        'answer' => 'A',
                    ],
                    [
                        'text' => 'If the long hand is on 12, it means:',
                        'options' => ['Half past',"O'clock",'Quarter to','Quarter past'],
                        'answer' => 'B',
                    ],
                ],
            ],

            // 9. Money
            [
                'title' => 'Money (Coins Recognition)',
                'description' => 'Recognizing common coins.',
                'content_type' => 'text',
                'content' => 'We use money to buy items.',
                'questions' => [
                    [
                        'text' => 'Money is used to:',
                        'options' => ['Play','Buy items','Sleep','Run'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => 'Which is a coin?',
                        'options' => ['Book','Pencil','Shilling','Bag'],
                        'answer' => 'C',
                    ],
                ],
            ],

            // 10. Word Problems
            [
                'title' => 'Simple Word Problems',
                'description' => 'Solving simple addition and subtraction word problems.',
                'content_type' => 'text',
                'content' => 'We read carefully and decide whether to add or subtract.',
                'questions' => [
                    [
                        'text' => 'John has 2 apples and gets 3 more. How many apples does he have?',
                        'options' => ['4','5','6','3'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => 'There are 6 birds. 2 fly away. How many remain?',
                        'options' => ['5','3','4','2'],
                        'answer' => 'C',
                    ],
                ],
            ],
        ];

        foreach ($lessons as $index => $l) {

            $lesson = Lesson::create([
                'class_id' => $class->id,
                'course_id' => $course->id,
                'teacher_id' => $teacherUserId,
                'title' => $l['title'],
                'description' => $l['description'],
                'content_type' => $l['content_type'],
                'content' => $l['content'] ?? null,
                'video_url' => $l['video_url'] ?? null,
                'order' => $index + 1,
                'status' => 'published',
            ]);

            $assessment = Assessment::create([
                'lesson_id' => $lesson->id,
                'teacher_id' => $teacherUserId,
                'title' => $l['title'] . ' Assessment',
                'instructions' => 'Answer all questions.',
                'type' => 'quiz',
                'total_marks' => count($l['questions']) * 2,
                'duration_minutes' => 5,
                'status' => 'published',
            ]);

            foreach ($l['questions'] as $q) {
                Question::create([
                    'assessment_id' => $assessment->id,
                    'set_by' => $teacherUserId,
                    'question_text' => $q['text'],
                    'marks' => 2,
                    'option_a' => $q['options'][0],
                    'option_b' => $q['options'][1],
                    'option_c' => $q['options'][2],
                    'option_d' => $q['options'][3],
                    'correct_option' => $q['answer'],
                ]);
            }
        }
    }
}