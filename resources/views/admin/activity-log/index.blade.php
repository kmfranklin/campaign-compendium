@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    {{-- Page heading --}}
    <div>
        <h1 class="text-2xl font-semibold text-text">Activity Log</h1>
        <p class="mt-1 text-sm text-muted">
            A full history of admin actions. This log is read-only and cannot be edited.
        </p>
    </div>

    {{--
        Filter form.

        All filters are GET parameters so filtered URLs are bookmarkable and
        the browser back button works correctly.

        The form is split into three visual rows:
          1. Event type pills (quick single-click filter)
          2. Admin search (free-text, who performed the action)
          3. Date range (from / to date pickers)

        All three can be combined freely — the controller applies whichever
        are set as independent WHERE clauses on the same query.
    --}}
    <form method="GET"
          action="{{ route('admin.activity.index') }}"
          class="space-y-3"
          role="search"
          aria-label="Filter activity log">

        {{-- Row 1: Event type pills --}}
        <fieldset class="flex flex-wrap items-center gap-1.5">
            <legend class="sr-only">Filter by event type</legend>

            <button type="submit" name="filter" value="all"
                    class="rounded-full px-3 py-1 text-xs font-medium border transition-colors duration-150
                           focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-1
                           {{ $filter === 'all'
                                ? 'bg-accent text-white border-accent dark:bg-purple-700 dark:border-purple-600'
                                : 'bg-surface text-muted border-border hover:text-text hover:border-accent' }}"
                    aria-pressed="{{ $filter === 'all' ? 'true' : 'false' }}">
                All Events
            </button>

            @foreach ($eventTypes as $value => $label)
                <button type="submit" name="filter" value="{{ $value }}"
                        class="rounded-full px-3 py-1 text-xs font-medium border transition-colors duration-150
                               focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-1
                               {{ $filter === $value
                                    ? 'bg-accent text-white border-accent dark:bg-purple-700 dark:border-purple-600'
                                    : 'bg-surface text-muted border-border hover:text-text hover:border-accent' }}"
                        aria-pressed="{{ $filter === $value ? 'true' : 'false' }}">
                    {{ $label }}
                </button>
            @endforeach
        </fieldset>

        {{-- Row 2 & 3: Admin search + date range, side by side on wider screens --}}
        <div class="flex flex-col sm:flex-row gap-3">

            {{-- Admin search --}}
            <div class="flex-1">
                <label for="admin_search" class="block text-xs font-medium text-muted mb-1">
                    Performed by
                </label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-muted"
                          aria-hidden="true">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </span>
                    <input type="search"
                           id="admin_search"
                           name="admin_search"
                           value="{{ $adminSearch }}"
                           placeholder="Filter by admin name…"
                           class="w-full rounded-md border border-border bg-surface pl-9 pr-4 py-2 text-sm
                                  text-text placeholder-muted shadow-sm
                                  focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent
                                  dark:bg-gray-900 dark:border-gray-600 dark:text-gray-100
                                  dark:placeholder-gray-500 dark:focus:border-indigo-400">
                </div>
            </div>

            {{-- Date range --}}
            <div class="flex items-end gap-2">
                <div>
                    <label for="date_from" class="block text-xs font-medium text-muted mb-1">
                        From
                    </label>
                    <input type="date"
                           id="date_from"
                           name="date_from"
                           value="{{ $dateFrom }}"
                           class="rounded-md border border-border bg-surface px-3 py-2 text-sm text-text
                                  shadow-sm focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent
                                  dark:bg-gray-900 dark:border-gray-600 dark:text-gray-100
                                  dark:focus:border-indigo-400">
                </div>
                <div>
                    <label for="date_to" class="block text-xs font-medium text-muted mb-1">
                        To
                    </label>
                    <input type="date"
                           id="date_to"
                           name="date_to"
                           value="{{ $dateTo }}"
                           class="rounded-md border border-border bg-surface px-3 py-2 text-sm text-text
                                  shadow-sm focus:border-accent focus:outline-none focus:ring-1 focus:ring-accent
                                  dark:bg-gray-900 dark:border-gray-600 dark:text-gray-100
                                  dark:focus:border-indigo-400">
                </div>
                <button type="submit"
                        class="rounded-md bg-accent px-3 py-2 text-sm font-medium text-white shadow-sm
                               hover:bg-accent/90 focus:outline-none focus:ring-2 focus:ring-accent
                               focus:ring-offset-2 transition-colors duration-150 dark:bg-purple-700
                               dark:hover:bg-purple-600 whitespace-nowrap">
                    Apply
                </button>
            </div>
        </div>

        {{-- Hidden filter input so the current event pill selection survives a
             date/admin form submission. Without this, submitting the date inputs
             would reset the filter pill back to 'all'. --}}
        <input type="hidden" name="filter" value="{{ $filter }}">

        {{-- Clear all filters link --}}
        @if ($filter !== 'all' || $adminSearch !== '' || $dateFrom !== '' || $dateTo !== '')
            <div>
                <a href="{{ route('admin.activity.index') }}"
                   class="text-xs text-muted hover:text-text transition-colors duration-150"
                   aria-label="Clear all filters">
                    ← Clear all filters
                </a>
            </div>
        @endif

    </form>

    {{-- Log table --}}
    <div class="overflow-x-auto">

        <p class="mb-3 text-xs text-muted" aria-live="polite">
            @if ($logs->total() === 0)
                No activity recorded yet.
            @elseif ($filter !== 'all' || $adminSearch !== '' || $dateFrom !== '' || $dateTo !== '')
                {{ $logs->total() }} {{ Str::plural('entry', $logs->total()) }} matching your filters.
            @else
                {{ $logs->total() }} total {{ Str::plural('entry', $logs->total()) }}.
            @endif
        </p>

        <table class="w-full text-left text-sm border-separate border-spacing-y-2"
               aria-label="Admin activity log">
            <thead>
                <tr class="text-muted text-xs uppercase tracking-wide">
                    <th class="px-3 py-2 w-40" scope="col">When</th>
                    <th class="px-3 py-2 w-px whitespace-nowrap" scope="col">Event</th>
                    <th class="px-3 py-2 w-36" scope="col">Admin</th>
                    <th class="px-3 py-2" scope="col">Details</th>
                    <th class="px-3 py-2 w-28 hidden lg:table-cell" scope="col">IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr class="bg-surface shadow-sm rounded-md align-top">

                        {{--
                            Timestamp — browser-local time via Alpine.

                            The server renders the relative time ("3 minutes ago")
                            which is timezone-independent. The absolute date is
                            rendered client-side using the browser's Intl API so it
                            automatically uses the user's system timezone, not the
                            server's UTC. We leave the element empty until Alpine
                            initialises so the user never sees a UTC time flash.

                            The <time> element's datetime attribute always holds the
                            canonical UTC ISO string for screen readers and other tools.
                        --}}
                        <td class="px-3 py-3">
                            <time datetime="{{ $log->created_at->toIso8601String() }}"
                                  class="text-muted text-xs">
                                {{ $log->created_at->diffForHumans() }}
                            </time>
                            <p class="text-muted text-xs mt-0.5 hidden sm:block"
                               x-data
                               x-init="
                                   $el.textContent = new Intl.DateTimeFormat(undefined, {
                                       month: 'short', day: 'numeric', year: 'numeric'
                                   }).format(new Date('{{ $log->created_at->toIso8601String() }}'))
                               ">
                                {{-- Populated by Alpine with browser local date --}}
                            </p>
                        </td>

                        {{-- Event badge --}}
                        <td class="px-3 py-3">
                            @php
                                $badgeClasses = match ($log->event) {
                                    \App\Models\ActivityLog::EVENT_USER_UPDATED          =>
                                        'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300',
                                    \App\Models\ActivityLog::EVENT_USER_SUSPENDED        =>
                                        'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300',
                                    \App\Models\ActivityLog::EVENT_USER_UNSUSPENDED      =>
                                        'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300',
                                    \App\Models\ActivityLog::EVENT_IMPERSONATION_STARTED =>
                                        'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300',
                                    \App\Models\ActivityLog::EVENT_IMPERSONATION_ENDED   =>
                                        'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
                                    default =>
                                        'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5
                                         text-xs font-medium whitespace-nowrap {{ $badgeClasses }}">
                                {{ $eventTypes[$log->event] ?? $log->event }}
                            </span>
                        </td>

                        {{--
                            Admin column. The relationship may be null if the admin
                            account was deleted after the action was recorded — the
                            migration uses nullOnDelete() on the FK for exactly this
                            reason, so we show a graceful fallback instead of crashing.
                        --}}
                        <td class="px-3 py-3">
                            @if ($log->admin)
                                <span class="text-text text-sm font-medium">
                                    {{ $log->admin->name }}
                                </span>
                                <p class="text-muted text-xs mt-0.5">
                                    {{ $log->admin->email }}
                                </p>
                            @else
                                <span class="text-muted text-xs italic">Deleted user</span>
                            @endif
                        </td>

                        {{-- Details --}}
                        <td class="px-3 py-3">
                            <p class="text-text text-sm">
                                {{ $log->description }}
                            </p>
                            @if (!empty($log->metadata['changed_fields']))
                                <p class="mt-1 text-xs text-muted">
                                    Fields changed:
                                    {{ implode(', ', $log->metadata['changed_fields']) }}
                                </p>
                            @endif
                        </td>

                        {{-- IP address — hidden on smaller screens --}}
                        <td class="px-3 py-3 font-mono text-xs text-muted hidden lg:table-cell">
                            {{ $log->ip_address ?? '—' }}
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-10 text-center text-sm text-muted">
                            @if ($filter !== 'all' || $adminSearch !== '' || $dateFrom !== '' || $dateTo !== '')
                                No entries match your current filters.
                            @else
                                No activity has been recorded yet.
                                Actions like editing users, suspending accounts, and impersonating
                                users will appear here.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($logs->hasPages())
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    @endif

</div>
@endsection
