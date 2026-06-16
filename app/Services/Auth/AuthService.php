<?php

namespace App\Services\Auth;

use App\Enums\OtpPurpose;
use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private UserRepository $users,
        private WalletRepository $wallets,
        private OtpService $otp,
    ) {}

    public function requestOtp(string $phone, ?string $ipAddress = null): void
    {
        $this->otp->send($phone, OtpPurpose::Login, $ipAddress);
    }

    public function verifyOtpAndLogin(
        string $phone,
        string $otp,
        array $deviceData,
        ?string $referralCode = null,
    ): array {
        $this->otp->verify($phone, $otp, OtpPurpose::Login);

        $user = $this->users->findByPhone($phone);

        if (! $user) {
            $referrer = $referralCode
                ? $this->users->findByReferralCode($referralCode)
                : null;

            $user = $this->users->create([
                'phone' => $phone,
                'phone_verified_at' => now(),
                'role' => UserRole::User,
                'referred_by' => $referrer?->id,
            ]);

            $this->wallets->createForUser($user);
        } else {
            $user->update(['phone_verified_at' => now()]);
        }

        $device = $this->users->registerDevice($user, array_merge($deviceData, [
            'last_active_at' => now(),
        ]));

        $token = $user->createToken($device->device_id)->plainTextToken;

        return [
            'user' => $user->load(['profile', 'wallet']),
            'token' => $token,
            'is_profile_complete' => $user->is_profile_complete,
        ];
    }

    public function loginWithGoogle(
        string $idToken,
        array $deviceData,
        ?string $referralCode = null,
    ): array {
        $googleUser = $this->verifyGoogleIdToken($idToken);

        $user = $this->users->findByGoogleId($googleUser['sub'])
            ?? $this->users->findByEmail($googleUser['email']);

        if (! $user) {
            $referrer = $referralCode
                ? $this->users->findByReferralCode($referralCode)
                : null;

            $user = $this->users->create([
                'name' => $googleUser['name'],
                'email' => $googleUser['email'],
                'google_id' => $googleUser['sub'],
                'email_verified_at' => now(),
                'role' => UserRole::User,
                'referred_by' => $referrer?->id,
            ]);

            $this->wallets->createForUser($user);
        } else {
            $user->update([
                'name' => $user->name ?: $googleUser['name'],
                'email' => $user->email ?: $googleUser['email'],
                'google_id' => $user->google_id ?: $googleUser['sub'],
                'email_verified_at' => $user->email_verified_at ?: now(),
            ]);
        }

        $device = $this->users->registerDevice($user, array_merge($deviceData, [
            'last_active_at' => now(),
        ]));

        $token = $user->createToken($device->device_id)->plainTextToken;

        return [
            'user' => $user->load(['profile', 'wallet']),
            'token' => $token,
            'is_profile_complete' => $user->is_profile_complete,
        ];
    }

    private function verifyGoogleIdToken(string $idToken): array
    {
        $clientId = config('services.google.client_id');

        if (! $clientId) {
            throw ValidationException::withMessages([
                'id_token' => ['Google login is not configured.'],
            ]);
        }

        $response = Http::acceptJson()->get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken,
        ]);

        if (! $response->ok()) {
            throw ValidationException::withMessages([
                'id_token' => ['The Google token is invalid.'],
            ]);
        }

        $payload = $response->json();
        $emailVerified = filter_var($payload['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (
            ($payload['aud'] ?? null) !== $clientId
            || empty($payload['sub'])
            || empty($payload['email'])
            || ! $emailVerified
        ) {
            throw ValidationException::withMessages([
                'id_token' => ['The Google token is invalid.'],
            ]);
        }

        return [
            'sub' => $payload['sub'],
            'email' => $payload['email'],
            'name' => $payload['name'] ?? strtok($payload['email'], '@'),
        ];
    }

    public function logout(User $user, ?string $deviceId = null): void
    {
        if ($deviceId) {
            $user->tokens()->where('name', $deviceId)->delete();
            $user->devices()->where('device_id', $deviceId)->update(['is_active' => false]);
        } else {
            $user->tokens()->delete();
            $user->devices()->update(['is_active' => false]);
        }
    }
}
