<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'unread');

        // System notifications display their full content in the list row —
        // there is nothing to "open." Visiting the inbox is equivalent to
        // reading them, so we mark them as read immediately on page load.
        // Campaign invite notifications are left alone; they stay unread
        // until the user accepts or declines (the action is the acknowledgement).
        auth()->user()->notifications()
            ->where('type', Notification::TYPE_SYSTEM)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $query = auth()->user()->notifications()->latest();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->get();

        return view('notifications.index', [
            'notifications' => $notifications,
            'active'        => $filter,
        ]);
    }

    public function markAllRead()
    {
        auth()->user()->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back();
    }
}
