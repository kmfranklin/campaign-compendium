@php use Illuminate\Support\Str; @endphp

<div>
    <h2 class="text-lg font-semibold text-text mb-4">Involved NPCs</h2>

    @if($quest->npcs->count())
        {{-- Desktop table --}}
        <div class="hidden sm:block ui-table-shell mb-6">
            <div class="ui-table-panel min-w-full sm:rounded-lg">
                <table class="ui-table">
                    <thead class="ui-table-head">
                        <tr>
                            <th class="ui-table-header">
                                Name
                            </th>
                            <th class="ui-table-header">
                                Role
                            </th>
                            <th class="ui-table-header">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($quest->npcs as $npc)
                            <tr class="ui-table-row">
                                <td class="ui-table-cell-strong whitespace-nowrap">
                                    <a href="{{ route('compendium.npcs.show', $npc) }}"
                                       class="hover:text-accent transition-colors duration-150">
                                        {{ $npc->name }}
                                    </a>
                                </td>

                                <td class="ui-table-cell whitespace-nowrap">
                                    {{ $npc->pivot->role ? Str::headline($npc->pivot->role) : '—' }}
                                </td>

                                <td class="ui-table-cell whitespace-nowrap">
                                    @can('update', $campaign)
                                        <form action="{{ route('campaigns.quests.npcs.detach', [$campaign, $quest, $npc]) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Detach this NPC from the quest?');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="ui-table-action-danger">
                                                Detach
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
        <p class="text-sm text-muted mb-6">No NPCs attached to this quest.</p>
    @endif

    {{-- Attach NPC --}}
    @can('update', $campaign)
        <form action="{{ route('campaigns.quests.npcs.attach', [$campaign, $quest]) }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="npc_id" class="block text-sm font-medium text-text">NPC</label>
                    <select name="npc_id" id="npc_id"
                            class="ui-select mt-1"
                            required>
                        @foreach($availableNpcs as $npc)
                            <option value="{{ $npc->id }}">{{ $npc->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label for="role" class="block text-sm font-medium text-text">Role (optional)</label>
                    <input type="text" name="role" id="role"
                           class="ui-field mt-1"
                           placeholder="quest_giver, ally, enemy">
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit"
                        class="btn btn-primary btn-sm">
                    Attach NPC
                </button>
            </div>
        </form>
    @endcan
</div>
