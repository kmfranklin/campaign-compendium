@extends('layouts.app')

@section('hero')
    <div class="w-full min-h-[350px] max-h-[700px] h-[70vh] overflow-hidden bg-center bg-cover relative flex items-center justify-center text-center"
         style="background-image: url('{{ asset('images/homepage-hero.jpg') }}');">

        {{-- Dark overlay --}}
        <div class="absolute inset-0 bg-black/60"></div>

        <div class="relative z-10 w-full px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="max-w-2xl mx-auto">

                <h1 class="font-sans text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight text-white leading-[0.92]">
                    Campaign Compendium
                </h1>

                @guest
                    <p class="mt-4 text-base sm:text-lg text-gray-200">
                        Your all-in-one TTRPG campaign manager. Explore the full D&amp;D SRD for free — spells, monsters, and items — or create an account to manage campaigns, characters, and custom content.
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row sm:justify-center gap-4">
                        <a href="{{ route('register') }}"
                           class="btn btn-primary btn-lg text-center focus:ring-offset-black/60">
                            Create an Account
                        </a>
                        <a href="#srd-tools"
                           class="btn btn-hero-secondary btn-lg text-center focus:ring-offset-black/60">
                            Explore the SRD
                        </a>
                    </div>
                @endguest

                @auth
                    <p class="mt-4 text-base sm:text-lg text-gray-200">
                        Welcome back, {{ Auth::user()->name }}!
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row sm:justify-center gap-4">
                        <a href="{{ route('campaigns.index') }}"
                           class="btn btn-primary btn-lg text-center focus:ring-offset-black/60">
                            My Campaigns
                        </a>
                        <a href="{{ route('compendium.npcs.index') }}"
                           class="btn btn-hero-secondary btn-lg text-center focus:ring-offset-black/60">
                            My Characters
                        </a>
                    </div>
                @endauth

            </div>
        </div>
    </div>
@endsection

