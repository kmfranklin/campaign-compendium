@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-surface border border-border shadow rounded-lg p-6">

    <a href="{{ route('campaigns.show', $campaign) }}"
       class="link-action mb-4">
        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Campaign
    </a>

    <h1 class="text-2xl font-bold text-text mb-6">Log a Session</h1>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-danger/10 border border-danger text-danger rounded" role="alert">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('campaigns.sessions.store', $campaign) }}" method="POST"
          enctype="multipart/form-data"
          x-data="{ submitting: false }"
          @submit="submitting = true">
        @csrf

        {{-- Title --}}
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-text">Session Title</label>
            <p class="text-xs text-muted mt-0.5 mb-1">e.g. "Session 4: The Sunken Temple"</p>
            <input type="text" name="title" id="title"
                   value="{{ old('title') }}"
                   class="ui-field mt-1"
                   required>
        </div>

        {{-- Session Date --}}
        <div class="mb-4">
            <label for="session_date" class="block text-sm font-medium text-text">Session Date</label>
            <input type="date" name="session_date" id="session_date"
                   value="{{ old('session_date', now()->toDateString()) }}"
                   class="ui-field mt-1"
                   required>
        </div>

        {{-- Summary --}}
        <div class="mb-4">
            <label for="summary" class="block text-sm font-medium text-text">Session Summary</label>
            <p class="text-xs text-muted mt-0.5 mb-1">What happened? Loose ends, memorable moments, things to remember next time.</p>
            <textarea name="summary" id="summary" rows="6"
                      class="ui-textarea mt-1">{{ old('summary') }}</textarea>
        </div>

        @include('session-logs.partials.relationship-selectors')

        <div class="pt-4 border-t border-border flex justify-between">
            <a href="{{ route('campaigns.show', $campaign) }}"
               class="btn btn-secondary btn-sm">
                Cancel
            </a>
            <button type="submit"
                    :disabled="submitting"
                    class="btn btn-primary btn-sm">
                <span x-show="!submitting">Save Session</span>
                <span x-show="submitting" x-cloak>Saving...</span>
            </button>
        </div>
    </form>
</div>
@endsection
