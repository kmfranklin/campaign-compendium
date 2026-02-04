@php use Illuminate\Support\Str; @endphp

<div>
    <h2 class="text-lg font-semibold text-text mb-4">Involved NPCs</h2>

    @if($quest->npcs->count())
        {{-- Desktop table --}}
        <div class="hidden sm:block overflow-x-auto mb-6">
            <div class="min-w-full bg-surface border border-border shadow-sm sm:rounded-lg">
                <table class="min-w-full">
                    <thead class="bg-bg border-b border-border">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">
                                Role
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($quest->npcs as $npc)
                            <tr class="odd:bg-surface even:bg-bg hover:bg-hover">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-text">
                                    <a href="{{ route('compendium.npcs.show', $npc) }}"
                                       class="text-accent hover:text-accent-hover font-medium">
                                        {{ $npc->name }}
                                    </a>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-muted">
                                    {{ $npc->pivot->role ? Str::headline($npc->pivot->role) : '—' }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @can('update', $campaign)
                                        <form action="{{ route('campaigns.quests.npcs.detach', [$campaign, $quest, $npc]) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Detach this NPC from the quest?');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="text-danger hover:text-red-600 font-medium">
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
                            class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                                   focus:border-accent focus:ring-accent sm:text-sm"
                            required>
                        @foreach($availableNpcs as $npc)
                            <option value="{{ $npc->id }}">{{ $npc->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label for="role" class="block text-sm font-medium text-text">Role (optional)</label>
                    <input type="text" name="role" id="role"
                           class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                                  focus:border-accent focus:ring-accent sm:text-sm"
                           placeholder="quest_giver, ally, enemy">
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit"
                        class="px-6 py-2 bg-accent text-on-accent font-semibold rounded
                               hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent">
                    Attach NPC
                </button>
            </div>
        </form>
    @endcan
</div>
