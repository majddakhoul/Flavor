<?php

namespace App\Http\Controllers\Web;

use App\Services\Support\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(private readonly NotificationService $notifications)
    {
    }

    public function index(Request $request): View
    {
        return view('notifications.index', [
            'notifications' => $this->notifications->paginate($request->user()),
        ]);
    }

    public function read(Request $request, string $notification): RedirectResponse
    {
        $this->notifications->markAsRead($request->user(), $notification);

        $target = $this->notifications->find($request->user(), $notification)?->data['url'] ?? null;

        return $target !== null ? redirect()->to($target) : back();
    }

    public function readAll(Request $request): RedirectResponse
    {
        $this->notifications->markAllAsRead($request->user());

        return back();
    }

    public function destroy(Request $request, string $notification): RedirectResponse
    {
        $this->notifications->delete($request->user(), $notification);

        return $this->done('notifications.index', __('flash.notifications.deleted'));
    }
}