@section('content')

    @guest
        {{-- ================================================ --}}
        {{-- SRD Tools Section                                 --}}
        {{-- ================================================ --}}
        <section id="srd-tools" aria-labelledby="srd-tools-heading" class="py-12">

            <div class="text-center mb-10">
                <h2 id="srd-tools-heading" class="font-sans text-2xl sm:text-3xl font-semibold tracking-[-0.02em] text-text">Free SRD Reference Tools</h2>
                <p class="mt-2 text-muted max-w-xl mx-auto text-sm">
                    The entire D&amp;D 5e Systems Reference Document — freely available, no account needed.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Spells --}}
                <a href="{{ route('spells.index') }}"
                   class="ui-card-interactive group block p-6 focus:outline-none focus:ring-2 focus:ring-accent">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-accent/10 text-accent" aria-hidden="true">
                            {{-- Sparkles icon --}}
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-text group-hover:text-accent transition-colors">Spells</h3>
                    </div>
                    <p class="text-sm text-muted leading-relaxed">
                        Browse all 319 SRD spells. Filter by class, level, school, and casting time. Full descriptions, components, and higher-level effects included.
                    </p>
                    <p class="mt-4 text-xs font-semibold text-accent uppercase tracking-wide">Browse Spells →</p>
                </a>

                {{-- Monsters --}}
                <a href="{{ route('creatures.index') }}"
                   class="ui-card-interactive group block p-6 focus:outline-none focus:ring-2 focus:ring-accent">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-accent/10 text-accent" aria-hidden="true">
                            {{-- Custom beholder icon --}}
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <circle cx="12" cy="13.5" r="5.5"/>
                                <path d="M9 13.5 C10 11.5 14 11.5 15 13.5 C14 15.5 10 15.5 9 13.5Z" fill="currentColor" stroke="none"/>
                                <line x1="12" y1="8" x2="12" y2="5" stroke-linecap="round"/>
                                <circle cx="12" cy="4" r="1.2" fill="currentColor" stroke="none"/>
                                <line x1="15.2" y1="9" x2="17.5" y2="6.8" stroke-linecap="round"/>
                                <circle cx="18.3" cy="5.9" r="1.2" fill="currentColor" stroke="none"/>
                                <line x1="8.8" y1="9" x2="6.5" y2="6.8" stroke-linecap="round"/>
                                <circle cx="5.7" cy="5.9" r="1.2" fill="currentColor" stroke="none"/>
                                <line x1="17.2" y1="12.5" x2="20" y2="11.5" stroke-linecap="round"/>
                                <circle cx="21" cy="11.1" r="1.2" fill="currentColor" stroke="none"/>
                                <line x1="6.8" y1="12.5" x2="4" y2="11.5" stroke-linecap="round"/>
                                <circle cx="3" cy="11.1" r="1.2" fill="currentColor" stroke="none"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-text group-hover:text-accent transition-colors">Monsters</h3>
                    </div>
                    <p class="text-sm text-muted leading-relaxed">
                        Browse all 328 SRD monsters with full statblocks. Filter by type, challenge rating, and size. Abilities, actions, and legendary actions included.
                    </p>
                    <p class="mt-4 text-xs font-semibold text-accent uppercase tracking-wide">Browse Monsters →</p>
                </a>

                {{-- Items --}}
                <a href="{{ route('srdItems.index') }}"
                   class="ui-card-interactive group block p-6 focus:outline-none focus:ring-2 focus:ring-accent">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-accent/10 text-accent" aria-hidden="true">
                            {{-- Bag/backpack icon --}}
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-text group-hover:text-accent transition-colors">Items &amp; Equipment</h3>
                    </div>
                    <p class="text-sm text-muted leading-relaxed">
                        Browse the full SRD equipment list — weapons, armor, tools, magic items, and more. Use any item as a base for your own custom creations.
                    </p>
                    <p class="mt-4 text-xs font-semibold text-accent uppercase tracking-wide">Browse Items →</p>
                </a>

                {{-- Dice Roller --}}
                <a href="{{ route('dice-roller') }}"
                   class="ui-card-interactive group block p-6 focus:outline-none focus:ring-2 focus:ring-accent">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-accent/10 text-accent" aria-hidden="true">
                            {{-- Cube/dice icon --}}
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-text group-hover:text-accent transition-colors">Dice Roller</h3>
                    </div>
                    <p class="text-sm text-muted leading-relaxed">
                        Roll any combination of polyhedral dice — d4 through d100 — with optional modifiers and a full roll history. No account needed.
                    </p>
                    <p class="mt-4 text-xs font-semibold text-accent uppercase tracking-wide">Roll Dice →</p>
                </a>

                {{-- Encounter Generator --}}
                <a href="{{ route('encounter-generator.index') }}"
                   class="ui-card-interactive group block p-6 focus:outline-none focus:ring-2 focus:ring-accent">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-accent/10 text-accent" aria-hidden="true">
                            {{-- Lightning bolt / action icon --}}
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-text group-hover:text-accent transition-colors">Encounter Generator</h3>
                    </div>
                    <p class="text-sm text-muted leading-relaxed">
                        Build balanced encounters for your party. Enter party size and difficulty — get monster suggestions with XP math done for you.
                    </p>
                    <p class="mt-4 text-xs font-semibold text-accent uppercase tracking-wide">Build an Encounter →</p>
                </a>

            </div>
        </section>

        {{-- Divider --}}
        <hr class="border-border my-2">

        {{-- ================================================ --}}
        {{-- Account Features Section                         --}}
        {{-- ================================================ --}}
        <section aria-labelledby="account-features-heading" class="py-12">

            <div class="text-center mb-10">
                <h2 id="account-features-heading" class="font-sans text-2xl sm:text-3xl font-semibold tracking-[-0.02em] text-text">More with a Free Account</h2>
                <p class="mt-2 text-muted max-w-xl mx-auto text-sm">
                    Create an account to unlock campaign management, custom content, and more — all free to get started.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Campaigns --}}
                <div class="ui-card p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-accent/10 text-accent" aria-hidden="true">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-text">Campaign Management</h3>
                    </div>
                    <p class="text-sm text-muted leading-relaxed">
                        Create and manage full campaigns. Track quests, NPCs, session notes, and invite players to collaborate.
                    </p>
                </div>

                {{-- Characters / NPCs --}}
                <div class="ui-card p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-accent/10 text-accent" aria-hidden="true">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-text">NPCs &amp; Characters</h3>
                    </div>
                    <p class="text-sm text-muted leading-relaxed">
                        Build a personal compendium of NPCs and characters. Assign them to campaigns and quests, and keep all their details in one place.
                    </p>
                </div>

                {{-- Custom Items --}}
                <div class="ui-card p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-accent/10 text-accent" aria-hidden="true">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l5.654-4.654m5.896-2.613.308-.73a3.75 3.75 0 0 0-.632-3.982L8.007 2.566a3.75 3.75 0 0 0-5.026-.175L6.38 6.432c.158.158.248.374.248.597v.495a.75.75 0 0 0 .328.624l.544.363a.75.75 0 0 1 .328.624v1.123c0 .208-.087.406-.24.55l-.138.128a.75.75 0 0 0-.24.55V15l-.138.128" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-text">Custom Items</h3>
                    </div>
                    <p class="text-sm text-muted leading-relaxed">
                        Clone any SRD item as a starting point and customize it to fit your world — tweak stats, rename it, or build something entirely new.
                    </p>
                </div>

            </div>

            <div class="mt-10 text-center">
                <a href="{{ route('register') }}"
                   class="btn btn-primary btn-lg px-8">
                    Create a Free Account
                </a>
                <p class="mt-3 text-xs text-muted">Already have an account?
                    <a href="{{ route('login') }}" class="link-action">Sign in</a>
                </p>
            </div>

        </section>
    @endguest

    @auth
        @php
            $user = auth()->user();
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

        <section aria-labelledby="dashboard-heading" class="py-12">
            <h2 id="dashboard-heading" class="font-sans text-2xl sm:text-3xl font-semibold tracking-[-0.02em] text-text mb-6">Jump Back In</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="{{ route('campaigns.index') }}"
                   class="ui-card-interactive group block p-6 focus:outline-none focus:ring-2 focus:ring-accent">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-accent/10 text-accent" aria-hidden="true">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-text group-hover:text-accent transition-colors">My Campaigns</h3>
                    </div>
                    <p class="text-sm text-muted leading-relaxed">View and manage your campaigns, quests, NPCs, and session notes.</p>
                    <p class="mt-4 text-xs font-semibold text-accent uppercase tracking-wide">Go to Campaigns →</p>
                </a>

                <a href="{{ route('compendium.npcs.index') }}"
                   class="ui-card-interactive group block p-6 focus:outline-none focus:ring-2 focus:ring-accent">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-accent/10 text-accent" aria-hidden="true">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-text group-hover:text-accent transition-colors">My Characters</h3>
                    </div>
                    <p class="text-sm text-muted leading-relaxed">Manage your NPCs and characters. Assign them to campaigns and quests.</p>
                    <p class="mt-4 text-xs font-semibold text-accent uppercase tracking-wide">Go to Characters →</p>
                </a>

                <a href="{{ route('items.index') }}"
                   class="ui-card-interactive group block p-6 focus:outline-none focus:ring-2 focus:ring-accent">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-accent/10 text-accent" aria-hidden="true">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-text group-hover:text-accent transition-colors">My Items</h3>
                    </div>
                    <p class="text-sm text-muted leading-relaxed">Browse your reference items or manage your custom creations.</p>
                    <p class="mt-4 text-xs font-semibold text-accent uppercase tracking-wide">Go to Items →</p>
                </a>
            </div>
        </section>

        <hr class="border-border my-2">

        <section aria-labelledby="reference-tools-heading" class="py-10">
            <h2 id="reference-tools-heading" class="font-sans text-2xl sm:text-3xl font-semibold tracking-[-0.02em] text-text">Reference Tools</h2>
            <p class="mt-2 text-muted max-w-3xl text-sm">
                Browse your reference library, open the rules, or jump straight into the session tools that support your next game.
            </p>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
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
                                <h3 class="text-base font-semibold text-text group-hover:text-accent transition-colors">Gear &amp; Treasure</h3>
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
    @endauth

@endsection
