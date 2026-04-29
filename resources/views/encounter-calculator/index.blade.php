@extends('layouts.app')

@section('hero')
    <div class="w-full py-12 sm:py-16 text-center relative overflow-hidden"
         style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 45%, #0f3460 100%);">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #e94560 0%, transparent 70%);"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #e94560 0%, transparent 70%);"></div>
        </div>
        <div class="relative z-10 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">Encounter Calculator</h1>
            <p class="mt-3 text-base text-blue-200">
                Build an encounter and see its difficulty — Easy, Medium, Hard, or Deadly.
            </p>
        </div>
    </div>
@endsection

@section('content')

{{-- ============================================================ --}}
{{-- All encounter logic lives in this single Alpine component.   --}}
{{--                                                              --}}
{{-- Key data structures:                                         --}}
{{--   party[]      — array of integer character levels           --}}
{{--   monsters[]   — array of monster entries (see addMonster)   --}}
{{--                                                              --}}
{{-- Key computed properties:                                     --}}
{{--   partyThresholds — summed Easy/Med/Hard/Deadly across party  --}}
{{--   totalXp         — raw XP sum of all monsters               --}}
{{--   multiplier      — DMG bracket multiplier (party-adjusted)  --}}
{{--   adjustedXp      — totalXp × multiplier                     --}}
{{--   difficulty      — label derived from adjustedXp vs party   --}}
{{-- ============================================================ --}}
<div
    x-data="{
        /* ── Party ───────────────────────────────────────────────── */
        party: [],
        newLevel: 1,

        addCharacter() {
            const lvl = parseInt(this.newLevel);
            if (lvl >= 1 && lvl <= 20) {
                this.party.push(lvl);
                this.newLevel = 1;
            }
        },

        removeCharacter(index) {
            this.party.splice(index, 1);
        },

        /* ── Monster search ──────────────────────────────────────── */
        searchQuery: '',
        crMin: '',
        crMax: '',
        searchResults: [],
        searching: false,
        searchDebounce: null,

        /* Manual entry fields */
        manualName: '',
        manualCr: '',
        manualXp: '',
        showManualForm: false,

        triggerSearch() {
            clearTimeout(this.searchDebounce);
            if (this.searchQuery.trim().length < 1) {
                this.searchResults = [];
                return;
            }
            this.searchDebounce = setTimeout(() => this.runSearch(), 300);
        },

        async runSearch() {
            this.searching = true;
            try {
                const params = new URLSearchParams({ q: this.searchQuery.trim() });
                if (this.crMin !== '') params.set('cr_min', this.crMin);
                if (this.crMax !== '') params.set('cr_max', this.crMax);

                const res = await fetch(`{{ route('encounter-calculator.creatures') }}?${params}`);
                this.searchResults = await res.json();
            } catch (e) {
                this.searchResults = [];
            } finally {
                this.searching = false;
            }
        },

        /* ── Monsters in encounter ───────────────────────────────── */
        monsters: [],

        addMonster(creature) {
            /* If this creature is already in the list, just bump its quantity */
            const existing = this.monsters.find(
                m => m.source !== 'manual' && m.creature_id === creature.id
            );
            if (existing) {
                existing.quantity++;
                return;
            }
            this.monsters.push({
                source:      creature.source,   /* 'srd' | 'custom_creature' */
                creature_id: creature.id,
                name:        creature.name,
                cr:          creature.cr,
                xp:          creature.xp,
                quantity:    1,
            });
            this.searchQuery   = '';
            this.searchResults = [];
        },

        addManualMonster() {
            const name = this.manualName.trim();
            const xp   = parseInt(this.manualXp);
            if (!name || isNaN(xp) || xp < 0) return;

            this.monsters.push({
                source:      'manual',
                creature_id: null,
                name:        name,
                cr:          this.manualCr.trim() || null,
                xp:          xp,
                quantity:    1,
            });
            this.manualName     = '';
            this.manualCr       = '';
            this.manualXp       = '';
            this.showManualForm = false;
        },

        removeMonster(index) {
            this.monsters.splice(index, 1);
        },

        setQuantity(index, delta) {
            const m = this.monsters[index];
            const next = m.quantity + delta;
            if (next < 1) {
                this.monsters.splice(index, 1);
            } else {
                m.quantity = next;
            }
        },

        /* ── XP / Difficulty tables (DMG) ───────────────────────── */

        /*
         * XP thresholds per character level.
         * Index 0 is unused; index 1 = level 1, index 20 = level 20.
         * Each inner array is [easy, medium, hard, deadly].
         */
        XP_THRESHOLDS: [
            null,
            [25,   50,   75,   100],
            [50,   100,  150,  200],
            [75,   150,  225,  400],
            [125,  250,  375,  500],
            [250,  500,  750,  1100],
            [300,  600,  900,  1400],
            [350,  750,  1100, 1700],
            [450,  900,  1400, 2100],
            [550,  1100, 1600, 2400],
            [600,  1200, 1900, 2800],
            [800,  1600, 2400, 3600],
            [1000, 2000, 3000, 4500],
            [1100, 2200, 3400, 5100],
            [1250, 2500, 3800, 5700],
            [1400, 2800, 4300, 6400],
            [1600, 3200, 4800, 7200],
            [2000, 3900, 5900, 8800],
            [2100, 4200, 6300, 9500],
            [2400, 4900, 7300, 10900],
            [2800, 5700, 8500, 12700],
        ],

        /*
         * Monster count multiplier brackets.
         * The DMG adjusts up one bracket for small parties (≤3)
         * and down one bracket for large parties (≥6).
         * Brackets: [minCount, maxCount, multiplier]
         */
        MULTIPLIER_BRACKETS: [
            [1,  1,  1.0],
            [2,  2,  1.5],
            [3,  6,  2.0],
            [7,  10, 2.5],
            [11, 14, 3.0],
            [15, Infinity, 4.0],
        ],

        /* ── Computed values ─────────────────────────────────────── */

        get monsterCount() {
            return this.monsters.reduce((sum, m) => sum + m.quantity, 0);
        },

        get totalXp() {
            return this.monsters.reduce((sum, m) => sum + (m.xp * m.quantity), 0);
        },

        get multiplier() {
            if (this.monsterCount === 0) return 1;

            const count      = this.monsterCount;
            const partySize  = this.party.length;

            /* Find the base bracket index */
            let bracketIdx = this.MULTIPLIER_BRACKETS.findIndex(
                ([min, max]) => count >= min && count <= max
            );
            if (bracketIdx === -1) bracketIdx = this.MULTIPLIER_BRACKETS.length - 1;

            /* Adjust for party size */
            if (partySize <= 3) bracketIdx = Math.min(bracketIdx + 1, this.MULTIPLIER_BRACKETS.length - 1);
            if (partySize >= 6) bracketIdx = Math.max(bracketIdx - 1, 0);

            return this.MULTIPLIER_BRACKETS[bracketIdx][2];
        },

        get adjustedXp() {
            return Math.floor(this.totalXp * this.multiplier);
        },

        get partyThresholds() {
            if (this.party.length === 0) return [0, 0, 0, 0];
            return this.party.reduce(
                (totals, level) => {
                    const t = this.XP_THRESHOLDS[level] ?? [0, 0, 0, 0];
                    return totals.map((v, i) => v + t[i]);
                },
                [0, 0, 0, 0]
            );
        },

        get difficulty() {
            if (this.party.length === 0 || this.monsters.length === 0) return null;
            const [easy, medium, hard, deadly] = this.partyThresholds;
            const xp = this.adjustedXp;
            if (xp >= deadly) return 'deadly';
            if (xp >= hard)   return 'hard';
            if (xp >= medium) return 'medium';
            if (xp >= easy)   return 'easy';
            return 'trivial';
        },

        get difficultyLabel() {
            return {
                trivial: 'Trivial',
                easy:    'Easy',
                medium:  'Medium',
                hard:    'Hard',
                deadly:  'Deadly',
            }[this.difficulty] ?? '—';
        },

        get difficultyColor() {
            return {
                trivial: 'text-gray-400',
                easy:    'text-green-400',
                medium:  'text-yellow-400',
                hard:    'text-orange-400',
                deadly:  'text-red-500',
            }[this.difficulty] ?? 'text-muted';
        },

        get difficultyBg() {
            return {
                trivial: 'bg-gray-500/10 border-gray-500/30',
                easy:    'bg-green-500/10 border-green-500/30',
                medium:  'bg-yellow-500/10 border-yellow-500/30',
                hard:    'bg-orange-500/10 border-orange-500/30',
                deadly:  'bg-red-500/10 border-red-600/40',
            }[this.difficulty] ?? 'bg-surface border-border';
        },

        /* Percentage of the way through the current bracket, for the bar */
        get difficultyProgress() {
            if (!this.difficulty || this.difficulty === 'trivial') return 0;
            const [easy, medium, hard, deadly] = this.partyThresholds;
            const xp = this.adjustedXp;
            const brackets = {
                easy:   [easy,   medium],
                medium: [medium, hard],
                hard:   [hard,   deadly],
                deadly: [deadly, deadly * 2],
            };
            const [lo, hi] = brackets[this.difficulty] ?? [0, 1];
            return Math.min(100, Math.round(((xp - lo) / (hi - lo)) * 100));
        },

        /* ── Save encounter ──────────────────────────────────────── */
        saving: false,
        saveSuccess: false,
        saveError: '',
        saveName: '',
        showSaveForm: false,

        async saveEncounter() {
            if (!this.difficulty || this.party.length === 0 || this.monsters.length === 0) return;
            this.saving    = true;
            this.saveError = '';

            try {
                const res = await fetch('{{ route('encounter-calculator.save') }}', {
                    method:  'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify({
                        name:         this.saveName || null,
                        party:        this.party,
                        monsters:     this.monsters,
                        total_xp:     this.totalXp,
                        adjusted_xp:  this.adjustedXp,
                        difficulty:   this.difficulty,
                    }),
                });

                if (res.ok) {
                    this.saveSuccess   = true;
                    this.showSaveForm  = false;
                    this.saveName      = '';
                    setTimeout(() => this.saveSuccess = false, 4000);
                } else {
                    const data     = await res.json();
                    this.saveError = data.message ?? 'Something went wrong. Please try again.';
                }
            } catch (e) {
                this.saveError = 'Network error. Please try again.';
            } finally {
                this.saving = false;
            }
        },

        /* ── Helpers ─────────────────────────────────────────────── */
        formatXp(n) {
            return n.toLocaleString();
        },
    }"
    class="py-8 space-y-6"
