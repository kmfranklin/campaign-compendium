@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Back link --}}
    <a href="{{ route('encounters.index') }}"
       class="inline-flex items-center text-sm text-accent hover:text-accent-hover mb-4 font-medium focus:outline-none focus:ring-2 focus:ring-accent rounded">
        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Saved Encounters
    </a>

    {{-- ── Header with inline rename ──────────────────────────────────────────── --}}
    @php
        $difficultyColors = [
            'trivial' => 'bg-gray-100 text-gray-600',
            'easy'    => 'bg-green-100 text-green-700',
            'medium'  => 'bg-yellow-100 text-yellow-700',
            'hard'    => 'bg-orange-100 text-orange-700',
            'deadly'  => 'bg-red-100 text-red-700',
        ];
        $badgeClass  = $difficultyColors[$encounter->difficulty] ?? 'bg-gray-100 text-gray-600';
        $monsters    = collect($encounter->monsters ?? []);
        $party       = $encounter->party ?? [];
        $partyCount  = count($party);
        $avgLevel    = $partyCount > 0 ? round(array_sum($party) / $partyCount, 1) : '—';
    @endphp

    <div x-data="{
            editing: false,
            name: {{ json_encode($encounter->name ?? '') }},
            saved: {{ json_encode($encounter->name ?? '') }},
            saving: false,
            error: null,

            async saveName() {
                this.saving = true;
                this.error  = null;
                try {
                    const res = await fetch('{{ route('encounters.update', $encounter) }}', {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ name: this.name }),
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Save failed.');
                    this.saved   = data.name ?? '';
                    this.name    = this.saved;
                    this.editing = false;
                } catch (e) {
                    this.error = e.message;
                } finally {
                    this.saving = false;
                }
            },

            cancelEdit() {
                this.name    = this.saved;
                this.editing = false;
                this.error   = null;
            },
        }"
         class="bg-surface border border-border rounded-xl shadow-sm overflow-hidden mb-6">

        {{-- Card header --}}
        <div class="px-6 py-5 border-b border-border">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">

                {{-- Name / rename form --}}
                <div class="flex-1 min-w-0">
                    {{-- Display mode --}}
                    <div x-show="!editing" class="flex flex-wrap items-center gap-2">
                        <h1 class="text-2xl font-bold text-text"
                            x-text="saved || 'Unnamed Encounter'"></h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold {{ $badgeClass }}">
                            {{ ucfirst($encounter->difficulty) }}
                        </span>
                    </div>

                    {{-- Edit mode --}}
                    <div x-show="editing" x-cloak class="flex items-center gap-2">
                        <label for="encounter-name" class="sr-only">Encounter name</label>
                        <input id="encounter-name"
                               type="text"
                               x-model="name"
                               placeholder="Encounter name (optional)"
                               maxlength="100"
                               @keydown.enter="saveName()"
                               @keydown.escape="cancelEdit()"
                               class="flex-1 min-w-0 rounded-md border border-border bg-bg text-text placeholder-muted px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-accent" />
                        <button @click="saveName()" :disabled="saving"
                                class="px-3 py-1.5 text-sm font-medium bg-accent hover:bg-accent-hover text-on-accent rounded-md disabled:opacity-50 focus:outline-none focus:ring-2 focus:ring-accent">
                            <span x-show="!saving">Save</span>
                            <span x-show="saving">Saving…</span>
                        </button>
                        <button @click="cancelEdit()"
                                class="px-3 py-1.5 text-sm font-medium text-muted hover:text-text border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-accent">
                            Cancel
                        </button>
                    </div>

                    <p x-show="error" x-text="error" class="mt-1 text-xs text-red-600" role="alert"></p>

                    <p class="mt-1 text-sm text-muted">
                        Saved {{ $encounter->created_at->diffForHumans() }}
                        @if($encounter->created_at != $encounter->updated_at)
                            · updated {{ $encounter->updated_at->diffForHumans() }}
                        @endif
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button x-show="!editing" @click="editing = true"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-text border border-border rounded-md hover:border-accent hover:text-accent transition-colors focus:outline-none focus:ring-2 focus:ring-accent">
                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                        Rename
                    </button>

                    <a href="{{ route('encounter-generator.index') }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-accent border border-accent rounded-md hover:bg-accent hover:text-on-accent transition-colors focus:outline-none focus:ring-2 focus:ring-accent"
                       title="Open a new encounter in the generator">
                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                        </svg>
                        Generator
                    </a>

                    <form action="{{ route('encounters.destroy', $encounter) }}" method="POST"
                          onsubmit="return confirm('Delete this encounter? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-600 border border-red-300 rounded-md hover:bg-red-600 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Two-column summary ────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-y sm:divide-y-0 divide-border">
            <div class="px-5 py-4 text-center">
                <p class="text-xs text-muted uppercase tracking-wide mb-0.5">Party Size</p>
                <p class="text-xl font-bold text-text">{{ $partyCount }}</p>
            </div>
            <div class="px-5 py-4 text-center">
                <p class="text-xs text-muted uppercase tracking-wide mb-0.5">Avg Level</p>
                <p class="text-xl font-bold text-text">{{ $avgLevel }}</p>
            </div>
            <div class="px-5 py-4 text-center">
                <p class="text-xs text-muted uppercase tracking-wide mb-0.5">Total XP</p>
                <p class="text-xl font-bold text-text">{{ number_format($encounter->total_xp) }}</p>
            </div>
            <div class="px-5 py-4 text-center">
                <p class="text-xs text-muted uppercase tracking-wide mb-0.5">Adjusted XP</p>
                <p class="text-xl font-bold text-text">{{ number_format($encounter->adjusted_xp) }}</p>
            </div>
        </div>
    </div>

    {{-- ── Party ────────────────────────────────────────────────────────────── --}}
    <section aria-labelledby="party-heading" class="mb-6">
        <h2 id="party-heading" class="text-lg font-semibold text-text mb-3">Party</h2>
        <div class="bg-surface border border-border rounded-xl overflow-hidden">
            <div class="flex flex-wrap gap-2 p-4">
                @foreach ($party as $level)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-accent/10 text-accent">
                        Level {{ $level }}
                    </span>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── Monsters ─────────────────────────────────────────────────────────── --}}
    <section aria-labelledby="monsters-heading" class="mb-6">
        <h2 id="monsters-heading" class="text-lg font-semibold text-text mb-3">Monsters</h2>
        <div class="bg-surface border border-border rounded-xl overflow-hidden">
            <table class="w-full text-sm" aria-label="Encounter monsters">
                <thead>
                    <tr class="border-b border-border bg-bg">
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wide">Monster</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-muted uppercase tracking-wide">CR</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-muted uppercase tracking-wide">Qty</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase tracking-wide">XP each</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-muted uppercase tracking-wide">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach ($monsters as $monster)
                        @php
                            $qty      = $monster['quantity'] ?? 1;
                            $xpEach   = $monster['xp'] ?? 0;
                            $subtotal = $qty * $xpEach;
                        @endphp
                        <tr class="hover:bg-hover transition-colors">
                            <td class="px-4 py-3 font-medium text-text">
                                {{ $monster['name'] ?? '—' }}
                                @if(($monster['source'] ?? '') === 'custom_creature')
                                    <span class="ml-1 text-xs text-purple-500">(custom)</span>
                                @elseif(($monster['source'] ?? '') === 'manual')
                                    <span class="ml-1 text-xs text-muted">(manual)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-muted">
                                {{ $monster['cr'] ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-center text-muted">
                                {{ $qty }}
                            </td>
                            <td class="px-4 py-3 text-right text-muted">
                                {{ $xpEach > 0 ? number_format($xpEach) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-text">
                                {{ $subtotal > 0 ? number_format($subtotal) : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-border bg-bg">
                        <td colspan="3" class="px-4 py-3 text-sm font-semibold text-text">
                            Totals
                        </td>
                        <td class="px-4 py-3 text-right text-sm font-semibold text-text">
                            {{ number_format($encounter->total_xp) }} XP
                        </td>
                        <td class="px-4 py-3 text-right text-sm font-semibold text-text">
                            {{ number_format($encounter->total_xp) }} XP
                        </td>
                    </tr>
                    <tr class="border-t border-border">
                        <td colspan="4" class="px-4 py-2 text-xs text-muted">
                            Adjusted XP (with count multiplier, used for difficulty)
                        </td>
                        <td class="px-4 py-2 text-right text-sm font-bold text-text">
                            {{ number_format($encounter->adjusted_xp) }} XP
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>

</div>
@endsection
