<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\ClassModel;
use App\Models\Course;
use App\Models\Teacher;

class Grade1EnvironmentalActivitiesSeeder extends Seeder
{
    public function run(): void
    {
        $grade   = 'Grade 1';
        $subject = 'Environmental Activities';

        $class   = ClassModel::where('name', $grade)->firstOrFail();
        $course  = Course::where('title', $subject)->firstOrFail();
        $teacher = Teacher::where('subject_specialization', $subject)->first();
        $teacherUserId = $teacher?->user_id;

        $lessons = [

            // 1. My Family
            [
                'title' => 'My Family',
                'description' => 'Identifying members of the family.',
                'content_type' => 'text',
                'content' => 'A family may include father, mother, brothers and sisters. Families care for one another.',
                'questions' => [
                    [
                        'text' => 'Who is part of a family?',
                        'options' => ['Tree','Mother','Car','Stone'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => 'Families should:',
                        'options' => ['Fight','Care for each other','Ignore','Shout'],
                        'answer' => 'B',
                    ],
                ],
            ],

            // 2. My School
            [
                'title' => 'My School',
                'description' => 'Identifying places and people in school.',
                'content_type' => 'text',
                'content' => 'A school has teachers, learners, classrooms and playgrounds.',
                'questions' => [
                    [
                        'text' => 'Who teaches learners?',
                        'options' => ['Driver','Teacher','Farmer','Doctor'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => 'Where do learners study?',
                        'options' => ['Market','Classroom','River','Forest'],
                        'answer' => 'B',
                    ],
                ],
            ],

            // 3. Parts of the Body
            [
                'title' => 'Parts of the Body',
                'description' => 'Identifying major body parts.',
                'content_type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=BwHMMZQGFoM',
                'questions' => [
                    [
                        'text' => 'Which part helps us see?',
                        'options' => ['Nose','Eyes','Legs','Hands'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => 'We walk using our:',
                        'options' => ['Ears','Hands','Legs','Eyes'],
                        'answer' => 'C',
                    ],
                ],
            ],

            // 4. Healthy Eating
            [
                'title' => 'Healthy Eating',
                'description' => 'Identifying healthy foods.',
                'content_type' => 'text',
                'content' => 'Healthy foods include fruits, vegetables and clean water.',
                'questions' => [
                    [
                        'text' => 'Which food is healthy?',
                        'options' => ['Stone','Apple','Plastic','Paper'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => 'We should drink clean:',
                        'options' => ['Milk only','Juice only','Water','Soda'],
                        'answer' => 'C',
                    ],
                ],
            ],

            // 5. Keeping Clean
            [
                'title' => 'Keeping Clean',
                'description' => 'Practicing personal hygiene.',
                'content_type' => 'text',
                'content' => 'We bathe daily, brush our teeth and wash our hands before eating.',
                'questions' => [
                    [
                        'text' => 'When should we wash hands?',
                        'options' => ['After eating','Before eating','Never','At night only'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => 'We brush our teeth to:',
                        'options' => ['Sleep','Stay clean','Play','Run'],
                        'answer' => 'B',
                    ],
                ],
            ],

            // 6. Weather
            [
                'title' => 'Weather Changes',
                'description' => 'Identifying different types of weather.',
                'content_type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=rD6FRDd9Hew',
                'questions' => [
                    [
                        'text' => 'When it rains, we use a:',
                        'options' => ['Hat','Umbrella','Book','Plate'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => 'The sun makes the day:',
                        'options' => ['Cold','Dark','Bright','Wet'],
                        'answer' => 'C',
                    ],
                ],
            ],

            // 7. Animals Around Us
            [
                'title' => 'Animals Around Us',
                'description' => 'Identifying domestic and wild animals.',
                'content_type' => 'text',
                'content' => 'Some animals live at home (domestic) while others live in the wild.',
                'questions' => [
                    [
                        'text' => 'Which is a domestic animal?',
                        'options' => ['Lion','Cow','Elephant','Tiger'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => 'A lion lives in the:',
                        'options' => ['House','Forest','Classroom','Kitchen'],
                        'answer' => 'B',
                    ],
                ],
            ],

            // 8. Plants Around Us
            [
                'title' => 'Plants Around Us',
                'description' => 'Identifying parts and uses of plants.',
                'content_type' => 'text',
                'content' => 'Plants provide food, shade and clean air.',
                'questions' => [
                    [
                        'text' => 'Plants give us:',
                        'options' => ['Plastic','Food','Metal','Stone'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => 'Trees give us:',
                        'options' => ['Shade','Fire only','Dust','Noise'],
                        'answer' => 'A',
                    ],
                ],
            ],

            // 9. Safety at Home
            [
                'title' => 'Safety at Home',
                'description' => 'Practicing safety rules at home.',
                'content_type' => 'text',
                'content' => 'We avoid playing with fire, sharp objects and electricity.',
                'questions' => [
                    [
                        'text' => 'We should not play with:',
                        'options' => ['Ball','Books','Fire','Toys'],
                        'answer' => 'C',
                    ],
                    [
                        'text' => 'Sharp objects can:',
                        'options' => ['Cook','Clean','Cut','Sing'],
                        'answer' => 'C',
                    ],
                ],
            ],

            // 10. Caring for the Environment
            [
                'title' => 'Caring for the Environment',
                'description' => 'Keeping our environment clean.',
                'content_type' => 'text',
                'content' => 'We keep the environment clean by disposing of waste properly.',
                'questions' => [
                    [
                        'text' => 'We throw waste in the:',
                        'options' => ['Road','River','Dustbin','Classroom'],
                        'answer' => 'C',
                    ],
                    [
                        'text' => 'Keeping the environment clean makes it:',
                        'options' => ['Dirty','Healthy','Dangerous','Noisy'],
                        'answer' => 'B',
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