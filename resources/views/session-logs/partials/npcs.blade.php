<div>
    <h2 class="text-lg font-semibold text-text mb-4">NPCs in This Session</h2>

    @if ($sessionLog->npcs->count())
        <div class="ui-table-shell mb-6">
            <div class="ui-table-panel rounded-lg">
            <table class="ui-table">
                <thead class="ui-table-head">
                    <tr>
                        <th class="ui-table-header">Name</th>
                        <th class="ui-table-header">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sessionLog->npcs as $npc)
                        <tr class="ui-table-row">
                            <td class="ui-table-cell-strong">
                                <a href="{{ route('compendium.npcs.show', $npc) }}"
                                   class="hover:text-accent transition-colors duration-150">
                                    {{ $npc->name }}
                                </a>
                            </td>
                            <td class="ui-table-cell">
                                @can('update', $campaign)
                                    <form action="{{ route('campaigns.sessions.npcs.detach', [$campaign, $sessionLog, $npc]) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Remove this NPC from the session?')">
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
        <p class="text-sm text-muted mb-6">No NPCs logged for this session yet.</p>
    @endif

    @can('update', $campaign)
        @if ($availableNpcs->count())
            <form action="{{ route('campaigns.sessions.npcs.attach', [$campaign, $sessionLog, '__NPC__']) }}"
                  method="POST" id="attach-npc-form">
                @csrf
                <div class="flex items-end gap-4">
                    <div class="flex-1">
                        <label for="npc_select" class="block text-sm font-medium text-text mb-1">Add NPC</label>
                        <select id="npc_select"
                                class="ui-select">
                            @foreach ($availableNpcs as $npc)
                                <option value="{{ route('campaigns.sessions.npcs.attach', [$campaign, $sessionLog, $npc]) }}">
                                    {{ $npc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                            onclick="event.preventDefault(); document.getElementById('attach-npc-form').action = document.getElementById('npc_select').value; document.getElementById('attach-npc-form').submit();"
                            class="btn btn-primary btn-sm">
                        Add
                    </button>
                </div>
            </form>
        @else
            <p class="text-sm text-muted">All campaign NPCs have been added to this session.</p>
        @endif
    @endcan
</div>
