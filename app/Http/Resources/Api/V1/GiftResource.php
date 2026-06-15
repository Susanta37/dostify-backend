<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GiftResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'animation_url' => $this->animation_url,
            'icon_url' => $this->icon_url,
            'coin_cost' => $this->coin_cost,
            'reward_value' => $this->reward_value,
        ];
    }
}
