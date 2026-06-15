<?php

namespace App\Repositories\Call;

use App\Enums\CallStatus;
use App\Models\CallLog;
use App\Models\CallSession;
use App\Models\User;
use App\Models\VideoLog;
use App\Models\VideoSession;
use Illuminate\Database\Eloquent\Collection;

class CallRepository
{
    public function createAudioSession(array $data): CallSession
    {
        return CallSession::query()->create($data);
    }

    public function createVideoSession(array $data): VideoSession
    {
        return VideoSession::query()->create($data);
    }

    public function findAudioSession(User $user, string $uuid): ?CallSession
    {
        return CallSession::query()
            ->where('uuid', $uuid)
            ->where(function ($q) use ($user) {
                $q->where('caller_id', $user->id)->orWhere('callee_id', $user->id);
            })
            ->first();
    }

    public function findVideoSession(User $user, string $uuid): ?VideoSession
    {
        return VideoSession::query()
            ->where('uuid', $uuid)
            ->where(function ($q) use ($user) {
                $q->where('caller_id', $user->id)->orWhere('callee_id', $user->id);
            })
            ->first();
    }

    public function findActiveAudioCall(User $user): ?CallSession
    {
        return CallSession::query()
            ->where(function ($q) use ($user) {
                $q->where('caller_id', $user->id)->orWhere('callee_id', $user->id);
            })
            ->whereIn('status', [CallStatus::Initiated, CallStatus::Ringing, CallStatus::Accepted, CallStatus::Active])
            ->latest()
            ->first();
    }

    public function findActiveVideoCall(User $user): ?VideoSession
    {
        return VideoSession::query()
            ->where(function ($q) use ($user) {
                $q->where('caller_id', $user->id)->orWhere('callee_id', $user->id);
            })
            ->whereIn('status', [CallStatus::Initiated, CallStatus::Ringing, CallStatus::Accepted, CallStatus::Active])
            ->latest()
            ->first();
    }

    public function logAudioEvent(CallSession $session, User $user, string $event, ?array $metadata = null): CallLog
    {
        return $session->logs()->create([
            'user_id' => $user->id,
            'event' => $event,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

    public function logVideoEvent(VideoSession $session, User $user, string $event, ?array $metadata = null): VideoLog
    {
        return $session->logs()->create([
            'user_id' => $user->id,
            'event' => $event,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

    public function getAudioHistory(User $user, int $limit = 20): Collection
    {
        return CallSession::query()
            ->with(['caller.profile', 'callee.profile'])
            ->where(function ($q) use ($user) {
                $q->where('caller_id', $user->id)->orWhere('callee_id', $user->id);
            })
            ->whereIn('status', [CallStatus::Ended, CallStatus::Missed, CallStatus::Rejected])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getVideoHistory(User $user, int $limit = 20): Collection
    {
        return VideoSession::query()
            ->with(['caller.profile', 'callee.profile'])
            ->where(function ($q) use ($user) {
                $q->where('caller_id', $user->id)->orWhere('callee_id', $user->id);
            })
            ->whereIn('status', [CallStatus::Ended, CallStatus::Missed, CallStatus::Rejected])
            ->latest()
            ->limit($limit)
            ->get();
    }
}
