<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_name'       => ['required', 'string', 'max:255'],
            'npsn'              => ['nullable', 'string', 'max:50', 'unique:school_profiles,npsn'],
            'nss'               => ['nullable', 'string', 'max:50', 'unique:school_profiles,nss'],
            'accreditation'     => ['nullable', 'string', 'in:A,B,C,D,E'],
            'school_type'       => ['nullable', 'string', 'in:Elementary,Junior High,Senior High'],
            'contact_email'     => ['nullable', 'email', 'max:255'],
            'contact_phone'     => ['nullable', 'string', 'max:50'],
            'website'           => ['nullable', 'url', 'max:255'],
            'address'           => ['nullable', 'string', 'max:255'],
            'province'          => ['nullable', 'string', 'max:255'],
            'city'              => ['nullable', 'string', 'max:255'],
            'district'          => ['nullable', 'string', 'max:255'],
            'postal_code'       => ['nullable', 'string', 'max:10'],
            'logo'              => ['nullable', 'string', 'max:255'],
            'headmaster_name'   => ['nullable', 'string', 'max:255'],
            'headmaster_nip'    => ['nullable', 'string', 'max:50'],
            'kkm_default'       => ['nullable', 'integer', 'min:0', 'max:100'],
            'status'            => ['sometimes', 'string', 'in:active,inactive'],
        ];
    }
}
