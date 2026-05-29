@if($campaign->quests->count())

    {{-- Desktop Table --}}
    <div class="hidden sm:block ui-table-shell">
        <div class="ui-table-panel min-w-full sm:rounded-lg">
            <table class="ui-table">
                <thead class="ui-table-head">
                    <tr>
                        <th class="ui-table-header">
                            Title
                        </th>
                        <th class="ui-table-header">
                            Status
                        </th>
                        <th class="ui-table-header">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($campaign->quests as $quest)
                        <tr class="ui-table-row">

                            {{-- Title --}}
                            <td class="ui-table-cell-strong whitespace-nowrap">
                                <a href="{{ route('campaigns.quests.show', [$campaign, $quest]) }}"
                                   class="hover:text-accent transition-colors duration-150">
                                    {{ $quest->title }}
                                </a>
                            </td>

                            {{-- Status --}}
                            <td class="ui-table-cell whitespace-nowrap">
                                <span class="{{ $quest->status->badgeClasses() }}">
                                    {{ $quest->status->label() }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="ui-table-cell whitespace-nowrap">
                                <div class="ui-table-action-row">
                                    <a href="{{ route('campaigns.quests.show', [$campaign, $quest]) }}"
                                       class="ui-table-action-view">
                                        View
                                    </a>

                                    @can('update', $campaign)
                                        <a href="{{ route('campaigns.quests.edit', [$campaign, $quest]) }}"
                                           class="ui-table-action-edit">
                                            Edit
                                        </a>
                                    @endcan

                                    @can('delete', $campaign)
                                        <form action="{{ route('campaigns.quests.destroy', [$campaign, $quest]) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Delete this quest?')">
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile Cards --}}
    <div class="sm:hidden space-y-4">
        @foreach($campaign->quests as $quest)
            <div class="ui-card p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-medium text-text">{{ $quest->title }}</h2>
                        <div class="mt-1">
                            <span class="{{ $quest->status->badgeClasses() }}">
                                {{ $quest->status->label() }}
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('campaigns.quests.show', [$campaign, $quest]) }}"
                       class="ui-table-action-view">
                        View
                    </a>
                </div>
            </div>
        @endforeach
    </div>

@else

    <x-empty-state
        icon="🗺️"
        title="No quests yet"
        message="Start your adventure by creating the first quest."
    >
        @can('update', $campaign)
            <a href="{{ route('campaigns.quests.create', $campaign) }}"
               class="mt-3 inline-flex items-center px-3 py-2 bg-accent hover:bg-accent-hover text-on-accent text-sm rounded shadow">
                Add quest
            </a>
        @endcan
    </x-empty-state>

@endif
