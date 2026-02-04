{{-- Desktop Table --}}
<div class="hidden sm:block">
    <div class="overflow-x-auto bg-surface border border-border shadow-sm sm:rounded-lg">
        <table class="min-w-full table-auto">
            <thead class="bg-bg border-b border-border">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Race/Species</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Class / Archetype</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Alignment</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($npcs as $npc)
                    <tr class="odd:bg-surface even:bg-bg hover:bg-hover transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-text whitespace-normal break-words max-w-xs">
                            {{ $npc->name }}
                        </td>

                        <td class="px-6 py-4 text-sm text-muted whitespace-normal break-words max-w-xs">
                            {{ $npc->race ?? '—' }}
                        </td>

                        <td class="px-6 py-4 text-sm text-muted whitespace-normal break-words max-w-xs">
                            {{ $npc['class'] ?? '—' }}
                        </td>

                        <td class="px-6 py-4 text-sm text-muted whitespace-nowrap">
                            {{ $npc['alignment'] ?? '—' }}
                        </td>

                        <td class="px-6 py-4 text-sm text-muted whitespace-normal break-words max-w-sm">
                            {{ $npc['role'] ?? '—' }}
                        </td>

                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            <a href="{{ route('compendium.npcs.show', $npc) }}"
                               class="text-accent hover:text-accent-hover font-medium">
                                View
                            </a>

                            <a href="{{ route('compendium.npcs.edit', $npc) }}"
                               class="ml-4 text-yellow-500 hover:text-yellow-600 font-medium">
                                Edit
                            </a>

                            <form action="{{ route('compendium.npcs.destroy', $npc) }}"
                                  method="POST"
                                  class="inline ml-4"
                                  onsubmit="return confirm('Delete this NPC?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-danger hover:text-red-600 font-medium">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-sm text-center text-muted">
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
        <div class="bg-surface border border-border shadow p-4 rounded-lg">
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
                   class="text-accent hover:text-accent-hover font-medium">
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
