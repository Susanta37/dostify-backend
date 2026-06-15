<?php

namespace App\Http\Controllers\Api\V1\Call;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CallSessionResource;
use App\Models\User;
use App\Services\Call\CallService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AudioCallController extends Controller
{
    public function __construct(private CallService $calls) {}

    public function initiate(Request $request): JsonResponse
    {
        $request->validate(['callee_uuid' => ['required', 'uuid', 'exists:users,uuid']]);

        $callee = User::query()->where('uuid', $request->input('callee_uuid'))->firstOrFail();
        $session = $this->calls->initiateAudio($request->user(), $callee);

        return response()->json([
            'call' => new CallSessionResource($session),
        ], 201);
    }

    public function accept(Request $request): JsonResponse
    {
        $request->validate(['call_uuid' => ['required', 'uuid', 'exists:call_sessions,uuid']]);

        $session = $this->calls->acceptAudio($request->user(), $request->input('call_uuid'));

        return response()->json(['call' => new CallSessionResource($session)]);
    }

    public function reject(Request $request): JsonResponse
    {
        $request->validate(['call_uuid' => ['required', 'uuid', 'exists:call_sessions,uuid']]);

        $session = $this->calls->rejectAudio($request->user(), $request->input('call_uuid'));

        return response()->json(['call' => new CallSessionResource($session)]);
    }

    public function end(Request $request): JsonResponse
    {
        $request->validate(['call_uuid' => ['required', 'uuid', 'exists:call_sessions,uuid']]);

        $session = $this->calls->endAudio($request->user(), $request->input('call_uuid'));

        return response()->json(['call' => new CallSessionResource($session)]);
    }

    public function history(Request $request): JsonResponse
    {
        return response()->json([
            'calls' => CallSessionResource::collection($this->calls->audioHistory($request->user())),
        ]);
    }
}
