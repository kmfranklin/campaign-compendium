@extends('layouts.app')

@section('hero')
    <div class="w-full py-16 sm:py-20 text-center relative overflow-hidden"
         style="background: linear-gradient(135deg, #2e1065 0%, #4c1d95 45%, #6d28d9 100%);">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #a78bfa 0%, transparent 70%);"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #a78bfa 0%, transparent 70%);"></div>
        </div>
        <div class="relative z-10 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">Dice Roller</h1>
            <p class="mt-4 text-base sm:text-lg text-violet-200 max-w-xl mx-auto">
                Pick a die, set your quantity and modifier, and roll.
            </p>
        </div>
    </div>
@endsection

@section('content')

{{-- ============================================================ --}}
{{-- Alpine component wraps the entire dice roller                --}}
{{-- ============================================================ --}}
<div
    x-data="{
        die: 20,
        quantity: 1,
        modifier: 0,
        results: [],
        total: null,
        rolledModifier: null,
        rolling: false,
        history: [],

        get rollLabel() {
            let label = this.quantity + 'd' + this.die;
            const mod = parseInt(this.modifier || 0);
            if (mod > 0) label += ' + ' + mod;
            if (mod < 0) label += ' − ' + Math.abs(mod);
            return label;
        },

        roll() {
            if (this.rolling) return;

            const label = this.rollLabel;
            const die   = this.die;
            const qty   = this.quantity;
            const mod   = parseInt(this.modifier || 0);

            this.rolling = true;

            setTimeout(() => {
                const rolls = Array.from({ length: qty }, () =>
                    Math.floor(Math.random() * die) + 1
                );
                const total = rolls.reduce((sum, r) => sum + r, 0) + mod;

                this.results        = rolls;
                this.total          = total;
                this.rolledModifier = mod;

                this.history.unshift({ label, results: [...rolls], total, die });
                if (this.history.length > 10) this.history.pop();

                this.rolling = false;
            }, 450);
        },

        incrementQuantity() { if (this.quantity < 10) this.quantity++; },
        decrementQuantity()  { if (this.quantity > 1)  this.quantity--; },
    }"
    class="py-12 max-w-2xl mx-auto"
