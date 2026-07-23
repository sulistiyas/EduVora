<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role_name'        => ['sometimes', 'string', 'max:255', 'unique:roles,role_name,' . $this->route('id') . ',role_id'],
            'role_description' => ['nullable', 'string', 'max:255'],
            'status'           => ['sometimes', 'string', 'in:active,inactive'],
        ];
    }
}
