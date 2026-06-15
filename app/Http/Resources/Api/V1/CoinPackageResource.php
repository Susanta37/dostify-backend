<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoinPackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'coins' => $this->coins,
            'price' => $this->price,
            'currency' => $this->currency,
            'discount_percent' => $this->discount_percent,
            'is_promotional' => $this->is_promotional,
        ];
    }
}
