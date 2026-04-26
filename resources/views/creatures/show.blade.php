@extends('layouts.app')

@php use App\Models\Creature; @endphp

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">

    <a href="{{ route('creatures.index') }}"
       class="inline-flex items-center text-sm text-accent hover:text-accent-hover mb-4 font-medium underline underline-offset-2">
        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Monsters
    </a>

    <div class="bg-surface border border-border shadow-md rounded-lg overflow-hidden">

        {{-- Header --}}
        <div class="p-6 border-b border-border">
            <h1 class="text-3xl font-bold text-text">{{ $creature->name }}</h1>
            <p class="mt-1 text-muted italic">
                {{ ucfirst($creature->size ?? '') }} {{ $creature->type?->name ?? '' }}@if($creature->alignment), {{ $creature->alignment }}@endif
            </p>
        </div>

        <div class="p-6 space-y-5">

            {{-- Core stats: compact definition table --}}
            @php
                $speed = collect([
                    $creature->speed_walk   ? $creature->speed_walk   . ' ft.'          : null,
                    $creature->speed_fly    ? 'fly '    . $creature->speed_fly    . ' ft.' : null,
                    $creature->speed_swim   ? 'swim '   . $creature->speed_swim   . ' ft.' : null,
                    $creature->speed_climb  ? 'climb '  . $creature->speed_climb  . ' ft.' : null,
                    $creature->speed_burrow ? 'burrow ' . $creature->speed_burrow . ' ft.' : null,
                ])->filter()->implode(', ') ?: '—';
            @endphp

            <table class="w-full text-sm border border-border rounded-lg overflow-hidden">
                <tbody class="divide-y divide-border">
                    <tr class="odd:bg-surface even:bg-bg">
                        <th scope="row" class="px-4 py-2 text-left font-semibold text-text w-40">Armor Class</th>
                        <td class="px-4 py-2 text-muted">
                            {{ $creature->armor_class ?? '—' }}
                            @if($creature->armor_detail)
                                <span class="text-text/50">({{ $creature->armor_detail }})</span>
                            @endif
                        </td>
                        <th scope="row" class="px-4 py-2 text-left font-semibold text-text w-40">Hit Points</th>
                        <td class="px-4 py-2 text-muted">
                            {{ $creature->hit_points ?? '—' }}
                            @if($creature->hit_dice)
                                <span class="text-text/50">({{ $creature->hit_dice }})</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="odd:bg-surface even:bg-bg">
                        <th scope="row" class="px-4 py-2 text-left font-semibold text-text">Speed</th>
                        <td class="px-4 py-2 text-muted" colspan="3">{{ $speed }}</td>
                    </tr>
                    <tr class="odd:bg-surface even:bg-bg">
                        <th scope="row" class="px-4 py-2 text-left font-semibold text-text">Challenge</th>
                        <td class="px-4 py-2 text-muted">{{ $creature->cr_display }}</td>
                        <th scope="row" class="px-4 py-2 text-left font-semibold text-text">Passive Perception</th>
                        <td class="px-4 py-2 text-muted">{{ $creature->passive_perception ?? '—' }}</td>
                    </tr>
                </tbody>
            </table>

            {{-- Ability scores: always a single horizontal row --}}
            <table class="w-full text-center text-sm border border-border rounded-lg overflow-hidden">
                <thead class="bg-bg border-b border-border">
                    <tr>
                        @foreach(['STR', 'DEX', 'CON', 'INT', 'WIS', 'CHA'] as $abbr)
                            <th scope="col" class="py-2 px-1 font-semibold text-muted text-xs uppercase tracking-wider">{{ $abbr }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-surface">
                        @foreach([
                            $creature->ability_score_strength,
                            $creature->ability_score_dexterity,
                            $creature->ability_score_constitution,
                            $creature->ability_score_intelligence,
                            $creature->ability_score_wisdom,
                            $creature->ability_score_charisma,
                        ] as $score)
                            <td class="py-3 px-1">
                                <div class="font-bold text-text text-base">{{ $score ?? '—' }}</div>
                                @if($score !== null)
                                    <div class="text-xs text-muted">{{ Creature::modifierString($score) }}</div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>

            {{-- Saving throws, skills, damage modifiers, senses, languages --}}
            <table class="w-full text-sm border border-border rounded-lg overflow-hidden">
                <tbody class="divide-y divide-border">

                    @if(!empty($creature->saving_throws))
                        <tr class="odd:bg-surface even:bg-bg">
                            <th scope="row" class="px-4 py-2 text-left font-semibold text-text w-48 align-top">Saving Throws</th>
                            <td class="px-4 py-2 text-muted">
                                {{ collect($creature->saving_throws)->map(fn($v, $k) => ucfirst($k) . ' +' . $v)->implode(', ') }}
                            </td>
                        </tr>
                    @endif

                    @if(!empty($creature->skill_bonuses))
                        <tr class="odd:bg-surface even:bg-bg">
                            <th scope="row" class="px-4 py-2 text-left font-semibold text-text align-top">Skills</th>
                            <td class="px-4 py-2 text-muted">
                                {{ collect($creature->skill_bonuses)->map(fn($v, $k) => ucwords(str_replace('_', ' ', $k)) . ' +' . $v)->implode(', ') }}
                            </td>
                        </tr>
                    @endif

                    @if(!empty($creature->damage_immunities))
                        <tr class="odd:bg-surface even:bg-bg">
                            <th scope="row" class="px-4 py-2 text-left font-semibold text-text align-top">Damage Immunities</th>
                            <td class="px-4 py-2 text-muted">{{ implode(', ', array_map('ucfirst', $creature->damage_immunities)) }}</td>
                        </tr>
                    @endif

                    @if(!empty($creature->damage_resistances))
                        <tr class="odd:bg-surface even:bg-bg">
                            <th scope="row" class="px-4 py-2 text-left font-semibold text-text align-top">Damage Resistances</th>
                            <td class="px-4 py-2 text-muted">{{ implode(', ', array_map('ucfirst', $creature->damage_resistances)) }}</td>
                        </tr>
                    @endif

                    @if(!empty($creature->damage_vulnerabilities))
                        <tr class="odd:bg-surface even:bg-bg">
                            <th scope="row" class="px-4 py-2 text-left font-semibold text-text align-top">Damage Vulnerabilities</th>
                            <td class="px-4 py-2 text-muted">{{ implode(', ', array_map('ucfirst', $creature->damage_vulnerabilities)) }}</td>
                        </tr>
                    @endif

                    @if(!empty($creature->condition_immunities))
                        <tr class="odd:bg-surface even:bg-bg">
                            <th scope="row" class="px-4 py-2 text-left font-semibold text-text align-top">Condition Immunities</th>
                            <td class="px-4 py-2 text-muted">{{ implode(', ', array_map('ucfirst', $creature->condition_immunities)) }}</td>
                        </tr>
                    @endif

                    @php
                        $senses = collect([
                            $creature->sense_darkvision  ? 'Darkvision '  . $creature->sense_darkvision  . ' ft.' : null,
                            $creature->sense_blindsight  ? 'Blindsight '  . $creature->sense_blindsight  . ' ft.' : null,
                            $creature->sense_tremorsense ? 'Tremorsense ' . $creature->sense_tremorsense . ' ft.' : null,
                            $creature->sense_truesight   ? 'Truesight '   . $creature->sense_truesight   . ' ft.' : null,
                            $creature->sense_telepathy   ? 'Telepathy '   . $creature->sense_telepathy   . ' ft.' : null,
                            'Passive Perception ' . ($creature->passive_perception ?? '—'),
                        ])->filter()->implode(', ');
                    @endphp
                    <tr class="odd:bg-surface even:bg-bg">
                        <th scope="row" class="px-4 py-2 text-left font-semibold text-text align-top">Senses</th>
                        <td class="px-4 py-2 text-muted">{{ $senses }}</td>
                    </tr>

                    @if($creature->languages_desc)
                        <tr class="odd:bg-surface even:bg-bg">
                            <th scope="row" class="px-4 py-2 text-left font-semibold text-text align-top">Languages</th>
                            <td class="px-4 py-2 text-muted">{{ $creature->languages_desc }}</td>
                        </tr>
                    @endif

                </tbody>
            </table>

            {{-- Traits --}}
            @if(!empty($creature->traits))
                <div>
                    <h2 class="text-sm font-semibold text-text uppercase tracking-wider border-b border-border pb-2 mb-3">Traits</h2>
                    <div class="space-y-3">
                        @foreach($creature->traits as $trait)
                            <p class="text-sm text-muted">
                                <span class="font-semibold italic text-text">{{ $trait['name'] }}.</span>
                                {{ $trait['desc'] }}
                            </p>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Actions, Bonus Actions, Reactions, Legendary Actions --}}
            @php
                $actionGroups = collect($creature->actions ?? [])
                    ->groupBy(fn ($a) => $a['action_type'] ?? 'ACTION');
            @endphp

            @foreach(['ACTION' => 'Actions', 'BONUS_ACTION' => 'Bonus Actions', 'REACTION' => 'Reactions', 'LEGENDARY_ACTION' => 'Legendary Actions'] as $type => $label)
                @if($actionGroups->has($type))
                    <div>
                        <h2 class="text-sm font-semibold text-text uppercase tracking-wider border-b border-border pb-2 mb-3">{{ $label }}</h2>
                        <div class="space-y-3">
                            @foreach($actionGroups[$type] as $action)
                                <p class="text-sm text-muted">
                                    <span class="font-semibold italic text-text">
                                        {{ $action['name'] }}@if(($action['legendary_cost'] ?? null) > 1) (Costs {{ $action['legendary_cost'] }} Actions)@endif.
                                    </span>
                                    {{ $action['desc'] }}
                                </p>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

        </div>
    </div>
</div>
@endsection
