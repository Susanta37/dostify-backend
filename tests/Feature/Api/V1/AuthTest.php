<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_otp(): void
    {
        $response = $this->postJson('/api/v1/auth/otp/request', [
            'phone' => '+919876543210',
        ]);

        $response->assertOk()
            ->assertJson(['message' => 'OTP sent successfully.']);
    }

    public function test_user_can_verify_otp_and_receive_token(): void
    {
        $this->postJson('/api/v1/auth/otp/request', [
            'phone' => '+919876543210',
        ]);

        $otpLog = \App\Models\OtpLog::query()->latest()->first();
        $otp = '123456';

        $otpLog->update(['otp_hash' => Hash::make($otp)]);

        $response = $this->postJson('/api/v1/auth/otp/verify', [
            'phone' => '+919876543210',
            'otp' => $otp,
            'device_id' => 'test-device-001',
            'device_type' => 'android',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'token',
                'user' => ['uuid', 'phone'],
            ]);

        $this->assertDatabaseHas('users', ['phone' => '+919876543210']);
        $this->assertDatabaseHas('wallets', [
            'user_id' => User::query()->where('phone', '+919876543210')->value('id'),
        ]);
    }
}
