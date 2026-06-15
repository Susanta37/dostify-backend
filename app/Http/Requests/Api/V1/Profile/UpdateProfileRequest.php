<?php

namespace App\Http\Requests\Api\V1\Profile;

use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'avatar' => ['nullable', 'string', 'max:500'],
            'nickname' => ['required', 'string', 'min:2', 'max:50'],
            'bio' => ['nullable', 'string', 'max:500'],
            'gender' => ['required', Rule::enum(Gender::class)],
            'language' => ['required', 'string', 'max:10'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:3'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
        ];
    }
}
