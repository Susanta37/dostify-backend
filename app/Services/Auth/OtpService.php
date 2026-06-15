<?php

namespace App\Services\Auth;

use App\Enums\OtpPurpose;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class OtpService
{
    public function __construct(private UserRepository $users) {}

    public function send(string $phone, OtpPurpose $purpose, ?string $ipAddress = null): void
    {
        $otp = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(config('voiceconnect.otp.expiry_minutes', 5));

        $this->users->createOtpLog([
            'phone' => $phone,
            'otp_hash' => Hash::make($otp),
            'purpose' => $purpose,
            'expires_at' => $expiresAt,
            'ip_address' => $ipAddress,
        ]);

        if (config('app.debug')) {
            logger()->info("OTP for {$phone}: {$otp}");
        }
    }

    public function verify(string $phone, string $otp, OtpPurpose $purpose): bool
    {
        $otpLog = $this->users->findLatestOtp($phone, $purpose->value);

        if (! $otpLog || $otpLog->isExpired()) {
            throw ValidationException::withMessages([
                'otp' => ['The OTP has expired or is invalid.'],
            ]);
        }

        if ($otpLog->attempts >= config('voiceconnect.otp.max_attempts', 5)) {
            throw ValidationException::withMessages([
                'otp' => ['Too many attempts. Please request a new OTP.'],
            ]);
        }

        if (! Hash::check($otp, $otpLog->otp_hash)) {
            $otpLog->increment('attempts');

            throw ValidationException::withMessages([
                'otp' => ['The OTP is incorrect.'],
            ]);
        }

        $otpLog->update(['verified_at' => now()]);

        return true;
    }
}
