<?php

namespace App\Repositories;

use App\Models\Profile;
use App\Models\User;
use App\Repositories\Contracts\BaseRepository;

class ProfileRepository extends BaseRepository
{
    public function __construct(Profile $model)
    {
        parent::__construct($model);
    }

    public function findByUser(User $user): ?Profile
    {
        return Profile::query()->where('user_id', $user->id)->first();
    }

    public function updateOrCreateForUser(User $user, array $data): Profile
    {
        return Profile::query()->updateOrCreate(
            ['user_id' => $user->id],
            $data
        );
    }
}
