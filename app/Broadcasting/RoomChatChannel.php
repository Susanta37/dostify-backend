<?php

namespace App\Broadcasting;

use App\Models\Room;
use App\Models\User;

class RoomChatChannel
{
    public function join(User $user, string $roomUuid): array|bool
    {
        $room = Room::query()->where('uuid', $roomUuid)->first();

        if (! $room) {
            return false;
        }

        return $room->members()
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->exists()
            ? ['id' => $user->uuid, 'nickname' => $user->profile?->nickname]
            : false;
    }
}
