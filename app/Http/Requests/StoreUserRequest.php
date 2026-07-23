<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'exists:roles,role_id'],
            'school' => ['required', 'exists:school_profiles,school_id'],
            'status' => ['required', 'in:active,inactive'],
            'profile' => ['nullable', 'array'],
            'profile.nip' => ['nullable', 'string', 'max:50'],
            'profile.nik' => ['nullable', 'string', 'max:20'],
            'profile.full_name' => ['nullable', 'string', 'max:255'],
            'profile.birth_place' => ['nullable', 'string', 'max:100'],
            'profile.birth_date' => ['nullable', 'date'],
            'profile.gender' => ['nullable', 'in:male,female'],
            'profile.religion' => ['nullable', 'string', 'max:50'],
            'profile.address' => ['nullable', 'string'],
            'profile.phone' => ['nullable', 'string', 'max:20'],
            'profile.email' => ['nullable', 'email', 'max:255'],
            'profile.employment_status' => ['nullable', 'string', 'max:50'],
            'profile.position' => ['nullable', 'string', 'max:100'],
            'profile.grade_level' => ['nullable', 'string', 'max:20'],
            'profile.education_level' => ['nullable', 'string', 'max:10'],
            'profile.major' => ['nullable', 'string', 'max:100'],
            'profile.certification' => ['nullable', 'string', 'max:100'],
            'profile.npwp' => ['nullable', 'string', 'max:20'],
            'profile.join_date' => ['nullable', 'date'],
            'profile.nis' => ['nullable', 'string', 'max:20'],
            'profile.nick_name' => ['nullable', 'string', 'max:100'],
            'profile.phone_number' => ['nullable', 'string', 'max:20'],
            'profile.city' => ['nullable', 'string', 'max:100'],
            'profile.province' => ['nullable', 'string', 'max:100'],
            'profile.postal_code' => ['nullable', 'string', 'max:10'],
            'profile.grade_id' => ['nullable', 'integer'],
            'profile.class_group' => ['nullable', 'string', 'max:50'],
            'profile.enrollment_date' => ['nullable', 'date'],
            'profile.graduation_date' => ['nullable', 'date'],
        ];
    }
}
