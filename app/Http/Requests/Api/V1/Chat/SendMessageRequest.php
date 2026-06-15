<?php

namespace App\Http\Requests\Api\V1\Chat;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => ['required_without:metadata', 'nullable', 'string', 'max:5000'],
            'type' => ['nullable', 'string', 'in:text,image,gift,audio'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
