@if ($campaign->sessionLogs->count())

    <div class="space-y-3">
        @foreach ($campaign->sessionLogs as $sessionLog)
            <div class="flex items-center justify-between p-4 bg-bg border border-border rounded-lg hover:bg-hover transition-colors">
                <div class="flex-1 min-w-0">
                    <a href="{{ route('campaigns.sessions.show', [$campaign, $sessionLog]) }}"
                       class="text-base font-medium text-accent hover:text-accent-hover truncate block">
                        {{ $sessionLog->title }}
                    </a>
                    <p class="text-sm text-muted mt-0.5">
                        {{ $sessionLog->session_date->format('F j, Y') }}
                        @if ($sessionLog->media)
                            <span class="ml-2 inline-flex items-center gap-1 text-xs text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                </svg>
                                Recording attached
                            </span>
                        @endif
                    </p>
                </div>
                <a href="{{ route('campaigns.sessions.show', [$campaign, $sessionLog]) }}"
                   class="ml-4 text-sm text-accent hover:text-accent-hover font-medium shrink-0">
                    View →
                </a>
            </div>
        @endforeach
    </div>

@else

    <x-empty-state
        icon="📜"
        title="No sessions logged yet"
        message="After your first session, log what happened to keep a chronicle of your campaign."
    >
        @can('update', $campaign)
            <a href="{{ route('campaigns.sessions.create', $campaign) }}"
               class="mt-3 inline-flex items-center px-3 py-2 bg-accent hover:bg-accent-hover text-on-accent text-sm rounded shadow">
                Log a Session
            </a>
        @endcan
    </x-empty-state>

@endif
