<div>
    <h2 class="text-lg font-semibold text-text mb-4">NPCs in This Session</h2>

    @if ($sessionLog->npcs->count())
        <div class="overflow-x-auto mb-6">
            <table class="min-w-full bg-surface border border-border rounded-lg">
                <thead class="bg-bg border-b border-border">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sessionLog->npcs as $npc)
                        <tr class="odd:bg-surface even:bg-bg hover:bg-hover">
                            <td class="px-6 py-4 text-sm font-medium">
                                <a href="{{ route('compendium.npcs.show', $npc) }}"
                                   class="text-accent hover:text-accent-hover">
                                    {{ $npc->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @can('update', $campaign)
                                    <form action="{{ route('campaigns.sessions.npcs.detach', [$campaign, $sessionLog, $npc]) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Remove this NPC from the session?')">
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
                                class="block w-full rounded-md border border-border bg-surface text-text shadow-sm
                                       focus:border-accent focus:ring-accent sm:text-sm">
                            @foreach ($availableNpcs as $npc)
                                <option value="{{ route('campaigns.sessions.npcs.attach', [$campaign, $sessionLog, $npc]) }}">
                                    {{ $npc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                            onclick="event.preventDefault(); document.getElementById('attach-npc-form').action = document.getElementById('npc_select').value; document.getElementById('attach-npc-form').submit();"
                            class="px-4 py-2 bg-accent text-on-accent text-sm font-semibold rounded
                                   hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent">
                        Add
                    </button>
                </div>
            </form>
        @else
            <p class="text-sm text-muted">All campaign NPCs have been added to this session.</p>
        @endif
    @endcan
</div>
