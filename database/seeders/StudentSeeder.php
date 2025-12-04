<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\School;
use App\Models\ClassModel;

class StudentSeeder extends Seeder
{
    public function run()
    {
        $schoolIds = School::pluck('id')->toArray();
        $classIds  = ClassModel::pluck('id')->toArray();

        for ($i = 1; $i <= 30; $i++) {

            $email = "student{$i}@elimuhub.ac.ke";

            // Create student user
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'first_name' => 'Student' . $i,
                    'last_name'  => 'User',
                    'role'       => 'student',
                    'phone'      => '07' . rand(10000000, 99999999),
                    'county'     => 1,
                    'status'     => 'active',
                    'password'   => bcrypt('Secret123')
                ]
            );

            // Create Guardian account
            $guardian = User::firstOrCreate(
                ['email' => "guardian{$i}@elimuhub.ac.ke"],
                [
                    'first_name' => 'Guardian' . $i,
                    'last_name'  => 'Parent',
                    'role'       => 'parent',
                    'phone'      => '07' . rand(10000000, 99999999),
                    'county'     => 1,
                    'status'     => 'active',
                    'password'   => bcrypt('Secret123')
                ]
            );

            Student::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'admission_number' => 'ADM-' . rand(1000,9999),
                    'grade_level'      => $classIds[array_rand($classIds)],
                    'school_name'      => 'ElimuHub School',
                    'dob'              => rand(2010, 2016) . '-' . rand(1,12) . '-' . rand(1,28),
                    'gender'           => rand(0,1) ? 'Male' : 'Female',
                    'guardian_id'      => $guardian->id,
                    'status'           => 'active'
                ]
            );
        }
    }
}
