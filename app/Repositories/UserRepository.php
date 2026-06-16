<?php

namespace App\Repositories;

use App\Models\OtpLog;
use App\Models\User;
use App\Models\UserDevice;
use App\Repositories\Contracts\BaseRepository;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByPhone(string $phone): ?User
    {
        return User::query()->where('phone', $phone)->first();
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    public function findByGoogleId(string $googleId): ?User
    {
        return User::query()->where('google_id', $googleId)->first();
    }

    public function findByReferralCode(string $code): ?User
    {
        return User::query()->where('referral_code', $code)->first();
    }

    public function registerDevice(User $user, array $data): UserDevice
    {
        return $user->devices()->updateOrCreate(
            ['device_id' => $data['device_id']],
            $data
        );
    }

    public function createOtpLog(array $data): OtpLog
    {
        return OtpLog::query()->create($data);
    }

    public function findLatestOtp(string $phone, string $purpose): ?OtpLog
    {
        return OtpLog::query()
            ->where('phone', $phone)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->latest()
            ->first();
    }
}
