<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\ClassModel;
use App\Models\Course;
use App\Models\Teacher;

class Grade1MovementPhysicalActivitiesSeeder extends Seeder
{
    public function run(): void
    {
        $class   = ClassModel::where('name', 'Grade 1')->firstOrFail();
        $course  = Course::where('title', 'Physical Education')->firstOrFail();
        $teacher = Teacher::where('subject_specialization', 'Physical Education')->first();
        $teacherUserId = $teacher?->user_id;

        $lessons = [

            [
                'title'=>'Warming Up Exercises',
                'description'=>'Learning simple warm-up movements before activity.',
                'content_type'=>'video',
                'video_url'=>'https://www.youtube.com/watch?v=dF7O6-QabIo',
                'content'=>null,
                'questions'=>[
                    ['text'=>'We warm up before exercise to:','options'=>['Prepare the body','Sleep','Eat','Sit'],'answer'=>'A'],
                    ['text'=>'Jumping jacks are a type of:','options'=>['Exercise','Food','Game board','Song'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Running and Walking',
                'description'=>'Practising safe running and walking.',
                'content_type'=>'text',
                'content'=>"OBJECTIVES:
- Differentiate walking and running.
- Practise safe movement.

ACTIVITY:
Walk slowly, then run gently in an open space.",
                'questions'=>[
                    ['text'=>'Running is faster than:','options'=>['Walking','Sleeping','Sitting','Reading'],'answer'=>'A'],
                    ['text'=>'We should run in a:','options'=>['Safe space','Classroom desk','Bathroom','Kitchen'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Jumping and Hopping',
                'description'=>'Practising balance through jumping.',
                'content_type'=>'video',
                'video_url'=>'https://www.youtube.com/watch?v=wjP1V7C0LxM',
                'content'=>null,
                'questions'=>[
                    ['text'=>'Hopping uses:','options'=>['One foot','Two chairs','Hands only','Eyes'],'answer'=>'A'],
                    ['text'=>'Jumping helps improve:','options'=>['Strength','Laziness','Sleep','Fear'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Throwing and Catching',
                'description'=>'Developing hand-eye coordination.',
                'content_type'=>'text',
                'content'=>"OBJECTIVES:
- Throw a ball correctly.
- Catch safely.

ACTIVITY:
Practise throwing and catching with a partner.",
                'questions'=>[
                    ['text'=>'We use our hands to:','options'=>['Catch','Kick','Sit','Sleep'],'answer'=>'A'],
                    ['text'=>'Throwing and catching improve:','options'=>['Coordination','Anger','Fear','Noise'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Simple Team Games',
                'description'=>'Playing games with others.',
                'content_type'=>'video',
                'video_url'=>'https://www.youtube.com/watch?v=Rz0go1pTda8',
                'content'=>null,
                'questions'=>[
                    ['text'=>'Team games require:','options'=>['Cooperation','Selfishness','Fighting','Hiding'],'answer'=>'A'],
                    ['text'=>'Playing together teaches:','options'=>['Sharing','Anger','Fear','Silence'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Balancing Activities',
                'description'=>'Maintaining balance while moving.',
                'content_type'=>'text',
                'content'=>"OBJECTIVES:
- Stand on one foot.
- Walk on a straight line.

ACTIVITY:
Balance on one foot for 5 seconds.",
                'questions'=>[
                    ['text'=>'Balancing helps improve:','options'=>['Stability','Sleep','Noise','Hunger'],'answer'=>'A'],
                    ['text'=>'Standing on one foot shows:','options'=>['Balance','Anger','Fear','Laziness'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Stretching Exercises',
                'description'=>'Learning simple stretches.',
                'content_type'=>'video',
                'video_url'=>'https://www.youtube.com/watch?v=L_xrDAtykMI',
                'content'=>null,
                'questions'=>[
                    ['text'=>'Stretching keeps muscles:','options'=>['Flexible','Hard','Cold','Broken'],'answer'=>'A'],
                    ['text'=>'We stretch before and after:','options'=>['Exercise','Sleeping','Eating','Reading'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Skipping',
                'description'=>'Learning to skip safely.',
                'content_type'=>'text',
                'content'=>"OBJECTIVES:
- Skip using a rope.

ACTIVITY:
Practise skipping slowly and safely.",
                'questions'=>[
                    ['text'=>'Skipping improves:','options'=>['Fitness','Anger','Sleep','Noise'],'answer'=>'A'],
                    ['text'=>'We skip using a:','options'=>['Rope','Book','Chair','Pen'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Healthy Habits',
                'description'=>'Understanding exercise and health.',
                'content_type'=>'text',
                'content'=>"OBJECTIVES:
- Understand why exercise is important.

ACTIVITY:
Name two benefits of exercise.",
                'questions'=>[
                    ['text'=>'Exercise keeps us:','options'=>['Healthy','Sick','Lazy','Weak'],'answer'=>'A'],
                    ['text'=>'Moving daily is:','options'=>['Good','Bad','Wrong','Scary'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Cool Down Activities',
                'description'=>'Cooling down after exercise.',
                'content_type'=>'video',
                'video_url'=>'https://www.youtube.com/watch?v=F2QW6e2GqfA',
                'content'=>null,
                'questions'=>[
                    ['text'=>'Cooling down helps the body to:','options'=>['Relax','Fight','Shout','Sleep immediately'],'answer'=>'A'],
                    ['text'=>'We cool down after:','options'=>['Exercise','Reading','Eating','Sleeping'],'answer'=>'A'],
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