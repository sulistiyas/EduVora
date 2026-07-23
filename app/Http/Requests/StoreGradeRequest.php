<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,academic_year_id'],
            'room_id' => ['nullable', 'integer', 'exists:rooms,room_id'],
            'homeroom_teacher_id' => ['nullable', 'integer', 'exists:teachers,teacher_id'],
            'grade_name' => ['required', 'string', 'max:255'],
            'level' => ['required', 'integer', 'min:1', 'max:255'],
            'status' => ['sometimes', 'string', 'in:active,inactive,graduated,archived'],
        ];
    }
}