>

    {{-- ============================================================ --}}
    {{-- Top layout: two columns on lg+, stacked on mobile            --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Left column: Party + Monster search ───────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ── Party Builder ─────────────────────────────────── --}}
            <section
                class="bg-surface border border-border rounded-xl p-5"
                aria-labelledby="party-heading"
            >
                <h2 id="party-heading" class="text-base font-semibold text-text mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-accent shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                    Party
                    <span class="text-xs font-normal text-muted ml-1"
                          x-text="party.length ? '(' + party.length + ' character' + (party.length !== 1 ? 's' : '') + ')' : ''">
                    </span>
                </h2>

                {{-- Add character row --}}
                <div class="flex items-end gap-3 mb-4">
                    <div class="flex-1">
                        <label for="char-level" class="block text-xs font-medium text-muted mb-1">
                            Character Level
                        </label>
                        <input
                            id="char-level"
                            type="number"
                            x-model.number="newLevel"
                            min="1"
                            max="20"
                            @keydown.enter.prevent="addCharacter()"
                            class="w-full px-3 py-2 rounded-lg border border-border bg-bg text-text text-sm
                                   focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent"
                        >
                    </div>
                    <button
                        type="button"
                        @click="addCharacter()"
                        class="px-4 py-2 rounded-lg bg-accent text-on-accent text-sm font-semibold
                               hover:bg-accent-hover transition-colors duration-150
                               focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2"
                    >
                        Add
                    </button>
                </div>

                {{-- Character list --}}
                <template x-if="party.length === 0">
                    <p class="text-sm text-muted italic">No characters added yet.</p>
                </template>

                <ul x-show="party.length > 0" class="flex flex-wrap gap-2" role="list" aria-label="Party members">
                    <template x-for="(level, index) in party" :key="index">
                        <li class="flex items-center gap-1.5 bg-hover border border-border rounded-lg px-3 py-1.5">
                            <span class="text-sm font-medium text-text" x-text="'Level ' + level"></span>
                            <button
                                type="button"
                                @click="removeCharacter(index)"
                                class="text-muted hover:text-red-400 transition-colors duration-100
                                       focus:outline-none focus:text-red-400"
                                :aria-label="'Remove level ' + level + ' character'"
                            >
                                <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </li>
                    </template>
                </ul>
            </section>

            {{-- ── Monster Search ──────────────────────────────────── --}}
            <section
                class="bg-surface border border-border rounded-xl p-5"
                aria-labelledby="monster-heading"
            >
                <h2 id="monster-heading" class="text-base font-semibold text-text mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-accent shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    Add Monsters
                </h2>

                {{-- Search + CR filters --}}
                <div class="flex flex-col sm:flex-row gap-3 mb-3">
                    <div class="flex-1 relative">
                        <label for="monster-search" class="sr-only">Search monsters by name</label>
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <input
                            id="monster-search"
                            type="search"
                            x-model="searchQuery"
                            @input="triggerSearch()"
                            placeholder="Search monsters…"
                            autocomplete="off"
                            aria-controls="monster-results"
                            aria-autocomplete="list"
                            class="w-full pl-9 pr-3 py-2 rounded-lg border border-border bg-bg text-text text-sm
                                   focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent"
                        >
                    </div>

                    {{-- CR range --}}
                    <div class="flex items-center gap-2 shrink-0">
                        <label class="text-xs text-muted whitespace-nowrap">CR</label>
                        <input
                            type="number"
                            x-model="crMin"
                            @input="triggerSearch()"
                            placeholder="Min"
                            min="0"
                            step="0.125"
                            aria-label="Minimum CR filter"
                            class="w-20 px-2 py-2 rounded-lg border border-border bg-bg text-text text-sm
                                   focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent"
                        >
                        <span class="text-muted text-xs">–</span>
                        <input
                            type="number"
                            x-model="crMax"
                            @input="triggerSearch()"
                            placeholder="Max"
                            min="0"
                            step="1"
                            aria-label="Maximum CR filter"
                            class="w-20 px-2 py-2 rounded-lg border border-border bg-bg text-text text-sm
                                   focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent"
                        >
                    </div>
                </div>

                {{-- Search results --}}
                <div
                    id="monster-results"
                    x-show="searchResults.length > 0 || searching"
                    x-cloak
                    class="mb-3 border border-border rounded-lg overflow-hidden"
                    role="listbox"
                    aria-label="Monster search results"
                >
                    <template x-if="searching">
                        <div class="px-4 py-3 text-sm text-muted" role="status" aria-live="polite">
                            Searching…
                        </div>
                    </template>

                    <template x-for="creature in searchResults" :key="creature.id">
                        <button
                            type="button"
                            @click="addMonster(creature)"
                            class="w-full flex items-center justify-between px-4 py-2.5 hover:bg-hover
                                   text-left transition-colors duration-100
                                   focus:outline-none focus:bg-hover border-b border-border last:border-0"
                            role="option"
                        >
                            <span class="flex items-center gap-2 min-w-0">
                                <span class="text-sm font-medium text-text truncate" x-text="creature.name"></span>
                                <template x-if="creature.source === 'custom_creature'">
                                    <span class="shrink-0 text-xs bg-accent/10 text-accent px-1.5 py-0.5 rounded font-medium">
                                        Custom
                                    </span>
                                </template>
                            </span>
                            <span class="shrink-0 ml-3 text-right">
                                <span class="text-xs font-semibold text-text" x-text="'CR ' + creature.cr"></span>
                                <span class="text-xs text-muted ml-1.5" x-text="formatXp(creature.xp) + ' XP'"></span>
                            </span>
                        </button>
                    </template>
                </div>

                {{-- Manual entry toggle --}}
                <div>
                    <button
                        type="button"
                        @click="showManualForm = !showManualForm"
                        class="text-xs text-muted hover:text-accent transition-colors duration-100
                               focus:outline-none focus:text-accent flex items-center gap-1"
                        :aria-expanded="showManualForm"
                    >
                        <svg class="w-3.5 h-3.5 transition-transform duration-150"
                             :class="showManualForm ? 'rotate-45' : ''"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Add homebrew / custom monster manually
                    </button>

                    <div x-show="showManualForm" x-cloak class="mt-3 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="sm:col-span-1">
                                <label for="manual-name" class="block text-xs font-medium text-muted mb-1">
                                    Monster Name <span class="text-red-400" aria-hidden="true">*</span>
                                </label>
                                <input
                                    id="manual-name"
                                    type="text"
                                    x-model="manualName"
                                    placeholder="e.g. Shadow Drake"
                                    class="w-full px-3 py-2 rounded-lg border border-border bg-bg text-text text-sm
                                           focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent"
                                >
                            </div>
                            <div>
                                <label for="manual-cr" class="block text-xs font-medium text-muted mb-1">
                                    CR (optional)
                                </label>
                                <input
                                    id="manual-cr"
                                    type="text"
                                    x-model="manualCr"
                                    placeholder="e.g. 5 or 1/2"
                                    class="w-full px-3 py-2 rounded-lg border border-border bg-bg text-text text-sm
                                           focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent"
                                >
                            </div>
                            <div>
                                <label for="manual-xp" class="block text-xs font-medium text-muted mb-1">
                                    XP Value <span class="text-red-400" aria-hidden="true">*</span>
                                </label>
                                <input
                                    id="manual-xp"
                                    type="number"
                                    x-model="manualXp"
                                    placeholder="e.g. 1800"
                                    min="0"
                                    class="w-full px-3 py-2 rounded-lg border border-border bg-bg text-text text-sm
                                           focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent"
                                >
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="addManualMonster()"
                            :disabled="!manualName.trim() || manualXp === ''"
                            class="px-4 py-2 rounded-lg bg-accent text-on-accent text-sm font-semibold
                                   hover:bg-accent-hover transition-colors duration-150
                                   focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2
                                   disabled:opacity-40 disabled:cursor-not-allowed"
                        >
                            Add to Encounter
                        </button>
                    </div>
                </div>
            </section>

            {{-- ── Encounter Monster List ────────────────────────── --}}
            <section
                x-show="monsters.length > 0"
                x-cloak
                class="bg-surface border border-border rounded-xl p-5"
                aria-labelledby="encounter-list-heading"
            >
                <h2 id="encounter-list-heading" class="text-base font-semibold text-text mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-accent shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    Encounter Monsters
                </h2>

                <ul class="space-y-2" role="list" aria-label="Monsters in this encounter">
                    <template x-for="(monster, index) in monsters" :key="index">
                        <li class="flex items-center gap-3 py-2 border-b border-border last:border-0">
                            {{-- Quantity stepper --}}
                            <div class="flex items-center gap-1 shrink-0" role="group" :aria-label="'Quantity for ' + monster.name">
                                <button
                                    type="button"
                                    @click="setQuantity(index, -1)"
                                    class="w-6 h-6 rounded flex items-center justify-center
                                           text-muted hover:text-text hover:bg-hover
                                           focus:outline-none focus:ring-1 focus:ring-accent
                                           transition-colors duration-100"
                                    :aria-label="'Decrease quantity of ' + monster.name"
                                >
                                    <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                    </svg>
                                </button>
                                <span class="w-6 text-center text-sm font-semibold text-text tabular-nums"
                                      x-text="monster.quantity"
                                      :aria-label="monster.quantity + ' of ' + monster.name"></span>
                                <button
                                    type="button"
                                    @click="setQuantity(index, 1)"
                                    class="w-6 h-6 rounded flex items-center justify-center
                                           text-muted hover:text-text hover:bg-hover
                                           focus:outline-none focus:ring-1 focus:ring-accent
                                           transition-colors duration-100"
                                    :aria-label="'Increase quantity of ' + monster.name"
                                >
                                    <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Name + tags --}}
                            <div class="flex-1 min-w-0">
                                <span class="text-sm font-medium text-text" x-text="monster.name"></span>
                                <template x-if="monster.source === 'manual'">
                                    <span class="ml-1.5 text-xs text-muted italic">homebrew</span>
                                </template>
                                <template x-if="monster.source === 'custom_creature'">
                                    <span class="ml-1.5 text-xs text-accent font-medium">custom</span>
                                </template>
                            </div>

                            {{-- CR + XP --}}
                            <div class="shrink-0 text-right">
                                <span class="text-xs font-semibold text-text"
                                      x-text="monster.cr ? 'CR ' + monster.cr : '—'"></span>
                                <span class="text-xs text-muted ml-1.5"
                                      x-text="formatXp(monster.xp * monster.quantity) + ' XP'"></span>
                            </div>

                            {{-- Remove --}}
                            <button
                                type="button"
                                @click="removeMonster(index)"
                                class="shrink-0 text-muted hover:text-red-400 transition-colors duration-100
                                       focus:outline-none focus:text-red-400"
                                :aria-label="'Remove ' + monster.name + ' from encounter'"
                            >
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </li>
                    </template>
                </ul>
            </section>

        </div>{{-- /left column --}}

        {{-- ── Right column: Difficulty Results ───────────────────── --}}
        <div class="lg:col-span-1">
            <div class="lg:sticky lg:top-8 space-y-4">

                {{-- Difficulty card --}}
                <section
                    class="border rounded-xl p-5 transition-colors duration-300"
                    :class="difficulty ? difficultyBg : 'bg-surface border-border'"
                    aria-labelledby="difficulty-heading"
                    aria-live="polite"
                    aria-atomic="true"
                >
                    <h2 id="difficulty-heading" class="text-base font-semibold text-text mb-4">
                        Difficulty
                    </h2>

                    {{-- Placeholder when no data yet --}}
                    <template x-if="!difficulty">
                        <p class="text-sm text-muted italic">
                            Add party members and monsters to calculate difficulty.
                        </p>
                    </template>

                    <template x-if="difficulty">
                        <div class="space-y-4">

                            {{-- Big difficulty label --}}
                            <div class="text-center py-2">
                                <span
                                    class="text-4xl font-extrabold tracking-tight"
                                    :class="difficultyColor"
                                    x-text="difficultyLabel"
                                ></span>
                            </div>

                            {{-- Progress bar within current bracket --}}
                            <div class="space-y-1">
                                <div class="flex justify-between text-xs text-muted">
                                    <span>Progress within bracket</span>
                                    <span x-text="difficultyProgress + '%'"></span>
                                </div>
                                <div class="h-2 rounded-full bg-hover overflow-hidden" role="presentation">
                                    <div
                                        class="h-full rounded-full transition-all duration-300"
                                        :class="{
                                            'bg-gray-400':   difficulty === 'trivial',
                                            'bg-green-400':  difficulty === 'easy',
                                            'bg-yellow-400': difficulty === 'medium',
                                            'bg-orange-400': difficulty === 'hard',
                                            'bg-red-500':    difficulty === 'deadly',
                                        }"
                                        :style="'width: ' + difficultyProgress + '%'"
                                    ></div>
                                </div>
                            </div>

                            {{-- XP breakdown --}}
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-muted">Monster count</dt>
                                    <dd class="font-semibold text-text tabular-nums" x-text="monsterCount"></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-muted">Raw XP</dt>
                                    <dd class="font-semibold text-text tabular-nums" x-text="formatXp(totalXp)"></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-muted">Multiplier</dt>
                                    <dd class="font-semibold text-text tabular-nums" x-text="'×' + multiplier"></dd>
                                </div>
                                <div class="flex justify-between border-t border-border pt-2 mt-2">
                                    <dt class="font-semibold text-text">Adjusted XP</dt>
                                    <dd class="font-bold text-text tabular-nums" x-text="formatXp(adjustedXp)"></dd>
                                </div>
                            </dl>
                        </div>
                    </template>
                </section>

                {{-- XP Thresholds breakdown --}}
                <section
                    x-show="party.length > 0"
                    x-cloak
                    class="bg-surface border border-border rounded-xl p-5"
                    aria-labelledby="thresholds-heading"
                >
                    <h2 id="thresholds-heading" class="text-sm font-semibold text-text mb-3">
                        Party Thresholds
                    </h2>
                    <dl class="space-y-2 text-sm">
                        <template x-for="(label, i) in ['Easy', 'Medium', 'Hard', 'Deadly']" :key="label">
                            <div class="flex items-center justify-between">
                                <dt class="flex items-center gap-2">
                                    <span
                                        class="w-2 h-2 rounded-full shrink-0"
                                        :class="{
                                            'bg-green-400':  i === 0,
                                            'bg-yellow-400': i === 1,
                                            'bg-orange-400': i === 2,
                                            'bg-red-500':    i === 3,
                                        }"
                                        aria-hidden="true"
                                    ></span>
                                    <span class="text-muted" x-text="label"></span>
                                </dt>
                                <dd class="font-semibold text-text tabular-nums"
                                    x-text="formatXp(partyThresholds[i]) + ' XP'"></dd>
                            </div>
                        </template>
                    </dl>
                </section>

                {{-- Save encounter (auth only) --}}
                @auth
                <section
                    x-show="difficulty && party.length > 0 && monsters.length > 0"
                    x-cloak
                    class="bg-surface border border-border rounded-xl p-5"
                    aria-labelledby="save-heading"
                >
                    <h2 id="save-heading" class="text-sm font-semibold text-text mb-3">
                        Save Encounter
                    </h2>

                    {{-- Success message --}}
                    <div
                        x-show="saveSuccess"
                        x-cloak
                        class="mb-3 px-3 py-2 rounded-lg bg-green-500/10 border border-green-500/30 text-green-400 text-sm"
                        role="status"
                        aria-live="polite"
                    >
                        Encounter saved successfully!
                    </div>

                    {{-- Error message --}}
                    <div
                        x-show="saveError"
                        x-cloak
                        class="mb-3 px-3 py-2 rounded-lg bg-red-500/10 border border-red-500/30 text-red-400 text-sm"
                        role="alert"
                        x-text="saveError"
                    ></div>

                    <div x-show="!showSaveForm" x-cloak>
                        <button
                            type="button"
                            @click="showSaveForm = true"
                            class="w-full px-4 py-2 rounded-lg bg-accent text-on-accent text-sm font-semibold
                                   hover:bg-accent-hover transition-colors duration-150
                                   focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2"
                        >
                            Save This Encounter
                        </button>
                    </div>

                    <div x-show="showSaveForm" x-cloak class="space-y-3">
                        <div>
                            <label for="save-name" class="block text-xs font-medium text-muted mb-1">
                                Encounter Name <span class="text-muted font-normal">(optional)</span>
                            </label>
                            <input
                                id="save-name"
                                type="text"
                                x-model="saveName"
                                placeholder="e.g. Goblin Ambush"
                                maxlength="100"
                                @keydown.enter.prevent="saveEncounter()"
                                class="w-full px-3 py-2 rounded-lg border border-border bg-bg text-text text-sm
                                       focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent"
                            >
                        </div>
                        <div class="flex gap-2">
                            <button
                                type="button"
                                @click="saveEncounter()"
                                :disabled="saving"
                                class="flex-1 px-4 py-2 rounded-lg bg-accent text-on-accent text-sm font-semibold
                                       hover:bg-accent-hover transition-colors duration-150
                                       focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2
                                       disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span x-show="!saving">Save</span>
                                <span x-show="saving" x-cloak>Saving…</span>
                            </button>
                            <button
                                type="button"
                                @click="showSaveForm = false; saveName = ''; saveError = ''"
                                class="px-4 py-2 rounded-lg border border-border text-muted text-sm
                                       hover:text-text hover:bg-hover transition-colors duration-150
                                       focus:outline-none focus:ring-2 focus:ring-accent"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </section>
                @endauth

                {{-- Unauthenticated save prompt --}}
                @guest
                <section
                    x-show="difficulty && party.length > 0 && monsters.length > 0"
                    x-cloak
                    class="bg-surface border border-border rounded-xl p-5 text-center"
                >
                    <p class="text-sm text-muted mb-3">
                        <a href="{{ route('login') }}"
                           class="text-accent hover:underline focus:outline-none focus:underline">Sign in</a>
                        to save this encounter for later.
                    </p>
                </section>
                @endguest

            </div>{{-- /sticky wrapper --}}
        </div>{{-- /right column --}}

    </div>{{-- /grid --}}

</div>{{-- /Alpine --}}

@endsection
