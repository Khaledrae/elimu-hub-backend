<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'admin_level' => 'required|in:super_admin,school_admin',
            'school_id' => 'nullable|required_if:admin_level,school_admin|exists:schools,id',
            'status' => 'nullable|string|in:active,inactive'
        ];
    }
}
