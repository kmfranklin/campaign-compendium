@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="py-6">
        <h1 class="text-2xl font-semibold text-text">Monsters</h1>
        <p class="mt-1 text-sm text-muted">Browse all {{ number_format(\App\Models\Creature::where('is_srd', true)->count()) }} SRD monsters. Filter by type, CR, or size.</p>
    </div>

    <div
        x-data="{
            q:      @js(request('q', '')),
            type:   @js(request('type', '')),
            cr:     @js(request('cr', '')),
            size:   @js(request('size', '')),
            loading: false,

            async applyFilters() {
                this.loading = true;
                const params = new URLSearchParams();
                if (this.q)    params.append('q', this.q);
                if (this.type) params.append('type', this.type);
                if (this.cr)   params.append('cr', this.cr);
                if (this.size) params.append('size', this.size);

                const url = `${window.location.pathname}?${params.toString()}`;
                const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                document.querySelector('#creature-results').innerHTML = await response.text();
                this.loading = false;
            }
        }"
        class="space-y-4"
    >
        {{-- Filter bar --}}
        <div class="bg-surface border border-border rounded-lg p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                {{-- Name search --}}
                <div class="lg:col-span-1">
                    <label for="monster-search" class="block text-xs font-medium text-muted mb-1">Search</label>
                    <input
                        id="monster-search"
                        type="search"
                        x-model="q"
                        @input.debounce.400ms="applyFilters"
                        placeholder="Monster name…"
                        class="w-full rounded-md border border-border bg-bg text-text text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent"
                    >
                </div>

                {{-- Type --}}
                <div>
                    <label for="monster-type" class="block text-xs font-medium text-muted mb-1">Type</label>
                    <select
                        id="monster-type"
                        x-model="type"
                        @change="applyFilters"
                        class="w-full rounded-md border border-border bg-bg text-text text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent"
                    >
                        <option value="">All types</option>
                        @foreach($types as $t)
                            <option value="{{ $t->slug }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- CR --}}
                <div>
                    <label for="monster-cr" class="block text-xs font-medium text-muted mb-1">Challenge Rating</label>
                    <select
                        id="monster-cr"
                        x-model="cr"
                        @change="applyFilters"
                        class="w-full rounded-md border border-border bg-bg text-text text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent"
                    >
                        <option value="">Any CR</option>
                        @foreach($crs as $value => $label)
                            <option value="{{ $value }}">CR {{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Size --}}
                <div>
                    <label for="monster-size" class="block text-xs font-medium text-muted mb-1">Size</label>
                    <select
                        id="monster-size"
                        x-model="size"
                        @change="applyFilters"
                        class="w-full rounded-md border border-border bg-bg text-text text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent"
                    >
                        <option value="">Any size</option>
                        @foreach($sizes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            {{-- Clear filters --}}
            <div class="mt-3">
                <template x-if="q || type || cr || size">
                    <button
                        type="button"
                        @click="q = ''; type = ''; cr = ''; size = ''; applyFilters()"
                        class="text-sm text-accent hover:text-accent-hover underline"
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
            <div id="creature-results">
                @include('creatures.partials.results', ['creatures' => $creatures])
            </div>
        </div>
    </div>
</div>
@endsection
