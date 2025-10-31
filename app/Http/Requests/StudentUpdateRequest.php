<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
     public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'admission_number' => 'nullable|string|max:50',
            'grade_level' => 'nullable|string|max:50',
            'school_name' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'guardian_id' => 'nullable|exists:users,id',
            'status' => 'nullable|string|max:50',
        ];
    }
}
