@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    {{-- Page heading --}}
    <div>
        <h1 class="text-2xl font-semibold text-text">Users</h1>
        <p class="mt-1 text-sm text-muted">
            View and manage all user accounts.
        </p>
    </div>

    {{--
        Search and filter bar.

        This is a standard GET form — submitting it adds ?search= and ?filter=
        to the URL, which the controller reads. Using GET (not POST) is
        intentional: it means the filtered URL is shareable and the browser's
        back button works correctly. No JavaScript needed for basic filtering.

        The role="search" landmark tells screen readers this region is a search
        form, giving it its own landmark in the page structure.
    --}}
    <form method="GET"
          action="{{ route('admin.users.index') }}"
          role="search"
          class="flex flex-col sm:flex-row gap-3">

        {{-- Text search --}}
        <div class="relative flex-1">
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-muted"
                  aria-hidden="true">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                </svg>
            </span>
            <input type="search"
                   id="search"
                   name="search"
                   value="{{ $search }}"
                   placeholder="Search by name or email…"
                   class="w-full rounded-md border border-border bg-surface pl-9 pr-4 py-2 text-sm
                          text-text placeholder-muted shadow-sm
                          focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent
                          dark:bg-gray-900 dark:border-gray-600 dark:text-gray-100
                          dark:placeholder-gray-500 dark:focus:border-indigo-400"
                   aria-label="Search users by name or email">
        </div>

        {{--
            Filter pills. These are radio-style buttons grouped inside a
            <fieldset> so screen readers announce them as a group ("Filter by
            role or status"). We use regular submit buttons instead of JS
            click handlers — each one submits the form with its own filter
            value via a hidden input swapped by a tiny Alpine snippet.
        --}}
        <fieldset class="flex items-center gap-1.5">
            <legend class="sr-only">Filter by role or status</legend>

            @foreach ([
                'all'         => 'All',
                'user'        => 'Users',
                'super_admin' => 'Super Admins',
                'suspended'   => 'Suspended',
            ] as $value => $label)
                <button type="submit"
                        name="filter"
                        value="{{ $value }}"
                        class="rounded-full px-3 py-1 text-xs font-medium border transition-colors duration-150
                               focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-1
                               {{ $filter === $value
                                    ? 'bg-accent text-white border-accent dark:bg-purple-700 dark:border-purple-600'
                                    : 'bg-surface text-muted border-border hover:text-text hover:border-accent' }}"
                        aria-pressed="{{ $filter === $value ? 'true' : 'false' }}">
                    {{ $label }}
                </button>
            @endforeach

            @if ($search !== '' || $filter !== 'all')
                <a href="{{ route('admin.users.index') }}"
                   class="rounded-full px-3 py-1 text-xs font-medium text-muted
                          hover:text-text transition-colors duration-150"
                   aria-label="Clear all filters">
                    Clear
                </a>
            @endif
        </fieldset>

    </form>

    {{-- Table --}}
    <div class="overflow-x-auto">

        {{-- Result count feedback for screen readers and sighted users alike --}}
        <p class="mb-3 text-xs text-muted" aria-live="polite">
            @if ($users->total() === 0)
                No users found.
            @elseif ($search !== '' || $filter !== 'all')
                {{ $users->total() }} {{ Str::plural('result', $users->total()) }} found.
            @else
                {{ $users->total() }} {{ Str::plural('user', $users->total()) }} total.
            @endif
        </p>

        <table class="w-full text-left text-sm border-separate border-spacing-y-2"
               aria-label="User accounts">
            <thead>
                <tr class="text-muted text-xs uppercase tracking-wide">
                    <th class="px-3 py-2" scope="col">ID</th>
                    <th class="px-3 py-2" scope="col">Name</th>
                    <th class="px-3 py-2" scope="col">Email</th>
                    <th class="px-3 py-2" scope="col">Status</th>
                    <th class="px-3 py-2 text-right" scope="col">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="bg-surface shadow-sm rounded-md
                               {{ $user->isSuspended() ? 'opacity-60' : '' }}">

                        <td class="px-3 py-3 font-mono text-xs text-muted">
                            {{ $user->id }}
                        </td>

                        <td class="px-3 py-3 text-text font-medium">
                            {{ $user->name }}
                        </td>

                        <td class="px-3 py-3 text-muted">
                            {{ $user->email }}
                        </td>

                        {{--
                            Status badge. Priority order: Suspended > Super Admin > User.
                            A suspended super admin would show Suspended (edge case, but
                            the controller currently blocks suspending super admins, so
                            this is belt-and-suspenders defensive display logic).
                        --}}
                        <td class="px-3 py-3">
                            @if ($user->isSuspended())
                                <span class="inline-flex items-center rounded-full
                                             bg-red-100 dark:bg-red-900/40
                                             px-2.5 py-0.5 text-xs font-medium
                                             text-red-700 dark:text-red-300">
                                    Suspended
                                </span>
                            @elseif ($user->isSuperAdmin())
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

                        {{-- Actions dropdown --}}
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
                                     x-transition
                                     class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md
                                            bg-surface shadow-lg ring-1 ring-black ring-opacity-5
                                            focus:outline-none"
                                     role="menu"
                                     aria-orientation="vertical">
                                    <div class="py-1 text-sm text-text">

                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                           class="block px-4 py-2 hover:bg-bg"
                                           role="menuitem">
                                            Edit
                                        </a>

                                        @if (!$user->isSuspended() && $user->id !== auth()->id() && !$user->isSuperAdmin())
                                            <form method="POST"
                                                  action="{{ route('admin.users.suspend', $user->id) }}">
                                                @csrf
                                                <button type="submit"
                                                        class="w-full text-left px-4 py-2 text-red-600
                                                               dark:text-red-400 hover:bg-bg"
                                                        role="menuitem">
                                                    Suspend
                                                </button>
                                            </form>
                                        @endif

                                        @if ($user->isSuspended())
                                            <form method="POST"
                                                  action="{{ route('admin.users.unsuspend', $user->id) }}">
                                                @csrf
                                                <button type="submit"
                                                        class="w-full text-left px-4 py-2 text-green-600
                                                               dark:text-green-400 hover:bg-bg"
                                                        role="menuitem">
                                                    Restore
                                                </button>
                                            </form>
                                        @endif

                                        @if (!$user->isSuspended())
                                            <form action="{{ route('admin.users.signInAs', $user->id) }}"
                                                  method="POST">
                                                @csrf
                                                <button type="submit"
                                                        class="w-full text-left px-4 py-2 hover:bg-bg"
                                                        role="menuitem">
                                                    Sign in as
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </div>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-8 text-center text-sm text-muted">
                            No users match your search.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{--
        Pagination links. Laravel's built-in paginator renders Tailwind-styled
        links automatically. withQueryString() on the paginator (set in the
        controller) ensures ?search= and ?filter= are preserved across pages.
    --}}
    @if ($users->hasPages())
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    @endif

</div>
@endsection
