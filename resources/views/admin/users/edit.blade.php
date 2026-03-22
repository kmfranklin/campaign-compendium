@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{--
        Breadcrumb navigation. The <nav aria-label="Breadcrumb"> landmark and <ol>
        structure communicate the page hierarchy to screen readers explicitly. Each
        crumb except the last is a link; the last item is the current page and marked
        with aria-current="page" so assistive technology announces "current page" after
        reading the label. The separating "/" is aria-hidden so it's not read aloud.
    --}}
    <nav aria-label="Breadcrumb">
        <ol class="flex items-center gap-1.5 text-sm text-muted flex-wrap">
            <li>
                <a href="{{ route('admin.dashboard') }}"
                   class="hover:text-text transition-colors duration-150">
                    Admin Dashboard
                </a>
            </li>
            <li aria-hidden="true" class="text-muted select-none">/</li>
            <li>
                <a href="{{ route('admin.users.index') }}"
                   class="hover:text-text transition-colors duration-150">
                    User Management
                </a>
            </li>
            <li aria-hidden="true" class="text-muted select-none">/</li>
            <li>
                <span class="text-text font-medium" aria-current="page">
                    {{ $user->name }}
                </span>
            </li>
        </ol>
    </nav>

    {{-- Page title --}}
    <div>
        <h1 class="text-2xl font-semibold text-text">Edit User</h1>
        <p class="mt-1 text-sm text-muted">
            ID #{{ $user->id }}
        </p>
    </div>

    {{-- Main User Info Form --}}
    <div class="bg-surface border border-border rounded-lg shadow-sm p-6">
        <form method="POST"
              action="{{ route('admin.users.update', $user) }}"
              class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div>
                <x-input-label for="name" value="Name" />
                <x-text-input id="name"
                              name="name"
                              type="text"
                              class="mt-1 block w-full"
                              value="{{ old('name', $user->name) }}"
                              required
                              autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            {{-- Email --}}
            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email"
                              name="email"
                              type="email"
                              class="mt-1 block w-full"
                              value="{{ old('email', $user->email) }}"
                              required
                              autocomplete="email" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Password --}}
            <div>
                <x-input-label for="password" value="New Password (optional)" />
                <x-text-input id="password"
                              name="password"
                              type="password"
                              class="mt-1 block w-full"
                              autocomplete="new-password" />
                <p class="mt-1 text-xs text-muted">
                    Leave blank to keep the current password.
                </p>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{--
                Account option toggles grouped in a <fieldset> with a <legend>.
                This is the semantically correct way to group related form controls —
                screen readers announce the legend text before each control within the
                group, giving users the context that these are "Account Options".
            --}}
            <fieldset>
                <legend class="text-sm font-medium text-text mb-3">Account Options</legend>
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">

                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox"
                               id="is_super_admin"
                               name="is_super_admin"
                               value="1"
                               class="rounded border-gray-300 text-accent shadow-sm
                                      focus:ring-accent dark:border-gray-600
                                      dark:bg-gray-800 dark:checked:bg-accent"
                               @checked(old('is_super_admin', $user->is_super_admin))>
                        <span class="text-sm text-text">Super Admin</span>
                    </label>

                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox"
                               id="force_email_verification"
                               name="force_email_verification"
                               value="1"
                               class="rounded border-gray-300 text-accent shadow-sm
                                      focus:ring-accent dark:border-gray-600
                                      dark:bg-gray-800 dark:checked:bg-accent">
                        <span class="text-sm text-text">Mark Email as Verified</span>
                    </label>

                </div>
            </fieldset>

            <div class="flex justify-end">
                <x-primary-button>
                    Save Changes
                </x-primary-button>
            </div>
        </form>
    </div>

    {{-- Danger Zone (placeholder for suspend / delete / restore) --}}
    <div class="bg-surface border border-red-300 dark:border-red-800 rounded-lg shadow-sm p-6">
        <h2 class="text-sm font-semibold text-red-700 dark:text-red-400">Danger Zone</h2>
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">
            Suspend, delete, or restore actions will appear here in a future update.
        </p>
    </div>

</div>
@endsection
