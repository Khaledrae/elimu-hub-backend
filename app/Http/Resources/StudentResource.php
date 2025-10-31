<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'guardian' => $this->guardian ? [
                'id' => $this->guardian->id,
                'name' => $this->guardian->name,
                'email' => $this->guardian->email,
            ] : null,
            'admission_number' => $this->admission_number,
            'grade_level' => $this->grade_level,
            'school_name' => $this->school_name,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
