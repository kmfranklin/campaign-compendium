@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4">

    {{-- Back link --}}
    <a href="{{ route('campaigns.show', $campaign) }}"
       class="inline-flex items-center text-sm text-accent hover:text-accent-hover mb-4 font-medium">
      <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
           viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 19l-7-7 7-7"/>
      </svg>
      Back to Campaign
    </a>

    <div x-data="{ tab: 'overview' }"
         class="bg-surface border border-border shadow-md rounded-lg overflow-hidden">

        {{-- Tab headers --}}
        <nav class="flex border-b border-border text-sm font-medium text-muted" aria-label="Quest sections">
            <button @click="tab = 'overview'"
                    :class="{ 'border-accent text-accent': tab === 'overview' }"
                    class="px-4 py-2 border-b-2 border-transparent hover:text-text focus:outline-none"
                    :aria-selected="tab === 'overview'" role="tab">
                Overview
            </button>

            <button @click="tab = 'npcs'"
                    :class="{ 'border-accent text-accent': tab === 'npcs' }"
                    class="px-4 py-2 border-b-2 border-transparent hover:text-text focus:outline-none"
                    :aria-selected="tab === 'npcs'" role="tab">
                NPCs
            </button>
        </nav>

        {{-- Tab content --}}
        <div class="p-6">

            {{-- Overview tab --}}
            <div x-show="tab === 'overview'">

                {{-- HEADER --}}
                <div class="flex flex-col sm:flex-row items-start sm:items-center mb-6">
                    <div class="flex-1">
                        <div class="flex items-start">
                            <div>
                                <h1 class="text-3xl font-bold text-text">{{ $quest->title }}</h1>

                                {{-- Status badge --}}
                                <div class="mt-2">
                                    <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-full
                                                 {{ $quest->status->badgeClasses() }}">
                                        {{ $quest->status->label() }}
                                    </span>
                                </div>

                                {{-- Type tag --}}
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="bg-accent/10 text-accent text-xs font-medium px-2 py-1 rounded">
                                        Quest
                                    </span>
                                </div>
                            </div>

                            {{-- Top-right actions --}}
                            <div class="ml-auto flex gap-2">
                                @can('update', $campaign)
                                    <a href="{{ route('campaigns.quests.edit', [$campaign, $quest]) }}"
                                       class="px-4 py-2 bg-warning text-on-warning font-semibold rounded hover:bg-warning-hover">
                                        Edit
                                    </a>
                                @endcan

                                @can('delete', $campaign)
                                    <form action="{{ route('campaigns.quests.destroy', [$campaign, $quest]) }}"
                                          method="POST"
                                          onsubmit="return confirm('Delete this quest?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-4 py-2 bg-danger-solid text-on-danger font-semibold rounded hover:bg-danger-hover">
                                            Delete
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
                {{-- /HEADER --}}

                {{-- DESCRIPTION --}}
                @if($quest->description)
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-text mb-2">Description</h2>
                        <p class="text-text whitespace-pre-line">{{ $quest->description }}</p>
                    </div>
                @endif
                {{-- /DESCRIPTION --}}

                {{-- DM NOTES (DM-only; hidden from players in a future view layer) --}}
                @can('update', $campaign)
                    @if($quest->notes)
                        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg
                                    dark:bg-amber-900/10 dark:border-amber-800">
                            <h2 class="text-sm font-semibold text-amber-800 dark:text-amber-400 mb-1 flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                DM Notes
                                <span class="text-xs font-normal text-amber-600 dark:text-amber-500">(not visible to players)</span>
                            </h2>
                            <p class="text-sm text-amber-900 dark:text-amber-300 whitespace-pre-line">{{ $quest->notes }}</p>
                        </div>
                    @endif
                @endcan
                {{-- /DM NOTES --}}
            </div>

            {{-- NPCs tab --}}
            <div x-show="tab === 'npcs'">
                @include('quests.partials.npcs', [
                    'quest' => $quest,
                    'campaign' => $campaign,
                    'availableNpcs' => $availableNpcs
                ])
            </div>

        </div>
    </div>
</div>
@endsection
