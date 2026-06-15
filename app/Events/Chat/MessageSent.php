<?php

namespace App\Events\Chat;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('private-chat.'.$this->message->conversation->uuid),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'uuid' => $this->message->uuid,
            'conversation_uuid' => $this->message->conversation->uuid,
            'user_uuid' => $this->message->user->uuid,
            'body' => $this->message->body,
            'type' => $this->message->type,
            'created_at' => $this->message->created_at?->toIso8601String(),
        ];
    }
}
