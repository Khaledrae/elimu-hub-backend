<?php

namespace App\Services;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

class TeacherService
{
    public function registerTeacher(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'role' => 'teacher',
                'status' => 'active',
            ]);

            $teacher = Teacher::create([
                'user_id' => $user->id,
                'staff_number' => $data['staff_number'],
                'subject_specialization' => $data['subject_specialization'] ?? null,
                'school_name' => $data['school_name'] ?? null,
                'qualification' => $data['qualification'] ?? null,
                'experience_years' => $data['experience_years'] ?? null,
                'gender' => $data['gender'] ?? null,
                'dob' => $data['dob'] ?? null,
            ]);

            return $user->load('teacher');
        });
    }
}
