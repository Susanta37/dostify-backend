<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'avatar' => $this->avatar,
            'nickname' => $this->nickname,
            'bio' => $this->bio,
            'gender' => $this->gender?->value,
            'language' => $this->language,
            'state' => $this->state,
            'country' => $this->country,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
        ];
    }
}
