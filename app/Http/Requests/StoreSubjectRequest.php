<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_name'   => ['required', 'string', 'max:255'],
            'subject_code'   => ['required', 'string', 'max:50', 'unique:subjects,subject_code'],
            'category'       => ['nullable', 'string', 'max:255'],
            'credits'        => ['nullable', 'integer', 'min:1', 'max:127'],
            'hours_per_week' => ['nullable', 'integer', 'min:1', 'max:127'],
            'description'    => ['nullable', 'string'],
            'status'         => ['sometimes', 'string', 'in:active,inactive'],
        ];
    }
}
