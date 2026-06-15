<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\ProfileRepository;

class ProfileService
{
    public function __construct(private ProfileRepository $profiles) {}

    public function getProfile(User $user)
    {
        return $this->profiles->findByUser($user)?->load('user');
    }

    public function updateOrCreate(User $user, array $data)
    {
        $profile = $this->profiles->updateOrCreateForUser($user, $data);

        $isComplete = filled($profile->nickname)
            && filled($profile->gender)
            && filled($profile->language);

        if ($isComplete && ! $user->is_profile_complete) {
            $user->update(['is_profile_complete' => true]);
        }

        return $profile->fresh();
    }
}
