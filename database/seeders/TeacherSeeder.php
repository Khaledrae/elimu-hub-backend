<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Course;

class TeacherSeeder extends Seeder
{
    public function run()
    {
        $courseTitles = [
            'Mathematics', 'English', 'Kiswahili', 'Science & Technology',
            'Social Studies', 'CRE', 'Music', 'Art & Craft', 'Physical Education',
            'Agriculture', 'Home Science',
            'Integrated Science', 'Pre-Technical Studies', 'Business Studies',
            'Computer Science', 'Life Skills', 'Visual Arts',
            'Performing Arts', 'Religious Education'
        ];

        foreach ($courseTitles as $title) {

            $email = strtolower(str_replace(' ', '_', $title)) . '@elimuhub.ac.ke';

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'first_name' => $title ,
                    'last_name' => 'Teacher',
                    'role' => 'teacher',
                    'phone' => '07' . rand(10000000, 99999999),
                    'county' => 1,
                    'status' => 'active',
                    'password' => bcrypt('Secret123')
                ]
            );

            Teacher::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'staff_number' => 'STF-' . rand(1000, 9999),
                    'subject_specialization' => $title,
                    'school_name' => 'ElimuHub School',
                    'qualification' => 'B.Ed',
                    'experience_years' => rand(1,10),
                    'gender' => rand(0,1) ? 'Male' : 'Female',
                    'dob' => rand(1985,1995).'-'.rand(1,12).'-'.rand(1,28),
                    'status' => 'active'
                ]
            );
        }
    }
}
