<div class="space-y-6" role="region" aria-live="polite" aria-atomic="true">

    {{-- Desktop table --}}
    <div class="hidden sm:block ui-table-panel ui-table-shell rounded-lg">
        <table class="ui-table">
            <thead class="ui-table-head">
                <tr>
                    <th scope="col" class="ui-table-header">Name</th>
                    <th scope="col" class="ui-table-header">Type</th>
                    <th scope="col" class="ui-table-header">Size</th>
                    <th scope="col" class="ui-table-header">CR</th>
                    <th scope="col" class="ui-table-header">AC</th>
                    <th scope="col" class="ui-table-header">HP</th>
                    <th scope="col" class="ui-table-header w-px">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($creatures as $creature)
                    <tr class="ui-table-row">
                        <td class="ui-table-cell-strong">{{ $creature->name }}</td>
                        <td class="ui-table-cell whitespace-nowrap">{{ $creature->type?->name ?? '—' }}</td>
                        <td class="ui-table-cell whitespace-nowrap">{{ ucfirst($creature->size ?? '—') }}</td>
                        <td class="ui-table-cell whitespace-nowrap">{{ $creature->cr_display }}</td>
                        <td class="ui-table-cell whitespace-nowrap">{{ $creature->armor_class ?? '—' }}</td>
                        <td class="ui-table-cell whitespace-nowrap">{{ $creature->hit_points ?? '—' }}</td>
                        <td class="ui-table-cell whitespace-nowrap">
                            <a href="{{ route('creatures.show', [$creature, ...request()->query()]) }}"
                               class="ui-table-action-view">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="ui-table-empty">No monsters found matching your filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="sm:hidden space-y-3">
        @forelse($creatures as $creature)
            <div class="ui-card p-4">
                <div class="flex justify-between items-start gap-2">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-sm font-medium text-text">{{ $creature->name }}</h2>
                        <p class="text-xs text-muted mt-0.5">
                            {{ ucfirst($creature->size ?? '') }} {{ $creature->type?->name ?? '' }}
                            &middot; CR {{ $creature->cr_display }}
                            &middot; AC {{ $creature->armor_class ?? '—' }}
                            &middot; {{ $creature->hit_points ?? '—' }} HP
                        </p>
                    </div>
                    <a href="{{ route('creatures.show', [$creature, ...request()->query()]) }}"
                       class="ui-table-action-view shrink-0">View</a>
                </div>
            </div>
        @empty
            <p class="text-center text-sm text-muted py-8">No monsters found matching your filters.</p>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($creatures->hasPages())
        <div class="mt-4" aria-label="Pagination">
            {!! $creatures->withQueryString()->links('pagination::tailwind') !!}
        </div>
    @endif

</div>
