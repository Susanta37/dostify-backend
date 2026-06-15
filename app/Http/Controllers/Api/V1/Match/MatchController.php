<?php

namespace App\Http\Controllers\Api\V1\Match;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Match\RequestMatchRequest;
use App\Http\Resources\Api\V1\MatchRequestResource;
use App\Http\Resources\Api\V1\UserMatchResource;
use App\Services\Match\MatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function __construct(private MatchService $matchService) {}

    public function request(RequestMatchRequest $request): JsonResponse
    {
        $result = $this->matchService->request($request->user(), $request->validated());

        return response()->json([
            'status' => $result['status'],
            'request' => new MatchRequestResource($result['request']),
            'match' => $result['match'] ? new UserMatchResource($result['match']) : null,
        ], 201);
    }

    public function cancel(Request $request): JsonResponse
    {
        $this->matchService->cancel($request->user());

        return response()->json(['message' => 'Match request cancelled.']);
    }

    public function status(Request $request): JsonResponse
    {
        $result = $this->matchService->status($request->user());

        return response()->json([
            'request' => $result['request'] ? new MatchRequestResource($result['request']) : null,
            'match' => $result['match'] ? new UserMatchResource($result['match']) : null,
        ]);
    }

    public function accept(Request $request): JsonResponse
    {
        $request->validate(['match_uuid' => ['required', 'uuid', 'exists:matches,uuid']]);

        $match = $this->matchService->accept($request->user(), $request->input('match_uuid'));

        return response()->json([
            'message' => 'Match accepted.',
            'match' => new UserMatchResource($match),
        ]);
    }

    public function reject(Request $request): JsonResponse
    {
        $request->validate(['match_uuid' => ['required', 'uuid', 'exists:matches,uuid']]);

        $match = $this->matchService->reject($request->user(), $request->input('match_uuid'));

        return response()->json([
            'message' => 'Match rejected.',
            'match' => new UserMatchResource($match),
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        return response()->json([
            'matches' => UserMatchResource::collection(
                $this->matchService->history($request->user())
            ),
        ]);
    }
}
