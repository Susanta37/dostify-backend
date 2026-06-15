<?php

namespace App\Broadcasting;

use App\Models\Conversation;
use App\Models\User;

class PrivateChatChannel
{
    public function join(User $user, string $conversationUuid): array|bool
    {
        $conversation = Conversation::query()->where('uuid', $conversationUuid)->first();

        if (! $conversation) {
            return false;
        }

        return $conversation->participants()
            ->where('user_id', $user->id)
            ->exists()
            ? ['id' => $user->uuid, 'nickname' => $user->profile?->nickname]
            : false;
    }
}
