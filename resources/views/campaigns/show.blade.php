@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4">

    {{-- Back link --}}
    <a href="{{ route('campaigns.index') }}"
       class="link-action mb-4">
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
                        <h1 class="page-title text-4xl text-text">{{ $campaign->name }}</h1>
                        <p class="text-muted mt-1">
                            Dungeon Master: {{ $campaign->dm->name ?? 'Unknown' }}
                        </p>

                        {{-- tags --}}
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="ui-chip-accent">
                                Campaign
                            </span>
                        </div>
                    </div>

                    {{-- Top-right actions --}}
                    <div class="ml-auto flex gap-2">
                        @can('update', $campaign)
                            <a href="{{ route('campaigns.edit', $campaign) }}"
                               class="btn btn-warning btn-sm">
                                Edit
                            </a>
                        @endcan

                        @can('delete', $campaign)
                            <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST"
                                  onsubmit="return confirm('Delete this campaign?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-danger btn-sm">
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
        <nav class="ui-tab-nav">
            <button @click="tab = 'overview'"
                    :class="tab === 'overview' ? 'ui-tab-btn-active' : ''"
                    class="ui-tab-btn">
                Overview
            </button>

            <button @click="tab = 'sessions'"
                    :class="tab === 'sessions' ? 'ui-tab-btn-active' : ''"
                    class="ui-tab-btn">
                Sessions
            </button>

            <button @click="tab = 'quests'"
                    :class="tab === 'quests' ? 'ui-tab-btn-active' : ''"
                    class="ui-tab-btn">
                Quests
            </button>

            <button @click="tab = 'npcs'"
                    :class="tab === 'npcs' ? 'ui-tab-btn-active' : ''"
                    class="ui-tab-btn">
                NPCs
            </button>
        </nav>

        {{-- Tab content --}}
        <div class="p-6">

            {{-- Overview tab --}}
            <div x-show="tab === 'overview'" x-cloak>
                @if($campaign->description)
                    <div class="mb-6">
                        <h2 class="section-title text-2xl text-text mb-2">Description</h2>
                        <p class="text-text">{{ $campaign->description }}</p>
                    </div>
                @endif

                @include('campaigns.partials.members')
            </div>

            {{-- Sessions tab --}}
            <div x-show="tab === 'sessions'" x-cloak>
                <div class="flex justify-end mb-4">
                    @can('update', $campaign)
                        <a href="{{ route('campaigns.sessions.create', $campaign) }}"
                           class="btn btn-primary btn-sm">
                            + Log Session
                        </a>
                    @endcan
                </div>
                @include('campaigns.partials.sessions')
            </div>

            {{-- Quests tab --}}
            <div x-show="tab === 'quests'" x-cloak>
                <div class="flex justify-end mb-4">
                    <a href="{{ route('campaigns.quests.create', $campaign) }}"
                       class="btn btn-primary btn-sm">
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
