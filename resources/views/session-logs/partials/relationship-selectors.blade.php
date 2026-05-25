@php
    $selectedNpcIds = collect(old('npc_ids', $selectedNpcIds ?? []))->map(fn ($id) => (int) $id)->all();
    $selectedQuestIds = collect(old('quest_ids', $selectedQuestIds ?? []))->map(fn ($id) => (int) $id)->all();

    $campaignNpcs = $availableNpcs->filter(fn ($npc) => $npc->campaign_id === $campaign->id);
    $unassignedNpcs = $availableNpcs->filter(fn ($npc) => $npc->campaign_id === null);
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <section class="p-4 border border-border rounded-lg bg-bg">
        <div class="flex items-start justify-between gap-3 mb-3">
            <div>
                <h2 class="text-base font-semibold text-text">Characters in This Session</h2>
                <p class="text-xs text-muted mt-1">
                    Select campaign NPCs or unassigned NPCs. Unassigned NPCs will be added to this campaign when you save.
                </p>
            </div>
            <span class="text-xs font-medium text-muted bg-surface px-2 py-1 rounded">
                {{ count($selectedNpcIds) }} selected
            </span>
        </div>

        @if ($availableNpcs->isNotEmpty())
            <div class="space-y-4 max-h-72 overflow-y-auto pr-1">
                @if ($campaignNpcs->isNotEmpty())
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-muted mb-2">Already in this campaign</h3>
                        <div class="space-y-2">
                            @foreach ($campaignNpcs as $npc)
                                <label class="flex items-start gap-3 rounded-lg border border-border bg-surface px-3 py-2 hover:border-accent cursor-pointer">
                                    <input type="checkbox"
                                           name="npc_ids[]"
                                           value="{{ $npc->id }}"
                                           @checked(in_array($npc->id, $selectedNpcIds, true))
                                           class="mt-0.5 rounded border-border text-accent focus:ring-accent">
                                    <span class="min-w-0">
                                        <span class="block text-sm font-medium text-text">{{ $npc->name }}</span>
                                        <span class="block text-xs text-muted">
                                            {{ $npc->class ?: 'No class' }} · {{ $npc->status ?: 'No status' }}
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($unassignedNpcs->isNotEmpty())
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-muted mb-2">Available from your compendium</h3>
                        <div class="space-y-2">
                            @foreach ($unassignedNpcs as $npc)
                                <label class="flex items-start gap-3 rounded-lg border border-border bg-surface px-3 py-2 hover:border-accent cursor-pointer">
                                    <input type="checkbox"
                                           name="npc_ids[]"
                                           value="{{ $npc->id }}"
                                           @checked(in_array($npc->id, $selectedNpcIds, true))
                                           class="mt-0.5 rounded border-border text-accent focus:ring-accent">
                                    <span class="min-w-0">
                                        <span class="block text-sm font-medium text-text">{{ $npc->name }}</span>
                                        <span class="block text-xs text-muted">
                                            {{ $npc->class ?: 'No class' }} · {{ $npc->status ?: 'No status' }} · not yet assigned to a campaign
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @else
            <p class="text-sm text-muted">No eligible NPCs yet. Create one in your compendium to attach it here later.</p>
        @endif
    </section>

    <section class="p-4 border border-border rounded-lg bg-bg">
        <div class="flex items-start justify-between gap-3 mb-3">
            <div>
                <h2 class="text-base font-semibold text-text">Quests Advanced or Relevant</h2>
                <p class="text-xs text-muted mt-1">
                    Link campaign quests that moved forward, were discussed, or mattered during this session.
                </p>
            </div>
            <span class="text-xs font-medium text-muted bg-surface px-2 py-1 rounded">
                {{ count($selectedQuestIds) }} selected
            </span>
        </div>

        @if ($availableQuests->isNotEmpty())
            <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                @foreach ($availableQuests as $quest)
                    <label class="flex items-start gap-3 rounded-lg border border-border bg-surface px-3 py-2 hover:border-accent cursor-pointer">
                        <input type="checkbox"
                               name="quest_ids[]"
                               value="{{ $quest->id }}"
                               @checked(in_array($quest->id, $selectedQuestIds, true))
                               class="mt-0.5 rounded border-border text-accent focus:ring-accent">
                        <span class="min-w-0">
                            <span class="block text-sm font-medium text-text">{{ $quest->title }}</span>
                            <span class="block text-xs text-muted">
                                {{ $quest->status->label() }}
                            </span>
                        </span>
                    </label>
                @endforeach
            </div>
        @else
            <p class="text-sm text-muted">This campaign does not have any quests yet.</p>
        @endif
    </section>
</div>
