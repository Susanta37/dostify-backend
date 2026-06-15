<?php

namespace App\Events\Chat;

use App\Models\MessageRead as MessageReadModel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReadEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public MessageReadModel $read) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('private-chat.'.$this->read->conversation->uuid),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.read';
    }

    public function broadcastWith(): array
    {
        return [
            'message_id' => $this->read->message_id,
            'user_uuid' => $this->read->user->uuid,
            'read_at' => $this->read->read_at->toIso8601String(),
        ];
    }
}
