<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController,
    NpcController,
    CampaignController,
    QuestController,
    ItemController,
    SpellController,
    CreatureController,
    CampaignInviteController,
    NotificationController,
    SuperAdminController,
    AdminUserController,
    ActivityLogController,
    AdminSystemNotificationController,
    SystemNotificationDismissalController,
};

// Public routes
Route::view('/', 'welcome');
Route::view('/dashboard', 'dashboard')->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated user routes
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Redirect legacy character route
    Route::redirect('/characters', '/compendium/npcs');

    // NPC Compendium
    Route::resource('compendium/npcs', NpcController::class)->names('compendium.npcs');

    // Custom Items (individual CRUD routes handled by ItemController)
    Route::get('/custom-items', [ItemController::class, 'customIndex'])->name('customItems.index');
    Route::get('/custom-items/create', [ItemController::class, 'create'])->name('items.custom.create');
    Route::post('/custom-items', [ItemController::class, 'store'])->name('items.store');
    Route::get('/custom-items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::patch('/custom-items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/custom-items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
    Route::get('/custom-items/{item}', [ItemController::class, 'show'])->name('items.custom.show');
});

// Campaigns and Quests
Route::resource('campaigns', CampaignController::class);
Route::resource('campaigns.quests', QuestController::class);

// Item Index (read-only public list + show)
Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');

// SRD and Custom Item Index Views (filtered) — public SRD view, authenticated custom view handled above
Route::get('/srd-items', [ItemController::class, 'srdIndex'])->name('srdItems.index');

// Spells — public SRD lookup (custom spell CRUD added in a later phase)
Route::get('/spells', [SpellController::class, 'index'])->name('spells.index');
Route::get('/spells/{spell}', [SpellController::class, 'show'])->name('spells.show');

// Monsters — public SRD bestiary (/monsters is the user-facing URL; model is Creature)
Route::get('/monsters', [CreatureController::class, 'index'])->name('creatures.index');
Route::get('/monsters/{creature}', [CreatureController::class, 'show'])->name('creatures.show');

// NPC–Quest relationships
Route::post('campaigns/{campaign}/quests/{quest}/npcs', [QuestController::class, 'attachNpc'])
    ->name('campaigns.quests.npcs.attach');
Route::delete('campaigns/{campaign}/quests/{quest}/npcs/{npc}', [QuestController::class, 'detachNpc'])
    ->name('campaigns.quests.npcs.detach');

// Campaign member management
Route::post('campaigns/{campaign}/members', [CampaignController::class, 'addMember'])
    ->name('campaigns.members.add');
Route::delete('campaigns/{campaign}/members', [CampaignController::class, 'removeMember'])
    ->name('campaigns.members.remove');

// Notifications system (campaign invites / user notifications)
Route::get('/notifications', [NotificationController::class, 'index'])
    ->middleware(['auth'])
    ->name('notifications.index');

Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])
    ->name('notifications.markAllRead');

// System notification dismissal — any authenticated user can dismiss a banner.
// Supports both AJAX (fetch from Alpine) and plain form POST fallback.
Route::post('/system-notifications/{notification}/dismiss', [SystemNotificationDismissalController::class, 'store'])
    ->middleware(['auth'])
    ->name('system-notifications.dismiss');

// Campaign invites
Route::post('campaigns/{campaign}/invites', [CampaignInviteController::class, 'store'])
    ->name('campaigns.invites.store');

Route::post('invites/{invite}/accept', [CampaignInviteController::class, 'accept'])
    ->name('invites.accept');

Route::post('invites/{invite}/decline', [CampaignInviteController::class, 'decline'])
    ->name('invites.decline');

// Super Admin routes
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [SuperAdminController::class, 'index'])->name('dashboard');

        // User management
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');

        Route::post('/users/{user}/sign-in-as', [AdminUserController::class, 'signInAs'])
            ->name('users.signInAs');

        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])
            ->name('users.edit');

        Route::put('/users/{user}', [AdminUserController::class, 'update'])
            ->name('users.update');

        // Suspension actions. We use POST (not PUT/PATCH) because these are
        // one-click state transitions rather than full resource updates.
        Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspend'])
            ->name('users.suspend');

        Route::post('/users/{user}/unsuspend', [AdminUserController::class, 'unsuspend'])
            ->name('users.unsuspend');

        // Activity log — read-only, so a single GET route is all we need.
        // Registering it as 'activity.index' matches the Route::has() guard
        // already in sidebar-links.blade.php, which will auto-unlock the link.
        Route::get('/activity-log', [ActivityLogController::class, 'index'])
            ->name('activity.index');

        // ── System Notifications ──────────────────────────────────────────────
        // Full CRUD for broadcast messages. Route names are prefixed 'admin.'
        // automatically by the group, so these become admin.notifications.*,
        // which matches the Route::has('admin.notifications.index') guard
        // already in sidebar-links.blade.php — the "Soon" badge disappears
        // as soon as this route group is registered.
        Route::get('/system-notifications', [AdminSystemNotificationController::class, 'index'])
            ->name('notifications.index');

        Route::get('/system-notifications/create', [AdminSystemNotificationController::class, 'create'])
            ->name('notifications.create');

        Route::post('/system-notifications', [AdminSystemNotificationController::class, 'store'])
            ->name('notifications.store');

        Route::get('/system-notifications/{notification}/edit', [AdminSystemNotificationController::class, 'edit'])
            ->name('notifications.edit');

        Route::patch('/system-notifications/{notification}', [AdminSystemNotificationController::class, 'update'])
            ->name('notifications.update');

        Route::delete('/system-notifications/{notification}', [AdminSystemNotificationController::class, 'destroy'])
            ->name('notifications.destroy');

        Route::post('/system-notifications/{notification}/activate', [AdminSystemNotificationController::class, 'activate'])
            ->name('notifications.activate');

        Route::post('/system-notifications/{notification}/deactivate', [AdminSystemNotificationController::class, 'deactivate'])
            ->name('notifications.deactivate');
    });

Route::post('/admin/return-to-admin', [AdminUserController::class, 'returnToAdmin'])
            ->name('admin.returnToAdmin');

// Auth scaffolding
require __DIR__.'/auth.php';
