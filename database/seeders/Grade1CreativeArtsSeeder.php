<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\ClassModel;
use App\Models\Course;
use App\Models\Teacher;

class Grade1CreativeArtsSeeder extends Seeder
{
    public function run(): void
    {
        $class   = ClassModel::where('name', 'Grade 1')->firstOrFail();
        $course  = Course::where('title', 'Creative Arts')->firstOrFail();
        $teacher = Teacher::where('subject_specialization', 'Creative Arts')->first();
        $teacherUserId = $teacher?->user_id;

        $lessons = [

            [
                'title'=>'Primary and Secondary Colours',
                'description'=>'Identifying and mixing colours.',
                'content_type'=>'video',
                'video_url'=>'https://www.youtube.com/watch?v=9oA3K0Rk7VQ',
                'content'=>null,
                'questions'=>[
                    ['text'=>'Which is a primary colour?','options'=>['Red','Green','Brown','Pink'],'answer'=>'A'],
                    ['text'=>'Blue and yellow make:','options'=>['Purple','Green','Black','White'],'answer'=>'B'],
                ],
            ],

            [
                'title'=>'Drawing My Family',
                'description'=>'Expressing ideas through drawing.',
                'content_type'=>'text',
                'content'=>"OBJECTIVES:
- Draw family members.
- Use colour neatly.

ACTIVITY:
Draw your family and colour your picture.",
                'questions'=>[
                    ['text'=>'Drawing is a form of:','options'=>['Art','Sport','Math','Science'],'answer'=>'A'],
                    ['text'=>'We use crayons to:','options'=>['Colour','Eat','Throw','Break'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Singing Action Songs',
                'description'=>'Singing with body movements.',
                'content_type'=>'video',
                'video_url'=>'https://www.youtube.com/watch?v=71hqRT9U0wg',
                'content'=>null,
                'questions'=>[
                    ['text'=>'When singing action songs we also:','options'=>['Move','Sleep','Cry','Sit still'],'answer'=>'A'],
                    ['text'=>'Songs make learning:','options'=>['Fun','Hard','Scary','Boring'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Making Simple Patterns',
                'description'=>'Creating patterns using shapes.',
                'content_type'=>'text',
                'content'=>"OBJECTIVES:
- Create repeating patterns.

ACTIVITY:
Draw circle, square, circle, square.",
                'questions'=>[
                    ['text'=>'A pattern repeats:','options'=>['Yes','No','Sometimes','Never'],'answer'=>'A'],
                    ['text'=>'Which is a shape?','options'=>['Circle','Song','Jump','Book'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Modeling with Clay',
                'description'=>'Creating objects using clay.',
                'content_type'=>'video',
                'video_url'=>'https://www.youtube.com/watch?v=H9j1k9Y5W9U',
                'content'=>null,
                'questions'=>[
                    ['text'=>'Clay is used for:','options'=>['Modeling','Eating','Throwing','Breaking'],'answer'=>'A'],
                    ['text'=>'We use our hands to:','options'=>['Shape','Hide','Sleep','Cry'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Traditional Songs',
                'description'=>'Learning local cultural songs.',
                'content_type'=>'video',
                'video_url'=>'https://www.youtube.com/watch?v=dZr1z6Kf0K4',
                'content'=>null,
                'questions'=>[
                    ['text'=>'Traditional songs show our:','options'=>['Culture','Food','Math','Shoes'],'answer'=>'A'],
                    ['text'=>'We sing together as a:','options'=>['Group','Wall','Desk','Stone'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Painting with Water Colours',
                'description'=>'Using paint creatively.',
                'content_type'=>'text',
                'content'=>"OBJECTIVES:
- Use paint neatly.

ACTIVITY:
Paint a tree using green and brown.",
                'questions'=>[
                    ['text'=>'We clean brushes with:','options'=>['Water','Fire','Sand','Oil'],'answer'=>'A'],
                    ['text'=>'Painting is part of:','options'=>['Art','Math','PE','Science'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Drama and Role Play',
                'description'=>'Acting short scenes.',
                'content_type'=>'video',
                'video_url'=>'https://www.youtube.com/watch?v=RZ7vD0sF1tE',
                'content'=>null,
                'questions'=>[
                    ['text'=>'Acting is also called:','options'=>['Drama','Math','Writing','Reading'],'answer'=>'A'],
                    ['text'=>'Drama helps us:','options'=>['Express','Sleep','Hide','Fight'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Making Musical Instruments',
                'description'=>'Creating simple instruments.',
                'content_type'=>'text',
                'content'=>"OBJECTIVES:
- Make simple shakers.

ACTIVITY:
Use a bottle and beans to make a shaker.",
                'questions'=>[
                    ['text'=>'Shakers make:','options'=>['Sound','Food','Water','Light'],'answer'=>'A'],
                    ['text'=>'Music is made using:','options'=>['Instruments','Books','Shoes','Walls'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Appreciating Artwork',
                'description'=>'Looking and talking about art.',
                'content_type'=>'text',
                'content'=>"OBJECTIVES:
- Describe a picture.

ACTIVITY:
Say what you see in a drawing.",
                'questions'=>[
                    ['text'=>'When we look at art we can:','options'=>['Describe','Break','Throw','Hide'],'answer'=>'A'],
                    ['text'=>'Art can show:','options'=>['Ideas','Noise','Water','Shoes'],'answer'=>'A'],
                ],
            ],
        ];

        foreach ($lessons as $index=>$l) {
            $lesson = Lesson::create([
                'class_id'=>$class->id,
                'course_id'=>$course->id,
                'teacher_id'=>$teacherUserId,
                'title'=>$l['title'],
                'description'=>$l['description'],
                'content_type'=>$l['content_type'],
                'content'=>$l['content'] ?? null,
                'video_url'=>$l['video_url'] ?? null,
                'order'=>$index+1,
                'status'=>'published',
            ]);

            $assessment = Assessment::create([
                'lesson_id'=>$lesson->id,
                'teacher_id'=>$teacherUserId,
                'title'=>$l['title'].' Assessment',
                'instructions'=>'Answer all questions.',
                'type'=>'quiz',
                'total_marks'=>4,
                'duration_minutes'=>5,
                'status'=>'published',
            ]);

            foreach($l['questions'] as $q){
                Question::create([
                    'assessment_id'=>$assessment->id,
                    'set_by'=>$teacherUserId,
                    'question_text'=>$q['text'],
                    'marks'=>2,
                    'option_a'=>$q['options'][0],
                    'option_b'=>$q['options'][1],
                    'option_c'=>$q['options'][2],
                    'option_d'=>$q['options'][3],
                    'correct_option'=>$q['answer'],
                ]);
            }
        }
    }
}