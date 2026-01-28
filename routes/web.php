<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController,
    NpcController,
    CampaignController,
    QuestController,
    ItemController,
    CampaignInviteController,
    NotificationController
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

// Notifications system
Route::get('/notifications', [NotificationController::class, 'index'])
    ->middleware(['auth'])
    ->name('notifications.index');

Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])
    ->name('notifications.markAllRead');

// Campaign invites
Route::post('campaigns/{campaign}/invites', [CampaignInviteController::class, 'store'])
    ->name('campaigns.invites.store');

Route::post('invites/{invite}/accept', [CampaignInviteController::class, 'accept'])
    ->name('invites.accept');

Route::post('invites/{invite}/decline', [CampaignInviteController::class, 'decline'])
    ->name('invites.decline');

// Auth scaffolding
require __DIR__.'/auth.php';
