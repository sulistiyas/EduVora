<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradeSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:subjects,id'],
            'teacher_id' => ['nullable', 'integer', 'exists:teachers,teacher_id'],
            'kkm' => ['nullable', 'integer', 'min:0', 'max:100'],
            'weight_harian' => ['nullable', 'integer', 'min:0', 'max:100'],
            'weight_uts' => ['nullable', 'integer', 'min:0', 'max:100'],
            'weight_uas' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }
}
