{{-- Desktop Table --}}
<div class="hidden sm:block">
    <div class="ui-table-panel ui-table-shell sm:rounded-lg">
        <table class="ui-table">
            <thead class="ui-table-head">
                <tr>
                    <th class="ui-table-header">Name</th>
                    <th class="ui-table-header">Race / Species</th>
                    <th class="ui-table-header">Class / Archetype</th>
                    <th class="ui-table-header">Alignment</th>
                    <th class="ui-table-header">Role</th>
                    <th class="ui-table-header">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($npcs as $npc)
                    <tr class="ui-table-row">
                        <td class="ui-table-cell-strong whitespace-normal break-words max-w-xs">
                            {{ $npc->name }}
                        </td>

                        <td class="ui-table-cell whitespace-normal break-words max-w-xs">
                            {{ $npc->race ?? '—' }}
                        </td>

                        <td class="ui-table-cell whitespace-normal break-words max-w-xs">
                            {{ $npc['class'] ?? '—' }}
                        </td>

                        <td class="ui-table-cell whitespace-nowrap">
                            {{ $npc['alignment'] ?? '—' }}
                        </td>

                        <td class="ui-table-cell whitespace-normal break-words max-w-sm">
                            {{ $npc['role'] ?? '—' }}
                        </td>

                        <td class="ui-table-cell whitespace-nowrap">
                            <div class="ui-table-action-row">
                            <a href="{{ route('compendium.npcs.show', $npc) }}"
                               class="ui-table-action-view">
                                View
                            </a>

                            <a href="{{ route('compendium.npcs.edit', $npc) }}"
                               class="ui-table-action-edit">
                                Edit
                            </a>

                            <form action="{{ route('compendium.npcs.destroy', $npc) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Delete this NPC?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="ui-table-action-danger">
                                    Delete
                                </button>
                            </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="ui-table-empty">
                            No NPCs found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Mobile Cards --}}
<div class="sm:hidden space-y-4">
    @forelse($npcs as $npc)
        <div class="ui-card p-4">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-lg font-medium text-text break-words">{{ $npc->name }}</h2>

                    <p class="text-sm text-muted">
                        {{ $npc['class'] ?? '—' }} &middot; {{ $npc->race ?? '—' }}
                    </p>

                    @if($npc['role'])
                        <p class="text-xs text-muted break-words">{{ $npc['role'] }}</p>
                    @endif
                </div>

                <a href="{{ route('compendium.npcs.show', $npc) }}"
                   class="ui-table-action-view">
                    View
                </a>
            </div>
        </div>
    @empty
        <p class="text-center text-muted">No NPCs found.</p>
    @endforelse
</div>

{{-- Pagination Links --}}
@if ($npcs->hasPages())
    <div id="pagination-links" class="mt-4">
        {{ $npcs->links() }}
    </div>
@endif
