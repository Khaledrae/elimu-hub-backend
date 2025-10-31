<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherStoreRequest extends FormRequest
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
            'staff_number' => 'required|string|unique:teachers',
            'subject_specialization' => 'nullable|string|max:255',
            'school_name' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'experience_years' => 'nullable|numeric|min:0',
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date',
        ];
    }
}
