<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $assessmentId = (int) file_get_contents(storage_path('app/assessment_id.txt'));
        $teacherId = 26;

        $questions = [
            [
                'question_text'  => 'Which of the following is a vowel?',
                'option_a'       => 'B',
                'option_b'       => 'C',
                'option_c'       => 'A',
                'option_d'       => 'D',
                'correct_option' => 'C',
                'marks'          => 2,
            ],
            [
                'question_text'  => 'Which word begins with the letter "C"?',
                'option_a'       => 'Dog',
                'option_b'       => 'Cat',
                'option_c'       => 'Sun',
                'option_d'       => 'Egg',
                'correct_option' => 'B',
                'marks'          => 2,
            ],
            [
                'question_text'  => 'Which sentence is correct?',
                'option_a'       => 'I am a boy.',
                'option_b'       => 'Cat big is.',
                'option_c'       => 'Go school we.',
                'option_d'       => 'Is sun the bright.',
                'correct_option' => 'A',
                'marks'          => 2,
            ],
            [
                'question_text'  => 'How many vowels are in the English alphabet?',
                'option_a'       => '3',
                'option_b'       => '5',
                'option_c'       => '7',
                'option_d'       => '10',
                'correct_option' => 'B',
                'marks'          => 2,
            ],
            [
                'question_text'  => 'Which of these is a simple English word?',
                'option_a'       => 'Xylophone',
                'option_b'       => 'Elephant',
                'option_c'       => 'Cat',
                'option_d'       => 'Aeroplane',
                'correct_option' => 'C',
                'marks'          => 2,
            ],
        ];

        foreach ($questions as $q) {
            Question::create([
                'assessment_id' => $assessmentId,
                'set_by'        => $teacherId,
                'question_text' => $q['question_text'],
                'option_a'      => $q['option_a'],
                'option_b'      => $q['option_b'],
                'option_c'      => $q['option_c'],
                'option_d'      => $q['option_d'],
                'correct_option'=> $q['correct_option'],
                'marks'         => $q['marks'],
            ]);
        }
    }
}
