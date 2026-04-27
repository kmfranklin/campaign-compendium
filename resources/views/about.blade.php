@extends('layouts.app')

@section('hero')
    {{-- ============================================================ --}}
    {{-- Gradient hero banner — no image, uses the app's accent palette --}}
    {{-- ============================================================ --}}
    <div class="w-full py-16 sm:py-20 text-center relative overflow-hidden"
         style="background: linear-gradient(135deg, #2e1065 0%, #4c1d95 45%, #6d28d9 100%);">

        {{-- Subtle decorative rings --}}
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #a78bfa 0%, transparent 70%);"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #a78bfa 0%, transparent 70%);"></div>
        </div>

        <div class="relative z-10 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">
                About Campaign Compendium
            </h1>
            <p class="mt-4 text-base sm:text-lg text-violet-200 max-w-xl mx-auto">
                An all-in-one TTRPG campaign management system, built for Dungeon Masters and players alike.
            </p>
        </div>
    </div>
@endsection

@section('content')

    {{-- ============================================================ --}}
    {{-- What is Campaign Compendium                                   --}}
    {{-- ============================================================ --}}
    <section aria-labelledby="about-heading" class="py-12 max-w-3xl mx-auto">
        <h2 id="about-heading" class="text-2xl font-bold text-text mb-4">What is Campaign Compendium?</h2>
        <p class="text-text leading-relaxed mb-4">
            Campaign Compendium is a web-based management tool for tabletop roleplaying games. It includes the full D&D 5e Systems Reference Document — spells, monsters, items, weapons, armor, and rules — available to anyone, no account required.
        </p>
        <p class="text-text leading-relaxed mb-4">
            Create an account, and it evolves into a full campaign hub: create and manage campaigns, track quests, build NPCs, encounters, and loot, and collaborate with other players.
        <p class="text-text leading-relaxed">
            The goal is simple: to be the tool you actually use at the table and between sessions, without the friction of overly complex or bloated sessions.
        </p>
    </section>

    <hr class="border-border">

    {{-- ============================================================ --}}
    {{-- What's Available Now                                          --}}
    {{-- ============================================================ --}}
    <section aria-labelledby="available-heading" class="py-12">

        <div class="text-center mb-10">
            <h2 id="available-heading" class="text-2xl font-bold text-text">What's Available Now</h2>
            <p class="mt-2 text-muted max-w-xl mx-auto text-sm">
                Everything below is live and free to use today.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- SRD Spells --}}
            <div class="bg-surface border border-border rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-accent/10 text-accent" aria-hidden="true">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-text">Spell Reference</h3>
                </div>
                <p class="text-sm text-muted leading-relaxed">
                    All 319 SRD spells with full descriptions, components, and higher-level effects. Filter by class,
                    level, school, and casting time.
                </p>
                <a href="{{ route('spells.index') }}"
                   class="mt-4 inline-block text-xs font-semibold text-accent uppercase tracking-wide hover:text-accent-hover focus:outline-none focus:underline">
                    Browse Spells →
                </a>
            </div>

            {{-- SRD Monsters --}}
            <div class="bg-surface border border-border rounded-xl p-6 shadow-sm">
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
                    <h3 class="text-base font-semibold text-text">Monster Bestiary</h3>
                </div>
                <p class="text-sm text-muted leading-relaxed">
                    All 328 SRD monsters with full statblocks, abilities, actions, and legendary actions. Filter by
                    type, challenge rating, and size.
                </p>
                <a href="{{ route('creatures.index') }}"
                   class="mt-4 inline-block text-xs font-semibold text-accent uppercase tracking-wide hover:text-accent-hover focus:outline-none focus:underline">
                    Browse Monsters →
                </a>
            </div>

            {{-- SRD Items --}}
            <div class="bg-surface border border-border rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-accent/10 text-accent" aria-hidden="true">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-text">Items &amp; Equipment</h3>
                </div>
                <p class="text-sm text-muted leading-relaxed">
                    The full SRD equipment list — weapons, armor, tools, and magic items. Clone any item as a starting
                    point for your own custom creations.
                </p>
                <a href="{{ route('srdItems.index') }}"
                   class="mt-4 inline-block text-xs font-semibold text-accent uppercase tracking-wide hover:text-accent-hover focus:outline-none focus:underline">
                    Browse Items →
                </a>
            </div>

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
                    Create campaigns, track quests, manage NPCs, and invite players to collaborate — all tied together
                    in one place.
                </p>
            </div>

            {{-- NPCs --}}
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
                    Build a personal library of NPCs and characters. Assign them to campaigns and quests, and keep
                    their details organized and accessible.
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
                    Clone any SRD item as a base and make it your own — adjust stats, rename it, or homebrew something
                    entirely new for your world.
                </p>
            </div>

        </div>
    </section>

    <hr class="border-border">

    {{-- ============================================================ --}}
    {{-- What's Coming                                                 --}}
    {{-- ============================================================ --}}
    <section aria-labelledby="roadmap-heading" class="py-12">

        <div class="text-center mb-10">
            <h2 id="roadmap-heading" class="text-2xl font-bold text-text">What's Coming</h2>
            <p class="mt-2 text-muted max-w-xl mx-auto text-sm">
                Campaign Compendium is actively in development. Here's where it's headed.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            {{-- Phase 3 --}}
            <div class="bg-surface border border-border rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-accent/10 text-accent" aria-hidden="true">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-text">Deeper Campaign Tools</h3>
                </div>
                <p class="text-sm text-muted leading-relaxed">
                    Session logs, campaign journals, named locations, factions, and a full campaign dashboard — everything a DM needs to run a living world between sessions.
                </p>
            </div>

            {{-- Phase 4 --}}
            <div class="bg-surface border border-border rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-accent/10 text-accent" aria-hidden="true">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-text">Encounters</h3>
                </div>
                <p class="text-sm text-muted leading-relaxed">
                    Full encounter management with an at-the-table initiative tracker, HP tracking, condition management, and automatic difficulty ratings based on party level and monster CR.
                </p>
            </div>

            {{-- Phase 5 --}}
            <div class="bg-surface border border-border rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-accent/10 text-accent" aria-hidden="true">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-text">Player Characters</h3>
                </div>
                <p class="text-sm text-muted leading-relaxed">
                    A guided character builder using the full SRD ruleset — races, classes, backgrounds, ability scores, and equipment — plus a full character sheet with level-up support and spellcasting.
                </p>
            </div>

            {{-- Phase 6 --}}
            <div class="bg-surface border border-border rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-accent/10 text-accent" aria-hidden="true">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 4.5a2.121 2.121 0 0 1 3 3L7.5 22.5l-4 1 1-4L19.5 4.5Z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-text">Content Generation</h3>
                </div>
                <p class="text-sm text-muted leading-relaxed">
                    Random encounter tables, treasure generators, NPC generators, and AI-assisted tools for generating plot hooks, item descriptions, location flavour text, and more.
                </p>
            </div>

        </div>
    </section>

    <hr class="border-border">

    {{-- ============================================================ --}}
    {{-- GitHub + CTA strip                                            --}}
    {{-- ============================================================ --}}
    <section aria-labelledby="cta-heading" class="py-12 text-center">

        <h2 id="cta-heading" class="text-xl font-bold text-text mb-2">Open Source &amp; In Progress</h2>
        <p class="text-muted text-sm max-w-lg mx-auto mb-8">
            Campaign Compendium is an open source project, actively developed on GitHub. Curious about the tech stack, want to follow along, or have a suggestion? The repo is public.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">

            {{-- GitHub link --}}
            <a href="https://github.com/kmfranklin/campaign-compendium"
               target="_blank"
               rel="noopener noreferrer"
               class="inline-flex items-center gap-2 px-6 py-3 bg-surface border border-border text-text font-semibold rounded-lg shadow-sm hover:border-accent hover:text-accent transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-bg">
                {{-- GitHub mark --}}
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0 1 12 6.844a9.59 9.59 0 0 1 2.504.337c1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.02 10.02 0 0 0 22 12.017C22 6.484 17.522 2 12 2Z" />
                </svg>
                View on GitHub
            </a>

            @guest
                <a href="{{ route('register') }}"
                   class="inline-flex items-center px-6 py-3 bg-accent text-on-accent font-semibold rounded-lg shadow hover:bg-accent-hover transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-bg">
                    Create a Free Account
                </a>
            @endguest

        </div>

    </section>

@endsection
