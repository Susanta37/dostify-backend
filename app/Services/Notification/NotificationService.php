<?php

namespace App\Services\Notification;

use App\Models\AppNotification;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NotificationService
{
    public function list(User $user, int $perPage = 20): LengthAwarePaginator
    {
        return AppNotification::query()
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->latest()
            ->paginate($perPage);
    }

    public function markRead(User $user, ?string $notificationId = null): int
    {
        $query = AppNotification::query()
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at');

        if ($notificationId) {
            $query->where('id', $notificationId);
        }

        return $query->update(['read_at' => now()]);
    }

    public function store(User $user, string $type, array $data): AppNotification
    {
        return AppNotification::query()->create([
            'type' => $type,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => $data,
        ]);
    }

    public function updateFcmToken(User $user, array $data): UserDevice
    {
        return $user->devices()->updateOrCreate(
            ['device_id' => $data['device_id']],
            [
                'device_name' => $data['device_name'] ?? null,
                'device_type' => $data['device_type'] ?? 'android',
                'fcm_token' => $data['fcm_token'],
                'last_active_at' => now(),
                'is_active' => true,
            ]
        );
    }
}
