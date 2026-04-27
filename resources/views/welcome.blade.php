@extends('layouts.app')

@section('hero')
    <div class="w-full min-h-[350px] max-h-[700px] h-[70vh] overflow-hidden bg-center bg-cover relative flex items-center justify-center text-center"
         style="background-image: url('{{ asset('images/homepage-hero.jpg') }}');">

        {{-- Dark overlay --}}
        <div class="absolute inset-0 bg-black/60"></div>

        <div class="relative z-10 w-full px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="max-w-2xl mx-auto">

                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-white leading-tight">
                    Campaign Compendium
                </h1>

                @guest
                    <p class="mt-4 text-base sm:text-lg text-gray-200">
                        Your all-in-one TTRPG campaign manager. Explore the full D&amp;D SRD for free — spells, monsters, and items — or create an account to manage campaigns, characters, and custom content.
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row sm:justify-center gap-4">
                        <a href="{{ route('register') }}"
                           class="px-6 py-3 bg-accent text-on-accent rounded-lg shadow hover:bg-accent-hover text-center font-semibold focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-black/60">
                            Create an Account
                        </a>
                        <a href="#srd-tools"
                           class="px-6 py-3 bg-white/10 text-white border border-white/30 rounded-lg shadow hover:bg-white/20 text-center font-semibold backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-black/60">
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
                           class="px-6 py-3 bg-accent text-on-accent rounded-lg shadow hover:bg-accent-hover text-center font-semibold focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-black/60">
                            My Campaigns
                        </a>
                        <a href="{{ route('compendium.npcs.index') }}"
                           class="px-6 py-3 bg-white/10 text-white border border-white/30 rounded-lg shadow hover:bg-white/20 text-center font-semibold backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-black/60">
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
                <h2 id="srd-tools-heading" class="text-2xl font-bold text-text">Free SRD Reference Tools</h2>
                <p class="mt-2 text-muted max-w-xl mx-auto text-sm">
                    The entire D&amp;D 5e Systems Reference Document — freely available, no account needed.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

                {{-- Spells --}}
                <a href="{{ route('spells.index') }}"
                   class="group block bg-surface border border-border rounded-xl p-6 shadow-sm hover:shadow-md hover:border-accent transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent">
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
                   class="group block bg-surface border border-border rounded-xl p-6 shadow-sm hover:shadow-md hover:border-accent transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent">
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
                   class="group block bg-surface border border-border rounded-xl p-6 shadow-sm hover:shadow-md hover:border-accent transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent">
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

            </div>
        </section>

        {{-- Divider --}}
        <hr class="border-border my-2">

        {{-- ================================================ --}}
        {{-- Account Features Section                         --}}
        {{-- ================================================ --}}
        <section aria-labelledby="account-features-heading" class="py-12">

            <div class="text-center mb-10">
                <h2 id="account-features-heading" class="text-2xl font-bold text-text">More with a Free Account</h2>
                <p class="mt-2 text-muted max-w-xl mx-auto text-sm">
                    Create an account to unlock campaign management, custom content, and more — all free to get started.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Campaigns --}}
                <div class="bg-surface border border-border rounded-xl p-6 shadow-sm">
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
                <div class="bg-surface border border-border rounded-xl p-6 shadow-sm">
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
                <div class="bg-surface border border-border rounded-xl p-6 shadow-sm">
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
                   class="inline-flex items-center px-8 py-3 bg-accent text-on-accent font-semibold rounded-lg shadow hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-bg">
                    Create a Free Account
                </a>
                <p class="mt-3 text-xs text-muted">Already have an account?
                    <a href="{{ route('login') }}" class="text-accent underline underline-offset-2 hover:text-accent-hover">Sign in</a>
                </p>
            </div>

        </section>
    @endguest

    @auth
        {{-- ================================================ --}}
        {{-- Authenticated Dashboard Section                  --}}
        {{-- ================================================ --}}
        <section aria-labelledby="dashboard-heading" class="py-12">

            <h2 id="dashboard-heading" class="text-xl font-bold text-text mb-6">Jump Back In</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Campaigns --}}
                <a href="{{ route('campaigns.index') }}"
                   class="group block bg-surface border border-border rounded-xl p-6 shadow-sm hover:shadow-md hover:border-accent transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent">
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

                {{-- Characters --}}
                <a href="{{ route('compendium.npcs.index') }}"
                   class="group block bg-surface border border-border rounded-xl p-6 shadow-sm hover:shadow-md hover:border-accent transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent">
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

                {{-- Items --}}
                <a href="{{ route('items.index') }}"
                   class="group block bg-surface border border-border rounded-xl p-6 shadow-sm hover:shadow-md hover:border-accent transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-accent/10 text-accent" aria-hidden="true">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-text group-hover:text-accent transition-colors">My Items</h3>
                    </div>
                    <p class="text-sm text-muted leading-relaxed">Browse SRD items or manage your custom creations.</p>
                    <p class="mt-4 text-xs font-semibold text-accent uppercase tracking-wide">Go to Items →</p>
                </a>

            </div>

        </section>

        <hr class="border-border my-2">

        {{-- SRD Quick Links for authenticated users --}}
        <section aria-labelledby="srd-quick-heading" class="py-10">

            <h2 id="srd-quick-heading" class="text-xl font-bold text-text mb-6">SRD Reference</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="{{ route('spells.index') }}"
                   class="flex items-center gap-3 bg-surface border border-border rounded-lg px-4 py-3 text-sm font-medium text-text hover:border-accent hover:text-accent transition-colors focus:outline-none focus:ring-2 focus:ring-accent">
                    <svg class="w-4 h-4 text-accent flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" />
                    </svg>
                    Spell Reference <span class="ml-auto text-xs text-muted">319 spells</span>
                </a>
                <a href="{{ route('creatures.index') }}"
                   class="flex items-center gap-3 bg-surface border border-border rounded-lg px-4 py-3 text-sm font-medium text-text hover:border-accent hover:text-accent transition-colors focus:outline-none focus:ring-2 focus:ring-accent">
                    <svg class="w-4 h-4 text-accent flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm0 8.625a1.125 1.125 0 1 0 0 2.25 1.125 1.125 0 0 0 0-2.25ZM8.25 12a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Zm5.25 0a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z" />
                    </svg>
                    Monster Bestiary <span class="ml-auto text-xs text-muted">328 monsters</span>
                </a>
                <a href="{{ route('srdItems.index') }}"
                   class="flex items-center gap-3 bg-surface border border-border rounded-lg px-4 py-3 text-sm font-medium text-text hover:border-accent hover:text-accent transition-colors focus:outline-none focus:ring-2 focus:ring-accent">
                    <svg class="w-4 h-4 text-accent flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                    </svg>
                    Items &amp; Equipment <span class="ml-auto text-xs text-muted">SRD items</span>
                </a>
            </div>

        </section>
    @endauth

@endsection
