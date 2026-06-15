<?php

namespace App\Http\Requests\Api\V1\Chat;

use App\Enums\ConversationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['nullable', Rule::enum(ConversationType::class)],
            'name' => ['nullable', 'string', 'max:100'],
            'participant_uuids' => ['required', 'array', 'min:1'],
            'participant_uuids.*' => ['uuid', 'exists:users,uuid'],
        ];
    }
}
