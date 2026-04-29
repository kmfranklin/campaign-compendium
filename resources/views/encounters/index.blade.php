@extends('layouts.app')

@section('content')
<div class="w-full">

    {{-- Page header --}}
    <div class="sm:flex sm:items-center sm:justify-between py-6">
        <div>
            <h1 class="text-2xl font-semibold text-text">Saved Encounters</h1>
            <p class="mt-1 text-sm text-muted">Encounters you've built and saved with the Encounter Generator.</p>
        </div>
        <a href="{{ route('encounter-generator.index') }}"
           class="mt-4 sm:mt-0 inline-flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent-hover text-on-accent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-accent">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
            </svg>
            New Encounter
        </a>
    </div>

    {{-- Flash success message --}}
    @if (session('success'))
        <div role="alert" class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if ($encounters->isEmpty())
        {{-- Empty state --}}
        <div class="text-center py-20 bg-surface border border-border rounded-xl">
            <div class="flex items-center justify-center w-14 h-14 rounded-full bg-accent/10 text-accent mx-auto mb-4" aria-hidden="true">
                <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-text">No saved encounters yet</h2>
            <p class="mt-2 text-sm text-muted max-w-sm mx-auto">
                Head to the Encounter Generator to build and save your first encounter.
            </p>
            <a href="{{ route('encounter-generator.index') }}"
               class="mt-6 inline-flex items-center px-5 py-2.5 bg-accent hover:bg-accent-hover text-on-accent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-accent">
                Go to Encounter Generator
            </a>
        </div>

    @else
        {{-- Encounter cards --}}
        <ul class="space-y-4" role="list" aria-label="Saved encounters">
            @foreach ($encounters as $encounter)
                @php
                    $difficultyColors = [
                        'trivial' => 'bg-gray-100 text-gray-600',
                        'easy'    => 'bg-green-100 text-green-700',
                        'medium'  => 'bg-yellow-100 text-yellow-700',
                        'hard'    => 'bg-orange-100 text-orange-700',
                        'deadly'  => 'bg-red-100 text-red-700',
                    ];
                    $badgeClass  = $difficultyColors[$encounter->difficulty] ?? 'bg-gray-100 text-gray-600';
                    $partyCount  = count($encounter->party ?? []);
                    $avgLevel    = $partyCount > 0 ? round(array_sum($encounter->party) / $partyCount, 1) : '—';
                    $monsterList = collect($encounter->monsters ?? []);
                    $totalCount  = $monsterList->sum('quantity');
                @endphp
                <li>
                    <div class="bg-surface border border-border rounded-xl p-5 hover:border-accent transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                            {{-- Left: name + meta --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h2 class="text-base font-semibold text-text truncate">
                                        {{ $encounter->name ?? 'Unnamed Encounter' }}
                                    </h2>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $badgeClass }}">
                                        {{ ucfirst($encounter->difficulty) }}
                                    </span>
                                </div>
                                <p class="text-sm text-muted">
                                    {{ $partyCount }} {{ Str::plural('character', $partyCount) }}, avg level {{ $avgLevel }}
                                    &middot;
                                    {{ $totalCount }} {{ Str::plural('monster', $totalCount) }}
                                    &middot;
                                    {{ number_format($encounter->adjusted_xp) }} adjusted XP
                                    &middot;
                                    <time datetime="{{ $encounter->created_at->toIso8601String() }}">
                                        {{ $encounter->created_at->diffForHumans() }}
                                    </time>
                                </p>
                            </div>

                            {{-- Right: actions --}}
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <a href="{{ route('encounters.show', $encounter) }}"
                                   class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-accent border border-accent rounded-md hover:bg-accent hover:text-on-accent transition-colors focus:outline-none focus:ring-2 focus:ring-accent">
                                    View
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
                </li>
            @endforeach
        </ul>

        {{-- Pagination --}}
        @if ($encounters->hasPages())
            <div class="mt-6">
                {{ $encounters->links() }}
            </div>
        @endif
    @endif

</div>
@endsection
