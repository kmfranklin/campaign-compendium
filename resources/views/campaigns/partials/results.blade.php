{{-- Desktop Table --}}
<div class="hidden sm:block">
    <div class="ui-table-panel ui-table-shell w-full max-w-7xl mx-auto sm:rounded-2xl">
        <table class="ui-table">
            <thead class="ui-table-head">
                <tr>
                    <th class="ui-table-header">
                        Name
                    </th>
                    <th class="ui-table-header">
                        Description
                    </th>
                    <th class="ui-table-header">
                        Actions
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse($campaigns as $campaign)
                    <tr class="ui-table-row">
                        <td class="ui-table-cell-strong whitespace-normal break-words">
                            {{ $campaign->name }}
                        </td>

                        <td class="ui-table-cell whitespace-normal break-words">
                            {{ $campaign->description ? Str::limit($campaign->description, 120) : '—' }}
                        </td>

                        <td class="ui-table-cell whitespace-nowrap">
                            <div class="ui-table-action-row">
                            {{-- View is always visible --}}
                            <a href="{{ route('campaigns.show', $campaign) }}"
                               class="ui-table-action-view">
                                View
                            </a>

                            {{-- Only show Edit if authorized --}}
                            @can('update', $campaign)
                                <a href="{{ route('campaigns.edit', $campaign) }}"
                                   class="ui-table-action-edit">
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
                                            class="ui-table-action-danger">
                                        Delete
                                    </button>
                                </form>
                            @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="ui-table-empty">
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
        <div class="ui-card p-4">
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
                   class="ui-table-action-view">
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
