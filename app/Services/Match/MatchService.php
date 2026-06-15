<?php

namespace App\Services\Match;

use App\Enums\MatchStatus;
use App\Models\User;
use App\Models\UserMatch;
use App\Repositories\Match\MatchRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MatchService
{
    public function __construct(private MatchRepository $matches) {}

    public function request(User $user, array $preferences): array
    {
        if ($this->matches->findActiveMatchForUser($user)) {
            throw ValidationException::withMessages([
                'match' => ['You already have an active match.'],
            ]);
        }

        return DB::transaction(function () use ($user, $preferences) {
            $this->matches->cancelPendingRequests($user);

            $request = $this->matches->createRequest($user, [
                'user_id' => $user->id,
                'preferred_language' => $preferences['preferred_language'] ?? null,
                'preferred_gender' => $preferences['preferred_gender'] ?? null,
                'match_type' => $preferences['match_type'] ?? 'random',
                'status' => MatchStatus::Pending,
                'expires_at' => now()->addMinutes(5),
            ]);

            $userProfile = $user->load('profile')->profile;
            $counterpart = $this->matches->findCompatibleRequest(
                $user,
                $userProfile?->language,
                $userProfile?->gender?->value,
            );

            if (! $counterpart) {
                return [
                    'status' => 'searching',
                    'request' => $request->fresh(),
                    'match' => null,
                ];
            }

            $match = $this->createPairMatch($request, $counterpart);

            return [
                'status' => 'matched',
                'request' => $request->fresh(),
                'match' => $match->load(['userOne.profile', 'userTwo.profile']),
            ];
        });
    }

    public function cancel(User $user): void
    {
        $this->matches->cancelPendingRequests($user);
    }

    public function status(User $user): array
    {
        $pendingRequest = $this->matches->findPendingForUser($user);
        $activeMatch = $this->matches->findActiveMatchForUser($user);

        return [
            'request' => $pendingRequest,
            'match' => $activeMatch?->load(['userOne.profile', 'userTwo.profile']),
        ];
    }

    public function accept(User $user, string $matchUuid): UserMatch
    {
        $match = $this->findMatchOrFail($user, $matchUuid);

        if ($match->status !== MatchStatus::Matched) {
            throw ValidationException::withMessages([
                'match' => ['This match cannot be accepted.'],
            ]);
        }

        $match->update(['status' => MatchStatus::Accepted]);

        return $match->fresh(['userOne.profile', 'userTwo.profile']);
    }

    public function reject(User $user, string $matchUuid): UserMatch
    {
        $match = $this->findMatchOrFail($user, $matchUuid);

        if (! in_array($match->status, [MatchStatus::Matched, MatchStatus::Accepted], true)) {
            throw ValidationException::withMessages([
                'match' => ['This match cannot be rejected.'],
            ]);
        }

        $match->update([
            'status' => MatchStatus::Rejected,
            'ended_at' => now(),
        ]);

        return $match->fresh(['userOne.profile', 'userTwo.profile']);
    }

    public function history(User $user)
    {
        return $this->matches->getHistoryForUser($user);
    }

    private function findMatchOrFail(User $user, string $matchUuid): UserMatch
    {
        $match = $this->matches->findMatchForUser($user, $matchUuid);

        if (! $match) {
            throw ValidationException::withMessages([
                'match' => ['Match not found.'],
            ]);
        }

        return $match;
    }

    private function createPairMatch($request, $counterpart): UserMatch
    {
        $request->update(['status' => MatchStatus::Matched]);
        $counterpart->update(['status' => MatchStatus::Matched]);

        return $this->matches->createMatch([
            'user_one_id' => $request->user_id,
            'user_two_id' => $counterpart->user_id,
            'match_request_one_id' => $request->id,
            'match_request_two_id' => $counterpart->id,
            'status' => MatchStatus::Matched,
        ]);
    }
}
