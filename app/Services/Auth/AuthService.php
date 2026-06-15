<?php

namespace App\Services\Auth;

use App\Enums\OtpPurpose;
use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;

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
