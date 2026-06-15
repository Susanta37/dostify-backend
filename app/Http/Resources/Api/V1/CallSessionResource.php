<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CallSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'status' => $this->status?->value,
            'agora_channel' => $this->agora_channel,
            'caller' => new UserResource($this->whenLoaded('caller')),
            'callee' => new UserResource($this->whenLoaded('callee')),
            'started_at' => $this->started_at?->toIso8601String(),
            'ended_at' => $this->ended_at?->toIso8601String(),
            'duration_seconds' => $this->duration_seconds,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