>

    {{-- ================================================ --}}
    {{-- Die type selector                                --}}
    {{-- ================================================ --}}
    <section aria-labelledby="die-selector-heading">
        <h2 id="die-selector-heading" class="text-sm font-semibold uppercase tracking-wide text-muted mb-3">
            Choose a Die
        </h2>

        <div class="flex flex-wrap gap-3" role="group" aria-label="Die type selection">
            @foreach ([4, 6, 8, 10, 12, 20, 100] as $sides)
                <button
                    @click="die = {{ $sides }}"
                    :aria-pressed="die === {{ $sides }}"
                    :class="die === {{ $sides }}
                        ? 'bg-accent text-on-accent border-accent shadow-md scale-105'
                        : 'bg-surface text-text border-border hover:border-accent hover:text-accent'"
                    class="w-16 h-16 rounded-xl border-2 text-lg font-bold transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-bg"
                    aria-label="Select d{{ $sides }}"
                >
                    d{{ $sides }}
                </button>
            @endforeach
        </div>
    </section>

    {{-- ================================================ --}}
    {{-- Quantity and modifier controls                   --}}
    {{-- ================================================ --}}
    <section aria-labelledby="controls-heading" class="mt-8">
        <h2 id="controls-heading" class="sr-only">Roll controls</h2>

        <div class="flex flex-col sm:flex-row gap-6 items-start">

            {{-- Quantity stepper --}}
            <div class="flex flex-col gap-2">
                <span class="text-sm font-semibold uppercase tracking-wide text-muted" id="quantity-label">
                    Number of Dice
                </span>
                <div class="flex items-center rounded-lg border border-border overflow-hidden"
                     role="group" aria-labelledby="quantity-label">
                    <button
                        @click="decrementQuantity()"
                        :disabled="quantity <= 1"
                        class="w-11 h-11 flex items-center justify-center bg-surface text-text text-xl font-bold
                               hover:bg-hover disabled:opacity-40 disabled:cursor-not-allowed
                               transition-colors focus:outline-none focus:ring-2 focus:ring-inset focus:ring-accent"
                        aria-label="Decrease number of dice"
                    >−</button>
                    <div
                        class="w-12 h-11 flex items-center justify-center bg-surface border-x border-border
                               text-text font-bold text-xl"
                        aria-live="polite"
                        x-text="quantity"
                    ></div>
                    <button
                        @click="incrementQuantity()"
                        :disabled="quantity >= 10"
                        class="w-11 h-11 flex items-center justify-center bg-surface text-text text-xl font-bold
                               hover:bg-hover disabled:opacity-40 disabled:cursor-not-allowed
                               transition-colors focus:outline-none focus:ring-2 focus:ring-inset focus:ring-accent"
                        aria-label="Increase number of dice"
                    >+</button>
                </div>
            </div>

            {{-- Modifier input --}}
            <div class="flex flex-col gap-2">
                <label for="modifier" class="text-sm font-semibold uppercase tracking-wide text-muted">
                    Modifier
                </label>
                <input
                    id="modifier"
                    type="number"
                    x-model="modifier"
                    class="w-28 h-11 px-3 rounded-lg border border-border bg-surface text-text text-center
                           text-xl font-bold focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent"
                    placeholder="0"
                    aria-describedby="modifier-hint"
                >
                <span id="modifier-hint" class="text-xs text-muted">Use negative values to subtract</span>
            </div>

        </div>
    </section>

    {{-- ================================================ --}}
    {{-- Roll button                                      --}}
    {{-- ================================================ --}}
    <div class="mt-8">
        <button
            @click="roll()"
            :disabled="rolling"
            :aria-label="'Roll ' + rollLabel"
            class="w-full sm:w-auto px-12 py-4 bg-accent text-on-accent font-bold text-xl rounded-xl shadow
                   hover:bg-accent-hover active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed
                   transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-accent
                   focus:ring-offset-2 focus:ring-offset-bg"
        >
            <span x-show="!rolling" x-text="'Roll ' + rollLabel"></span>
            <span x-show="rolling" x-cloak>Rolling…</span>
        </button>
    </div>

    {{-- ================================================ --}}
    {{-- Results display                                  --}}
    {{-- ================================================ --}}
    <section
        aria-labelledby="results-heading"
        aria-live="polite"
        aria-atomic="true"
        class="mt-10"
    >
        <h2 id="results-heading" class="text-sm font-semibold uppercase tracking-wide text-muted mb-3">
            Result
        </h2>

        {{-- Empty state --}}
        <div
            x-show="results.length === 0 && !rolling"
            class="bg-surface border border-border rounded-xl p-10 text-center text-muted"
        >
            <svg class="w-10 h-10 mx-auto mb-3 opacity-30" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
            </svg>
            <p class="text-sm">Select a die and hit Roll to see your results.</p>
        </div>

        {{-- Rolling animation --}}
        <div
            x-show="rolling"
            x-cloak
            class="bg-surface border border-border rounded-xl p-10 text-center"
            aria-hidden="true"
        >
            <div class="inline-flex gap-3">
                <span class="w-10 h-10 rounded-lg bg-accent/30 animate-bounce [animation-delay:0ms]"></span>
                <span class="w-10 h-10 rounded-lg bg-accent/30 animate-bounce [animation-delay:150ms]"></span>
                <span class="w-10 h-10 rounded-lg bg-accent/30 animate-bounce [animation-delay:300ms]"></span>
            </div>
        </div>

        {{-- Results --}}
        <div
            x-show="results.length > 0 && !rolling"
            x-cloak
            class="bg-surface border border-border rounded-xl p-6"
        >
            {{-- Individual die results --}}
            {{-- Max roll = accent highlight, min roll (1) = red, otherwise neutral --}}
            <div class="flex flex-wrap gap-2 mb-5" aria-label="Individual die results">
                <template x-for="(result, index) in results" :key="index">
                    <span
                        :class="{
                            'bg-accent text-on-accent border-accent':          result === die,
                            'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-red-300 dark:border-red-700': result === 1 && die !== 1,
                            'bg-surface text-text border-border':              result !== die && !(result === 1 && die !== 1)
                        }"
                        class="inline-flex items-center justify-center w-12 h-12 rounded-lg border-2 text-lg font-bold"
                        :aria-label="'Die ' + (index + 1) + ': rolled ' + result"
                        x-text="result"
                    ></span>
                </template>
            </div>

            {{-- Modifier line --}}
            <div x-show="rolledModifier !== null && rolledModifier !== 0" class="text-sm text-muted mb-3 flex items-center gap-1">
                <span x-text="rolledModifier > 0 ? '+' + rolledModifier : rolledModifier"></span>
                <span>modifier</span>
            </div>

            {{-- Total --}}
            <div class="border-t border-border pt-4 flex items-baseline gap-3">
                <span class="text-sm font-semibold uppercase tracking-wide text-muted">Total</span>
                <span
                    class="text-5xl font-extrabold text-accent tabular-nums"
                    x-text="total"
                    :aria-label="'Total: ' + total"
                ></span>
            </div>
        </div>
    </section>

    {{-- ================================================ --}}
    {{-- Roll history                                     --}}
    {{-- ================================================ --}}
    <section
        aria-labelledby="history-heading"
        class="mt-10"
        x-show="history.length > 0"
        x-cloak
    >
        <div class="flex items-center justify-between mb-3">
            <h2 id="history-heading" class="text-sm font-semibold uppercase tracking-wide text-muted">
                Roll History
            </h2>
            <button
                @click="history = []"
                class="text-xs text-muted hover:text-text transition-colors focus:outline-none focus:underline"
                aria-label="Clear roll history"
            >
                Clear
            </button>
        </div>

        <ol class="space-y-2" aria-label="Previous rolls, most recent first">
            <template x-for="(entry, index) in history" :key="index">
                <li class="flex items-center justify-between bg-surface border border-border rounded-lg px-4 py-3 text-sm gap-4">
                    <span class="font-semibold text-text shrink-0" x-text="entry.label"></span>
                    <span class="text-muted truncate">
                        [<span x-text="entry.results.join(', ')"></span>]
                    </span>
                    <span class="font-bold text-accent shrink-0" x-text="entry.total"></span>
                </li>
            </template>
        </ol>
    </section>

</div>

@endsection
