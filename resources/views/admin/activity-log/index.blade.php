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
        Filter bar.

        Same GET-form pattern as the users list: submitting adds ?filter= to
        the URL, which the controller reads. No JavaScript needed.

        We iterate over $eventTypes (passed from the controller) so adding a
        new event type in the future only requires updating the model constants
        and the controller array — no changes needed here.
    --}}
    <form method="GET" action="{{ route('admin.activity.index') }}">
        <fieldset class="flex flex-wrap items-center gap-1.5">
            <legend class="sr-only">Filter by event type</legend>

            <button type="submit"
                    name="filter"
                    value="all"
                    class="rounded-full px-3 py-1 text-xs font-medium border transition-colors duration-150
                           focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-1
                           {{ $filter === 'all'
                                ? 'bg-accent text-white border-accent dark:bg-purple-700 dark:border-purple-600'
                                : 'bg-surface text-muted border-border hover:text-text hover:border-accent' }}"
                    aria-pressed="{{ $filter === 'all' ? 'true' : 'false' }}">
                All Events
            </button>

            @foreach ($eventTypes as $value => $label)
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

            @if ($filter !== 'all')
                <a href="{{ route('admin.activity.index') }}"
                   class="rounded-full px-3 py-1 text-xs font-medium text-muted
                          hover:text-text transition-colors duration-150"
                   aria-label="Clear filter">
                    Clear
                </a>
            @endif
        </fieldset>
    </form>

    {{-- Log table --}}
    <div class="overflow-x-auto">

        <p class="mb-3 text-xs text-muted" aria-live="polite">
            @if ($logs->total() === 0)
                No activity recorded yet.
            @elseif ($filter !== 'all')
                {{ $logs->total() }} {{ Str::plural('entry', $logs->total()) }} matching this filter.
            @else
                {{ $logs->total() }} total {{ Str::plural('entry', $logs->total()) }}.
            @endif
        </p>

        <table class="w-full text-left text-sm border-separate border-spacing-y-2"
               aria-label="Admin activity log">
            <thead>
                <tr class="text-muted text-xs uppercase tracking-wide">
                    <th class="px-3 py-2 w-44" scope="col">When</th>
                    <th class="px-3 py-2 w-40" scope="col">Event</th>
                    <th class="px-3 py-2" scope="col">Details</th>
                    <th class="px-3 py-2 w-28" scope="col">IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr class="bg-surface shadow-sm rounded-md align-top">

                        {{--
                            Timestamp. We display a human-friendly relative time
                            ("3 hours ago") by default, with the exact datetime
                            shown in a <time> element's title attribute as a
                            tooltip on hover. The datetime attribute uses ISO 8601
                            so browsers and assistive tech can interpret it.
                        --}}
                        <td class="px-3 py-3">
                            <time datetime="{{ $log->created_at->toIso8601String() }}"
                                  title="{{ $log->created_at->format('F j, Y \a\t g:i A') }}"
                                  class="text-muted text-xs">
                                {{ $log->created_at->diffForHumans() }}
                            </time>
                            <p class="text-muted text-xs mt-0.5 hidden sm:block">
                                {{ $log->created_at->format('M j, Y') }}
                            </p>
                        </td>

                        {{--
                            Event badge. Color-coded by event type for quick
                            visual scanning. The badge label is the human-readable
                            name from $eventTypes; we fall back to the raw event
                            string for any future events not yet in the map.
                        --}}
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
                                         text-xs font-medium {{ $badgeClasses }}">
                                {{ $eventTypes[$log->event] ?? $log->event }}
                            </span>
                        </td>

                        {{--
                            Details column. Shows the description sentence and,
                            if metadata exists, a subtle breakdown of extra context
                            (e.g. which fields were changed on a user edit).
                        --}}
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

                        {{-- IP address --}}
                        <td class="px-3 py-3 font-mono text-xs text-muted">
                            {{ $log->ip_address ?? '—' }}
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-3 py-10 text-center text-sm text-muted">
                            No activity has been recorded yet.
                            Actions like editing users, suspending accounts, and impersonating
                            users will appear here.
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
