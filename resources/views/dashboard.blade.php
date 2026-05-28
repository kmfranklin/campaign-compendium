@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $campaignCount = $user->campaigns()->count();
    $npcCount = $user->npcs()->count();
    $unreadInviteCount = $user->notifications()
        ->where('type', \App\Models\Notification::TYPE_INVITE)
        ->whereNull('read_at')
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
            <div class="ui-card p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">Campaigns</p>
                <p class="mt-3 text-3xl font-semibold text-text">{{ $campaignCount }}</p>
                <p class="mt-2 text-sm text-muted">Campaigns you can currently access.</p>
            </div>

            <div class="ui-card p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">Invites</p>
                <p class="mt-3 text-3xl font-semibold text-text">{{ $unreadInviteCount }}</p>
                <p class="mt-2 text-sm text-muted">Unread campaign invitations waiting for you.</p>
            </div>

            <div class="ui-card p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">Characters</p>
                <p class="mt-3 text-3xl font-semibold text-text">{{ $npcCount }}</p>
                <p class="mt-2 text-sm text-muted">NPCs and characters in your personal compendium.</p>
            </div>
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

                <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <a href="{{ route('spells.index') }}" class="ui-card-compact flex items-center justify-between gap-3 text-sm font-medium text-text hover:border-accent hover:text-accent transition-colors">
                        Browse Spells
                        <span class="text-xs text-muted">SRD</span>
                    </a>
                    <a href="{{ route('creatures.index') }}" class="ui-card-compact flex items-center justify-between gap-3 text-sm font-medium text-text hover:border-accent hover:text-accent transition-colors">
                        Browse Monsters
                        <span class="text-xs text-muted">SRD</span>
                    </a>
                    <a href="{{ route('dice-roller') }}" class="ui-card-compact flex items-center justify-between gap-3 text-sm font-medium text-text hover:border-accent hover:text-accent transition-colors">
                        Open Dice Roller
                        <span class="text-xs text-muted">Tool</span>
                    </a>
                </div>
            </section>
        @else
            <section>
                <div>
                    <h2 class="text-xl font-semibold text-text">Quick Actions</h2>
                    <p class="mt-1 text-sm text-muted">Shortcuts for the things you’re most likely to do next.</p>
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <a href="{{ route('campaigns.index') }}" class="ui-card-interactive group block p-5 focus:outline-none focus:ring-2 focus:ring-accent">
                        <h3 class="text-base font-semibold text-text group-hover:text-accent transition-colors">Open My Campaigns</h3>
                        <p class="mt-2 text-sm text-muted">Jump into your active campaigns, quests, sessions, and members.</p>
                    </a>

                    <a href="{{ route('campaigns.create') }}" class="ui-card-interactive group block p-5 focus:outline-none focus:ring-2 focus:ring-accent">
                        <h3 class="text-base font-semibold text-text group-hover:text-accent transition-colors">Create Another Campaign</h3>
                        <p class="mt-2 text-sm text-muted">Spin up a one-shot, side campaign, or a fresh long-term adventure.</p>
                    </a>

                    <a href="{{ route('compendium.npcs.index') }}" class="ui-card-interactive group block p-5 focus:outline-none focus:ring-2 focus:ring-accent">
                        <h3 class="text-base font-semibold text-text group-hover:text-accent transition-colors">Manage Characters</h3>
                        <p class="mt-2 text-sm text-muted">Keep your recurring NPCs, rivals, and party contacts organized.</p>
                    </a>

                    <a href="{{ route('encounter-generator.index') }}" class="ui-card-interactive group block p-5 focus:outline-none focus:ring-2 focus:ring-accent">
                        <h3 class="text-base font-semibold text-text group-hover:text-accent transition-colors">Build an Encounter</h3>
                        <p class="mt-2 text-sm text-muted">Use the public encounter generator and save what works for your table.</p>
                    </a>
                </div>
            </section>
        @endif

        <section>
            <h2 class="text-xl font-semibold text-text">Reference Tools</h2>
            <p class="mt-1 text-sm text-muted">Useful SRD tools are always one click away, whether you’re building or running a session.</p>

            <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <a href="{{ route('spells.index') }}" class="ui-card-compact flex items-center justify-between gap-3 text-sm font-medium text-text hover:border-accent hover:text-accent transition-colors">
                    Spell Reference
                    <span class="text-xs text-muted">319 spells</span>
                </a>
                <a href="{{ route('creatures.index') }}" class="ui-card-compact flex items-center justify-between gap-3 text-sm font-medium text-text hover:border-accent hover:text-accent transition-colors">
                    Monster Bestiary
                    <span class="text-xs text-muted">328 monsters</span>
                </a>
                <a href="{{ route('srdItems.index') }}" class="ui-card-compact flex items-center justify-between gap-3 text-sm font-medium text-text hover:border-accent hover:text-accent transition-colors">
                    SRD Items
                    <span class="text-xs text-muted">Equipment</span>
                </a>
                <a href="{{ route('rules.index') }}" class="ui-card-compact flex items-center justify-between gap-3 text-sm font-medium text-text hover:border-accent hover:text-accent transition-colors">
                    Rules Reference
                    <span class="text-xs text-muted">Conditions</span>
                </a>
            </div>
        </section>
    </div>
</div>
@endsection
