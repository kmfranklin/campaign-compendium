<div id="spell-results" class="space-y-6" role="region" aria-live="polite" aria-atomic="true">

    {{-- Desktop table --}}
    <div class="hidden sm:block ui-table-panel ui-table-shell rounded-lg">
        <table class="ui-table">
            <thead class="ui-table-head">
                <tr>
                    <th scope="col" class="ui-table-header">Name</th>
                    <th scope="col" class="ui-table-header">Level</th>
                    <th scope="col" class="ui-table-header">School</th>
                    <th scope="col" class="ui-table-header">Casting Time</th>
                    <th scope="col" class="ui-table-header">Classes</th>
                    <th scope="col" class="ui-table-header w-px">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($spells as $spell)
                    <tr class="ui-table-row">
                        <td class="ui-table-cell-strong">
                            {{ $spell->name }}
                            <div class="flex gap-1 mt-1">
                                @if($spell->concentration)
                                    <span class="ui-chip-info">Concentration</span>
                                @endif
                                @if($spell->ritual)
                                    <span class="ui-chip-accent">Ritual</span>
                                @endif
                            </div>
                        </td>
                        <td class="ui-table-cell whitespace-nowrap">{{ $spell->level_label }}</td>
                        <td class="ui-table-cell whitespace-nowrap">{{ $spell->school?->name ?? '—' }}</td>
                        <td class="ui-table-cell whitespace-nowrap">{{ $spell->casting_time_label }}</td>
                        <td class="ui-table-cell">{{ implode(', ', $spell->class_names) }}</td>
                        <td class="ui-table-cell whitespace-nowrap">
                            <a href="{{ route('spells.show', [$spell, ...request()->query()]) }}"
                               class="ui-table-action-view">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="ui-table-empty">No spells found matching your filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="sm:hidden space-y-3">
        @forelse($spells as $spell)
            <div class="ui-card p-4">
                <div class="flex justify-between items-start gap-2">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-sm font-medium text-text">{{ $spell->name }}</h2>
                        <p class="text-xs text-muted mt-0.5">
                            {{ $spell->level_label }} &middot; {{ $spell->school?->name ?? '—' }} &middot; {{ $spell->casting_time_label }}
                        </p>
                        <div class="flex gap-1 mt-1">
                            @if($spell->concentration)
                                <span class="ui-chip-info">Concentration</span>
                            @endif
                            @if($spell->ritual)
                                <span class="ui-chip-accent">Ritual</span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('spells.show', [$spell, ...request()->query()]) }}"
                       class="ui-table-action-view shrink-0">View</a>
                </div>
            </div>
        @empty
            <p class="text-center text-sm text-muted py-8">No spells found matching your filters.</p>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($spells->hasPages())
        <div class="mt-4" aria-label="Pagination">
            {!! $spells->withQueryString()->links('pagination::tailwind') !!}
        </div>
    @endif

</div>
