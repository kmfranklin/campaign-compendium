@extends('layouts.app')

@section('content')
@php
    $oldInviteEmails = collect(old('invite_emails', []))
        ->map(fn ($email) => trim((string) $email))
        ->filter()
        ->values()
        ->all();
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <a href="{{ route('campaigns.index') }}"
       class="link-action mb-4">
        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Campaigns
    </a>

    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-danger bg-danger-soft px-4 py-4 text-sm text-danger">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_20rem]">
        <form action="{{ route('campaigns.store') }}"
              method="POST"
              x-data="campaignInviteQueue(@js($oldInviteEmails))"
              class="space-y-6">
            @csrf

            <section class="ui-card p-6 sm:p-7">
                <div class="max-w-3xl">
                    <p class="eyebrow-label">Start Here</p>
                    <h1 class="mt-2 page-title text-4xl text-text">Create Campaign</h1>
                    <p class="mt-3 text-sm text-muted">
                        Name the adventure, add the pitch your players will see, and optionally queue invitations so your table can join as soon as the campaign is live.
                    </p>
                </div>

                <div class="mt-8 space-y-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-text mb-1.5">Campaign Name</label>
                        <input type="text" name="name" id="name"
                               value="{{ old('name') }}"
                               class="ui-field"
                               placeholder="Curse of Strahd, Moonlit Heist, The Ember Coast..."
                               required>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-text mb-1.5">Description</label>
                        <textarea name="description" id="description" rows="5"
                                  class="ui-textarea"
                                  placeholder="Give players a quick sense of the setting, tone, and what makes this campaign yours.">{{ old('description') }}</textarea>
                        <p class="mt-2 text-xs text-muted">Optional, but useful for invites and giving your future self a clear campaign snapshot.</p>
                    </div>
                </div>
            </section>

            <section class="ui-card p-6 sm:p-7">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="eyebrow-label">Optional</p>
                        <h2 class="mt-2 section-title text-2xl text-text">Invite Players</h2>
                        <p class="mt-2 text-sm text-muted max-w-2xl">
                            Add email addresses now and Campaign Compendium will queue invitations automatically after the campaign is created.
                        </p>
                    </div>
                    <span class="ui-chip-muted shrink-0">
                        <span x-text="inviteEmails.length"></span> queued
                    </span>
                </div>

                <div class="mt-6 rounded-2xl border border-border bg-bg/40 p-4">
                    <label for="invite-email" class="block text-sm font-medium text-text mb-1.5">Player Email</label>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <input id="invite-email"
                               type="email"
                               x-model.trim="emailInput"
                               @keydown.enter.prevent="addEmail()"
                               class="ui-field"
                               placeholder="player@example.com">
                        <button type="button"
                                @click="addEmail()"
                                class="btn btn-secondary btn-sm sm:self-start">
                            Add to Queue
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-muted">
                        Existing users will get an in-app notification too. You can always invite more people later from the campaign page.
                    </p>
                    <p x-show="errorMessage"
                       x-cloak
                       class="mt-3 text-sm text-danger"
                       x-text="errorMessage"></p>
                </div>

                <template x-for="(email, index) in inviteEmails" :key="email">
                    <input type="hidden" name="invite_emails[]" :value="email">
                </template>

                <div class="mt-5" x-show="inviteEmails.length > 0" x-cloak>
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-muted">Queued invites</p>
                        <button type="button"
                                @click="clearQueue()"
                                class="text-xs text-muted hover:text-text focus:outline-none focus:underline">
                            Clear all
                        </button>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <template x-for="email in inviteEmails" :key="'pill-' + email">
                            <button type="button"
                                    @click="removeEmail(email)"
                                    class="ui-chip-accent text-sm hover:bg-accent/15 focus:outline-none focus:ring-2 focus:ring-accent">
                                <span x-text="email"></span>
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </template>
                    </div>
                </div>
            </section>

            <div class="flex flex-col-reverse gap-3 border-t border-border pt-4 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('campaigns.index') }}"
                   class="btn btn-secondary btn-sm">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-primary btn-sm">
                    Create Campaign
                </button>
            </div>
        </form>

        <aside class="space-y-6 xl:sticky xl:top-6 self-start">
            <section class="ui-card p-5">
                <p class="eyebrow-label">What Happens Next</p>
                <h2 class="mt-2 text-lg font-semibold text-text">Your first session setup</h2>
                <ol class="mt-4 space-y-3 text-sm text-muted">
                    <li class="flex gap-3">
                        <span class="ui-chip-muted shrink-0">1</span>
                        <span>Create the campaign shell and claim the DM seat.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="ui-chip-muted shrink-0">2</span>
                        <span>Invite players now or later from the campaign page.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="ui-chip-muted shrink-0">3</span>
                        <span>Add quests, NPCs, and your first session notes.</span>
                    </li>
                </ol>
            </section>

            <section class="ui-card p-5">
                <p class="eyebrow-label">Helpful Prompt</p>
                <h2 class="mt-2 text-lg font-semibold text-text">A strong campaign description usually includes:</h2>
                <ul class="mt-4 space-y-2 text-sm text-muted">
                    <li>The setting or premise</li>
                    <li>The vibe or tone players should expect</li>
                    <li>What the party is trying to survive, solve, or discover</li>
                </ul>
            </section>
        </aside>
    </div>
</div>

<script>
    function campaignInviteQueue(initialEmails = []) {
        return {
            emailInput: '',
            inviteEmails: initialEmails,
            errorMessage: '',

            addEmail() {
                const email = this.emailInput.trim().toLowerCase();

                if (!email) {
                    return;
                }

                if (!this.isValidEmail(email)) {
                    this.errorMessage = 'Enter a valid email address before adding it to the queue.';
                    return;
                }

                if (this.inviteEmails.includes(email)) {
                    this.errorMessage = 'That email is already in the invite queue.';
                    return;
                }

                if (this.inviteEmails.length >= 12) {
                    this.errorMessage = 'You can queue up to 12 invite emails when creating a campaign.';
                    return;
                }

                this.inviteEmails.push(email);
                this.emailInput = '';
                this.errorMessage = '';
            },

            removeEmail(email) {
                this.inviteEmails = this.inviteEmails.filter((value) => value !== email);
            },

            clearQueue() {
                this.inviteEmails = [];
                this.errorMessage = '';
            },

            isValidEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            },
        };
    }
</script>
@endsection
