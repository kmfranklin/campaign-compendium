@php
    $npcMap = collect();

    foreach ($quests as $quest) {
        foreach ($quest->npcs as $npc) {
            $npcMap[$npc->id] ??= [
                'npc' => $npc,
                'quests' => collect(),
            ];

            $npcMap[$npc->id]['quests']->push([
                'title' => $quest->title,
                'role' => $npc->pivot->role,
                'quest_id' => $quest->id,
            ]);
        }
    }
@endphp

@if($npcMap->isEmpty())
    <x-empty-state
        icon="🧙"
        title="No NPCs yet"
        message="Bring your world to life by adding characters."
    />
@else

    {{-- Add NPC button --}}
    <div class="flex justify-end mb-4">
        <a href="{{ route('compendium.npcs.create') }}"
           class="inline-flex items-center px-3 py-2 bg-accent hover:bg-accent-hover text-on-accent text-sm rounded shadow">
            + Add NPC
        </a>
    </div>

    {{-- Supporting text --}}
    <p class="text-sm text-muted mb-4">
        These NPCs are involved in one or more quests in this campaign. Click a name to view their roles and profile.
    </p>

    {{-- Collapsible NPC list --}}
    <ul class="divide-y divide-border">
        @foreach($npcMap as $entry)
            @php $npc = $entry['npc']; @endphp

            <li x-data="{ open: false }" class="py-4">

                {{-- Toggle button --}}
                <button @click="open = !open"
                        class="w-full text-left flex justify-between items-center font-medium text-text hover:text-accent focus:outline-none">
                    <span>{{ $npc->name }}</span>

                    <svg :class="{ 'rotate-180': open }"
                         class="h-5 w-5 transform transition-transform duration-200"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Expanded content --}}
                <div x-show="open" x-cloak class="mt-3 pl-4 text-sm text-text space-y-2">

                    {{-- Quest list --}}
                    <ul class="space-y-1">
                        @foreach($entry['quests'] as $q)
                            <li>
                                <a href="{{ route('campaigns.quests.show', [$campaign, $q['quest_id']]) }}"
                                   class="text-accent hover:text-accent-hover">
                                    {{ $q['title'] }}
                                </a>

                                <span class="ml-2 text-muted">
                                    ({{ Str::headline($q['role']) }})
                                </span>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Profile link --}}
                    <a href="{{ route('compendium.npcs.show', $npc) }}"
                       class="inline-block text-accent hover:text-accent-hover font-medium">
                        View full profile →
                    </a>

                </div>
            </li>
        @endforeach
    </ul>

@endif
