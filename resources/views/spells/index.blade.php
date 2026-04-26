@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="py-6">
        <h1 class="text-2xl font-semibold text-text">Spells</h1>
        <p class="mt-1 text-sm text-muted">Browse all {{ number_format(\App\Models\Spell::where('is_srd', true)->count()) }} SRD spells. Filter by class, level, school, or casting time.</p>
    </div>

    <div
        x-data="{
            q:           @js(request('q', '')),
            level:       @js(request('level', '')),
            school:      @js(request('school', '')),
            classFilter: @js(request('class', '')),
            castingTime: @js(request('casting_time', '')),
            loading: false,

            async applyFilters() {
                this.loading = true;
                const params = new URLSearchParams();
                if (this.q)           params.append('q', this.q);
                if (this.level !== '') params.append('level', this.level);
                if (this.school)      params.append('school', this.school);
                if (this.classFilter) params.append('class', this.classFilter);
                if (this.castingTime) params.append('casting_time', this.castingTime);

                const url = `${window.location.pathname}?${params.toString()}`;
                const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                document.querySelector('#spell-results').innerHTML = await response.text();
                this.loading = false;
            }
        }"
        class="space-y-4"
    >
        {{-- Filter bar --}}
        <div class="bg-surface border border-border rounded-lg p-4 space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">

                {{-- Name search --}}
                <div class="xl:col-span-2">
                    <label for="spell-search" class="block text-xs font-medium text-muted mb-1">Search</label>
                    <input
                        id="spell-search"
                        type="search"
                        x-model="q"
                        @input.debounce.400ms="applyFilters"
                        placeholder="Spell name…"
                        class="w-full rounded-md border border-border bg-bg text-text text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent"
                    >
                </div>

                {{-- Level --}}
                <div>
                    <label for="spell-level" class="block text-xs font-medium text-muted mb-1">Level</label>
                    <select
                        id="spell-level"
                        x-model="level"
                        @change="applyFilters"
                        class="w-full rounded-md border border-border bg-bg text-text text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent"
                    >
                        <option value="">All levels</option>
                        <option value="0">Cantrip</option>
                        @foreach(range(1, 9) as $lvl)
                            <option value="{{ $lvl }}">Level {{ $lvl }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- School --}}
                <div>
                    <label for="spell-school" class="block text-xs font-medium text-muted mb-1">School</label>
                    <select
                        id="spell-school"
                        x-model="school"
                        @change="applyFilters"
                        class="w-full rounded-md border border-border bg-bg text-text text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent"
                    >
                        <option value="">All schools</option>
                        @foreach($schools as $s)
                            <option value="{{ $s->slug }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Class --}}
                <div>
                    <label for="spell-class" class="block text-xs font-medium text-muted mb-1">Class</label>
                    <select
                        id="spell-class"
                        x-model="classFilter"
                        @change="applyFilters"
                        class="w-full rounded-md border border-border bg-bg text-text text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent"
                    >
                        <option value="">All classes</option>
                        @foreach($classes as $slug => $label)
                            <option value="{{ $slug }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            {{-- Second row: casting time + clear --}}
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="spell-casting-time" class="block text-xs font-medium text-muted mb-1">Casting Time</label>
                    <select
                        id="spell-casting-time"
                        x-model="castingTime"
                        @change="applyFilters"
                        class="rounded-md border border-border bg-bg text-text text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent"
                    >
                        <option value="">Any casting time</option>
                        @foreach($castingTimes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Clear filters --}}
                <template x-if="q || level !== '' || school || classFilter || castingTime">
                    <button
                        type="button"
                        @click="q = ''; level = ''; school = ''; classFilter = ''; castingTime = ''; applyFilters()"
                        class="text-sm text-accent hover:text-accent-hover underline pb-2"
                    >
                        Clear all filters
                    </button>
                </template>
            </div>
        </div>

        {{-- Results --}}
        <div class="relative">
            <div x-show="loading" x-cloak x-transition.opacity
                 class="absolute inset-0 bg-surface/70 flex items-center justify-center z-10">
                <svg class="animate-spin h-10 w-10 text-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-label="Loading">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
            </div>
            <div id="spell-results">
                @include('spells.partials.results', ['spells' => $spells])
            </div>
        </div>
    </div>
</div>
@endsection
