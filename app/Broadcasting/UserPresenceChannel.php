<?php

namespace App\Broadcasting;

use App\Models\User;

class UserPresenceChannel
{
    public function join(User $user, string $userUuid): array|bool
    {
        return $user->uuid === $userUuid
            ? ['id' => $user->uuid, 'online_at' => now()->toIso8601String()]
            : false;
    }
}
