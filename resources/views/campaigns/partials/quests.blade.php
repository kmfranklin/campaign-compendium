@if($campaign->quests->count())

    {{-- Desktop Table --}}
    <div class="hidden sm:block overflow-x-auto">
        <div class="min-w-full bg-surface border border-border shadow-sm sm:rounded-lg">
            <table class="min-w-full">
                <thead class="bg-bg border-b border-border">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">
                            Title
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($campaign->quests as $quest)
                        <tr class="odd:bg-surface even:bg-bg hover:bg-hover transition-colors">

                            {{-- Title --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-text">
                                <a href="{{ route('campaigns.quests.show', [$campaign, $quest]) }}"
                                   class="text-accent hover:text-accent-hover font-medium">
                                    {{ $quest->title }}
                                </a>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-muted">
                                {{ ucfirst($quest->status) }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm">

                                <a href="{{ route('campaigns.quests.show', [$campaign, $quest]) }}"
                                   class="text-accent hover:text-accent-hover font-medium">
                                    View
                                </a>

                                <a href="{{ route('campaigns.quests.edit', [$campaign, $quest]) }}"
                                   class="ml-4 text-yellow-500 hover:text-yellow-600 font-medium">
                                    Edit
                                </a>

                                <form action="{{ route('campaigns.quests.destroy', [$campaign, $quest]) }}"
                                      method="POST"
                                      class="inline ml-4"
                                      onsubmit="return confirm('Delete this quest?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-danger hover:text-red-600 font-medium">
                                        Delete
                                    </button>
                                </form>

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
            <div class="bg-surface border border-border shadow p-4 rounded-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-medium text-text">{{ $quest->title }}</h2>
                        <p class="text-sm text-muted">
                            Status: {{ ucfirst($quest->status) }}
                        </p>
                    </div>

                    <a href="{{ route('campaigns.quests.show', [$campaign, $quest]) }}"
                       class="text-accent hover:text-accent-hover font-medium">
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
        <a href="{{ route('campaigns.quests.create', $campaign) }}"
           class="mt-3 inline-flex items-center px-3 py-2 bg-accent hover:bg-accent-hover text-on-accent text-sm rounded shadow">
            Add quest
        </a>
    </x-empty-state>

@endif
