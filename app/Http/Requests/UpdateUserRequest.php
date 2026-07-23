<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$userId],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['nullable', 'exists:roles,role_id'],
            'school' => ['nullable', 'exists:school_profiles,school_id'],
            'profile' => ['nullable', 'array'],
            'profile.nip' => ['nullable', 'string', 'max:50'],
            'profile.nik' => ['nullable', 'string', 'max:20'],
            'profile.full_name' => ['nullable', 'string', 'max:255'],
            'profile.birth_place' => ['nullable', 'string', 'max:100'],
            'profile.birth_date' => ['nullable', 'date'],
            'profile.gender' => ['nullable', 'string', 'max:20'],
            'profile.religion' => ['nullable', 'string', 'max:50'],
            'profile.address' => ['nullable', 'string'],
            'profile.employment_status' => ['nullable', 'string', 'max:50'],
            'profile.position' => ['nullable', 'string', 'max:100'],
            'profile.education_level' => ['nullable', 'string', 'max:10'],
            'profile.major' => ['nullable', 'string', 'max:100'],
            'profile.certification' => ['nullable', 'string', 'max:100'],
            'profile.join_date' => ['nullable', 'date'],
            'profile.nis' => ['nullable', 'string', 'max:20'],
            'profile.class_group' => ['nullable', 'string', 'max:50'],
            'profile.enrollment_date' => ['nullable', 'date'],
            'profile.city' => ['nullable', 'string', 'max:100'],
            'profile.province' => ['nullable', 'string', 'max:100'],
        ];
    }
}
