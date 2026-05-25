@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-surface border border-border shadow rounded-lg p-6">

    {{-- Back link --}}
    <a href="{{ route('campaigns.show', $campaign) }}"
       class="link-action mb-4">
      <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
           viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 19l-7-7 7-7"/>
      </svg>
      Back to Campaign
    </a>

    <h1 class="text-2xl font-bold text-text mb-6">Create Quest</h1>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-danger/10 border border-danger text-danger rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('campaigns.quests.store', $campaign) }}" method="POST">
        @csrf

        {{-- Title --}}
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-text">Quest Title</label>
            <input type="text" name="title" id="title"
                   value="{{ old('title') }}"
                   class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                          focus:border-accent focus:ring-accent sm:text-sm"
                   required>
        </div>

        {{-- Description --}}
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-text">Description</label>
            <p class="text-xs text-muted mt-0.5 mb-1">The public-facing quest summary — what the players know.</p>
            <textarea name="description" id="description" rows="4"
                      class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                             focus:border-accent focus:ring-accent sm:text-sm">{{ old('description') }}</textarea>
        </div>

        {{-- DM Notes --}}
        <div class="mb-4">
            <label for="notes" class="block text-sm font-medium text-text">DM Notes</label>
            <p class="text-xs text-muted mt-0.5 mb-1">Private notes for the DM only — hidden twists, clues, session prep. Not visible to players.</p>
            <textarea name="notes" id="notes" rows="3"
                      class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                             focus:border-accent focus:ring-accent sm:text-sm">{{ old('notes') }}</textarea>
        </div>

        {{-- Status --}}
        <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-text">Status</label>
            <select name="status" id="status"
                    class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                           focus:border-accent focus:ring-accent sm:text-sm"
                    required>
                @foreach($statuses as $status)
                    <option value="{{ $status->value }}" @selected(old('status') === $status->value)>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Actions --}}
        <div class="pt-4 border-t border-border flex justify-between">
            <a href="{{ route('campaigns.show', $campaign) }}"
               class="btn btn-secondary btn-sm">
                Cancel
            </a>

            <button type="submit"
                    class="btn btn-primary btn-sm">
                Save Quest
            </button>
        </div>
    </form>
</div>
@endsection
