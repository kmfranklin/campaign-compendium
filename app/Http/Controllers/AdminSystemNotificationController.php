<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\SystemNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Admin CRUD for System Notifications.
 *
 * All routes are protected by the 'admin' middleware (applied at the route
 * group level in web.php). Only super admins can create, edit, activate,
 * deactivate, or delete notifications.
 *
 * The notification banners themselves are rendered for all authenticated users
 * via the banner partial — this controller is only for managing them.
 */
class AdminSystemNotificationController extends Controller
{
    /**
     * List all notifications, newest first.
     * We eager-load createdBy to avoid an N+1 on the admin name column,
     * and append dismissals_count so we can show how many users dismissed each.
     */
    public function index(): View
    {
        $notifications = SystemNotification::with('createdBy')
            ->withCount('dismissals')
            ->latest()
            ->paginate(20);

        return view('admin.system-notifications.index', compact('notifications'));
    }

    /**
     * Show the create form.
     */
    public function create(): View
    {
        $types        = SystemNotification::types();
        $templates    = SystemNotification::templates();
        $notification = new SystemNotification();   // blank instance for form defaults

        return view('admin.system-notifications.create', compact('types', 'templates', 'notification'));
    }

    /**
     * Validate and persist a new notification.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'message'    => ['required', 'string', 'max:2000'],
            'type'       => ['required', 'in:' . implode(',', array_keys(SystemNotification::types()))],
            'is_active'  => ['boolean'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        // Checkboxes send '1' when ticked; absent means false.
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['created_by'] = auth()->id();

        $notification = SystemNotification::create($validated);

        ActivityLog::record(
            ActivityLog::EVENT_NOTIFICATION_CREATED,
            auth()->user()->name . ' created system notification "' . $notification->title . '"',
            null,
            ['notification_id' => $notification->id, 'type' => $notification->type]
        );

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'Notification "' . $notification->title . '" created successfully.');
    }

    /**
     * Show the edit form for an existing notification.
     */
    public function edit(SystemNotification $notification): View
    {
        $types     = SystemNotification::types();
        $templates = SystemNotification::templates();

        return view('admin.system-notifications.edit', compact('notification', 'types', 'templates'));
    }

    /**
     * Validate and update an existing notification.
     */
    public function update(Request $request, SystemNotification $notification): RedirectResponse
    {
        $validated = $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'message'    => ['required', 'string', 'max:2000'],
            'type'       => ['required', 'in:' . implode(',', array_keys(SystemNotification::types()))],
            'is_active'  => ['boolean'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        // Capture changed fields before save (Eloquent clears dirty state on save).
        $changedFields = array_keys($notification->fill($validated)->getDirty());
        $notification->save();

        if (!empty($changedFields)) {
            ActivityLog::record(
                ActivityLog::EVENT_NOTIFICATION_UPDATED,
                auth()->user()->name . ' updated notification "' . $notification->title . '"'
                    . ' (' . implode(', ', $changedFields) . ')',
                null,
                ['notification_id' => $notification->id, 'changed_fields' => $changedFields]
            );
        }

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'Notification updated successfully.');
    }

    /**
     * Permanently delete a notification and all its dismissal records.
     * Dismissal rows cascade-delete via the FK constraint in the migration.
     */
    public function destroy(SystemNotification $notification): RedirectResponse
    {
        $title = $notification->title;
        $notification->delete();

        ActivityLog::record(
            ActivityLog::EVENT_NOTIFICATION_DELETED,
            auth()->user()->name . ' deleted notification "' . $title . '"'
        );

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'Notification "' . $title . '" deleted.');
    }

    /**
     * Set is_active = true. Used by the quick-toggle button on the index page.
     */
    public function activate(SystemNotification $notification): RedirectResponse
    {
        $notification->update(['is_active' => true]);

        ActivityLog::record(
            ActivityLog::EVENT_NOTIFICATION_UPDATED,
            auth()->user()->name . ' activated notification "' . $notification->title . '"',
            null,
            ['notification_id' => $notification->id, 'changed_fields' => ['is_active']]
        );

        return back()->with('success', 'Notification activated.');
    }

    /**
     * Set is_active = false. Used by the quick-toggle button on the index page.
     */
    public function deactivate(SystemNotification $notification): RedirectResponse
    {
        $notification->update(['is_active' => false]);

        ActivityLog::record(
            ActivityLog::EVENT_NOTIFICATION_UPDATED,
            auth()->user()->name . ' deactivated notification "' . $notification->title . '"',
            null,
            ['notification_id' => $notification->id, 'changed_fields' => ['is_active']]
        );

        return back()->with('success', 'Notification deactivated.');
    }
}
