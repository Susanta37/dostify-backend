<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Gift\SendGiftRequest;
use App\Http\Resources\Api\V1\GiftResource;
use App\Models\Gift;
use App\Models\Room;
use App\Models\User;
use App\Services\GiftService;
use Illuminate\Http\JsonResponse;

class GiftController extends Controller
{
    public function __construct(private GiftService $gifts) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'gifts' => GiftResource::collection($this->gifts->catalog()),
        ]);
    }

    public function send(SendGiftRequest $request): JsonResponse
    {
        $gift = Gift::query()->where('uuid', $request->validated('gift_uuid'))->firstOrFail();
        $receiver = User::query()->where('uuid', $request->validated('receiver_uuid'))->firstOrFail();

        $roomId = null;
        if ($request->filled('room_uuid')) {
            $roomId = Room::query()->where('uuid', $request->validated('room_uuid'))->value('id');
        }

        $transaction = $this->gifts->send(
            sender: $request->user(),
            receiver: $receiver,
            gift: $gift,
            roomId: $roomId,
            contextType: $request->validated('context_type'),
            contextId: $request->validated('context_id'),
        );

        return response()->json([
            'message' => 'Gift sent successfully.',
            'transaction_uuid' => $transaction->uuid,
        ], 201);
    }
}
