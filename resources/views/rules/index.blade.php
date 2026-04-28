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
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">Rules Reference</h1>
            <p class="mt-4 text-base sm:text-lg text-violet-200 max-w-xl mx-auto">
                Searchable SRD rules, conditions, and combat reference.
            </p>
        </div>
    </div>
@endsection

@section('content')

{{-- ============================================================ --}}
{{-- Global cross-section search                                  --}}
{{-- @js() encodes the PHP $searchEntries collection as a JS      --}}
{{-- array — names, section labels, and pre-built anchor URLs.   --}}
{{-- ============================================================ --}}
<div
    x-data="{
        search: '',
        entries: @js($searchEntries),

        get query() { return this.search.toLowerCase().trim(); },

        get results() {
            if (this.query.length < 2) return [];
            return this.entries
                .filter(e => e.name.toLowerCase().includes(this.query)
                          || e.section.toLowerCase().includes(this.query))
                .slice(0, 16);
        },

        get grouped() {
            const groups = {};
            for (const r of this.results) {
                if (!groups[r.section]) groups[r.section] = [];
                groups[r.section].push(r);
            }
            return Object.entries(groups);
        },
    }"
    class="pt-8 pb-4 relative"
>
    {{-- Search input --}}
    <div class="relative max-w-xl mx-auto">
        <label for="global-rules-search" class="sr-only">Search all rules and conditions</label>
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted pointer-events-none"
             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
        <input
            id="global-rules-search"
            type="search"
            x-model="search"
            placeholder="Search rules, conditions, spells… e.g. &ldquo;Strength&rdquo; or &ldquo;grappled&rdquo;"
            class="w-full pl-12 pr-4 py-3.5 rounded-xl border border-border bg-surface text-text shadow-sm
                   text-base focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent"
            autocomplete="off"
            aria-controls="search-results"
            aria-autocomplete="list"
        >

        {{-- Results panel --}}
        <div
            id="search-results"
            x-show="results.length > 0"
            x-cloak
            @click.outside="search = ''"
            class="absolute left-0 right-0 top-full mt-2 bg-surface border border-border rounded-xl
                   shadow-xl overflow-hidden z-50"
            role="listbox"
            aria-label="Search results"
        >
            <template x-for="[section, items] in grouped" :key="section">
                <div>
                    {{-- Section group header --}}
                    <div class="px-4 pt-3 pb-1 text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"
                         x-text="section"></div>

                    {{-- Results within section --}}
                    <template x-for="entry in items" :key="entry.url">
                        <a
                            :href="entry.url"
                            @click="search = ''"
                            class="flex items-center justify-between px-4 py-2.5 hover:bg-hover
                                   text-text text-sm transition-colors duration-100
                                   focus:outline-none focus:bg-hover"
                            role="option"
                        >
                            <span x-text="entry.name" class="font-medium"></span>
                            <svg class="w-3.5 h-3.5 text-muted shrink-0 ml-2" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </template>
                </div>
            </template>

            {{-- Hint when results are capped at 16 --}}
            <div
                x-show="results.length === 16"
                class="px-4 py-2 text-xs text-muted border-t border-border"
            >
                Showing top 16 matches — keep typing to narrow down.
            </div>
        </div>

        {{-- No results --}}
        <div
            x-show="query.length >= 2 && results.length === 0"
            x-cloak
            class="absolute left-0 right-0 top-full mt-2 bg-surface border border-border rounded-xl
                   shadow-xl px-4 py-4 text-sm text-muted text-center z-50"
            role="status"
            aria-live="polite"
        >
            No rules found for "<span x-text="search" class="font-medium text-text"></span>".
        </div>
    </div>
</div>

<div class="py-4">

    {{-- ================================================ --}}
    {{-- Conditions — featured card                       --}}
    {{-- ================================================ --}}
    <section aria-labelledby="conditions-category-heading" class="mb-10">
        <h2 id="conditions-category-heading" class="sr-only">Featured section</h2>

        <a
            href="{{ route('rules.conditions') }}"
            class="group block bg-surface border-2 border-accent rounded-xl p-6 shadow-sm
                   hover:shadow-md hover:border-accent-hover transition-all duration-150
                   focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-bg"
        >
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-accent/10 text-accent shrink-0"
                             aria-hidden="true">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-text group-hover:text-accent transition-colors duration-150">
                            Conditions
                        </h3>
                        <span class="ml-auto text-xs font-semibold text-on-accent bg-accent px-2 py-0.5 rounded-full">
                            {{ $conditionCount }}
                        </span>
                    </div>
                    <p class="text-sm text-muted">
                        Status effects that alter a creature's capabilities — Blinded, Charmed, Grappled, Prone, and more.
                    </p>
                </div>
                <svg class="w-5 h-5 text-muted group-hover:text-accent transition-colors duration-150 shrink-0 mt-1"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </div>
        </a>
    </section>

    {{-- ================================================ --}}
    {{-- Ruleset category grid                            --}}
    {{-- ================================================ --}}
    <section aria-labelledby="categories-heading">
        <h2 id="categories-heading" class="text-sm font-semibold uppercase tracking-wide text-muted mb-4">
            All Sections
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($ruleSets as $ruleSet)
                <a
                    href="{{ route('rules.show', $ruleSet->slug) }}"
                    class="group block bg-surface border border-border rounded-xl p-5 shadow-sm
                           hover:shadow-md hover:border-accent transition-all duration-150
                           focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-bg"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1.5">
                                <h3 class="text-sm font-semibold text-text group-hover:text-accent transition-colors duration-150 truncate">
                                    {{ $ruleSet->name }}
                                </h3>
                                @if ($ruleSet->rules_count > 0)
                                    <span class="shrink-0 text-xs font-medium text-muted bg-hover px-1.5 py-0.5 rounded">
                                        {{ $ruleSet->rules_count }}
                                    </span>
                                @endif
                            </div>

                            @if ($ruleSet->desc_plain)
                                <p class="text-xs text-muted leading-relaxed line-clamp-2">
                                    {{ $ruleSet->desc_plain }}
                                </p>
                            @endif
                        </div>
                        <svg class="w-4 h-4 text-muted group-hover:text-accent transition-colors duration-150 shrink-0 mt-0.5"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

</div>

@endsection
