@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4">

    {{-- Back link --}}
    <a href="{{ route('compendium.npcs.index') }}"
       class="inline-flex items-center text-sm text-accent hover:text-accent-hover mb-4 font-medium">
        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Compendium
    </a>

    <div class="bg-surface border border-border shadow-md rounded-lg overflow-hidden">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row items-start p-6 border-b border-border sm:items-center">

            @if($npc->portrait_path)
                <img src="{{ $npc->portrait_path }}"
                     alt="{{ $npc->name }} portrait"
                     class="w-32 h-32 sm:w-48 sm:h-48 rounded-full object-cover border-4 border-bg shadow-sm">
            @endif

            <div class="mt-4 sm:mt-0 flex-1 {{ $npc->portrait_path ? 'sm:ml-6' : '' }}">
                <div class="flex items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-text">{{ $npc->name }}</h1>

                        @if($npc->alias)
                            <p class="text-muted italic">“{{ $npc->alias }}”</p>
                        @endif

                        {{-- Tags --}}
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="bg-accent/10 text-accent text-xs font-medium px-2 py-1 rounded">NPC</span>

                            @if($npc->race)
                                <span class="bg-green-500/10 text-green-400 text-xs font-medium px-2 py-1 rounded">
                                    {{ $npc->race }}
                                </span>
                            @endif

                            @if($npc->class)
                                <span class="bg-yellow-500/10 text-yellow-400 text-xs font-medium px-2 py-1 rounded">
                                    {{ $npc->class }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="ml-auto flex gap-2">
                        <a href="{{ route('compendium.npcs.edit', $npc) }}"
                           class="inline-flex items-center px-4 py-2 h-10 bg-yellow-500 hover:bg-yellow-600 text-on-accent rounded shadow">
                            Edit
                        </a>

                        <form action="{{ route('compendium.npcs.destroy', $npc) }}"
                              method="POST"
                              onsubmit="return confirm('Delete this NPC?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-danger hover:bg-red-600 text-on-accent rounded shadow">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Quick Stats --}}
                @php
                    $quick = [
                        'Hit Points' => $npc->hit_points,
                        'Armor Class' => $npc->armor_class,
                        'Speed' => $npc->speed,
                        'Challenge Rating' => $npc->challenge_rating,
                        'Proficiency Bonus' => $npc->proficiency_bonus,
                    ];
                    $quick = array_filter($quick, fn($v) => !is_null($v) && $v !== '');
                @endphp

                @if(count($quick))
                    <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($quick as $label => $value)
                            <div class="bg-bg border border-border rounded-lg p-3 text-center">
                                <div class="text-xs font-medium text-muted uppercase">{{ $label }}</div>
                                <div class="mt-1 text-xl font-bold text-text">{{ $value }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        {{-- /HEADER --}}

        {{-- Core Identity --}}
        @php
            $core = [
                'Alignment' => $npc->alignment,
                'Location' => $npc->location,
                'Status' => $npc->status,
                'Role' => $npc->role,
            ];
            $core = array_filter($core, fn($v) => !is_null($v) && $v !== '');
        @endphp

        @if(count($core))
            <div class="p-6 bg-bg border-b border-border">
                <h2 class="text-lg font-semibold text-text mb-4">Core Identity</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    @foreach($core as $label => $value)
                        <div>
                            <dt class="font-medium text-muted">{{ $label }}</dt>
                            <dd class="text-text">{{ $value }}</dd>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Description --}}
        @if($npc->description || $npc->personality || $npc->quirks)
            <div class="p-6 bg-surface border-b border-border">
                <h2 class="text-lg font-semibold text-text mb-4">Description</h2>

                @if($npc->description)
                    <p class="mb-4 text-text">{{ $npc->description }}</p>
                @endif

                @if($npc->personality)
                    <p class="mb-4 italic text-muted">Personality: {{ $npc->personality }}</p>
                @endif

                @if($npc->quirks)
                    <p class="text-muted">Quirks: {{ $npc->quirks }}</p>
                @endif
            </div>
        @endif

        {{-- Combat Stats --}}
        @php
            $stats = ['strength','dexterity','constitution','intelligence','wisdom','charisma'];
            $hasStat = collect($stats)->some(fn($s) => !is_null($npc->$s));
        @endphp

        @if($hasStat)
            <div class="p-6 bg-bg border-b border-border">
                <h2 class="text-lg font-semibold text-text mb-4">Abilities + Stats</h2>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($stats as $stat)
                        @if(!is_null($npc->$stat))
                            <div class="bg-surface border border-border rounded-lg p-3 text-center">
                                <div class="text-xs font-medium text-muted uppercase">{{ ucfirst($stat) }}</div>
                                <div class="mt-1 text-xl font-bold text-text">{{ $npc->$stat }}</div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Quest Appearances --}}
        @if($quests->isNotEmpty())
            <div class="p-6 bg-surface border-t border-border">
                <h2 class="text-lg font-semibold text-text mb-4">Quest Appearances</h2>

                <ul class="divide-y divide-border">
                    @foreach($quests as $quest)
                        <li class="py-2">
                            <a href="{{ route('campaigns.quests.show', [$quest->campaign, $quest]) }}"
                               class="text-accent hover:text-accent-hover font-medium">
                                {{ $quest->title }}
                            </a>

                            <span class="ml-2 text-sm text-muted">
                                ({{ Str::headline($quest->pivot->role) }} in {{ $quest->campaign->name }})
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="p-6 bg-surface border-t border-border">
                <x-empty-state
                    icon="📜"
                    title="No quest appearances"
                    message="This character hasn’t been linked to any quests yet."
                />
            </div>
        @endif
    </div>
</div>
@endsection
