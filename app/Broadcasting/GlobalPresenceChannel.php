<?php

namespace App\Broadcasting;

use App\Models\User;

class GlobalPresenceChannel
{
    public function join(User $user): array|bool
    {
        return [
            'id' => $user->uuid,
            'nickname' => $user->profile?->nickname,
            'online_at' => now()->toIso8601String(),
        ];
    }
}
