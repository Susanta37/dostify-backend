<?php

namespace Tests\Feature\Api\V1;

use App\Models\OtpLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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

        $otpLog = OtpLog::query()->latest()->first();
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

    public function test_user_can_login_with_google_and_receive_token(): void
    {
        config(['services.google.client_id' => 'google-client-id']);

        Http::fake([
            'https://oauth2.googleapis.com/tokeninfo*' => Http::response([
                'aud' => 'google-client-id',
                'sub' => 'google-user-123',
                'email' => 'google-user@example.com',
                'email_verified' => 'true',
                'name' => 'Google User',
            ]),
        ]);

        $response = $this->postJson('/api/v1/auth/google', [
            'id_token' => 'valid-google-id-token',
            'device_id' => 'test-device-002',
            'device_type' => 'android',
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'Login successful.',
                'user' => [
                    'email' => 'google-user@example.com',
                    'name' => 'Google User',
                ],
            ])
            ->assertJsonStructure([
                'token',
                'user' => ['uuid', 'email'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'google-user@example.com',
            'google_id' => 'google-user-123',
        ]);
        $this->assertDatabaseHas('wallets', [
            'user_id' => User::query()->where('email', 'google-user@example.com')->value('id'),
        ]);
    }

    public function test_google_login_rejects_invalid_google_token(): void
    {
        config(['services.google.client_id' => 'google-client-id']);

        Http::fake([
            'https://oauth2.googleapis.com/tokeninfo*' => Http::response([
                'aud' => 'another-client-id',
                'sub' => 'google-user-123',
                'email' => 'google-user@example.com',
                'email_verified' => 'true',
            ]),
        ]);

        $response = $this->postJson('/api/v1/auth/google', [
            'id_token' => 'invalid-google-id-token',
            'device_id' => 'test-device-002',
            'device_type' => 'android',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['id_token']);
    }
}
