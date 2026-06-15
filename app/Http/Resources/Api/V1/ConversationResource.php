<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $lastMessage = $this->relationLoaded('messages') ? $this->messages->first() : null;

        return [
            'uuid' => $this->uuid,
            'type' => $this->type?->value,
            'name' => $this->name,
            'participants' => ConversationParticipantResource::collection(
                $this->whenLoaded('participants')
            ),
            'last_message' => $lastMessage ? new MessageResource($lastMessage) : null,
            'updated_at' => $this->updated_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
