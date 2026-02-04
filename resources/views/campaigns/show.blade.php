@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4">

    {{-- Back link --}}
    <a href="{{ route('campaigns.index') }}"
       class="inline-flex items-center text-sm text-accent hover:text-accent-hover mb-4 font-medium">
        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Campaigns
    </a>

    <div x-data="{ tab: 'overview' }"
         class="bg-surface border border-border shadow-md rounded-lg overflow-hidden">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row items-start bg-surface p-6 border-b border-border sm:items-center">
            <div class="flex-1">
                <div class="flex items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-text">{{ $campaign->name }}</h1>
                        <p class="text-muted mt-1">
                            Dungeon Master: {{ $campaign->dm->name ?? 'Unknown' }}
                        </p>

                        {{-- tags --}}
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="bg-accent/10 text-accent text-xs font-medium px-2 py-1 rounded">
                                Campaign
                            </span>
                        </div>
                    </div>

                    {{-- Top-right actions --}}
                    <div class="ml-auto flex gap-2">
                        @can('update', $campaign)
                            <a href="{{ route('campaigns.edit', $campaign) }}"
                               class="inline-flex items-center px-4 h-10 bg-yellow-500 hover:bg-yellow-600 text-on-accent rounded shadow">
                                Edit
                            </a>
                        @endcan

                        @can('delete', $campaign)
                            <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST"
                                  onsubmit="return confirm('Delete this campaign?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-danger hover:bg-red-700 text-on-accent rounded shadow">
                                    Delete
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        {{-- /HEADER --}}

        {{-- Tabs --}}
        <nav class="flex border-b border-border text-sm font-medium text-muted bg-bg">
            <button @click="tab = 'overview'"
                    :class="{ 'border-accent text-accent': tab === 'overview' }"
                    class="px-4 py-2 border-b-2 border-transparent hover:text-text focus:outline-none">
                Overview
            </button>

            <button @click="tab = 'quests'"
                    :class="{ 'border-accent text-accent': tab === 'quests' }"
                    class="px-4 py-2 border-b-2 border-transparent hover:text-text focus:outline-none">
                Quests
            </button>

            <button @click="tab = 'npcs'"
                    :class="{ 'border-accent text-accent': tab === 'npcs' }"
                    class="px-4 py-2 border-b-2 border-transparent hover:text-text focus:outline-none">
                NPCs
            </button>
        </nav>

        {{-- Tab content --}}
        <div class="p-6">

            {{-- Overview tab --}}
            <div x-show="tab === 'overview'" x-cloak>
                @if($campaign->description)
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-text mb-2">Description</h2>
                        <p class="text-text">{{ $campaign->description }}</p>
                    </div>
                @endif

                @include('campaigns.partials.members')
            </div>

            {{-- Quests tab --}}
            <div x-show="tab === 'quests'" x-cloak>
                <div class="flex justify-end mb-4">
                    <a href="{{ route('campaigns.quests.create', $campaign) }}"
                       class="inline-flex items-center px-3 py-2 bg-accent hover:bg-accent-hover text-on-accent text-sm rounded shadow">
                        + Add quest
                    </a>
                </div>

                @include('campaigns.partials.quests')
            </div>

            {{-- NPCs tab --}}
            <div x-show="tab === 'npcs'" x-cloak>
                @include('campaigns.partials.npcs')
            </div>

        </div>
    </div>
</div>
@endsection
