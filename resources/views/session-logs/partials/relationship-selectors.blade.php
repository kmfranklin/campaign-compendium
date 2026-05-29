@php
    $selectedNpcIds = collect(old('npc_ids', $selectedNpcIds ?? []))
        ->map(fn ($id) => (string) $id)
        ->values();

    $selectedQuestIds = collect(old('quest_ids', $selectedQuestIds ?? []))
        ->map(fn ($id) => (string) $id)
        ->values();

    $npcOptions = $availableNpcs
        ->map(fn ($npc) => [
            'id' => (string) $npc->id,
            'name' => $npc->name,
            'subtitle' => collect([
                $npc->class ?: 'No class',
                $npc->status ?: 'No status',
                $npc->campaign_id === null ? 'not yet assigned to a campaign' : null,
            ])->filter()->implode(' · '),
            'group' => $npc->campaign_id === $campaign->id ? 'campaign' : 'unassigned',
        ])
        ->values();

    $questOptions = $availableQuests
        ->map(fn ($quest) => [
            'id' => (string) $quest->id,
            'title' => $quest->title,
            'status' => $quest->status->label(),
        ])
        ->values();
@endphp

<div
    x-data="{
        npcSearch: '',
        questSearch: '',
        selectedNpcIds: @js($selectedNpcIds),
        selectedQuestIds: @js($selectedQuestIds),
        npcOptions: @js($npcOptions),
        questOptions: @js($questOptions),

        normalize(value) {
            return (value || '').toLowerCase().trim();
        },

        toggleSelection(collection, id) {
            id = String(id);
            const idx = this[collection].indexOf(id);

            if (idx === -1) this[collection].push(id);
            else this[collection].splice(idx, 1);
        },

        isSelected(collection, id) {
            return this[collection].includes(String(id));
        },

        matchesNpc(option) {
            const q = this.normalize(this.npcSearch);
            if (!q) return true;

            return [option.name, option.subtitle, option.group]
                .some((value) => this.normalize(value).includes(q));
        },

        matchesQuest(option) {
            const q = this.normalize(this.questSearch);
            if (!q) return true;

            return [option.title, option.status]
                .some((value) => this.normalize(value).includes(q));
        },

        filteredNpcGroup(group) {
            return this.npcOptions.filter((option) => option.group === group && this.matchesNpc(option));
        },

        selectedNpcOptions() {
            return this.npcOptions.filter((option) => this.isSelected('selectedNpcIds', option.id));
        },

        selectedQuestOptions() {
            return this.questOptions.filter((option) => this.isSelected('selectedQuestIds', option.id));
        },
    }"
    class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6"
