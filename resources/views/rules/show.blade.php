@extends('layouts.app')

@section('hero')
    <div class="w-full py-12 sm:py-16 text-center relative overflow-hidden"
         style="background: linear-gradient(135deg, #2e1065 0%, #4c1d95 45%, #6d28d9 100%);">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #a78bfa 0%, transparent 70%);"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #a78bfa 0%, transparent 70%);"></div>
        </div>
        <div class="relative z-10 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">{{ $title }}</h1>
        </div>
    </div>
@endsection

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="Breadcrumb" class="pt-6 pb-2">
    <ol class="flex items-center gap-1.5 text-sm text-muted">
        <li>
            <a href="{{ route('rules.index') }}"
               class="hover:text-text transition-colors duration-150 focus:outline-none focus:underline">
                Rules Reference
            </a>
        </li>
        <li aria-hidden="true">
            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
        </li>
        <li aria-current="page" class="text-text font-medium">{{ $title }}</li>
    </ol>
</nav>

{{-- ============================================================ --}}
{{-- Alpine component                                             --}}
{{-- @js() is Laravel's helper to safely JSON-encode PHP data    --}}
{{-- for use in JavaScript — it handles escaping automatically.  --}}
{{-- We embed just the searchable text (id, name, body) here;    --}}
{{-- the rendered HTML is kept in the DOM separately.            --}}
{{-- ============================================================ --}}
<div
    x-data="{
        search: '',
        entries: @js($entries->map(fn($e) => ['id' => $e->id, 'name' => $e->name, 'body' => $e->body])),

        get query() {
            return this.search.toLowerCase().trim();
        },

        /*
         * matchingIds is either null (no search — show everything)
         * or a Set of IDs that match the query.
         * Using a Set makes the isVisible() lookup O(1).
         */
        get matchingIds() {
            if (!this.query) return null;
            return new Set(
                this.entries
                    .filter(e => (e.name + ' ' + e.body).toLowerCase().includes(this.query))
                    .map(e => e.id)
            );
        },

        isVisible(id) {
            return this.matchingIds === null || this.matchingIds.has(id);
        },

        get hasResults() {
            return this.matchingIds === null || this.matchingIds.size > 0;
        },
    }"
    class="flex gap-8 py-8 items-start"
>

    {{-- ================================================ --}}
    {{-- Sidebar                                          --}}
    {{-- ================================================ --}}
    <aside
        class="hidden lg:flex flex-col w-56 xl:w-64 shrink-0 sticky top-8 gap-4"
        aria-label="Rules sections"
    >
        {{-- Search --}}
        <div>
            <label for="rules-search" class="sr-only">Search within {{ $title }}</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input
                    id="rules-search"
                    type="search"
                    x-model="search"
                    placeholder="Search…"
                    class="w-full pl-9 pr-3 py-2 rounded-lg border border-border bg-surface text-text text-sm
                           focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent"
                    :aria-label="'Search within ' + {{ json_encode($title) }}"
                >
            </div>
        </div>

        {{-- Nav --}}
        <nav>
            <a href="{{ route('rules.index') }}"
               class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-muted
                      hover:text-text transition-colors duration-100 mb-3 focus:outline-none focus:underline">
                <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                All Sections
            </a>

            <ul class="space-y-0.5 text-sm" role="list">
                {{-- Conditions section link --}}
                <li>
                    <a href="{{ route('rules.conditions') }}"
                       class="block px-3 py-1.5 rounded-md transition-colors duration-100 focus:outline-none focus:ring-2 focus:ring-accent
                              {{ $currentSlug === 'conditions'
                                  ? 'bg-accent/10 text-accent font-semibold'
                                  : 'text-muted hover:text-text hover:bg-hover' }}">
                        Conditions
                    </a>

                    {{-- Sub-entries for Conditions when it's the current section --}}
                    @if ($currentSlug === 'conditions')
                        <ul class="mt-0.5 ml-3 border-l border-border pl-3 space-y-0.5" role="list">
                            @foreach ($entries as $entry)
                                <li>
                                    <a
                                        href="#entry-{{ $entry->id }}"
                                        @click.prevent="search = ''; $nextTick(() => document.getElementById('entry-{{ $entry->id }}').scrollIntoView({ behavior: 'smooth', block: 'start' }))"
                                        class="block py-1 text-xs text-muted hover:text-accent transition-colors duration-100 focus:outline-none focus:text-accent truncate"
                                    >{{ $entry->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>

                @foreach ($allRuleSets as $rs)
                    <li>
                        <a href="{{ route('rules.show', $rs->slug) }}"
                           class="block px-3 py-1.5 rounded-md transition-colors duration-100 focus:outline-none focus:ring-2 focus:ring-accent
                                  {{ $currentSlug === $rs->slug
                                      ? 'bg-accent/10 text-accent font-semibold'
                                      : 'text-muted hover:text-text hover:bg-hover' }}">
                            {{ $rs->name }}
                        </a>

                        {{-- Sub-entries for the current ruleset section --}}
                        @if ($currentSlug === $rs->slug && $entries->isNotEmpty())
                            <ul class="mt-0.5 ml-3 border-l border-border pl-3 space-y-0.5" role="list">
                                @foreach ($entries as $entry)
                                    <li>
                                        <a
                                            href="#entry-{{ $entry->id }}"
                                            @click.prevent="search = ''; $nextTick(() => document.getElementById('entry-{{ $entry->id }}').scrollIntoView({ behavior: 'smooth', block: 'start' }))"
                                            class="block py-1 text-xs text-muted hover:text-accent transition-colors duration-100 focus:outline-none focus:text-accent truncate"
                                        >{{ $entry->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>
    </aside>

    {{-- ================================================ --}}
    {{-- Main content                                     --}}
    {{-- ================================================ --}}
    <div class="flex-1 min-w-0">

        {{-- Mobile search --}}
        <div class="lg:hidden mb-6">
            <label for="rules-search-mobile" class="sr-only">Search within {{ $title }}</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input
                    id="rules-search-mobile"
                    type="search"
                    x-model="search"
                    placeholder="Search {{ $title }}…"
                    class="w-full pl-9 pr-3 py-2 rounded-lg border border-border bg-surface text-text text-sm
                           focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent"
                >
            </div>
        </div>

        {{-- No results --}}
        <div
            x-show="!hasResults"
            x-cloak
            class="py-16 text-center text-muted"
            role="status"
            aria-live="polite"
        >
            <p class="text-lg font-semibold">No results for "<span x-text="search"></span>"</p>
            <p class="text-sm mt-1">
                Try a different term, or
                <button
                    @click="search = ''"
                    class="underline hover:text-text focus:outline-none focus:text-text"
                >clear the search</button>.
            </p>
        </div>

        {{-- -------------------------------------------- --}}
        {{-- Section intro — shown whenever a desc exists,--}}
        {{-- whether or not there are also sub-entries.   --}}
        {{-- -------------------------------------------- --}}
        @if ($descHtml && $entries->isNotEmpty())
            {{--
                When sub-entries exist, the desc is an intro paragraph.
                We render it in a subtly styled block so it's clearly
                introductory, not just another rule card.
            --}}
            <div class="mb-6 px-5 py-4 rounded-xl border border-border bg-surface/50
                        prose prose-sm max-w-none dark:prose-invert
                        prose-headings:text-text prose-headings:font-semibold
                        prose-a:text-accent prose-strong:text-text
                        prose-table:text-sm prose-td:py-1 prose-th:py-1">
                {!! $descHtml !!}
            </div>
        @endif

        {{-- -------------------------------------------- --}}
        {{-- Sub-rule entry cards                          --}}
        {{-- -------------------------------------------- --}}
        @if ($entries->isNotEmpty())
            <div class="space-y-4" aria-live="polite" aria-atomic="false">
                @foreach ($entries as $entry)
                    <article
                        id="entry-{{ $entry->id }}"
                        x-show="isVisible({{ $entry->id }})"
                        class="bg-surface border border-border rounded-xl p-5"
                        aria-labelledby="entry-heading-{{ $entry->id }}"
                    >
                        <h2 id="entry-heading-{{ $entry->id }}"
                            class="text-base font-semibold text-accent mb-3">
                            {{ $entry->name }}
                        </h2>
                        <div class="prose prose-sm max-w-none dark:prose-invert
                                    prose-headings:text-text prose-headings:font-semibold
                                    prose-a:text-accent prose-strong:text-text
                                    prose-table:text-sm prose-td:py-1 prose-th:py-1">
                            {!! $entry->body_html !!}
                        </div>
                    </article>
                @endforeach
            </div>

        {{-- -------------------------------------------- --}}
        {{-- Content-only section: desc IS the content    --}}
        {{-- -------------------------------------------- --}}
        @elseif ($descHtml)
            <div class="bg-surface border border-border rounded-xl p-6">
                <div class="prose prose-sm max-w-none dark:prose-invert
                            prose-headings:text-text prose-headings:font-semibold
                            prose-a:text-accent prose-strong:text-text
                            prose-table:text-sm prose-td:py-1 prose-th:py-1">
                    {!! $descHtml !!}
                </div>
            </div>

        @else
            <p class="text-muted text-sm italic">No content available for this section.</p>
        @endif

    </div>{{-- /main --}}

</div>{{-- /Alpine wrapper --}}

@endsection
