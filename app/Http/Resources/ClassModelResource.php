<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassModelResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'level_group' => $this->level_group,
            'status' => $this->status ?? 'active',
            'manager_id' => $this->manager_id,
            'manager' => $this->whenLoaded('manager', function () {
                return [
                    'id' => $this->manager->id,
                    'first_name' => $this->manager->first_name,
                    'last_name' => $this->manager->last_name,
                    'email' => $this->manager->email,
                ];
            }),
            'student_count' => $this->whenLoaded('students', function () {
                return $this->students->count();
            }),
            'course_count' => $this->whenLoaded('courses', function () {
                return $this->courses->count();
            }),
            'lesson_count' => $this->whenLoaded('lessons', function () {
                return $this->lessons->count();
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}