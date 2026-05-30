@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $campaignCount = $user->campaigns()->count();
    $npcCount = $user->npcs()->count();
    $unreadNotificationCount = $user->notifications()
        ->whereNull('read_at')
        ->count();
    $unreadInviteCount = $user->notifications()
        ->where('type', \App\Models\Notification::TYPE_INVITE)
        ->whereNull('read_at')
        ->count();
    $spellCount = \App\Models\Spell::query()
        ->where(function ($query) use ($user) {
            $query->where('is_srd', true)
                ->orWhere('user_id', $user->id);
        })
        ->count();
    $creatureCount = \App\Models\Creature::query()
        ->where(function ($query) use ($user) {
            $query->where('is_srd', true)
                ->orWhere('user_id', $user->id);
        })
        ->count();
    $itemCount = \App\Models\Item::query()
        ->where(function ($query) use ($user) {
            $query->where('is_srd', true)
                ->orWhere('user_id', $user->id);
        })
        ->count();
@endphp

<div class="py-2">
    <div class="space-y-8">
        <section class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-semibold text-text">Dashboard</h1>
                <p class="mt-1 text-sm text-muted">
                    Welcome back, {{ $user->name }}. Here’s the quickest way to jump into your next session.
                </p>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-3">
            <a href="{{ route('campaigns.index') }}"
               class="ui-card-interactive block p-5 focus:outline-none focus:ring-2 focus:ring-accent">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">Campaigns</p>
                <p class="mt-3 text-3xl font-semibold text-text">{{ $campaignCount }}</p>
                <p class="mt-2 text-sm text-muted">Campaigns you can currently access.</p>
            </a>

            <a href="{{ route('notifications.index') }}"
               class="ui-card-interactive block p-5 focus:outline-none focus:ring-2 focus:ring-accent">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">Notifications</p>
                <p class="mt-3 text-3xl font-semibold text-text">{{ $unreadNotificationCount }}</p>
                <p class="mt-2 text-sm text-muted">Unread updates, invitations, and account activity.</p>
            </a>

            <a href="{{ route('compendium.npcs.index') }}"
               class="ui-card-interactive block p-5 focus:outline-none focus:ring-2 focus:ring-accent">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">Characters</p>
                <p class="mt-3 text-3xl font-semibold text-text">{{ $npcCount }}</p>
                <p class="mt-2 text-sm text-muted">NPCs and characters in your personal compendium.</p>
            </a>
        </section>

        @if ($unreadInviteCount > 0)
            <section class="ui-card border-accent/30 bg-accent/10 p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-text">Pending Invites</h2>
                        <p class="mt-1 text-sm text-text/80">
                            You have {{ $unreadInviteCount }} campaign {{ \Illuminate\Support\Str::plural('invite', $unreadInviteCount) }} waiting in your notifications.
                        </p>
                    </div>

                    <a href="{{ route('notifications.index') }}" class="btn btn-primary btn-sm">
                        Review Invites
                    </a>
                </div>
            </section>
        @endif

        @if ($campaignCount === 0)
            <section class="ui-card p-6">
                <x-empty-state
                    icon="🗺️"
                    title="Create your first campaign"
                    message="Start a new adventure, invite your players, and keep your session details in one place."
                    :action="route('campaigns.create')"
                    actionLabel="Create Campaign"
                />
            </section>
        @endif

        <section>
            <h2 class="text-xl font-semibold text-text">Reference Tools</h2>
            <p class="mt-1 text-sm text-muted">Open the rules, browse your reference library, or jump straight into the tools that support your next session.</p>

            <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <a href="{{ route('spells.index') }}" class="ui-card-interactive group block p-5 focus:outline-none focus:ring-2 focus:ring-accent">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.5 6.75A2.25 2.25 0 0 1 6.75 4.5h10.5A2.25 2.25 0 0 1 19.5 6.75v10.5A2.25 2.25 0 0 1 17.25 19.5H6.75A2.25 2.25 0 0 1 4.5 17.25V6.75Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8.25 8.25h7.5M8.25 12h7.5M8.25 15.75h4.5" />
                                </svg>
                                <h3 class="text-base font-semibold text-text group-hover:text-accent transition-colors">Spellbook Archive</h3>
                            </div>
                            <p class="mt-2 text-sm text-muted">Browse your spell library for casting details, classes, and effect text.</p>
                        </div>
                        <span class="text-xs font-medium text-muted whitespace-nowrap">{{ number_format($spellCount) }} spells</span>
                    </div>
                </a>

                <a href="{{ route('creatures.index') }}" class="ui-card-interactive group block p-5 focus:outline-none focus:ring-2 focus:ring-accent">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.75 6.75c0-1.657-1.68-3-3.75-3s-3.75 1.343-3.75 3c0 .768.36 1.469.952 2.001A6.716 6.716 0 0 0 7.5 13.5v1.125c0 .621-.504 1.125-1.125 1.125h-.75a1.125 1.125 0 0 0 0 2.25h.75c1.864 0 3.375-1.511 3.375-3.375V13.5c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v1.125c0 1.864 1.511 3.375 3.375 3.375h.75a1.125 1.125 0 1 0 0-2.25h-.75c-.621 0-1.125-.504-1.125-1.125V13.5a6.716 6.716 0 0 0-1.702-4.749A2.968 2.968 0 0 0 15.75 6.75Z" />
                                </svg>
                                <h3 class="text-base font-semibold text-text group-hover:text-accent transition-colors">Monster Bestiary</h3>
                            </div>
                            <p class="mt-2 text-sm text-muted">Scan stat blocks, traits, actions, and challenge ratings for your encounters.</p>
                        </div>
                        <span class="text-xs font-medium text-muted whitespace-nowrap">{{ number_format($creatureCount) }} monsters</span>
                    </div>
                </a>

                <a href="{{ route('items.index') }}" class="ui-card-interactive group block p-5 focus:outline-none focus:ring-2 focus:ring-accent">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.568 3.75 4.5 8.818v6.364l5.068 5.068h4.864l5.068-5.068V8.818L14.432 3.75H9.568Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 9h6v6H9z" />
                                </svg>
                                <h3 class="text-base font-semibold text-text group-hover:text-accent transition-colors">Gear & Treasure</h3>
                            </div>
                            <p class="mt-2 text-sm text-muted">Look up equipment, magic items, and the custom loot you’ve added to your toolkit.</p>
                        </div>
                        <span class="text-xs font-medium text-muted whitespace-nowrap">{{ number_format($itemCount) }} items</span>
                    </div>
                </a>

                <a href="{{ route('rules.index') }}" class="ui-card-interactive group block p-5 focus:outline-none focus:ring-2 focus:ring-accent">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7.5 4.5h9A2.25 2.25 0 0 1 18.75 6.75v10.5A2.25 2.25 0 0 1 16.5 19.5h-9A2.25 2.25 0 0 1 5.25 17.25V6.75A2.25 2.25 0 0 1 7.5 4.5Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8.25 8.25h7.5M8.25 12h7.5M8.25 15.75h3" />
                                </svg>
                                <h3 class="text-base font-semibold text-text group-hover:text-accent transition-colors">Rules Reference</h3>
                            </div>
                            <p class="mt-2 text-sm text-muted">Open the SRD rules, conditions, and quick-reference sections you need mid-session.</p>
                        </div>
                        <span class="text-xs font-medium text-muted whitespace-nowrap">SRD guide</span>
                    </div>
                </a>

                <a href="{{ route('dice-roller') }}" class="ui-card-interactive group block p-5 focus:outline-none focus:ring-2 focus:ring-accent">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="m7.5 3.75 9 2.25 3 6-4.5 8.25h-9L1.5 12l3-6 3-2.25Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="m7.5 3.75 4.5 4.5m4.5-2.25L12 8.25m7.5 3.75H12m-10.5 0H12m3 8.25L12 12m-6 8.25L12 12" />
                                </svg>
                                <h3 class="text-base font-semibold text-text group-hover:text-accent transition-colors">Dice Roller</h3>
                            </div>
                            <p class="mt-2 text-sm text-muted">Roll fast, readable checks and damage without leaving the app.</p>
                        </div>
                        <span class="text-xs font-medium text-muted whitespace-nowrap">Quick tool</span>
                    </div>
                </a>

                <a href="{{ route('encounter-generator.index') }}" class="ui-card-interactive group block p-5 focus:outline-none focus:ring-2 focus:ring-accent">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3.75 6.75h16.5M6.75 3.75v6m10.5-6v6M5.25 9.75h13.5A1.5 1.5 0 0 1 20.25 11.25v7.5a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-7.5a1.5 1.5 0 0 1 1.5-1.5Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8.25 14.25h7.5M8.25 17.25h4.5" />
                                </svg>
                                <h3 class="text-base font-semibold text-text group-hover:text-accent transition-colors">Encounter Builder</h3>
                            </div>
                            <p class="mt-2 text-sm text-muted">Assemble balanced combats and explore threat levels before initiative starts.</p>
                        </div>
                        <span class="text-xs font-medium text-muted whitespace-nowrap">Session prep</span>
                    </div>
                </a>
            </div>
        </section>
    </div>
</div>
@endsection
