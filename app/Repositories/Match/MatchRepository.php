<?php

namespace App\Repositories\Match;

use App\Enums\MatchStatus;
use App\Models\MatchRequest;
use App\Models\User;
use App\Models\UserMatch;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class MatchRepository extends BaseRepository
{
    public function __construct(MatchRequest $model)
    {
        parent::__construct($model);
    }

    public function findPendingForUser(User $user): ?MatchRequest
    {
        return MatchRequest::query()
            ->where('user_id', $user->id)
            ->where('status', MatchStatus::Pending)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    public function findCompatibleRequest(User $user, ?string $language, ?string $gender): ?MatchRequest
    {
        return MatchRequest::query()
            ->where('user_id', '!=', $user->id)
            ->where('status', MatchStatus::Pending)
            ->where('expires_at', '>', now())
            ->when($language, fn ($q) => $q->where(function ($q) use ($language) {
                $q->whereNull('preferred_language')->orWhere('preferred_language', $language);
            }))
            ->when($gender, fn ($q) => $q->where(function ($q) use ($gender) {
                $q->whereNull('preferred_gender')->orWhere('preferred_gender', $gender);
            }))
            ->oldest()
            ->first();
    }

    public function createRequest(User $user, array $data): MatchRequest
    {
        return MatchRequest::query()->create($data);
    }

    public function createMatch(array $data): UserMatch
    {
        return UserMatch::query()->create($data);
    }

    public function findActiveMatchForUser(User $user): ?UserMatch
    {
        return UserMatch::query()
            ->where(function ($q) use ($user) {
                $q->where('user_one_id', $user->id)->orWhere('user_two_id', $user->id);
            })
            ->whereIn('status', [MatchStatus::Matched, MatchStatus::Accepted])
            ->latest()
            ->first();
    }

    public function findMatchForUser(User $user, string $uuid): ?UserMatch
    {
        return UserMatch::query()
            ->where('uuid', $uuid)
            ->where(function ($q) use ($user) {
                $q->where('user_one_id', $user->id)->orWhere('user_two_id', $user->id);
            })
            ->first();
    }

    public function getHistoryForUser(User $user, int $perPage = 20): Collection
    {
        return UserMatch::query()
            ->with(['userOne.profile', 'userTwo.profile'])
            ->where(function ($q) use ($user) {
                $q->where('user_one_id', $user->id)->orWhere('user_two_id', $user->id);
            })
            ->whereIn('status', [MatchStatus::Accepted, MatchStatus::Rejected, MatchStatus::Expired])
            ->latest()
            ->limit($perPage)
            ->get();
    }

    public function cancelPendingRequests(User $user): void
    {
        MatchRequest::query()
            ->where('user_id', $user->id)
            ->where('status', MatchStatus::Pending)
            ->update(['status' => MatchStatus::Cancelled]);
    }
}
