@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="sm:flex sm:items-center sm:justify-between py-6">
        <h1 class="text-2xl font-semibold text-text">Character Compendium</h1>

        <a href="{{ route('compendium.npcs.create') }}"
           class="btn btn-primary btn-sm">
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
        <form class="ui-filter-panel mb-4 space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-3">

                {{-- Search --}}
                <div class="xl:col-span-2">
                    <label for="npc-search" class="form-label text-xs text-muted">Search</label>
                    <input
                        id="npc-search"
                        name="q"
                        x-model="q"
                        type="search"
                        placeholder="Search by name…"
                        @input.debounce.500ms="applyFilters"
                        class="ui-field"
                    />
                </div>

                {{-- Class --}}
                <div>
                    <label for="npc-class" class="form-label text-xs text-muted">Class</label>
                    <select id="npc-class" name="class" x-model="classFilter" @change="applyFilters"
                            class="ui-select">
                        <option value="">All classes</option>
                        @foreach(\App\Models\Npc::CLASSES as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Alignment --}}
                <div>
                    <label for="npc-alignment" class="form-label text-xs text-muted">Alignment</label>
                    <select id="npc-alignment" name="alignment" x-model="alignmentFilter" @change="applyFilters"
                            class="ui-select">
                        <option value="">All alignments</option>
                        @foreach(\App\Models\Npc::ALIGNMENTS as $a)
                            <option value="{{ $a }}">{{ $a }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Role --}}
                <div>
                    <label for="npc-role" class="form-label text-xs text-muted">Role</label>
                    <select id="npc-role" name="role" x-model="roleFilter" @change="applyFilters"
                            class="ui-select">
                        <option value="">All roles</option>
                        @foreach(\App\Models\Npc::SOCIAL_ROLES as $r)
                            <option value="{{ $r }}">{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap items-end gap-3">
                <button type="button"
                        @click="applyFilters"
                        class="btn btn-primary btn-sm">
                    Search
                </button>

                <a href="{{ route('compendium.npcs.index') }}"
                   class="btn btn-secondary btn-sm text-center">
                    Reset
                </a>
            </div>
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
