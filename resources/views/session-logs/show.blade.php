@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4">

    <a href="{{ route('campaigns.show', $campaign) }}"
       class="inline-flex items-center text-sm text-accent hover:text-accent-hover mb-4 font-medium">
        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Campaign
    </a>

    <div x-data="{ tab: 'overview' }"
         class="bg-surface border border-border shadow-md rounded-lg overflow-hidden">

        {{-- Tab nav --}}
        <nav class="flex border-b border-border text-sm font-medium text-muted" aria-label="Session sections">
            <button @click="tab = 'overview'"
                    :class="{ 'border-accent text-accent': tab === 'overview' }"
                    :aria-selected="tab === 'overview'" role="tab"
                    class="px-4 py-2 border-b-2 border-transparent hover:text-text focus:outline-none">
                Overview
            </button>
            <button @click="tab = 'npcs'"
                    :class="{ 'border-accent text-accent': tab === 'npcs' }"
                    :aria-selected="tab === 'npcs'" role="tab"
                    class="px-4 py-2 border-b-2 border-transparent hover:text-text focus:outline-none">
                NPCs
            </button>
            <button @click="tab = 'quests'"
                    :class="{ 'border-accent text-accent': tab === 'quests' }"
                    :aria-selected="tab === 'quests'" role="tab"
                    class="px-4 py-2 border-b-2 border-transparent hover:text-text focus:outline-none">
                Quests
            </button>
        </nav>

        <div class="p-6">

            {{-- Overview tab --}}
            <div x-show="tab === 'overview'" x-cloak>

                {{-- Header --}}
                <div class="flex items-start mb-6">
                    <div class="flex-1">
                        <h1 class="page-title text-4xl text-text">{{ $sessionLog->title }}</h1>
                        <p class="text-muted mt-1">
                            {{ $sessionLog->session_date->format('F j, Y') }}
                        </p>
                        <div class="mt-3">
                            <span class="bg-accent/10 text-accent text-xs font-medium px-2 py-1 rounded">
                                Session Log
                            </span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="ml-auto flex gap-2">
                        @can('update', $campaign)
                            <a href="{{ route('campaigns.sessions.edit', [$campaign, $sessionLog]) }}"
                               class="px-4 py-2 bg-warning text-on-warning font-semibold rounded hover:bg-warning-hover">
                                Edit
                            </a>
                        @endcan
                        @can('delete', $campaign)
                            <form action="{{ route('campaigns.sessions.destroy', [$campaign, $sessionLog]) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this session log?')">
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

                {{-- Audio player --}}
                @if ($sessionLog->media && $sessionLog->media->isAudio())
                    <div class="mb-6 p-4 border border-border rounded-lg bg-bg">
                        <h2 class="eyebrow-label text-sm mb-2 flex items-center gap-1.5 text-text">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-accent" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                            </svg>
                            Session Recording
                            <span class="text-xs font-normal text-muted">
                                — {{ $sessionLog->media->filename }}
                                ({{ $sessionLog->media->formattedSize() }})
                            </span>
                        </h2>
                        {{-- Native HTML5 audio player — no JS libraries needed --}}
                        <audio controls class="w-full mt-1"
                               aria-label="Session recording: {{ $sessionLog->media->filename }}">
                            <source src="{{ $sessionLog->media->url() }}" type="{{ $sessionLog->media->mime_type }}">
                            Your browser does not support the audio element.
                            <a href="{{ $sessionLog->media->url() }}" class="text-accent underline">Download recording</a>
                        </audio>
                    </div>
                @endif

                {{-- Summary --}}
                @if ($sessionLog->summary)
                    <div class="mb-6">
                        <h2 class="section-title text-2xl text-text mb-2">Summary</h2>
                        <p class="text-text whitespace-pre-line">{{ $sessionLog->summary }}</p>
                    </div>
                @endif

            </div>

            {{-- NPCs tab --}}
            <div x-show="tab === 'npcs'" x-cloak>
                @include('session-logs.partials.npcs')
            </div>

            {{-- Quests tab --}}
            <div x-show="tab === 'quests'" x-cloak>
                @include('session-logs.partials.quests')
            </div>

        </div>
    </div>
</div>
@endsection
