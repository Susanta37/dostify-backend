<?php

namespace App\Http\Requests\Api\V1\Gift;

use Illuminate\Foundation\Http\FormRequest;

class SendGiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gift_uuid' => ['required', 'uuid', 'exists:gifts,uuid'],
            'receiver_uuid' => ['required', 'uuid', 'exists:users,uuid'],
            'room_uuid' => ['nullable', 'uuid', 'exists:rooms,uuid'],
            'context_type' => ['nullable', 'string', 'max:50'],
            'context_id' => ['nullable', 'integer'],
        ];
    }
}
