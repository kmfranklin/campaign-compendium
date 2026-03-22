<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a paginated, filterable list of admin activity log entries.
     *
     * This is intentionally a read-only controller — there are no store,
     * update, or destroy methods. Log entries are created exclusively by
     * AdminUserController and should never be modified or deleted through
     * the UI.
     *
     * We eager-load the admin and targetUser relationships with ->with() to
     * avoid the N+1 query problem. Without eager loading, displaying 20 rows
     * would fire 1 query for the logs + up to 40 extra queries (one per
     * relationship per row) to fetch the associated users. With eager loading,
     * it's always 3 queries total regardless of page size.
     */
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'all');

        $query = ActivityLog::with(['admin', 'targetUser'])
                            ->latest('created_at');

        if ($filter !== 'all') {
            $query->where('event', $filter);
        }

        $logs = $query->paginate(25)->withQueryString();

        // Pass the full list of event types so the view can render filter
        // options dynamically without hardcoding them in the template.
        $eventTypes = [
            ActivityLog::EVENT_USER_UPDATED          => 'User Edited',
            ActivityLog::EVENT_USER_SUSPENDED        => 'Suspended',
            ActivityLog::EVENT_USER_UNSUSPENDED      => 'Restored',
            ActivityLog::EVENT_IMPERSONATION_STARTED => 'Impersonation Started',
            ActivityLog::EVENT_IMPERSONATION_ENDED   => 'Impersonation Ended',
        ];

        return view('admin.activity-log.index', [
            'logs'       => $logs,
            'filter'     => $filter,
            'eventTypes' => $eventTypes,
        ]);
    }
}
