<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_name' => ['sometimes', 'string', 'max:255'],
            'code' => ['sometimes', 'string', 'max:50'],
            'type' => ['sometimes', 'string', 'in:classroom,lab,library,office,sport'],
            'floor' => ['sometimes', 'integer', 'min:0', 'max:255'],
            'building' => ['sometimes', 'string', 'max:255'],
            'capacity' => ['sometimes', 'integer', 'min:1', 'max:255'],
            'facility' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'in:available,maintenance,inactive'],
        ];
    }
}
