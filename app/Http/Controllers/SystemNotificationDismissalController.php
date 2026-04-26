<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\SystemNotification;
use App\Models\SystemNotificationDismissal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Handles a user dismissing a system notification banner.
 *
 * This controller is available to all authenticated users (not just admins).
 * It is protected by the 'auth' middleware at the route level.
 *
 * When a notification uses 'both' delivery (inbox + banner), dismissing the
 * banner also marks the corresponding inbox notification as read. If you've
 * seen it enough to dismiss it, you've read it.
 */
class SystemNotificationDismissalController extends Controller
{
    public function store(Request $request, SystemNotification $notification): JsonResponse|RedirectResponse
    {
        $userId = auth()->id();

        // Record the banner dismissal. firstOrCreate() prevents a 500 if the
        // user double-clicks before the first request completes — the unique
        // DB constraint is the final safety net, but this avoids the exception.
        SystemNotificationDismissal::firstOrCreate([
            'system_notification_id' => $notification->id,
            'user_id'                => $userId,
        ]);

        // If this notification was also delivered to the inbox, mark that
        // copy as read. Dismissing the banner is a clear signal the user
        // has seen the message, so we sync the two systems.
        if ($notification->delivery_method === SystemNotification::DELIVERY_BOTH) {
            Notification::where('user_id', $userId)
                ->where('notifiable_type', SystemNotification::class)
                ->where('notifiable_id', $notification->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        if ($request->expectsJson()) {
            return response()->json(null, 204);
        }

        return back();
    }
}
