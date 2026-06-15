<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserMatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $partner = $this->user_one_id === $request->user()?->id
            ? $this->userTwo
            : $this->userOne;

        return [
            'uuid' => $this->uuid,
            'status' => $this->status?->value,
            'partner' => new UserResource($partner?->load('profile')),
            'ended_at' => $this->ended_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
