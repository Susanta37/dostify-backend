<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Profile\UpdateProfileRequest;
use App\Http\Resources\Api\V1\ProfileResource;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $profiles) {}

    public function show(Request $request): JsonResponse
    {
        $profile = $this->profiles->getProfile($request->user());

        return response()->json([
            'profile' => $profile ? new ProfileResource($profile) : null,
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $profile = $this->profiles->updateOrCreate(
            $request->user(),
            $request->validated(),
        );

        return response()->json([
            'message' => 'Profile updated successfully.',
            'profile' => new ProfileResource($profile),
        ]);
    }
}
