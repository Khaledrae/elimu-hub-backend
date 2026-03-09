<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\ClassModel;
use App\Models\Course;
use App\Models\Teacher;

class Grade1KiswahiliSeeder extends Seeder
{
    public function run(): void
    {
        $grade   = 'Grade 1';
        $subject = 'Kiswahili';

        $class   = ClassModel::where('name', $grade)->firstOrFail();
        $course  = Course::where('title', $subject)->firstOrFail();
        $teacher = Teacher::where('subject_specialization', $subject)->first();
        $teacherUserId = $teacher?->user_id;

        $lessons = [

            // 1. Salamu
            [
                'title' => 'Salamu na Maneno ya Heshima',
                'description' => 'Kutumia salamu kwa usahihi.',
                'content_type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=J3b7lCk6wTQ',
                'questions' => [
                    [
                        'text' => 'Tunasema nini asubuhi?',
                        'options' => ['Habari','Shikamoo','Asante','Pole'],
                        'answer' => 'A',
                    ],
                    [
                        'text' => 'Neno la heshima ni:',
                        'options' => ['Tafadhali','Ruka','Kimbia','Lia'],
                        'answer' => 'A',
                    ],
                ],
            ],

            // 2. Kujitambulisha
            [
                'title' => 'Kujitambulisha',
                'description' => 'Kujitambulisha kwa sentensi rahisi.',
                'content_type' => 'text',
                'content' => 'Ninasema: Jina langu ni ____. Niko Darasa la Kwanza.',
                'questions' => [
                    [
                        'text' => 'Unapotambulisha jina unasema:',
                        'options' => ['Kimbia','Jina langu ni...','Simama','Lala'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => 'Tunazungumza kwa:',
                        'options' => ['Hasira','Uwazi','Kelele','Kilio'],
                        'answer' => 'B',
                    ],
                ],
            ],

            // 3. Kusikiliza
            [
                'title' => 'Kusikiliza Maelekezo',
                'description' => 'Kufuata maelekezo rahisi.',
                'content_type' => 'text',
                'content' => 'Sikiliza kwa makini. Mfano: Simama. Kaa chini.',
                'questions' => [
                    [
                        'text' => 'Tunaanza kwa:',
                        'options' => ['Kuzungumza','Kusikiliza','Kulala','Kucheza'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => '“Simama” ni:',
                        'options' => ['Wimbo','Hadithi','Maelekezo','Jina'],
                        'answer' => 'C',
                    ],
                ],
            ],

            // 4. Herufi
            [
                'title' => 'Herufi za Alfabeti (A–E)',
                'description' => 'Kutambua herufi za kwanza.',
                'content_type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=l4WNrvVjiTw',
                'questions' => [
                    [
                        'text' => 'Herufi gani inaanza na sauti /a/?',
                        'options' => ['A','B','D','F'],
                        'answer' => 'A',
                    ],
                    [
                        'text' => 'Herufi B inatoa sauti:',
                        'options' => ['/a/','/b/','/m/','/s/'],
                        'answer' => 'B',
                    ],
                ],
            ],

            // 5. Kusoma Maneno
            [
                'title' => 'Kusoma Maneno Rahisi',
                'description' => 'Kusoma maneno mafupi.',
                'content_type' => 'text',
                'content' => 'Mfano wa maneno: mama, baba, paka, mbwa.',
                'questions' => [
                    [
                        'text' => 'Ni lipi neno sahihi?',
                        'options' => ['Mama','Mxa','Zpp','Tkk'],
                        'answer' => 'A',
                    ],
                    [
                        'text' => 'Neno “paka” lina herufi ngapi?',
                        'options' => ['2','3','4','5'],
                        'answer' => 'C',
                    ],
                ],
            ],

            // 6. Kuandika
            [
                'title' => 'Kuandika Sentensi Rahisi',
                'description' => 'Kuandika sentensi fupi.',
                'content_type' => 'text',
                'content' => 'Sentensi huanza kwa herufi kubwa na kuishia kwa nukta.',
                'questions' => [
                    [
                        'text' => 'Sentensi huanza kwa:',
                        'options' => ['Herufi ndogo','Herufi kubwa','Namba','Alama'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => 'Mwisho wa sentensi tunaweka:',
                        'options' => ['Koma','Swali','Nukta','Hakuna'],
                        'answer' => 'C',
                    ],
                ],
            ],

            // 7. Majina ya Wanyama
            [
                'title' => 'Majina ya Wanyama',
                'description' => 'Kutambua wanyama wa nyumbani na porini.',
                'content_type' => 'text',
                'content' => 'Mfano: ng’ombe, simba, mbuzi, paka.',
                'questions' => [
                    [
                        'text' => 'Simba ni mnyama wa:',
                        'options' => ['Nyumbani','Porini','Shuleni','Sokoni'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => 'Ng’ombe anaishi:',
                        'options' => ['Bahari','Msitu','Nyumbani','Mlimani'],
                        'answer' => 'C',
                    ],
                ],
            ],

            // 8. Sehemu za Mwili
            [
                'title' => 'Sehemu za Mwili',
                'description' => 'Kutambua sehemu kuu za mwili.',
                'content_type' => 'text',
                'content' => 'Tuna macho, masikio, mikono na miguu.',
                'questions' => [
                    [
                        'text' => 'Tunatumia macho kwa:',
                        'options' => ['Kusikia','Kuona','Kutembea','Kula'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => 'Tunatembea kwa kutumia:',
                        'options' => ['Mikono','Masikio','Miguu','Macho'],
                        'answer' => 'C',
                    ],
                ],
            ],

            // 9. Hadithi Fupi
            [
                'title' => 'Kusikiliza Hadithi Fupi',
                'description' => 'Kusikiliza na kueleza hadithi.',
                'content_type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=Yf8c3jP1YxA',
                'questions' => [
                    [
                        'text' => 'Baada ya hadithi tunafanya nini?',
                        'options' => ['Kulala','Kusimulia','Kukimbia','Kulia'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => 'Hadithi ina mwanzo, kati na:',
                        'options' => ['Mwisho','Juu','Chini','Pembeni'],
                        'answer' => 'A',
                    ],
                ],
            ],

            // 10. Maswali Rahisi
            [
                'title' => 'Maneno ya Maswali (Nani, Nini, Wapi)',
                'description' => 'Kutumia maneno ya maswali.',
                'content_type' => 'text',
                'content' => 'Maswali huanza na nani, nini au wapi.',
                'questions' => [
                    [
                        'text' => '“Nani” huuliza kuhusu:',
                        'options' => ['Mahali','Mtu','Rangi','Namba'],
                        'answer' => 'B',
                    ],
                    [
                        'text' => '“Wapi” huuliza kuhusu:',
                        'options' => ['Mahali','Mtu','Chakula','Muda'],
                        'answer' => 'A',
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
                'instructions' => 'Jibu maswali yote.',
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