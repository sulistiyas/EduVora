<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSemesterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_year_id'  => ['required', 'integer', 'exists:academic_years,academic_year_id'],
            'semester_name'     => ['required', 'string', 'max:255'],
            'start_date'        => ['required', 'date'],
            'end_date'          => ['required', 'date', 'after_or_equal:start_date'],
            'midterm_start_date'=> ['nullable', 'date'],
            'midterm_end_date'  => ['nullable', 'date', 'after_or_equal:midterm_start_date'],
            'final_start_date'  => ['nullable', 'date'],
            'final_end_date'    => ['nullable', 'date', 'after_or_equal:final_start_date'],
            'status'            => ['sometimes', 'string', 'in:active,inactive'],
        ];
    }
}
