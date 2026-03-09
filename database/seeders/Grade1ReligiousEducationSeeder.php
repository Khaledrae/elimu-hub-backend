<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\ClassModel;
use App\Models\Course;
use App\Models\Teacher;

class Grade1ReligiousEducationSeeder extends Seeder
{
    public function run(): void
    {
        $class   = ClassModel::where('name', 'Grade 1')->firstOrFail();
        $course  = Course::where('title', 'CRE')->firstOrFail();
        $teacher = Teacher::where('subject_specialization', 'CRE')->first();
        $teacherUserId = $teacher?->user_id;

        $lessons = [

            [
                'title'=>'God as Creator',
                'description'=>'Learning that God created the world.',
                'content'=>"OBJECTIVES:\n- Understand that God created the world.\n\nACTIVITY:\nName things around you that God created.",
                'questions'=>[
                    ['text'=>'Who created the world?','options'=>['God','Teacher','Farmer','Doctor'],'answer'=>'A'],
                    ['text'=>'Trees were created by:','options'=>['God','Cars','People','Books'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Thanking God',
                'description'=>'Learning to say thank you to God.',
                'content'=>"OBJECTIVES:\n- Say simple prayers of thanks.\n\nACTIVITY:\nSay a short thank you prayer.",
                'questions'=>[
                    ['text'=>'We thank God for:','options'=>['Life','Fighting','Stealing','Crying'],'answer'=>'A'],
                    ['text'=>'Prayer is talking to:','options'=>['God','Chair','Book','Food'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Loving One Another',
                'description'=>'Showing love and kindness.',
                'content'=>"OBJECTIVES:\n- Show kindness to others.\n\nACTIVITY:\nHelp a friend today.",
                'questions'=>[
                    ['text'=>'We should treat others with:','options'=>['Love','Anger','Hate','Rudeness'],'answer'=>'A'],
                    ['text'=>'Helping is a sign of:','options'=>['Kindness','Pride','Anger','Fear'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Honesty',
                'description'=>'Telling the truth.',
                'content'=>"OBJECTIVES:\n- Always tell the truth.\n\nACTIVITY:\nDiscuss why lying is wrong.",
                'questions'=>[
                    ['text'=>'Telling the truth means being:','options'=>['Honest','Angry','Lazy','Rude'],'answer'=>'A'],
                    ['text'=>'Lying is:','options'=>['Wrong','Right','Fun','Good'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Sharing',
                'description'=>'Learning to share with others.',
                'content'=>"OBJECTIVES:\n- Share toys and food.\n\nACTIVITY:\nShare a pencil with a friend.",
                'questions'=>[
                    ['text'=>'Sharing shows:','options'=>['Love','Selfishness','Anger','Fear'],'answer'=>'A'],
                    ['text'=>'We share with:','options'=>['Others','Nobody','Walls','Books'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Obeying Parents and Teachers',
                'description'=>'Respecting authority.',
                'content'=>"OBJECTIVES:\n- Listen to parents and teachers.\n\nACTIVITY:\nFollow a teacher’s instruction.",
                'questions'=>[
                    ['text'=>'We should obey our:','options'=>['Parents','Strangers','Thieves','Enemies'],'answer'=>'A'],
                    ['text'=>'Obeying shows:','options'=>['Respect','Rudeness','Anger','Pride'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Caring for Creation',
                'description'=>'Taking care of the environment.',
                'content'=>"OBJECTIVES:\n- Keep surroundings clean.\n\nACTIVITY:\nPick litter from the classroom.",
                'questions'=>[
                    ['text'=>'We care for the environment by:','options'=>['Keeping clean','Throwing dirt','Breaking things','Burning waste'],'answer'=>'A'],
                    ['text'=>'The earth is God’s:','options'=>['Creation','Toy','Book','Game'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Forgiveness',
                'description'=>'Learning to forgive others.',
                'content'=>"OBJECTIVES:\n- Say sorry.\n- Forgive others.\n\nACTIVITY:\nRole-play saying sorry.",
                'questions'=>[
                    ['text'=>'When we hurt someone we should:','options'=>['Say sorry','Run away','Laugh','Hide'],'answer'=>'A'],
                    ['text'=>'Forgiving shows:','options'=>['Kindness','Anger','Hate','Pride'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Helping at Home',
                'description'=>'Doing small chores.',
                'content'=>"OBJECTIVES:\n- Help with simple tasks.\n\nACTIVITY:\nName one chore you can do.",
                'questions'=>[
                    ['text'=>'Helping at home shows:','options'=>['Responsibility','Laziness','Anger','Fear'],'answer'=>'A'],
                    ['text'=>'A chore is:','options'=>['Work at home','Game','Song','Story'],'answer'=>'A'],
                ],
            ],

            [
                'title'=>'Respecting Others',
                'description'=>'Treating others well.',
                'content'=>"OBJECTIVES:\n- Use polite words.\n\nACTIVITY:\nSay “please” and “thank you”.",
                'questions'=>[
                    ['text'=>'We show respect by saying:','options'=>['Please','Shout','Push','Ignore'],'answer'=>'A'],
                    ['text'=>'Respect means:','options'=>['Treating others well','Fighting','Laughing at others','Ignoring'],'answer'=>'A'],
                ],
            ],

        ];

        foreach ($lessons as $index=>$l) {
            $lesson=Lesson::create([
                'class_id'=>$class->id,
                'course_id'=>$course->id,
                'teacher_id'=>$teacherUserId,
                'title'=>$l['title'],
                'description'=>$l['description'],
                'content_type'=>'text',
                'content'=>$l['content'],
                'order'=>$index+1,
                'status'=>'published',
            ]);

            $assessment=Assessment::create([
                'lesson_id'=>$lesson->id,
                'teacher_id'=>$teacherUserId,
                'title'=>$l['title'].' Assessment',
                'instructions'=>'Answer all questions.',
                'type'=>'quiz',
                'total_marks'=>count($l['questions'])*2,
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