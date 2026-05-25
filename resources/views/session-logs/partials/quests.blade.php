<div>
    <h2 class="text-lg font-semibold text-text mb-4">Quests Advanced This Session</h2>

    @if ($sessionLog->quests->count())
        <div class="overflow-x-auto mb-6">
            <table class="min-w-full bg-surface border border-border rounded-lg">
                <thead class="bg-bg border-b border-border">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Quest</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sessionLog->quests as $quest)
                        <tr class="odd:bg-surface even:bg-bg hover:bg-hover">
                            <td class="px-6 py-4 text-sm font-medium">
                                <a href="{{ route('campaigns.quests.show', [$campaign, $quest]) }}"
                                   class="text-accent hover:text-accent-hover">
                                    {{ $quest->title }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="{{ $quest->status->badgeClasses() }}">
                                    {{ $quest->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @can('update', $campaign)
                                    <form action="{{ route('campaigns.sessions.quests.detach', [$campaign, $sessionLog, $quest]) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Remove this quest from the session?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-danger hover:text-red-600 font-medium">
                                            Remove
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-sm text-muted mb-6">No quests logged for this session yet.</p>
    @endif

    @can('update', $campaign)
        @if ($availableQuests->count())
            <form action="#" method="POST" id="attach-quest-form">
                @csrf
                <div class="flex items-end gap-4">
                    <div class="flex-1">
                        <label for="quest_select" class="block text-sm font-medium text-text mb-1">Mark Quest as Advanced</label>
                        <select id="quest_select"
                                class="block w-full rounded-md border border-border bg-surface text-text shadow-sm
                                       focus:border-accent focus:ring-accent sm:text-sm">
                            @foreach ($availableQuests as $quest)
                                <option value="{{ route('campaigns.sessions.quests.attach', [$campaign, $sessionLog, $quest]) }}">
                                    {{ $quest->title }} ({{ $quest->status->label() }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                            onclick="event.preventDefault(); document.getElementById('attach-quest-form').action = document.getElementById('quest_select').value; document.getElementById('attach-quest-form').submit();"
                            class="px-4 py-2 bg-accent text-on-accent text-sm font-semibold rounded
                                   hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent">
                        Add
                    </button>
                </div>
            </form>
        @else
            <p class="text-sm text-muted">All campaign quests have been linked to this session.</p>
        @endif
    @endcan
</div>
