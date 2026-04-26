@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">

    <a href="{{ route('spells.index') }}"
       class="inline-flex items-center text-sm text-accent hover:text-accent-hover mb-4 font-medium underline underline-offset-2">
        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Spells
    </a>

    <div class="bg-surface border border-border shadow-md rounded-lg overflow-hidden">

        {{-- Header --}}
        <div class="p-6 border-b border-border">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-text">{{ $spell->name }}</h1>
                    <p class="mt-1 text-muted italic">
                        {{ $spell->level_label }}
                        @if($spell->school)
                            {{ strtolower($spell->level_label) === 'cantrip' ? $spell->school->name . ' cantrip' : $spell->school->name . ' spell' }}
                        @endif
                        @if($spell->ritual)
                            <span class="not-italic text-xs bg-purple-500/10 text-purple-400 px-1.5 py-0.5 rounded ml-1">Ritual</span>
                        @endif
                    </p>
                </div>
                @auth
                    {{-- Clone button placeholder for Phase 5 --}}
                @endauth
            </div>
        </div>

        {{-- Stat block --}}
        <div class="p-6 space-y-4">

            {{-- At-a-glance properties --}}
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3 text-sm">

                <div class="flex gap-2">
                    <dt class="font-semibold text-text w-32 shrink-0">Casting Time</dt>
                    <dd class="text-muted">{{ $spell->casting_time_label }}</dd>
                </div>

                <div class="flex gap-2">
                    <dt class="font-semibold text-text w-32 shrink-0">Range</dt>
                    <dd class="text-muted">{{ $spell->range_text ?? '—' }}</dd>
                </div>

                <div class="flex gap-2">
                    <dt class="font-semibold text-text w-32 shrink-0">Duration</dt>
                    <dd class="text-muted">
                        @if($spell->concentration)
                            <span class="text-blue-400">Concentration</span>,
                        @endif
                        {{ $spell->duration ?? '—' }}
                    </dd>
                </div>

                <div class="flex gap-2">
                    <dt class="font-semibold text-text w-32 shrink-0">School</dt>
                    <dd class="text-muted">{{ $spell->school?->name ?? '—' }}</dd>
                </div>

                {{-- Components --}}
                <div class="flex gap-2 sm:col-span-2">
                    <dt class="font-semibold text-text w-32 shrink-0">Components</dt>
                    <dd class="text-muted">
                        @php
                            $components = array_filter([
                                $spell->verbal   ? 'V' : null,
                                $spell->somatic  ? 'S' : null,
                                $spell->material ? 'M' : null,
                            ]);
                        @endphp
                        {{ implode(', ', $components) ?: '—' }}
                        @if($spell->material && $spell->material_specified)
                            <span class="text-text/60">({{ $spell->material_specified }})</span>
                        @endif
                    </dd>
                </div>

                @if($spell->saving_throw_ability)
                    <div class="flex gap-2">
                        <dt class="font-semibold text-text w-32 shrink-0">Saving Throw</dt>
                        <dd class="text-muted">{{ ucfirst($spell->saving_throw_ability) }}</dd>
                    </div>
                @endif

                @if(!empty($spell->damage_types))
                    <div class="flex gap-2">
                        <dt class="font-semibold text-text w-32 shrink-0">Damage Type</dt>
                        <dd class="text-muted">{{ implode(', ', array_map('ucfirst', $spell->damage_types)) }}</dd>
                    </div>
                @endif

            </dl>

            <hr class="border-border">

            {{-- Description --}}
            <div class="prose prose-sm dark:prose-invert max-w-none text-text">
                {!! \Illuminate\Support\Str::markdown($spell->description ?? '') !!}
            </div>

            @if($spell->higher_level)
                <div class="bg-bg border border-border rounded-md p-4 text-sm">
                    <p class="font-semibold text-text mb-1">At Higher Levels</p>
                    <p class="text-muted">{{ $spell->higher_level }}</p>
                </div>
            @endif

            {{-- Classes --}}
            @if(!empty($spell->class_names))
                <div>
                    <p class="text-sm font-semibold text-text mb-2">Available to</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($spell->class_names as $className)
                            <span class="text-xs font-medium border border-accent text-accent px-2.5 py-1 rounded-full">{{ $className }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
