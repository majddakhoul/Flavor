<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\NotificationResource;
use App\Services\Support\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private readonly NotificationService $notifications)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->notifications->paginate($request->user());

        return $this->paginated($paginator, NotificationResource::class, null);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return $this->ok(['unread' => $this->notifications->unreadCount($request->user())]);
    }

    public function read(Request $request, string $notification): JsonResponse
    {
        $this->notifications->markAsRead($request->user(), $notification);

        return $this->noContent();
    }

    public function readAll(Request $request): JsonResponse
    {
        $this->notifications->markAllAsRead($request->user());

        return $this->noContent();
    }

    public function destroy(Request $request, string $notification): JsonResponse
    {
        $this->notifications->delete($request->user(), $notification);

        return $this->noContent(__('flash.notifications.deleted'));
    }
}
