<form class="mb-4 flex gap-4 flex-wrap flex-col sm:flex-row items-stretch sm:items-center">

    {{-- Search Input --}}
    <input
        name="q"
        x-model="q"
        type="text"
        placeholder="Search by name…"
        @input.debounce.500ms="applyFilters"
        class="flex-1 px-3 py-2 rounded-md border border-border bg-surface text-text
               focus:border-accent focus:ring-accent shadow-sm sm:text-sm"
    />

    {{-- Category Filter --}}
    <select
        name="category"
        x-model="categoryFilter"
        @change="applyFilters"
        class="rounded-md border border-border bg-surface text-text px-3 py-2
               focus:border-accent focus:ring-accent shadow-sm sm:text-sm"
    >
        <option value="">All Categories</option>
        @foreach(\App\Models\ItemCategory::all() as $c)
            <option value="{{ $c->slug }}">{{ $c->name }}</option>
        @endforeach
    </select>

    {{-- Rarity Filter --}}
    <select
        name="rarity"
        x-model="rarityFilter"
        @change="applyFilters"
        class="rounded-md border border-border bg-surface text-text px-3 py-2
               focus:border-accent focus:ring-accent shadow-sm sm:text-sm"
    >
        <option value="">All Rarities</option>
        @foreach(\App\Models\ItemRarity::all() as $r)
            <option value="{{ $r->slug }}">{{ $r->name }}</option>
        @endforeach
    </select>

    {{-- Search Button --}}
    <button
        type="button"
        @click="applyFilters"
        class="px-4 py-2 bg-accent text-on-accent rounded-md font-medium
               hover:bg-accent-hover focus:ring-accent"
    >
        Search
    </button>

    {{-- Reset Button --}}
    <a href="{{ url()->current() }}"
       class="px-4 py-2 bg-bg text-text rounded-md font-medium text-center
              hover:bg-hover border border-border"
    >
        Reset
    </a>

</form>
