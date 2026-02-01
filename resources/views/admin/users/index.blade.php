@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-10 space-y-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Users</h1>

    <p class="text-gray-600 dark:text-gray-400">
        View and manage all user accounts.
    </p>

    <div class="overflow-x-auto">
        <table class="mt-6 w-full text-left text-sm border-separate border-spacing-y-2">
            <thead>
                <tr class="text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wide">
                    <th class="px-3 py-2">ID</th>
                    <th class="px-3 py-2">Name</th>
                    <th class="px-3 py-2">Email</th>
                    <th class="px-3 py-2">Role</th>
                    <th class="px-3 py-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="bg-white dark:bg-gray-800 shadow-sm rounded-md">
                        <td class="px-3 py-3 font-mono text-xs text-gray-700 dark:text-gray-300">
                            {{ $user->id }}
                        </td>
                        <td class="px-3 py-3 text-gray-900 dark:text-white font-medium">
                            {{ $user->name }}
                        </td>
                        <td class="px-3 py-3 text-gray-600 dark:text-gray-400">
                            {{ $user->email }}
                        </td>
                        <td class="px-3 py-3">
                            @if ($user->is_super_admin)
                                <span class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300">
                                    Super Admin
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    User
                                </span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-right">
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button @click="open = !open"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6h.01M12 12h.01M12 18h.01" />
                                    </svg>
                                </button>

                                <div x-show="open" @click.away="open = false"
                                     class="absolute right-0 z-10 mt-2 w-40 origin-top-right rounded-md bg-white dark:bg-gray-900 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                     x-transition>
                                    <div class="py-1 text-sm text-gray-700 dark:text-gray-300">
                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                           class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-800">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.users.signInAs', $user->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-800">
                                                Sign in as
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
