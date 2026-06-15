<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Call\CallService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgoraController extends Controller
{
    public function __construct(private CallService $calls) {}

    public function token(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'channel_name' => ['required', 'string', 'max:255'],
            'uid' => ['nullable', 'integer', 'min:1'],
        ]);

        $tokenData = $this->calls->agoraToken(
            $request->user(),
            $validated['channel_name'],
            $validated['uid'] ?? null,
        );

        return response()->json($tokenData);
    }
}