>
    <section class="p-4 border border-border rounded-lg bg-bg self-start">
        <div class="flex items-start justify-between gap-3 mb-3">
            <div>
                <h2 class="text-base font-semibold text-text">Characters in This Session</h2>
                <p class="text-xs text-muted mt-1">
                    Select campaign NPCs or unassigned NPCs. Unassigned NPCs will be added to this campaign when you save.
                </p>
            </div>
            <span class="ui-badge ui-badge-muted shrink-0">
                <span x-text="selectedNpcIds.length"></span> selected
            </span>
        </div>

        <template x-for="npcId in selectedNpcIds" :key="'npc-input-' + npcId">
            <input type="hidden" name="npc_ids[]" :value="npcId">
        </template>

        <div class="mb-3">
            <label for="session-npc-search" class="sr-only">Search eligible NPCs</label>
            <input
                id="session-npc-search"
                type="search"
                x-model="npcSearch"
                placeholder="Search characters by name, class, or status…"
                class="ui-field"
            >
        </div>

        <div x-show="selectedNpcOptions().length > 0" x-cloak class="mb-3 rounded-lg border border-accent/20 bg-accent/5 px-3 py-2">
            <div class="flex items-center justify-between gap-3 mb-2">
                <p class="text-xs font-semibold uppercase tracking-wide text-muted">Selected</p>
                <button
                    type="button"
                    @click="selectedNpcIds = []"
                    class="text-xs text-muted hover:text-text focus:outline-none focus:underline"
                >
                    Clear
                </button>
            </div>
            <div class="flex flex-wrap gap-2">
                <template x-for="option in selectedNpcOptions()" :key="'npc-pill-' + option.id">
                    <button
                        type="button"
                        @click="toggleSelection('selectedNpcIds', option.id)"
                        class="ui-chip-accent text-sm hover:bg-accent/15 focus:outline-none focus:ring-2 focus:ring-accent"
                    >
                        <span x-text="option.name"></span>
                        <span aria-hidden="true">&times;</span>
                    </button>
                </template>
            </div>
        </div>

        <div class="rounded-lg border border-border bg-surface">
            <div class="scrollbar-themed max-h-80 overflow-y-auto p-3 space-y-4">
                <template x-if="npcOptions.length === 0">
                    <p class="text-sm text-muted">No eligible NPCs yet. Create one in your compendium to attach it here later.</p>
                </template>

                <template x-if="npcOptions.length > 0 && filteredNpcGroup('campaign').length === 0 && filteredNpcGroup('unassigned').length === 0">
                    <p class="text-sm text-muted">No characters match your search.</p>
                </template>

                <div x-show="filteredNpcGroup('campaign').length > 0" x-cloak>
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-muted mb-2">Already in this campaign</h3>
                    <div class="space-y-2">
                        <template x-for="option in filteredNpcGroup('campaign')" :key="'campaign-npc-' + option.id">
                            <button
                                type="button"
                                @click="toggleSelection('selectedNpcIds', option.id)"
                                :class="isSelected('selectedNpcIds', option.id)
                                    ? 'border-accent bg-accent/10'
                                    : 'border-border bg-bg hover:border-accent/70'"
                                class="w-full text-left rounded-lg border px-3 py-2.5 transition-colors focus:outline-none focus:ring-2 focus:ring-accent"
                            >
                                <div class="flex items-start gap-3">
                                    <div class="pt-0.5">
                                        <span
                                            :class="isSelected('selectedNpcIds', option.id)
                                                ? 'bg-accent border-accent text-on-accent'
                                                : 'bg-surface border-border text-transparent'"
                                            class="flex h-4 w-4 items-center justify-center rounded border text-[10px] font-bold"
                                            aria-hidden="true"
                                        >
                                            ✓
                                        </span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-text" x-text="option.name"></p>
                                        <p class="text-xs text-muted mt-0.5" x-text="option.subtitle"></p>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                <div x-show="filteredNpcGroup('unassigned').length > 0" x-cloak>
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-muted mb-2">Available from your compendium</h3>
                    <div class="space-y-2">
                        <template x-for="option in filteredNpcGroup('unassigned')" :key="'unassigned-npc-' + option.id">
                            <button
                                type="button"
                                @click="toggleSelection('selectedNpcIds', option.id)"
                                :class="isSelected('selectedNpcIds', option.id)
                                    ? 'border-accent bg-accent/10'
                                    : 'border-border bg-bg hover:border-accent/70'"
                                class="w-full text-left rounded-lg border px-3 py-2.5 transition-colors focus:outline-none focus:ring-2 focus:ring-accent"
                            >
                                <div class="flex items-start gap-3">
                                    <div class="pt-0.5">
                                        <span
                                            :class="isSelected('selectedNpcIds', option.id)
                                                ? 'bg-accent border-accent text-on-accent'
                                                : 'bg-surface border-border text-transparent'"
                                            class="flex h-4 w-4 items-center justify-center rounded border text-[10px] font-bold"
                                            aria-hidden="true"
                                        >
                                            ✓
                                        </span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-text" x-text="option.name"></p>
                                        <p class="text-xs text-muted mt-0.5" x-text="option.subtitle"></p>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="p-4 border border-border rounded-lg bg-bg self-start">
        <div class="flex items-start justify-between gap-3 mb-3">
            <div>
                <h2 class="text-base font-semibold text-text">Quests Advanced or Relevant</h2>
                <p class="text-xs text-muted mt-1">
                    Link campaign quests that moved forward, were discussed, or mattered during this session.
                </p>
            </div>
            <span class="ui-badge ui-badge-muted shrink-0">
                <span x-text="selectedQuestIds.length"></span> selected
            </span>
        </div>

        <template x-for="questId in selectedQuestIds" :key="'quest-input-' + questId">
            <input type="hidden" name="quest_ids[]" :value="questId">
        </template>

        <div class="mb-3">
            <label for="session-quest-search" class="sr-only">Search campaign quests</label>
            <input
                id="session-quest-search"
                type="search"
                x-model="questSearch"
                placeholder="Search quests by title or status…"
                class="ui-field"
            >
        </div>

        <div x-show="selectedQuestOptions().length > 0" x-cloak class="mb-3 rounded-lg border border-accent/20 bg-accent/5 px-3 py-2">
            <div class="flex items-center justify-between gap-3 mb-2">
                <p class="text-xs font-semibold uppercase tracking-wide text-muted">Selected</p>
                <button
                    type="button"
                    @click="selectedQuestIds = []"
                    class="text-xs text-muted hover:text-text focus:outline-none focus:underline"
                >
                    Clear
                </button>
            </div>
            <div class="flex flex-wrap gap-2">
                <template x-for="option in selectedQuestOptions()" :key="'quest-pill-' + option.id">
                    <button
                        type="button"
                        @click="toggleSelection('selectedQuestIds', option.id)"
                        class="ui-chip-accent text-sm hover:bg-accent/15 focus:outline-none focus:ring-2 focus:ring-accent"
                    >
                        <span x-text="option.title"></span>
                        <span aria-hidden="true">&times;</span>
                    </button>
                </template>
            </div>
        </div>

        <div class="rounded-lg border border-border bg-surface">
            <div class="scrollbar-themed max-h-56 overflow-y-auto p-3 space-y-2">
                <template x-if="questOptions.length === 0">
                    <p class="text-sm text-muted">This campaign does not have any quests yet.</p>
                </template>

                <template x-if="questOptions.length > 0 && questOptions.filter((option) => matchesQuest(option)).length === 0">
                    <p class="text-sm text-muted">No quests match your search.</p>
                </template>

                <template x-for="option in questOptions.filter((quest) => matchesQuest(quest))" :key="'quest-' + option.id">
                    <button
                        type="button"
                        @click="toggleSelection('selectedQuestIds', option.id)"
                        :class="isSelected('selectedQuestIds', option.id)
                            ? 'border-accent bg-accent/10'
                            : 'border-border bg-bg hover:border-accent/70'"
                        class="w-full text-left rounded-lg border px-3 py-2.5 transition-colors focus:outline-none focus:ring-2 focus:ring-accent"
                    >
                        <div class="flex items-start gap-3">
                            <div class="pt-0.5">
                                <span
                                    :class="isSelected('selectedQuestIds', option.id)
                                        ? 'bg-accent border-accent text-on-accent'
                                        : 'bg-surface border-border text-transparent'"
                                    class="flex h-4 w-4 items-center justify-center rounded border text-[10px] font-bold"
                                    aria-hidden="true"
                                >
                                    ✓
                                </span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-text" x-text="option.title"></p>
                                <p class="text-xs text-muted mt-0.5" x-text="option.status"></p>
                            </div>
                        </div>
                    </button>
                </template>
            </div>
        </div>
    </section>
</div>
