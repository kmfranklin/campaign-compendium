@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-surface border border-border shadow rounded-lg p-6">

    <a href="{{ route('campaigns.show', $campaign) }}"
       class="inline-flex items-center text-sm text-accent hover:text-accent-hover mb-4 font-medium">
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

    {{-- x-data tracks file selection state and upload-in-progress to give feedback
         on large file uploads where the page appears frozen without indication. --}}
    <form action="{{ route('campaigns.sessions.store', $campaign) }}" method="POST"
          enctype="multipart/form-data"
          x-data="{ uploading: false, fileName: '', fileSize: '' }"
          @submit="uploading = true">
        @csrf

        {{-- Title --}}
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-text">Session Title</label>
            <p class="text-xs text-muted mt-0.5 mb-1">e.g. "Session 4: The Sunken Temple"</p>
            <input type="text" name="title" id="title"
                   value="{{ old('title') }}"
                   class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                          focus:border-accent focus:ring-accent sm:text-sm"
                   required>
        </div>

        {{-- Session Date --}}
        <div class="mb-4">
            <label for="session_date" class="block text-sm font-medium text-text">Session Date</label>
            <input type="date" name="session_date" id="session_date"
                   value="{{ old('session_date', now()->toDateString()) }}"
                   class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                          focus:border-accent focus:ring-accent sm:text-sm"
                   required>
        </div>

        {{-- Summary --}}
        <div class="mb-4">
            <label for="summary" class="block text-sm font-medium text-text">Session Summary</label>
            <p class="text-xs text-muted mt-0.5 mb-1">What happened? Loose ends, memorable moments, things to remember next time.</p>
            <textarea name="summary" id="summary" rows="6"
                      class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                             focus:border-accent focus:ring-accent sm:text-sm">{{ old('summary') }}</textarea>
        </div>

        {{-- Audio Recording Upload --}}
        <div class="mb-6 p-4 border border-border rounded-lg bg-bg">
            <label for="media" class="block text-sm font-medium text-text">Session Recording</label>
            <p class="text-xs text-muted mt-0.5 mb-2">
                Optional. Attach an audio recording of this session.
                Accepted formats: MP3, WAV, OGG, FLAC, M4A, AAC. Max 200 MB.
            </p>
            <input type="file" name="media" id="media"
                   accept="audio/mpeg,audio/wav,audio/ogg,audio/flac,audio/mp4,audio/aac,.mp3,.wav,.ogg,.flac,.m4a,.aac"
                   class="block w-full text-sm text-text
                          file:mr-4 file:py-2 file:px-4 file:rounded file:border-0
                          file:text-sm file:font-medium file:bg-accent file:text-on-accent
                          hover:file:bg-accent-hover"
                   @change="
                       const f = $event.target.files[0];
                       if (f) {
                           fileName = f.name;
                           fileSize = (f.size / (1024 * 1024)).toFixed(1) + ' MB';
                       } else {
                           fileName = ''; fileSize = '';
                       }
                   ">

            {{-- Show selected file name + size once chosen --}}
            <p x-show="fileName" x-cloak class="mt-2 text-xs text-muted">
                Selected: <span class="text-text font-medium" x-text="fileName"></span>
                (<span x-text="fileSize"></span>) — this may take a moment to upload.
            </p>

            @error('media')
                <p class="mt-1 text-xs text-danger">{{ $message }}</p>
            @enderror
        </div>

        {{-- Upload progress notice --}}
        <div x-show="uploading" x-cloak
             class="mb-4 flex items-center gap-3 p-3 bg-accent/10 border border-accent/30 rounded-lg text-sm text-accent">
            <svg class="animate-spin h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Uploading recording — please don't close this tab…
        </div>

        <div class="pt-4 border-t border-border flex justify-between">
            <a href="{{ route('campaigns.show', $campaign) }}"
               class="px-4 py-2 bg-bg text-text rounded border border-border hover:bg-hover">
                Cancel
            </a>
            <button type="submit"
                    :disabled="uploading"
                    class="px-6 py-2 bg-accent text-on-accent font-semibold rounded
                           hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent
                           disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!uploading">Save Session</span>
                <span x-show="uploading" x-cloak>Saving…</span>
            </button>
        </div>
    </form>
</div>
@endsection
