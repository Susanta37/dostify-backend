<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Enums\DeviceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GoogleLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_token' => ['required', 'string'],
            'device_id' => ['required', 'string', 'max:255'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'device_type' => ['required', Rule::enum(DeviceType::class)],
            'fcm_token' => ['nullable', 'string'],
            'referral_code' => ['nullable', 'string', 'max:20'],
        ];
    }
}
