<?php

namespace App\Http\Controllers\Api\V1\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Chat\CreateConversationRequest;
use App\Http\Requests\Api\V1\Chat\SendMessageRequest;
use App\Http\Resources\Api\V1\ConversationResource;
use App\Http\Resources\Api\V1\MessageResource;
use App\Services\Chat\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function __construct(private ChatService $chat) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'conversations' => ConversationResource::collection(
                $this->chat->listConversations($request->user())
            ),
        ]);
    }

    public function store(CreateConversationRequest $request): JsonResponse
    {
        $conversation = $this->chat->createConversation($request->user(), $request->validated());

        return response()->json([
            'conversation' => new ConversationResource($conversation),
        ], 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $conversation = $this->chat->getConversation($request->user(), $id);

        return response()->json([
            'conversation' => new ConversationResource($conversation),
        ]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $this->chat->deleteConversation($request->user(), $id);

        return response()->json(['message' => 'Left conversation.']);
    }
}
