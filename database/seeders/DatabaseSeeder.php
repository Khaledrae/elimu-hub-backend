<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            // CountySeeder::class,
            // SchoolSeeder::class,
            // AdminSeeder::class,
            // ClassModelSeeder::class,
            // StudentSeeder::class,
            // CourseSeeder::class,
            // TeacherSeeder::class,
            // PlanSeeder::class,
            //LessonSeeder::class,
            // AssessmentSeeder::class,
            // QuestionSeeder::class,
            //Grade2EnglishSeeder::class,
            // Grade1EnglishSeeder::class,
            //Grade1CreativeArtsSeeder::class,
            //Grade1MovementPhysicalActivitiesSeeder::class,
            //Grade1KiswahiliSeeder::class,
            // Grade1EnvironmentalActivitiesSeeder::class,
            // Grade1ReligiousEducationSeeder::class,
            // Grade1MathematicsSeeder::class,
            
        ]);
    }
}
