<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\RequestOtpRequest;
use App\Http\Requests\Api\V1\Auth\VerifyOtpRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $auth) {}

    public function requestOtp(RequestOtpRequest $request): JsonResponse
    {
        $this->auth->requestOtp(
            $request->validated('phone'),
            $request->ip(),
        );

        return response()->json([
            'message' => 'OTP sent successfully.',
        ]);
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->auth->verifyOtpAndLogin(
            phone: $request->validated('phone'),
            otp: $request->validated('otp'),
            deviceData: [
                'device_id' => $request->validated('device_id'),
                'device_name' => $request->validated('device_name'),
                'device_type' => $request->validated('device_type'),
                'fcm_token' => $request->validated('fcm_token'),
                'ip_address' => $request->ip(),
            ],
            referralCode: $request->validated('referral_code'),
        );

        return response()->json([
            'message' => 'Login successful.',
            'token' => $result['token'],
            'is_profile_complete' => $result['is_profile_complete'],
            'user' => new UserResource($result['user']),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->auth->logout(
            $request->user(),
            $request->input('device_id'),
        );

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource(
                $request->user()->load(['profile', 'wallet'])
            ),
        ]);
    }
}
