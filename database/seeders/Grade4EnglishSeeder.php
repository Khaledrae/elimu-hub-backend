<?php

namespace Database\Seeders;

use App\Models\Assessment;
use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Teacher;
use App\Models\ClassModel;
use App\Models\Course;

class Grade4EnglishSeeder extends Seeder
{
    public function run(): void
    {
        $class  = ClassModel::where('name', 'Grade 4')->firstOrFail();
        $course = Course::where('title', 'English')->firstOrFail();
        $teacher = Teacher::where('subject_specialization', 'English')->first();
        $teacherUserId = $teacher?->user_id;

        $lessons = [
            [
                'title' => 'Listening to Stories',
                'description' => 'Listening and answering questions from stories.',
                'content' => 'Learners listen to a short story and identify key ideas.',
                'questions' => [
                    [
                        'text' => 'What should you do when listening to a story?',
                        'options' => ['Talk loudly', 'Listen carefully', 'Run around', 'Sleep'],
                        'answer' => 'B',
                        'marks' => 1,
                        'blooms' => 'understand',
                    ],
                ],
            ],
            [
                'title' => 'Reading for Meaning',
                'description' => 'Understanding what we read.',
                'content' => 'Learners read a passage and answer questions.',
                'questions' => [
                    [
                        'text' => 'What is the main idea of a story?',
                        'options' => [
                            'The longest word',
                            'What the story is mostly about',
                            'The last sentence',
                            'The title only'
                        ],
                        'answer' => 'B',
                        'marks' => 2,
                        'blooms' => 'analyze',
                    ],
                ],
            ],
            [
                'title' => 'Common and Proper Nouns',
                'description' => 'Identifying common and proper nouns.',
                'content' => 'Proper nouns name specific people or places.',
                'questions' => [
                    [
                        'text' => 'Which is a proper noun?',
                        'options' => ['school', 'teacher', 'Nairobi', 'child'],
                        'answer' => 'C',
                        'marks' => 1,
                        'blooms' => 'remember',
                    ],
                ],
            ],
            [
                'title' => 'Present and Past Tense',
                'description' => 'Using verbs correctly.',
                'content' => 'Verbs can show actions now or in the past.',
                'questions' => [
                    [
                        'text' => 'Which word is in the past tense?',
                        'options' => ['Run', 'Runs', 'Ran', 'Running'],
                        'answer' => 'C',
                        'marks' => 1,
                        'blooms' => 'apply',
                    ],
                ],
            ],
            [
                'title' => 'Pronouns',
                'description' => 'Replacing nouns with pronouns.',
                'content' => 'Pronouns replace naming words.',
                'questions' => [
                    [
                        'text' => 'Which word is a pronoun?',
                        'options' => ['John', 'School', 'He', 'Book'],
                        'answer' => 'C',
                        'marks' => 1,
                        'blooms' => 'remember',
                    ],
                ],
            ],
            [
                'title' => 'Paragraph Writing',
                'description' => 'Writing short paragraphs.',
                'content' => 'A paragraph has a topic sentence and details.',
                'questions' => [
                    [
                        'text' => 'What does a paragraph need?',
                        'options' => [
                            'Only pictures',
                            'A topic sentence',
                            'Only questions',
                            'No punctuation'
                        ],
                        'answer' => 'B',
                        'marks' => 2,
                        'blooms' => 'understand',
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
