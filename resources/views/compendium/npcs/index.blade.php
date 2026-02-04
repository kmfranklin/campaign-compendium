@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="sm:flex sm:items-center sm:justify-between py-6">
        <h1 class="text-2xl font-semibold text-text">Character Compendium</h1>

        <a href="{{ route('compendium.npcs.create') }}"
           class="inline-flex items-center px-4 py-2 bg-accent hover:bg-accent-hover text-on-accent text-sm font-medium rounded-md shadow focus:outline-none focus:ring-2 focus:ring-accent">
            + New NPC
        </a>
    </div>

    <div
        x-data="{
            q: @js(request('q')),
            classFilter: @js(request('class')),
            alignmentFilter: @js(request('alignment')),
            roleFilter: @js(request('role')),
            loading: false,
            async applyFilters() {
                this.loading = true;

                const params = new URLSearchParams();
                if (this.q) params.append('q', this.q);
                if (this.classFilter) params.append('class', this.classFilter);
                if (this.alignmentFilter) params.append('alignment', this.alignmentFilter);
                if (this.roleFilter) params.append('role', this.roleFilter);

                const url = `{{ route('compendium.npcs.index') }}?${params.toString()}`;

                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const html = await response.text();
                document.querySelector('#npc-results').innerHTML = html;

                this.loading = false;
            }
        }"
        class="space-y-4"
    >

        {{-- Filters --}}
        <form class="mb-4 flex gap-4 flex-wrap flex-col sm:flex-row items-stretch sm:items-center">

            {{-- Search --}}
            <input
                name="q"
                x-model="q"
                type="text"
                placeholder="Search by name…"
                @input.debounce.500ms="applyFilters"
                class="border border-border bg-surface text-text rounded px-3 py-2 flex-1 focus:border-accent focus:ring-accent"
            />

            {{-- Class --}}
            <select name="class" x-model="classFilter" @change="applyFilters"
                    class="custom-select bg-surface border-border text-text">
                <option value="">All Classes</option>
                @foreach(\App\Models\Npc::CLASSES as $c)
                    <option value="{{ $c }}">{{ $c }}</option>
                @endforeach
            </select>

            {{-- Alignment --}}
            <select name="alignment" x-model="alignmentFilter" @change="applyFilters"
                    class="custom-select bg-surface border-border text-text">
                <option value="">All Alignments</option>
                @foreach(\App\Models\Npc::ALIGNMENTS as $a)
                    <option value="{{ $a }}">{{ $a }}</option>
                @endforeach
            </select>

            {{-- Role --}}
            <select name="role" x-model="roleFilter" @change="applyFilters"
                    class="custom-select bg-surface border-border text-text">
                <option value="">All Roles</option>
                @foreach(\App\Models\Npc::SOCIAL_ROLES as $r)
                    <option value="{{ $r }}">{{ $r }}</option>
                @endforeach
            </select>

            {{-- Search button --}}
            <button type="button"
                    @click="applyFilters"
                    class="bg-accent text-on-accent px-4 py-2 rounded hover:bg-accent-hover font-medium shadow">
                Search
            </button>

            {{-- Reset --}}
            <a href="{{ route('compendium.npcs.index') }}"
               class="px-4 py-2 bg-bg text-text border border-border rounded hover:bg-hover text-center font-medium">
                Reset
            </a>
        </form>

        {{-- Results + Overlay --}}
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

            {{-- Results container --}}
            <div id="npc-results">
                @include('compendium.npcs.partials.results', ['npcs' => $npcs])
            </div>
        </div>
    </div>
</div>
@endsection
