<?php

namespace App\Http\Controllers;

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
 * The store() method supports both AJAX (fetch from the Alpine banner) and
 * regular form POST (graceful fallback). When the request expects JSON it
 * returns a 204 No Content; otherwise it redirects back.
 *
 * We use firstOrCreate() rather than create() to handle the race condition
 * where a user double-clicks the dismiss button before the first request
 * completes. The unique constraint in the database is the final safety net,
 * but firstOrCreate() prevents the 500 error that would otherwise result.
 */
class SystemNotificationDismissalController extends Controller
{
    public function store(Request $request, SystemNotification $notification): JsonResponse|RedirectResponse
    {
        // Only dismiss visible notifications — if a notification has been
        // deactivated or expired, silently succeed rather than 404ing the user.
        SystemNotificationDismissal::firstOrCreate([
            'system_notification_id' => $notification->id,
            'user_id'                => auth()->id(),
        ]);

        // AJAX path: the Alpine banner uses fetch(), which sends an Accept:
        // application/json header. Return 204 so Alpine knows it succeeded
        // without parsing a response body.
        if ($request->expectsJson()) {
            return response()->json(null, 204);
        }

        // Non-JS fallback: redirect back to wherever the user was.
        return back();
    }
}
