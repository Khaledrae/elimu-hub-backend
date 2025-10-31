<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Base user info
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
        ];

        // Conditional loading based on role
        switch ($this->role) {
            case 'student':
                $data['student_profile'] = $this->whenLoaded('student', function () {
                    return new StudentResource($this->student);
                });
                break;

            case 'teacher':
                $data['teacher_profile'] = $this->whenLoaded('teacher', function () {
                    return [
                        'id' => $this->teacher->id,
                        'staff_number' => $this->teacher->staff_number,
                        'subject_specialization' => $this->teacher->subject_specialization,
                        'school_name' => $this->teacher->school_name,
                        'qualification' => $this->teacher->qualification,
                        'experience_years' => $this->teacher->experience_years,
                        'gender' => $this->teacher->gender,
                        'dob' => $this->teacher->dob,
                        'status' => $this->teacher->status,
                        'created_at' => $this->teacher->created_at,
                        'updated_at' => $this->teacher->updated_at,
                    ];
                });
                break;

            default:
                // Admin or generic user
                $data['profile'] = null;
        }

        return $data;
    }
}
