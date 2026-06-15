<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'phone' => $this->phone,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role?->value,
            'is_profile_complete' => $this->is_profile_complete,
            'is_active' => $this->is_active,
            'referral_code' => $this->referral_code,
            'last_seen_at' => $this->last_seen_at?->toIso8601String(),
            'profile' => new ProfileResource($this->whenLoaded('profile')),
            'wallet' => new WalletResource($this->whenLoaded('wallet')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
