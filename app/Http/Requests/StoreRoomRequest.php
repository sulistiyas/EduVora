<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50'],
            'type' => ['required', 'string', 'in:classroom,lab,library,office,sport'],
            'floor' => ['required', 'integer', 'min:0', 'max:255'],
            'building' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1', 'max:255'],
            'facility' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'in:available,maintenance,inactive'],
        ];
    }
}
