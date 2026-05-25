<div>
    <h2 class="text-lg font-semibold text-text mb-4">Quests Advanced This Session</h2>

    @if ($sessionLog->quests->count())
        <div class="ui-table-shell mb-6">
            <div class="ui-table-panel rounded-lg">
            <table class="ui-table">
                <thead class="ui-table-head">
                    <tr>
                        <th class="ui-table-header">Quest</th>
                        <th class="ui-table-header">Status</th>
                        <th class="ui-table-header">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sessionLog->quests as $quest)
                        <tr class="ui-table-row">
                            <td class="ui-table-cell-strong">
                                <a href="{{ route('campaigns.quests.show', [$campaign, $quest]) }}"
                                   class="hover:text-accent transition-colors duration-150">
                                    {{ $quest->title }}
                                </a>
                            </td>
                            <td class="ui-table-cell">
                                <span class="{{ $quest->status->badgeClasses() }}">
                                    {{ $quest->status->label() }}
                                </span>
                            </td>
                            <td class="ui-table-cell">
                                @can('update', $campaign)
                                    <form action="{{ route('campaigns.sessions.quests.detach', [$campaign, $sessionLog, $quest]) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Remove this quest from the session?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ui-table-action-danger">
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
                                class="ui-select">
                            @foreach ($availableQuests as $quest)
                                <option value="{{ route('campaigns.sessions.quests.attach', [$campaign, $sessionLog, $quest]) }}">
                                    {{ $quest->title }} ({{ $quest->status->label() }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                            onclick="event.preventDefault(); document.getElementById('attach-quest-form').action = document.getElementById('quest_select').value; document.getElementById('attach-quest-form').submit();"
                            class="btn btn-primary btn-sm">
                        Add
                    </button>
                </div>
            </form>
        @else
            <p class="text-sm text-muted">All campaign quests have been linked to this session.</p>
        @endif
    @endcan
</div>
