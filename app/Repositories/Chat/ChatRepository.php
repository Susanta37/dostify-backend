<?php

namespace App\Repositories\Chat;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ChatRepository
{
    public function getConversationsForUser(User $user): Collection
    {
        return Conversation::query()
            ->whereHas('participants', fn ($q) => $q->where('user_id', $user->id))
            ->with(['participants.user.profile', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->latest('updated_at')
            ->get();
    }

    public function findForUser(User $user, string $uuid): ?Conversation
    {
        return Conversation::query()
            ->where('uuid', $uuid)
            ->whereHas('participants', fn ($q) => $q->where('user_id', $user->id))
            ->with(['participants.user.profile'])
            ->first();
    }

    public function findPrivateBetween(User $userA, User $userB): ?Conversation
    {
        return Conversation::query()
            ->where('type', 'private')
            ->whereHas('participants', fn ($q) => $q->where('user_id', $userA->id))
            ->whereHas('participants', fn ($q) => $q->where('user_id', $userB->id))
            ->first();
    }

    public function createConversation(array $data, array $participantIds): Conversation
    {
        $conversation = Conversation::query()->create($data);

        foreach ($participantIds as $participantId) {
            ConversationParticipant::query()->create([
                'conversation_id' => $conversation->id,
                'user_id' => $participantId,
            ]);
        }

        return $conversation->load(['participants.user.profile']);
    }

    public function removeParticipant(Conversation $conversation, User $user): void
    {
        $conversation->participants()->where('user_id', $user->id)->delete();
    }

    public function getMessages(Conversation $conversation, int $perPage = 30): LengthAwarePaginator
    {
        return $conversation->messages()
            ->with('user.profile')
            ->latest()
            ->paginate($perPage);
    }

    public function createMessage(Conversation $conversation, User $user, array $data): Message
    {
        return $conversation->messages()->create(array_merge($data, [
            'user_id' => $user->id,
        ]));
    }

    public function markMessageRead(Conversation $conversation, Message $message, User $user): MessageRead
    {
        return MessageRead::query()->firstOrCreate(
            ['message_id' => $message->id, 'user_id' => $user->id],
            [
                'conversation_id' => $conversation->id,
                'read_at' => now(),
            ]
        );
    }

    public function updateLastRead(Conversation $conversation, User $user): void
    {
        $conversation->participants()
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);
    }
}
