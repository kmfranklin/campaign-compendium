@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-semibold text-text">Users</h1>
        <p class="mt-1 text-sm text-muted">
            View and manage all user accounts.
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-separate border-spacing-y-2">
            <thead>
                <tr class="text-muted text-xs uppercase tracking-wide">
                    <th class="px-3 py-2" scope="col">ID</th>
                    <th class="px-3 py-2" scope="col">Name</th>
                    <th class="px-3 py-2" scope="col">Email</th>
                    <th class="px-3 py-2" scope="col">Role</th>
                    <th class="px-3 py-2 text-right" scope="col">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="bg-surface shadow-sm rounded-md">
                        <td class="px-3 py-3 font-mono text-xs text-muted">
                            {{ $user->id }}
                        </td>
                        <td class="px-3 py-3 text-text font-medium">
                            {{ $user->name }}
                        </td>
                        <td class="px-3 py-3 text-muted">
                            {{ $user->email }}
                        </td>
                        <td class="px-3 py-3">
                            @if ($user->is_super_admin)
                                <span class="inline-flex items-center rounded-full
                                             bg-indigo-100 dark:bg-indigo-900
                                             px-2.5 py-0.5 text-xs font-medium
                                             text-indigo-800 dark:text-indigo-300">
                                    Super Admin
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full
                                             bg-gray-100 dark:bg-gray-700
                                             px-2.5 py-0.5 text-xs font-medium
                                             text-gray-700 dark:text-gray-300">
                                    User
                                </span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-right">
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button @click="open = !open"
                                        type="button"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full
                                               hover:bg-gray-100 dark:hover:bg-gray-700
                                               focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2"
                                        :aria-expanded="open.toString()"
                                        aria-haspopup="true"
                                        aria-label="Actions for {{ $user->name }}">
                                    <svg class="w-4 h-4 text-muted"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24"
                                         aria-hidden="true">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M12 6h.01M12 12h.01M12 18h.01" />
                                    </svg>
                                </button>

                                <div x-show="open"
                                     @click.away="open = false"
                                     @keydown.escape.window="open = false"
                                     class="absolute right-0 z-10 mt-2 w-44 origin-top-right rounded-md
                                            bg-surface shadow-lg ring-1 ring-black ring-opacity-5
                                            focus:outline-none"
                                     x-transition
                                     role="menu"
                                     aria-orientation="vertical">
                                    <div class="py-1 text-sm text-text">
                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                           class="block px-4 py-2 hover:bg-bg"
                                           role="menuitem">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.users.signInAs', $user->id) }}"
                                              method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="w-full text-left px-4 py-2 hover:bg-bg"
                                                    role="menuitem">
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
