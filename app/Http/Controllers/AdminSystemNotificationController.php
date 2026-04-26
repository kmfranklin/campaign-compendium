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
 * INBOX FAN-OUT BEHAVIOUR
 * ────────────────────────
 * When a notification's delivery_method includes 'inbox' (i.e. 'inbox' or
 * 'both'), activating it triggers fanOutToUsers(), which bulk-inserts one
 * Notification row per user into the notifications table. fanOutToUsers()
 * is idempotent — it stamps sent_at after completion and exits early on
 * subsequent calls, so activating an already-sent notification is safe.
 *
 * Fan-out is triggered in two places:
 *   store()    — if the notification is created with is_active = true
 *   activate() — when an admin flips an inactive notification to active
 *
 * Editing a notification after fan-out does NOT re-send to inboxes; the
 * inbox copy is a point-in-time snapshot. Banner content, however, is
 * read live from the database on every page load, so edits take effect
 * immediately for banners.
 */
class AdminSystemNotificationController extends Controller
{
    /**
     * List all notifications, newest first.
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
        $deliveries   = SystemNotification::deliveries();
        $templates    = SystemNotification::templates();
        $notification = new SystemNotification();

        return view('admin.system-notifications.create', compact('types', 'deliveries', 'templates', 'notification'));
    }

    /**
     * Validate and persist a new notification.
     * If is_active is true and delivery includes inbox, fans out immediately.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'message'         => ['required', 'string', 'max:2000'],
            'type'            => ['required', 'in:' . implode(',', array_keys(SystemNotification::types()))],
            'delivery_method' => ['required', 'in:' . implode(',', array_keys(SystemNotification::deliveries()))],
            'is_active'       => ['boolean'],
            'expires_at'      => ['nullable', 'date', 'after:now'],
            'show_at'         => ['nullable', 'date'],
        ]);

        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['created_by'] = auth()->id();

        $notification = SystemNotification::create($validated);

        // Fan out to inboxes immediately if the notification is active and
        // delivery includes the inbox channel.
        if ($notification->is_active && in_array($notification->delivery_method, ['inbox', 'both'])) {
            $notification->fanOutToUsers();
        }

        ActivityLog::record(
            ActivityLog::EVENT_NOTIFICATION_CREATED,
            auth()->user()->name . ' created system notification "' . $notification->title . '"',
            null,
            [
                'notification_id' => $notification->id,
                'type'            => $notification->type,
                'delivery_method' => $notification->delivery_method,
            ]
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
        $types      = SystemNotification::types();
        $deliveries = SystemNotification::deliveries();
        $templates  = SystemNotification::templates();

        return view('admin.system-notifications.edit', compact('notification', 'types', 'deliveries', 'templates'));
    }

    /**
     * Validate and update an existing notification.
     *
     * Editing does NOT re-fan-out to inboxes (inbox copies are point-in-time
     * snapshots). If the delivery_method changes to include inbox and the
     * notification is active but was never sent, we fan out now.
     */
    public function update(Request $request, SystemNotification $notification): RedirectResponse
    {
        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'message'         => ['required', 'string', 'max:2000'],
            'type'            => ['required', 'in:' . implode(',', array_keys(SystemNotification::types()))],
            'delivery_method' => ['required', 'in:' . implode(',', array_keys(SystemNotification::deliveries()))],
            'is_active'       => ['boolean'],
            'expires_at'      => ['nullable', 'date'],
            'show_at'         => ['nullable', 'date'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $changedFields = array_keys($notification->fill($validated)->getDirty());
        $notification->save();

        // If the notification is active, delivery now includes inbox, and
        // the inbox fan-out hasn't happened yet, send it now.
        if (
            $notification->is_active
            && in_array($notification->delivery_method, ['inbox', 'both'])
            && $notification->sent_at === null
        ) {
            $notification->fanOutToUsers();
        }

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
     * Set is_active = true and fan out to inboxes if applicable.
     */
    public function activate(SystemNotification $notification): RedirectResponse
    {
        $notification->update(['is_active' => true]);

        // Fan out to inboxes on first activation. fanOutToUsers() is
        // idempotent — it checks sent_at internally and exits early if
        // the notification was already sent.
        if (in_array($notification->delivery_method, ['inbox', 'both'])) {
            $notification->fanOutToUsers();
        }

        ActivityLog::record(
            ActivityLog::EVENT_NOTIFICATION_UPDATED,
            auth()->user()->name . ' activated notification "' . $notification->title . '"',
            null,
            ['notification_id' => $notification->id, 'changed_fields' => ['is_active']]
        );

        return back()->with('success', 'Notification activated.');
    }

    /**
     * Set is_active = false.
     * Deactivating does not retract inbox notifications already sent.
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
