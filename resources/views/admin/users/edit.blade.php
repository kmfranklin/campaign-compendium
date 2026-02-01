<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                    Edit User
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $user->name }} (ID: {{ $user->id }})
                </p>
            </div>

            <a href="{{ route('admin.users.index') }}"
               class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                ← Back to Users
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto space-y-6">

            @if (session('success'))
                <div class="rounded-md bg-green-50 dark:bg-green-900/30 p-4">
                    <p class="text-sm text-green-700 dark:text-green-300">
                        {{ session('success') }}
                    </p>
                </div>
            @endif

            {{-- Main User Info Form --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Name --}}
                    <div>
                        <x-input-label for="name" value="Name" />
                        <x-text-input id="name" name="name" type="text"
                                      class="mt-1 block w-full"
                                      value="{{ old('name', $user->name) }}" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    {{-- Email --}}
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email"
                                      class="mt-1 block w-full"
                                      value="{{ old('email', $user->email) }}" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Password --}}
                    <div>
                        <x-input-label for="password" value="New Password (optional)" />
                        <x-text-input id="password" name="password" type="password"
                                      class="mt-1 block w-full" />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Leave blank to keep the current password.
                        </p>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    {{-- Toggles --}}
                    <div class="flex items-center gap-6">

                        {{-- Super Admin Toggle --}}
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_super_admin" value="1"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                   @checked(old('is_super_admin', $user->is_super_admin))>
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Super Admin
                            </span>
                        </label>

                        {{-- Force Email Verification --}}
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="force_email_verification" value="1"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Mark Email as Verified
                            </span>
                        </label>

                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>
                            Save Changes
                        </x-primary-button>
                    </div>
                </form>
            </div>

            {{-- Placeholder for Danger Zone (Suspend/Delete/Restore) --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 border border-red-300 dark:border-red-700">
                <h3 class="text-sm font-semibold text-red-700 dark:text-red-400">
                    Danger Zone
                </h3>
                <p class="mt-1 text-sm text-red-600 dark:text-red-300">
                    Suspend, delete, or restore actions will appear here in the next steps.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
