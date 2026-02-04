@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="sm:flex sm:items-center sm:justify-between py-6">
        <h1 class="text-2xl font-semibold text-text">SRD Items</h1>

        <a href="{{ route('items.custom.create', ['from' => 'srd']) }}"
           class="inline-flex items-center px-4 py-2 bg-accent hover:bg-accent-hover text-on-accent text-sm font-medium rounded-md shadow focus:outline-none focus:ring-2 focus:ring-accent">
            + New Item
        </a>
    </div>

    {{-- Alpine state --}}
    <div
        x-data="{
            q: @js(request('q')),
            categoryFilter: @js(request('category')),
            rarityFilter: @js(request('rarity')),
            loading: false,

            async applyFilters() {
                this.loading = true;

                const params = new URLSearchParams();
                if (this.q) params.append('q', this.q);
                if (this.categoryFilter) params.append('category', this.categoryFilter);
                if (this.rarityFilter) params.append('rarity', this.rarityFilter);

                const url = `${window.location.pathname}?${params.toString()}`;

                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const html = await response.text();
                document.querySelector('#item-results').innerHTML = html;

                this.loading = false;
            }
        }"
        class="space-y-4"
    >

        {{-- Filters --}}
        @include('items.partials.filter', ['items' => $items])

        {{-- Results + overlay --}}
        <div class="relative">

            {{-- Loading overlay --}}
            <div
                x-show="loading"
                x-cloak
                x-transition.opacity
                class="absolute inset-0 bg-surface/70 flex items-center justify-center z-10"
            >
                <svg class="animate-spin h-10 w-10 text-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <circle class="opacity-75" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                            stroke-linecap="round"
                            stroke-dasharray="80"
                            stroke-dashoffset="60" />
                </svg>
            </div>

            {{-- Results --}}
            <div id="item-results">
                @include('items.partials.results', ['items' => $items])
            </div>
        </div>
    </div>
</div>
@endsection
