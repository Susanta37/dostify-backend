<?php

namespace App\Http\Controllers\Api\V1\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Chat\SendMessageRequest;
use App\Http\Resources\Api\V1\MessageResource;
use App\Services\Chat\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(private ChatService $chat) {}

    public function index(Request $request, string $id): JsonResponse
    {
        $messages = $this->chat->getMessages($request->user(), $id);

        return response()->json([
            'messages' => MessageResource::collection($messages),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'total' => $messages->total(),
            ],
        ]);
    }

    public function store(SendMessageRequest $request, string $id): JsonResponse
    {
        $message = $this->chat->sendMessage($request->user(), $id, $request->validated());

        return response()->json([
            'message' => new MessageResource($message),
        ], 201);
    }

    public function markRead(Request $request, string $id): JsonResponse
    {
        $this->chat->markRead($request->user(), $id);

        return response()->json(['message' => 'Message marked as read.']);
    }

    public function typing(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'conversation_uuid' => ['required', 'uuid', 'exists:conversations,uuid'],
            'is_typing' => ['nullable', 'boolean'],
        ]);

        $this->chat->typing(
            $request->user(),
            $validated['conversation_uuid'],
            $validated['is_typing'] ?? true,
        );

        return response()->json(['message' => 'Typing status sent.']);
    }
}
