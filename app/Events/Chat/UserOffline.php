<?php

namespace App\Events\Chat;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserOffline implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public User $user) {}

    public function broadcastOn(): array
    {
        return [new PresenceChannel('presence-user')];
    }

    public function broadcastAs(): string
    {
        return 'user.offline';
    }

    public function broadcastWith(): array
    {
        return [
            'user_uuid' => $this->user->uuid,
            'offline_at' => now()->toIso8601String(),
        ];
    }
}
