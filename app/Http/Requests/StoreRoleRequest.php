<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role_name' => ['required', 'string', 'max:255', 'unique:roles,role_name'],
            'role_description' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'in:active,inactive'],
        ];
    }
}
