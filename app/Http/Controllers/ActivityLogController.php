<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a paginated, filterable list of admin activity log entries.
     *
     * Supports four independent filters that can be combined freely:
     *   - filter:       match a specific event type (e.g. 'user.suspended')
     *   - admin_search: match the name of the admin who performed the action
     *   - date_from:    show only entries on or after this date (YYYY-MM-DD)
     *   - date_to:      show only entries on or before this date (YYYY-MM-DD)
     *
     * All filters are GET parameters so filtered URLs are bookmarkable and
     * the browser back button works correctly.
     *
     * We eager-load the admin and targetUser relationships with ->with() to
     * avoid the N+1 query problem. Without eager loading, displaying 25 rows
     * would fire 1 query for the logs + up to 50 extra queries (one per
     * relationship per row) to fetch the associated users. With eager loading,
     * it's always 3 queries total regardless of page size.
     */
    public function index(Request $request)
    {
        $filter      = $request->input('filter', 'all');
        $adminSearch = $request->string('admin_search')->trim()->toString();
        $dateFrom    = $request->input('date_from');
        $dateTo      = $request->input('date_to');

        $query = ActivityLog::with(['admin', 'targetUser'])
                            ->latest('created_at');

        // Filter by event type
        if ($filter !== 'all') {
            $query->where('event', $filter);
        }

        // Filter by admin name.
        // whereHas() adds an EXISTS subquery: "only include logs where
        // a related admin user exists whose name matches the search term."
        // This correctly handles NULLs — logs with no admin (deleted user)
        // are excluded when an admin_search is active, which is the right
        // behaviour (a deleted user can't match a name search).
        if ($adminSearch !== '') {
            $query->whereHas('admin', function ($q) use ($adminSearch) {
                $q->where('name', 'like', "%{$adminSearch}%");
            });
        }

        // Filter by date range.
        // whereDate() compares only the date portion of the timestamp,
        // ignoring the time component. This means date_from = "2026-03-22"
        // includes all records from that entire day, not just midnight.
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $logs = $query->paginate(25)->withQueryString();

        $eventTypes = [
            ActivityLog::EVENT_USER_UPDATED          => 'User Edited',
            ActivityLog::EVENT_USER_SUSPENDED        => 'Suspended',
            ActivityLog::EVENT_USER_UNSUSPENDED      => 'Restored',
            ActivityLog::EVENT_IMPERSONATION_STARTED => 'Impersonation Started',
            ActivityLog::EVENT_IMPERSONATION_ENDED   => 'Impersonation Ended',
        ];

        return view('admin.activity-log.index', [
            'logs'        => $logs,
            'filter'      => $filter,
            'eventTypes'  => $eventTypes,
            'adminSearch' => $adminSearch,
            'dateFrom'    => $dateFrom ?? '',
            'dateTo'      => $dateTo ?? '',
        ]);
    }
}
