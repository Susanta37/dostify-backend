<?php

namespace App\Http\Requests\Api\V1\Match;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequestMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'preferred_language' => ['nullable', 'string', 'max:10'],
            'preferred_gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'match_type' => ['nullable', Rule::in(['random', 'language', 'gender'])],
        ];
    }
}
