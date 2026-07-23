<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_year_id'      => ['sometimes', 'integer', 'exists:academic_years,academic_year_id'],
            'room_id'               => ['nullable', 'integer', 'exists:rooms,room_id'],
            'homeroom_teacher_id'   => ['nullable', 'integer', 'exists:teachers,teacher_id'],
            'grade_name'            => ['sometimes', 'string', 'max:255'],
            'level'                 => ['sometimes', 'integer', 'min:1', 'max:255'],
            'status'                => ['sometimes', 'string', 'in:active,inactive,graduated,archived'],
        ];
    }
}
