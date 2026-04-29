@extends('layouts.app')

@section('content')
<div class="pb-12">

    {{-- Page header --}}
    <div class="py-6">
        <h1 class="text-2xl font-semibold text-text">Encounter Calculator</h1>
        <p class="mt-1 text-sm text-muted">
            Build your party, choose a target difficulty, and let the generator suggest balanced encounter compositions.
        </p>
    </div>

    {{-- ─────────────────────────────────────────────────────────────────────── --}}
    {{-- Alpine root component — all state lives here.                          --}}
    {{-- ─────────────────────────────────────────────────────────────────────── --}}
    <div
        x-data="encounterCalculator()"
        x-init="init()"
        class="space-y-6"
    >

        {{-- ── Row 1: Party Builder + Parameters ─────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- ── Party Builder ──────────────────────────────────────────────── --}}
            <section
                aria-labelledby="party-heading"
                class="bg-surface border border-border rounded-lg p-6"
            >
                <h2 id="party-heading" class="text-lg font-semibold text-text mb-4">
                    Party
                </h2>

                {{-- Add member row --}}
                <div class="flex items-end gap-3 mb-4">
                    <div class="flex-1">
                        <label for="new-level" class="block text-sm font-medium text-text mb-1">
                            Character Level
                        </label>
                        <select
                            id="new-level"
                            x-model.number="newLevel"
                            class="block w-full rounded-md border border-border bg-bg text-text text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent"
                        >
                            @for ($lvl = 1; $lvl <= 20; $lvl++)
                                <option value="{{ $lvl }}">Level {{ $lvl }}</option>
                            @endfor
                        </select>
                    </div>
                    <button
                        type="button"
                        @click="addPartyMember()"
                        class="px-4 py-2 bg-accent text-on-accent text-sm font-medium rounded-md hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 transition-colors"
                    >
                        Add
                    </button>
                </div>

                {{-- Party chips --}}
                <div
                    class="flex flex-wrap gap-2 min-h-[2.5rem]"
                    role="list"
                    aria-label="Party members"
                >
                    <template x-if="party.length === 0">
                        <p class="text-sm text-muted italic">No party members yet. Add at least one to continue.</p>
                    </template>
                    <template x-for="(level, index) in party" :key="index">
                        <span
                            role="listitem"
                            class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-hover text-accent border border-border"
                        >
                            <span x-text="'Level ' + level"></span>
                            <button
                                type="button"
                                @click="removePartyMember(index)"
                                class="ml-1 text-muted hover:text-text focus:outline-none focus:ring-1 focus:ring-accent rounded-full"
                                :aria-label="'Remove level ' + level + ' character'"
                            >
                                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/>
                                </svg>
                            </button>
                        </span>
                    </template>
                </div>

                {{-- Summary --}}
                <p
                    class="mt-3 text-sm text-muted"
                    x-show="party.length > 0"
                    x-text="party.length + ' player' + (party.length !== 1 ? 's' : '') + ' · avg level ' + avgLevel"
                    aria-live="polite"
                ></p>
            </section>

            {{-- ── Parameters ──────────────────────────────────────────────────── --}}
            <section
                aria-labelledby="params-heading"
                class="bg-surface border border-border rounded-lg p-6"
            >
                <h2 id="params-heading" class="text-lg font-semibold text-text mb-4">
                    Parameters
                </h2>

                {{-- Difficulty radios --}}
                <fieldset class="mb-5">
                    <legend class="block text-sm font-medium text-text mb-2">
                        Target Difficulty
                    </legend>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach (['easy' => 'Easy', 'medium' => 'Medium', 'hard' => 'Hard', 'deadly' => 'Deadly'] as $val => $label)
                            <label class="relative flex cursor-pointer">
                                <input
                                    type="radio"
                                    name="difficulty"
                                    value="{{ $val }}"
                                    x-model="difficulty"
                                    class="sr-only peer"
                                >
                                <span class="w-full text-center px-3 py-2 rounded-md border text-sm font-medium transition-colors
                                    border-border text-muted hover:bg-hover
                                    peer-focus:ring-2 peer-focus:ring-accent peer-focus:ring-offset-1
                                    peer-checked:bg-accent peer-checked:text-on-accent peer-checked:border-accent">
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                {{-- Optional XP minimum --}}
                <div class="mb-5">
                    <label for="xp-min" class="block text-sm font-medium text-text mb-1">
                        Minimum XP Award
                        <span class="font-normal text-muted">(optional — for XP-based leveling)</span>
                    </label>
                    <input
                        id="xp-min"
                        type="number"
                        min="0"
                        step="50"
                        x-model.number="xpMin"
                        placeholder="Leave blank if using milestone leveling"
                        class="block w-full rounded-md border border-border bg-bg text-text text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent"
                    >
                </div>

                {{-- Creature type chips --}}
                <div>
                    <p class="block text-sm font-medium text-text mb-2">
                        Creature Types
                        <span class="font-normal text-muted">(optional — select any to filter)</span>
                    </p>
                    <div class="flex flex-wrap gap-2" role="group" aria-label="Creature type filters">
                        @foreach ($creatureTypes as $type)
                            <label class="relative flex cursor-pointer">
                                <input
                                    type="checkbox"
                                    value="{{ $type->id }}"
                                    @change="toggleType({{ $type->id }})"
                                    :checked="selectedTypes.includes({{ $type->id }})"
                                    class="sr-only peer"
                                >
                                <span class="px-3 py-1 rounded-full border text-sm font-medium transition-colors
                                    border-border text-muted hover:bg-hover
                                    peer-focus:ring-2 peer-focus:ring-accent peer-focus:ring-offset-1
                                    peer-checked:bg-hover peer-checked:border-accent peer-checked:text-accent">
                                    {{ $type->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>

        {{-- ── Suggest Button ──────────────────────────────────────────────────── --}}
        <div class="flex justify-center">
            <button
                type="button"
                @click="suggestEncounter()"
                :disabled="party.length === 0 || loading"
                class="px-8 py-3 bg-accent text-on-accent font-semibold rounded-lg
                    hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2
                    disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
                <span x-show="!loading">Suggest Encounter</span>
                <span x-show="loading" class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    Generating…
                </span>
            </button>
        </div>

        {{-- ── Error Banner ────────────────────────────────────────────────────── --}}
        <div
            x-show="error"
            x-transition
            class="bg-red-50 border border-red-300 text-red-700 rounded-lg px-5 py-4 text-sm"
            role="alert"
            x-text="error"
        ></div>

        {{-- ── Suggestion Cards ────────────────────────────────────────────────── --}}
        <div x-show="suggestions.length > 0" x-transition>
            <h2 class="text-lg font-semibold text-text mb-1">Suggested Encounters</h2>
            <p class="text-sm text-muted mb-4">
                Choose one to load it into the builder. Selecting a suggestion replaces the current encounter.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <template x-for="(combo, i) in suggestions" :key="i">
                    <article
                        class="bg-surface border-2 rounded-lg transition-colors"
                        :class="selectedSuggestion === i
                            ? 'border-accent'
                            : 'border-border hover:border-accent'"
                    >
                        <div class="p-5">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="font-semibold text-text" x-text="combo.style"></h3>
                                <span
                                    class="text-xs font-semibold uppercase tracking-wide px-2 py-0.5 rounded"
                                    :class="difficultyBadgeClass(combo.difficulty)"
                                    x-text="combo.difficulty"
                                ></span>
                            </div>
                            <p class="text-xs text-muted mb-3" x-text="combo.description"></p>

                            {{-- Monster list --}}
                            <ul class="space-y-1 mb-4" aria-label="Monsters in this suggestion">
                                <template x-for="(m, mi) in combo.monsters" :key="mi">
                                    <li class="flex justify-between text-sm text-text">
                                        <span>
                                            <span x-text="m.quantity"></span>×
                                            <span x-text="m.name"></span>
                                        </span>
                                        <span class="text-muted text-xs self-center">
                                            CR <span x-text="m.cr"></span>
                                        </span>
                                    </li>
                                </template>
                            </ul>

                            {{-- XP summary --}}
                            <dl class="grid grid-cols-2 gap-x-2 gap-y-0.5 text-xs text-text mb-4">
                                <dt class="text-muted">Raw XP</dt>
                                <dd x-text="combo.total_xp.toLocaleString()"></dd>
                                <dt class="text-muted">Adjusted XP</dt>
                                <dd x-text="combo.adjusted_xp.toLocaleString()"></dd>
                                <dt class="text-muted">Multiplier</dt>
                                <dd x-text="'×' + combo.multiplier"></dd>
                            </dl>

                            <button
                                type="button"
                                @click="selectSuggestion(i)"
                                class="w-full py-2 px-4 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-1 transition-colors"
                                :class="selectedSuggestion === i
                                    ? 'bg-accent text-on-accent hover:bg-accent-hover'
                                    : 'bg-hover text-text hover:bg-border'"
                                x-text="selectedSuggestion === i ? 'Selected' : 'Select This'"
                            ></button>
                        </div>
                    </article>
                </template>
            </div>
        </div>

        {{-- ── No Results Notice ───────────────────────────────────────────────── --}}
        <div
            x-show="noResults"
            x-transition
            class="bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-lg px-5 py-4 text-sm"
            role="status"
        >
            No matching creatures were found for this combination of difficulty, party, and type filters.
            Try broadening your creature type selection or adjusting the difficulty.
        </div>

        {{-- ── Builder + Difficulty Panel ─────────────────────────────────────── --}}
        <div
            x-show="encounter.length > 0"
            x-transition
            class="grid grid-cols-1 lg:grid-cols-3 gap-6"
        >
            {{-- ── Monster List ──────────────────────────────────────────────── --}}
            <section
                aria-labelledby="builder-heading"
                class="lg:col-span-2 bg-surface border border-border rounded-lg p-6"
            >
                <div class="flex items-center justify-between mb-4">
                    <h2 id="builder-heading" class="text-lg font-semibold text-text">
                        Encounter Builder
                    </h2>
                    <button
                        type="button"
                        @click="clearEncounter()"
                        class="text-sm text-red-500 hover:text-red-700 focus:outline-none focus:ring-1 focus:ring-red-500 rounded"
                    >
                        Clear
                    </button>
                </div>

                <ul class="divide-y divide-border" aria-label="Monsters in encounter">
                    <template x-for="(m, idx) in encounter" :key="idx">
                        <li class="py-3 flex items-center justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-text truncate" x-text="m.name"></p>
                                <p class="text-xs text-muted">
                                    CR <span x-text="m.cr"></span>
                                    · <span x-text="m.xp.toLocaleString()"></span> XP each
                                    · <span
                                        class="uppercase tracking-wide font-medium"
                                        :class="sourceClass(m.source)"
                                        x-text="m.source === 'srd' ? 'SRD' : m.source === 'custom_creature' ? 'Custom' : 'Manual'"
                                    ></span>
                                </p>
                            </div>

                            {{-- Quantity stepper --}}
                            <div class="flex items-center gap-1" role="group" :aria-label="'Quantity of ' + m.name">
                                <button
                                    type="button"
                                    @click="decrement(idx)"
                                    class="w-7 h-7 flex items-center justify-center rounded-md border border-border text-muted hover:bg-hover focus:outline-none focus:ring-1 focus:ring-accent"
                                    :aria-label="'Decrease quantity of ' + m.name"
                                >−</button>
                                <span class="w-8 text-center text-sm font-medium text-text" x-text="m.quantity"></span>
                                <button
                                    type="button"
                                    @click="increment(idx)"
                                    class="w-7 h-7 flex items-center justify-center rounded-md border border-border text-muted hover:bg-hover focus:outline-none focus:ring-1 focus:ring-accent"
                                    :aria-label="'Increase quantity of ' + m.name"
                                >+</button>
                            </div>

                            <button
                                type="button"
                                @click="removeMonster(idx)"
                                class="text-red-400 hover:text-red-600 focus:outline-none focus:ring-1 focus:ring-red-400 rounded"
                                :aria-label="'Remove ' + m.name + ' from encounter'"
                            >
                                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C9.327 4.025 10.16 4 11 4h-1Zm-5.5 5.25a.75.75 0 0 1 .75-.75h9.5a.75.75 0 0 1 0 1.5h-9.5a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </li>
                    </template>
                </ul>

                {{-- Candidate filter --}}
                <div class="mt-4 pt-4 border-t border-border">
                    <label for="candidate-search" class="block text-sm font-medium text-text mb-2">
                        Add from Candidates
                    </label>
                    <input
                        id="candidate-search"
                        type="search"
                        x-model="candidateSearch"
                        placeholder="Filter candidates by name…"
                        class="block w-full rounded-md border border-border bg-bg text-text text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent"
                        aria-controls="candidate-list"
                    >
                </div>
            </section>

            {{-- ── Live Difficulty Panel ────────────────────────────────────── --}}
            <section
                aria-labelledby="difficulty-heading"
                aria-live="polite"
                aria-atomic="true"
                class="bg-surface border border-border rounded-lg p-6 self-start lg:sticky lg:top-6"
            >
                <h2 id="difficulty-heading" class="text-lg font-semibold text-text mb-4">
                    Difficulty
                </h2>

                {{-- Current difficulty label --}}
                <div class="flex items-center justify-between mb-4">
                    <span
                        class="text-2xl font-bold capitalize"
                        :class="difficultyTextClass(currentDifficulty)"
                        x-text="currentDifficulty"
                    ></span>
                    <span
                        class="text-xs font-semibold uppercase tracking-wide px-2 py-1 rounded"
                        :class="difficultyBadgeClass(currentDifficulty)"
                        x-text="currentDifficulty"
                    ></span>
                </div>

                {{-- XP breakdown --}}
                <dl class="space-y-2 text-sm mb-4">
                    <div class="flex justify-between">
                        <dt class="text-muted">Monsters (raw)</dt>
                        <dd class="font-medium text-text" x-text="totalXp.toLocaleString() + ' XP'"></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-muted">Multiplier</dt>
                        <dd class="font-medium text-text" x-text="'×' + currentMultiplier"></dd>
                    </div>
                    <div class="flex justify-between border-t border-border pt-2">
                        <dt class="font-medium text-text">Adjusted XP</dt>
                        <dd class="font-bold text-text" x-text="adjustedXp.toLocaleString() + ' XP'"></dd>
                    </div>
                </dl>

                {{-- Threshold progress bars --}}
                <div class="space-y-2" aria-label="XP thresholds">
                    <template x-for="tier in ['easy','medium','hard','deadly']" :key="tier">
                        <div>
                            <div class="flex justify-between text-xs text-muted mb-0.5">
                                <span x-text="tier.charAt(0).toUpperCase() + tier.slice(1)"></span>
                                <span x-text="(target && target.thresholds ? target.thresholds[tier] : 0).toLocaleString()"></span>
                            </div>
                            <div class="h-1.5 rounded-full bg-border overflow-hidden">
                                <div
                                    class="h-full rounded-full transition-all duration-300"
                                    :class="thresholdBarClass(tier)"
                                    :style="'width:' + thresholdProgress(tier) + '%'"
                                    role="progressbar"
                                    :aria-valuenow="adjustedXp"
                                    :aria-valuemin="0"
                                    :aria-valuemax="target && target.thresholds ? target.thresholds[tier] : 1"
                                    :aria-label="tier + ' threshold progress'"
                                ></div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Monster count note --}}
                <p class="text-xs text-muted mt-3">
                    <span x-text="monsterCount"></span> monster<span x-show="monsterCount !== 1">s</span>
                    · Party of <span x-text="party.length"></span>
                    · Raw XP: <span x-text="totalXp.toLocaleString()"></span>
                </p>

                {{-- Save (auth only) --}}
                @auth
                <div class="mt-5 pt-4 border-t border-border">
                    <label for="encounter-name" class="block text-sm font-medium text-text mb-1">
                        Encounter Name <span class="font-normal text-muted">(optional)</span>
                    </label>
                    <input
                        id="encounter-name"
                        type="text"
                        x-model="encounterName"
                        placeholder="e.g. Goblin Ambush"
                        maxlength="100"
                        class="block w-full rounded-md border border-border bg-bg text-text text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent mb-3"
                    >
                    <button
                        type="button"
                        @click="saveEncounter()"
                        :disabled="encounter.length === 0 || saving"
                        class="w-full py-2 px-4 bg-green-600 text-white text-sm font-medium rounded-md
                            hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1
                            disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <span x-show="!saving">Save Encounter</span>
                        <span x-show="saving">Saving…</span>
                    </button>
                    <p
                        x-show="saveSuccess"
                        x-transition
                        class="mt-2 text-sm text-green-600 text-center"
                        role="status"
                    >Encounter saved!</p>
                    <p
                        x-show="saveError"
                        x-transition
                        class="mt-2 text-sm text-red-600 text-center"
                        role="alert"
                        x-text="saveError"
                    ></p>
                </div>
                @else
                <p class="mt-4 text-xs text-muted text-center">
                    <a
                        href="{{ route('login') }}"
                        class="text-accent hover:underline focus:outline-none focus:ring-1 focus:ring-accent rounded"
                    >Log in</a>
                    to save encounters.
                </p>
                @endauth
            </section>
        </div>

        {{-- ── Candidate List ──────────────────────────────────────────────────── --}}
        <div x-show="candidates.length > 0" x-transition>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold text-text">
                    Candidates
                    <span class="text-sm font-normal text-muted ml-1">— creatures in the CR range for this encounter</span>
                </h2>
                <span class="text-sm text-muted" x-text="filteredCandidates.length + ' shown'"></span>
            </div>

            <div
                id="candidate-list"
                class="bg-surface border border-border rounded-lg overflow-hidden"
                role="list"
                aria-label="Candidate creatures"
            >
                <template x-for="(c, ci) in filteredCandidates" :key="ci">
                    <div
                        role="listitem"
                        class="flex items-center justify-between px-5 py-3 border-b border-border last:border-0 hover:bg-hover"
                    >
                        <div>
                            <span class="text-sm font-medium text-text" x-text="c.name"></span>
                            <span class="ml-2 text-xs text-muted">
                                CR <span x-text="c.cr"></span>
                                · <span x-text="c.xp.toLocaleString()"></span> XP
                            </span>
                        </div>
                        <button
                            type="button"
                            @click="addFromCandidate(c)"
                            class="text-sm text-accent hover:text-accent-hover font-medium focus:outline-none focus:ring-1 focus:ring-accent rounded px-2 py-0.5"
                            :aria-label="'Add ' + c.name + ' to encounter'"
                        >
                            + Add
                        </button>
                    </div>
                </template>
                <p
                    x-show="filteredCandidates.length === 0"
                    class="px-5 py-4 text-sm text-muted italic"
                >No candidates match your filter.</p>
            </div>
        </div>

    </div>{{-- /x-data --}}
