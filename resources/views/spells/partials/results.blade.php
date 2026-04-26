<div id="spell-results" class="space-y-6" role="region" aria-live="polite" aria-atomic="true">

    {{-- Desktop table --}}
    <div class="hidden sm:block overflow-x-auto bg-surface border border-border shadow-sm rounded-lg">
        <table class="min-w-full table-auto">
            <thead class="bg-bg border-b border-border">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Level</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">School</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Casting Time</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Classes</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider w-px">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($spells as $spell)
                    <tr class="odd:bg-surface even:bg-bg hover:bg-hover transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-text">
                            {{ $spell->name }}
                            <div class="flex gap-1 mt-1">
                                @if($spell->concentration)
                                    <span class="text-xs bg-blue-500/10 text-blue-400 px-1.5 py-0.5 rounded">Concentration</span>
                                @endif
                                @if($spell->ritual)
                                    <span class="text-xs bg-purple-500/10 text-purple-400 px-1.5 py-0.5 rounded">Ritual</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-muted whitespace-nowrap">{{ $spell->level_label }}</td>
                        <td class="px-6 py-4 text-sm text-muted whitespace-nowrap">{{ $spell->school?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-muted whitespace-nowrap">{{ $spell->casting_time_label }}</td>
                        <td class="px-6 py-4 text-sm text-muted">{{ implode(', ', $spell->class_names) }}</td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            <a href="{{ route('spells.show', $spell) }}"
                               class="text-accent hover:text-accent-hover font-medium underline underline-offset-2">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-muted">No spells found matching your filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="sm:hidden space-y-3">
        @forelse($spells as $spell)
            <div class="bg-surface border border-border shadow rounded-lg p-4">
                <div class="flex justify-between items-start gap-2">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-sm font-medium text-text">{{ $spell->name }}</h2>
                        <p class="text-xs text-muted mt-0.5">
                            {{ $spell->level_label }} &middot; {{ $spell->school?->name ?? '—' }} &middot; {{ $spell->casting_time_label }}
                        </p>
                        <div class="flex gap-1 mt-1">
                            @if($spell->concentration)
                                <span class="text-xs bg-blue-500/10 text-blue-400 px-1.5 py-0.5 rounded">Concentration</span>
                            @endif
                            @if($spell->ritual)
                                <span class="text-xs bg-purple-500/10 text-purple-400 px-1.5 py-0.5 rounded">Ritual</span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('spells.show', $spell) }}"
                       class="text-accent hover:text-accent-hover text-sm font-medium underline underline-offset-2 shrink-0">View</a>
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
