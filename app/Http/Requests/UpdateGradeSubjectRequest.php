<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradeSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_id'     => ['sometimes', 'integer', 'exists:teachers,teacher_id'],
            'kkm'            => ['sometimes', 'integer', 'min:0', 'max:100'],
            'weight_harian'  => ['sometimes', 'integer', 'min:0', 'max:100'],
            'weight_uts'     => ['sometimes', 'integer', 'min:0', 'max:100'],
            'weight_uas'     => ['sometimes', 'integer', 'min:0', 'max:100'],
            'status'         => ['sometimes', 'string', 'in:active,inactive'],
        ];
    }
}
