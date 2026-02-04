{{-- Desktop Table --}}
<div class="hidden sm:block">
    <div class="w-full max-w-7xl mx-auto overflow-x-auto bg-surface border border-border shadow-sm sm:rounded-lg">
        <table class="min-w-full table-auto">
            <thead class="bg-bg">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">
                        Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">
                        Description
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse($campaigns as $campaign)
                    <tr class="odd:bg-surface even:bg-bg hover:bg-hover">
                        <td class="px-6 py-4 text-sm font-medium text-text whitespace-normal break-words">
                            {{ $campaign->name }}
                        </td>

                        <td class="px-6 py-4 text-sm text-muted whitespace-normal break-words">
                            {{ $campaign->description ? Str::limit($campaign->description, 120) : '—' }}
                        </td>

                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            {{-- View is always visible --}}
                            <a href="{{ route('campaigns.show', $campaign) }}"
                               class="text-accent hover:text-accent-hover font-medium focus:outline-none focus:ring-2 focus:ring-accent">
                                View
                            </a>

                            {{-- Only show Edit if authorized --}}
                            @can('update', $campaign)
                                <a href="{{ route('campaigns.edit', $campaign) }}"
                                   class="ml-4 text-yellow-600 hover:text-yellow-700 font-medium focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                    Edit
                                </a>
                            @endcan

                            {{-- Only show Delete if authorized --}}
                            @can('delete', $campaign)
                                <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST"
                                      class="inline ml-4"
                                      onsubmit="return confirm('Delete this campaign?');">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="text-danger hover:text-red-700 font-medium focus:outline-none focus:ring-2 focus:ring-red-500">
                                        Delete
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-sm text-center text-muted">
                            No campaigns found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Mobile Cards --}}
<div class="sm:hidden space-y-4">
    @forelse($campaigns as $campaign)
        <div class="bg-surface border border-border shadow p-4 rounded-lg">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-lg font-medium text-text break-words">
                        {{ $campaign->name }}
                    </h2>

                    @if($campaign->description)
                        <p class="text-sm text-muted break-words">
                            {{ Str::limit($campaign->description, 120) }}
                        </p>
                    @endif
                </div>

                <a href="{{ route('campaigns.show', $campaign) }}"
                   class="text-accent hover:text-accent-hover font-medium focus:outline-none focus:ring-2 focus:ring-accent">
                    View
                </a>
            </div>
        </div>
    @empty
        <p class="text-center text-muted">No campaigns found.</p>
    @endforelse
</div>

{{-- Pagination Links --}}
@if ($campaigns->hasPages())
    <div id="pagination-links" class="mt-4">
        {{ $campaigns->links() }}
    </div>
@endif
