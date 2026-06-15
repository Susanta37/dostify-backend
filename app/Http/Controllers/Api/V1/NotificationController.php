<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\NotificationResource;
use App\Services\Notification\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->notifications->list($request->user());

        return response()->json([
            'notifications' => NotificationResource::collection($paginator),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function markRead(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'notification_id' => ['nullable', 'uuid'],
        ]);

        $count = $this->notifications->markRead(
            $request->user(),
            $validated['notification_id'] ?? null,
        );

        return response()->json([
            'message' => 'Notifications marked as read.',
            'count' => $count,
        ]);
    }

    public function updateFcmToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => ['required', 'string', 'max:255'],
            'fcm_token' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'device_type' => ['nullable', 'string', 'in:ios,android,web'],
        ]);

        $device = $this->notifications->updateFcmToken($request->user(), $validated);

        return response()->json([
            'message' => 'FCM token updated.',
            'device_uuid' => $device->uuid,
        ]);
    }
}