</div>
@endsection

@push('scripts')
<script>
function encounterCalculator() {
    return {
        // ── Party ──────────────────────────────────────────────────────────────
        party:    [],
        newLevel: 1,

        // ── Parameters ────────────────────────────────────────────────────────
        difficulty:    'medium',
        xpMin:         null,
        selectedTypes: [],

        // ── UI state ──────────────────────────────────────────────────────────
        loading:   false,
        error:     null,
        noResults: false,

        // ── Suggestion results ─────────────────────────────────────────────────
        suggestions:        [],
        selectedSuggestion: null,
        target:             null,
        candidates:         [],
        candidateSearch:    '',

        // ── Current encounter ──────────────────────────────────────────────────
        encounter:     [],
        encounterName: '',

        // ── Save state ─────────────────────────────────────────────────────────
        saving:      false,
        saveSuccess: false,
        saveError:   null,

        // ── Lifecycle ──────────────────────────────────────────────────────────
        init() {},

        // ── Computed ───────────────────────────────────────────────────────────
        get avgLevel() {
            if (this.party.length === 0) return 0;
            return (this.party.reduce((a, b) => a + b, 0) / this.party.length).toFixed(1);
        },

        get monsterCount() {
            return this.encounter.reduce((sum, m) => sum + m.quantity, 0);
        },

        get totalXp() {
            return this.encounter.reduce((sum, m) => sum + (m.xp * m.quantity), 0);
        },

        get adjustedXp() {
            return Math.round(this.totalXp * this.calcMultiplier(this.monsterCount, this.party.length));
        },

        get currentMultiplier() {
            return this.calcMultiplier(this.monsterCount, this.party.length).toFixed(1);
        },

        get currentDifficulty() {
            if (!this.target || !this.target.thresholds || this.encounter.length === 0) return '—';
            const t  = this.target.thresholds;
            const xp = this.adjustedXp;
            if (xp < t.easy)   return 'trivial';
            if (xp < t.medium) return 'easy';
            if (xp < t.hard)   return 'medium';
            if (xp < t.deadly) return 'hard';
            return 'deadly';
        },

        get filteredCandidates() {
            const q = this.candidateSearch.trim().toLowerCase();
            if (!q) return this.candidates;
            return this.candidates.filter(c => c.name.toLowerCase().includes(q));
        },

        // ── Party management ───────────────────────────────────────────────────
        addPartyMember() {
            if (this.party.length >= 20) return;
            this.party.push(Number(this.newLevel));
        },

        removePartyMember(idx) {
            this.party.splice(idx, 1);
        },

        // ── Type filter ────────────────────────────────────────────────────────
        toggleType(id) {
            const idx = this.selectedTypes.indexOf(id);
            if (idx === -1) this.selectedTypes.push(id);
            else            this.selectedTypes.splice(idx, 1);
        },

        // ── Multiplier (mirrors PHP controller exactly) ────────────────────────
        //
        // The brackets and step-adjustment logic are identical to the server.
        // Keeping them in sync means the live difficulty panel in the builder
        // always agrees with what the server computed for the suggestion cards.
        calcMultiplier(count, partySize) {
            const brackets = [
                [1,  1,         1.0],
                [2,  2,         1.5],
                [3,  6,         2.0],
                [7,  10,        2.5],
                [11, 14,        3.0],
                [15, Infinity,  4.0],
            ];
            const steps = [1.0, 1.5, 2.0, 2.5, 3.0, 4.0];

            let base = 1.0;
            for (const [min, max, mult] of brackets) {
                if (count >= min && count <= max) { base = mult; break; }
            }

            const idx = steps.indexOf(base);
            if      (partySize <= 2 && idx < steps.length - 1) base = steps[idx + 1];
            else if (partySize >= 6 && idx > 0)                 base = steps[idx - 1];

            return base;
        },

        // ── Suggest ────────────────────────────────────────────────────────────
        async suggestEncounter() {
            this.loading            = true;
            this.error              = null;
            this.noResults          = false;
            this.suggestions        = [];
            this.selectedSuggestion = null;

            try {
                const res = await fetch('{{ route('encounter-calculator.suggest') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify({
                        party:      this.party,
                        difficulty: this.difficulty,
                        xp_min:     this.xpMin || null,
                        types:      this.selectedTypes.length > 0 ? this.selectedTypes : null,
                    }),
                });

                if (!res.ok) {
                    const data = await res.json().catch(() => ({}));
                    this.error = data.message || 'Something went wrong. Please try again.';
                    return;
                }

                const data       = await res.json();
                this.target      = data.target;
                this.suggestions = data.suggestions;
                this.candidates  = data.candidates;

                if (this.suggestions.length === 0) this.noResults = true;

            } catch (e) {
                this.error = 'Network error — please check your connection and try again.';
            } finally {
                this.loading = false;
            }
        },

        // ── Select a suggestion ────────────────────────────────────────────────
        selectSuggestion(idx) {
            this.selectedSuggestion = idx;
            // Deep-clone so editing the builder doesn't mutate the suggestion card
            this.encounter     = this.suggestions[idx].monsters.map(m => ({ ...m }));
            this.encounterName = '';
            this.saveSuccess   = false;
            this.saveError     = null;
        },

        // ── Encounter editing ──────────────────────────────────────────────────
        increment(idx) {
            if (this.encounter[idx].quantity < 99) this.encounter[idx].quantity++;
        },

        decrement(idx) {
            if (this.encounter[idx].quantity > 1) this.encounter[idx].quantity--;
            else                                  this.removeMonster(idx);
        },

        removeMonster(idx) {
            this.encounter.splice(idx, 1);
        },

        clearEncounter() {
            this.encounter          = [];
            this.selectedSuggestion = null;
            this.encounterName      = '';
            this.saveSuccess        = false;
            this.saveError          = null;
        },

        addFromCandidate(c) {
            const existing = this.encounter.findIndex(
                m => m.creature_id !== null && m.creature_id === c.creature_id
            );
            if (existing !== -1) this.encounter[existing].quantity++;
            else                  this.encounter.push({ ...c });
        },

        // ── Save ───────────────────────────────────────────────────────────────
        async saveEncounter() {
            this.saving      = true;
            this.saveSuccess = false;
            this.saveError   = null;

            try {
                const res = await fetch('{{ route('encounter-calculator.save') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify({
                        name:        this.encounterName || null,
                        party:       this.party,
                        monsters:    this.encounter,
                        total_xp:    this.totalXp,
                        adjusted_xp: this.adjustedXp,
                        difficulty:  this.currentDifficulty === '—' ? 'trivial' : this.currentDifficulty,
                    }),
                });

                const data = await res.json().catch(() => ({}));

                if (!res.ok) {
                    this.saveError = data.message || 'Save failed. Please try again.';
                    return;
                }

                this.saveSuccess = true;

            } catch (e) {
                this.saveError = 'Network error — could not save.';
            } finally {
                this.saving = false;
            }
        },

        // ── Threshold progress ─────────────────────────────────────────────────
        thresholdProgress(tier) {
            if (!this.target || !this.target.thresholds) return 0;
            const max = this.target.thresholds[tier];
            if (!max) return 0;
            return Math.min(100, Math.round((this.adjustedXp / max) * 100));
        },

        // ── CSS helpers ────────────────────────────────────────────────────────
        difficultyBadgeClass(d) {
            const map = {
                trivial: 'bg-gray-100 text-gray-600',
                easy:    'bg-green-100 text-green-700',
                medium:  'bg-yellow-100 text-yellow-700',
                hard:    'bg-orange-100 text-orange-700',
                deadly:  'bg-red-100 text-red-700',
                '—':     'bg-gray-100 text-gray-400',
            };
            return map[d] ?? 'bg-gray-100 text-gray-600';
        },

        difficultyTextClass(d) {
            const map = {
                trivial: 'text-gray-500',
                easy:    'text-green-600',
                medium:  'text-yellow-600',
                hard:    'text-orange-600',
                deadly:  'text-red-600',
                '—':     'text-gray-300',
            };
            return map[d] ?? 'text-gray-500';
        },

        thresholdBarClass(tier) {
            const order     = ['trivial', 'easy', 'medium', 'hard', 'deadly'];
            const tierIdx   = order.indexOf(tier);
            const activeIdx = order.indexOf(this.currentDifficulty);

            if (activeIdx > 0 && activeIdx >= tierIdx) {
                const colors = {
                    easy:   'bg-green-400',
                    medium: 'bg-yellow-400',
                    hard:   'bg-orange-400',
                    deadly: 'bg-red-500',
                };
                return colors[tier] ?? 'bg-gray-300';
            }
            return 'bg-gray-300';
        },

        sourceClass(source) {
            if (source === 'custom_creature') return 'text-purple-500';
            if (source === 'manual')          return 'text-gray-400';
            return 'text-blue-400';
        },
    };
}
</script>
@endpush
