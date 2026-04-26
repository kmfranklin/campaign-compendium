<div id="creature-results" class="space-y-6" role="region" aria-live="polite" aria-atomic="true">

    {{-- Desktop table --}}
    <div class="hidden sm:block overflow-x-auto bg-surface border border-border shadow-sm rounded-lg">
        <table class="min-w-full table-auto">
            <thead class="bg-bg border-b border-border">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Type</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Size</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">CR</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">AC</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">HP</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider w-px">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($creatures as $creature)
                    <tr class="odd:bg-surface even:bg-bg hover:bg-hover transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-text">{{ $creature->name }}</td>
                        <td class="px-6 py-4 text-sm text-muted whitespace-nowrap">{{ $creature->type?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-muted whitespace-nowrap">{{ ucfirst($creature->size ?? '—') }}</td>
                        <td class="px-6 py-4 text-sm text-muted whitespace-nowrap">{{ $creature->cr_display }}</td>
                        <td class="px-6 py-4 text-sm text-muted whitespace-nowrap">{{ $creature->armor_class ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-muted whitespace-nowrap">{{ $creature->hit_points ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            <a href="{{ route('creatures.show', $creature) }}"
                               class="text-accent hover:text-accent-hover font-medium underline underline-offset-2">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-sm text-muted">No monsters found matching your filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="sm:hidden space-y-3">
        @forelse($creatures as $creature)
            <div class="bg-surface border border-border shadow rounded-lg p-4">
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
                    <a href="{{ route('creatures.show', $creature) }}"
                       class="text-accent hover:text-accent-hover text-sm font-medium underline underline-offset-2 shrink-0">View</a>
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
