<?php

namespace App\Services\Call;

use App\Enums\CallStatus;
use App\Models\CallSession;
use App\Models\User;
use App\Models\VideoSession;
use App\Repositories\Call\CallRepository;
use App\Services\Agora\AgoraService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CallService
{
    public function __construct(
        private CallRepository $calls,
        private AgoraService $agora,
    ) {}

    public function initiateAudio(User $caller, User $callee): CallSession
    {
        if ($caller->id === $callee->id) {
            throw ValidationException::withMessages(['callee' => ['Cannot call yourself.']]);
        }

        if ($this->calls->findActiveAudioCall($caller) || $this->calls->findActiveAudioCall($callee)) {
            throw ValidationException::withMessages(['call' => ['User is already on a call.']]);
        }

        return DB::transaction(function () use ($caller, $callee) {
            $session = $this->calls->createAudioSession([
                'caller_id' => $caller->id,
                'callee_id' => $callee->id,
                'status' => CallStatus::Ringing,
                'agora_channel' => $this->agora->generateChannelName('audio', (string) \Illuminate\Support\Str::uuid()),
            ]);

            $tokenData = $this->agora->buildToken(
                $session->agora_channel,
                $this->agora->generateUid($caller),
            );

            $session->update(['agora_token' => $tokenData['token']]);
            $this->calls->logAudioEvent($session, $caller, 'initiated');

            return $session->fresh(['caller.profile', 'callee.profile']);
        });
    }

    public function initiateVideo(User $caller, User $callee): VideoSession
    {
        if ($caller->id === $callee->id) {
            throw ValidationException::withMessages(['callee' => ['Cannot call yourself.']]);
        }

        if ($this->calls->findActiveVideoCall($caller) || $this->calls->findActiveVideoCall($callee)) {
            throw ValidationException::withMessages(['call' => ['User is already on a call.']]);
        }

        return DB::transaction(function () use ($caller, $callee) {
            $session = $this->calls->createVideoSession([
                'caller_id' => $caller->id,
                'callee_id' => $callee->id,
                'status' => CallStatus::Ringing,
                'agora_channel' => $this->agora->generateChannelName('video', (string) \Illuminate\Support\Str::uuid()),
            ]);

            $tokenData = $this->agora->buildToken(
                $session->agora_channel,
                $this->agora->generateUid($caller),
            );

            $session->update(['agora_token' => $tokenData['token']]);
            $this->calls->logVideoEvent($session, $caller, 'initiated');

            return $session->fresh(['caller.profile', 'callee.profile']);
        });
    }

    public function acceptAudio(User $user, string $sessionUuid): CallSession
    {
        $session = $this->findAudioOrFail($user, $sessionUuid);

        if ($session->callee_id !== $user->id || $session->status !== CallStatus::Ringing) {
            throw ValidationException::withMessages(['call' => ['Call cannot be accepted.']]);
        }

        $session->update([
            'status' => CallStatus::Accepted,
            'started_at' => now(),
        ]);

        $this->calls->logAudioEvent($session, $user, 'accepted');

        return $session->fresh(['caller.profile', 'callee.profile']);
    }

    public function acceptVideo(User $user, string $sessionUuid): VideoSession
    {
        $session = $this->findVideoOrFail($user, $sessionUuid);

        if ($session->callee_id !== $user->id || $session->status !== CallStatus::Ringing) {
            throw ValidationException::withMessages(['call' => ['Call cannot be accepted.']]);
        }

        $session->update([
            'status' => CallStatus::Accepted,
            'started_at' => now(),
        ]);

        $this->calls->logVideoEvent($session, $user, 'accepted');

        return $session->fresh(['caller.profile', 'callee.profile']);
    }

    public function rejectAudio(User $user, string $sessionUuid): CallSession
    {
        $session = $this->findAudioOrFail($user, $sessionUuid);

        if ($session->callee_id !== $user->id || ! in_array($session->status, [CallStatus::Ringing, CallStatus::Initiated], true)) {
            throw ValidationException::withMessages(['call' => ['Call cannot be rejected.']]);
        }

        $session->update(['status' => CallStatus::Rejected, 'ended_at' => now()]);
        $this->calls->logAudioEvent($session, $user, 'rejected');

        return $session->fresh(['caller.profile', 'callee.profile']);
    }

    public function rejectVideo(User $user, string $sessionUuid): VideoSession
    {
        $session = $this->findVideoOrFail($user, $sessionUuid);

        if ($session->callee_id !== $user->id || ! in_array($session->status, [CallStatus::Ringing, CallStatus::Initiated], true)) {
            throw ValidationException::withMessages(['call' => ['Call cannot be rejected.']]);
        }

        $session->update(['status' => CallStatus::Rejected, 'ended_at' => now()]);
        $this->calls->logVideoEvent($session, $user, 'rejected');

        return $session->fresh(['caller.profile', 'callee.profile']);
    }

    public function endAudio(User $user, string $sessionUuid): CallSession
    {
        $session = $this->findAudioOrFail($user, $sessionUuid);

        if (! in_array($session->status, [CallStatus::Accepted, CallStatus::Active, CallStatus::Ringing], true)) {
            throw ValidationException::withMessages(['call' => ['Call cannot be ended.']]);
        }

        $duration = $session->started_at ? now()->diffInSeconds($session->started_at) : 0;

        $session->update([
            'status' => CallStatus::Ended,
            'ended_at' => now(),
            'duration_seconds' => $duration,
        ]);

        $this->calls->logAudioEvent($session, $user, 'ended', ['duration_seconds' => $duration]);

        return $session->fresh(['caller.profile', 'callee.profile']);
    }

    public function endVideo(User $user, string $sessionUuid): VideoSession
    {
        $session = $this->findVideoOrFail($user, $sessionUuid);

        if (! in_array($session->status, [CallStatus::Accepted, CallStatus::Active, CallStatus::Ringing], true)) {
            throw ValidationException::withMessages(['call' => ['Call cannot be ended.']]);
        }

        $duration = $session->started_at ? now()->diffInSeconds($session->started_at) : 0;

        $session->update([
            'status' => CallStatus::Ended,
            'ended_at' => now(),
            'duration_seconds' => $duration,
        ]);

        $this->calls->logVideoEvent($session, $user, 'ended', ['duration_seconds' => $duration]);

        return $session->fresh(['caller.profile', 'callee.profile']);
    }

    public function audioHistory(User $user)
    {
        return $this->calls->getAudioHistory($user);
    }

    public function videoHistory(User $user)
    {
        return $this->calls->getVideoHistory($user);
    }

    public function agoraToken(User $user, string $channelName, ?int $uid = null): array
    {
        return $this->agora->buildToken(
            $channelName,
            $uid ?? $this->agora->generateUid($user),
        );
    }

    private function findAudioOrFail(User $user, string $uuid): CallSession
    {
        $session = $this->calls->findAudioSession($user, $uuid);

        if (! $session) {
            throw ValidationException::withMessages(['call' => ['Call session not found.']]);
        }

        return $session;
    }

    private function findVideoOrFail(User $user, string $uuid): VideoSession
    {
        $session = $this->calls->findVideoSession($user, $uuid);

        if (! $session) {
            throw ValidationException::withMessages(['call' => ['Call session not found.']]);
        }

        return $session;
    }
}
