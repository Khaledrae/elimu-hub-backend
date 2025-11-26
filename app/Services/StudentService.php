<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StudentService
{
    /**
     * Creates a new student user + student profile (from registration)
     */
    public function registerStudent(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Step 1: Create user
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'county' => $data['county'],
                'phone' => $data['phone'],
                'password' => bcrypt($data['password']),
                'role' => 'student',
                'status' => 'active',
            ]);

            // Step 2: Create student profile
            $student = Student::create([
                'user_id' => $user->id,
                'admission_number' => $data['admission_number'] ?? null,
                'grade_level' => $data['grade_level'] ?? null,
                'school_name' => $data['school_name'] ?? null,
                'dob' => $data['dob'] ?? null,
                'gender' => $data['gender'] ?? null,
                'guardian_id' => $data['guardian_id'] ?? null,
            ]);

            return $user->load('student');
        });
    }

    /**
     * Creates a student profile for an existing user (admin or system trigger)
     */
    public function createForUser(User $user, array $data = [])
    {
        return Student::create([
            'user_id' => $user->id,
            'admission_number' => $data['admission_number'] ?? null,
            'grade_level' => $data['grade_level'] ?? null,
            'school_name' => $data['school_name'] ?? null,
            'dob' => $data['dob'] ?? null,
            'gender' => $data['gender'] ?? null,
            'guardian_id' => $data['guardian_id'] ?? null,
        ]);
    }
}
