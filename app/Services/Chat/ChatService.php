<?php

namespace App\Services\Chat;

use App\Enums\ConversationType;
use App\Events\Chat\MessageReadEvent;
use App\Events\Chat\MessageSent;
use App\Events\Chat\UserTyping;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Repositories\Chat\ChatRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChatService
{
    public function __construct(private ChatRepository $chat) {}

    public function listConversations(User $user)
    {
        return $this->chat->getConversationsForUser($user);
    }

    public function createConversation(User $user, array $data): Conversation
    {
        return DB::transaction(function () use ($user, $data) {
            $type = ConversationType::from($data['type'] ?? 'private');
            $participantUuids = $data['participant_uuids'] ?? [];

            $participants = User::query()->whereIn('uuid', $participantUuids)->pluck('id')->all();
            $participants[] = $user->id;
            $participants = array_unique($participants);

            if ($type === ConversationType::Private && count($participants) === 2) {
                $otherUserId = collect($participants)->first(fn ($id) => $id !== $user->id);
                $existing = $this->chat->findPrivateBetween($user, User::query()->find($otherUserId));

                if ($existing) {
                    return $existing->load(['participants.user.profile']);
                }
            }

            return $this->chat->createConversation([
                'type' => $type,
                'name' => $data['name'] ?? null,
                'created_by' => $user->id,
            ], $participants);
        });
    }

    public function getConversation(User $user, string $uuid): Conversation
    {
        return $this->findConversationOrFail($user, $uuid);
    }

    public function deleteConversation(User $user, string $uuid): void
    {
        $conversation = $this->findConversationOrFail($user, $uuid);
        $this->chat->removeParticipant($conversation, $user);
    }

    public function getMessages(User $user, string $uuid, int $perPage = 30)
    {
        $conversation = $this->findConversationOrFail($user, $uuid);

        return $this->chat->getMessages($conversation, $perPage);
    }

    public function sendMessage(User $user, string $uuid, array $data): Message
    {
        $conversation = $this->findConversationOrFail($user, $uuid);

        $message = $this->chat->createMessage($conversation, $user, [
            'body' => $data['body'] ?? null,
            'type' => $data['type'] ?? 'text',
            'metadata' => $data['metadata'] ?? null,
        ]);

        $conversation->touch();
        $message->load(['user.profile', 'conversation']);

        MessageSent::dispatch($message);

        return $message;
    }

    public function markRead(User $user, string $messageUuid): void
    {
        $message = Message::query()
            ->where('uuid', $messageUuid)
            ->with('conversation')
            ->firstOrFail();

        $conversation = $message->conversation;
        $this->ensureParticipant($user, $conversation);

        $read = $this->chat->markMessageRead($conversation, $message, $user);
        $this->chat->updateLastRead($conversation, $user);

        $read->load(['user', 'conversation']);
        MessageReadEvent::dispatch($read);
    }

    public function typing(User $user, string $conversationUuid, bool $isTyping = true): void
    {
        $conversation = $this->findConversationOrFail($user, $conversationUuid);
        UserTyping::dispatch($conversation, $user, $isTyping);
    }

    private function findConversationOrFail(User $user, string $uuid): Conversation
    {
        $conversation = $this->chat->findForUser($user, $uuid);

        if (! $conversation) {
            throw ValidationException::withMessages([
                'conversation' => ['Conversation not found.'],
            ]);
        }

        return $conversation;
    }

    private function ensureParticipant(User $user, Conversation $conversation): void
    {
        if (! $conversation->participants()->where('user_id', $user->id)->exists()) {
            throw ValidationException::withMessages([
                'conversation' => ['You are not a participant in this conversation.'],
            ]);
        }
    }
}
