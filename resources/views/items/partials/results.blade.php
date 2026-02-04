<div id="item-results" class="space-y-6" role="region" aria-live="polite" aria-atomic="true">

  {{-- Desktop Table --}}
  <div class="hidden sm:block">
    <div class="overflow-x-auto bg-surface border border-border shadow-sm sm:rounded-lg">
      <table class="min-w-full table-auto">
        <thead class="bg-bg border-b border-border">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Name</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Category</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Rarity</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Details</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">Actions</th>
          </tr>
        </thead>

        <tbody>
          @forelse($items as $item)
            <tr class="odd:bg-surface even:bg-bg hover:bg-hover transition-colors">
              <td class="px-6 py-4 text-sm font-medium text-text whitespace-normal break-words max-w-xs">
                {{ $item->name }}

                @if($item->baseItem)
                  <div class="text-xs text-muted">
                    Variant of
                    <a href="{{ route('items.show', $item->baseItem) }}" class="underline text-accent hover:text-accent-hover">
                      {{ $item->baseItem->name }}
                    </a>
                  </div>
                @endif
              </td>

              <td class="px-6 py-4 text-sm text-muted whitespace-nowrap">
                {{ $item->category?->name ?? '—' }}
              </td>

              <td class="px-6 py-4 text-sm text-muted whitespace-nowrap">
                {{ $item->rarity?->name ?? '—' }}
              </td>

              <td class="px-6 py-4 text-sm text-muted whitespace-normal break-words max-w-md">
                @if($item->weapon)
                  {{ $item->weapon->damage_dice }} {{ $item->weapon->damageType?->name }}
                @elseif($item->armor)
                  AC {{ $item->armor->base_ac }}
                  @if($item->armor->adds_dex_mod)
                    + Dex (cap {{ $item->armor->dex_mod_cap ?? '∞' }})
                  @endif
                @else
                  {{ $item->description ? Str::limit(strip_tags(Str::markdown($item->description)), 120) : '—' }}
                @endif
              </td>

              <td class="px-6 py-4 text-sm whitespace-nowrap">

                {{-- View --}}
                <a href="{{ route('items.show', $item) }}"
                   class="text-accent hover:text-accent-hover font-medium mr-3">
                  View
                </a>

                @auth
                  @if($item->is_srd)
                    {{-- Clone --}}
                    <a href="{{ route('items.custom.create', ['base_item_id' => $item->id, 'from' => request('from') ?? session('items.last_index')]) }}"
                       class="text-accent hover:text-accent-hover font-medium mr-3">
                      Clone
                    </a>
                  @else
                    {{-- Edit --}}
                    @can('update', $item)
                      <a href="{{ route('items.edit', $item) }}?from={{ request('from') ?? session('items.last_index') }}"
                         class="text-yellow-500 hover:text-yellow-600 font-medium mr-3">
                        Edit
                      </a>
                    @endcan

                    {{-- Delete --}}
                    @can('delete', $item)
                      <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline"
                            onsubmit="return confirm('Delete this item?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="text-danger hover:text-red-600 font-medium">
                          Delete
                        </button>
                      </form>
                    @endcan
                  @endif
                @endauth

              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-6 py-4 text-sm text-center text-muted">
                No items found.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Mobile Cards --}}
  <div class="sm:hidden space-y-4">
    @forelse($items as $item)
      <div class="bg-surface border border-border shadow p-4 rounded-lg">
        <div class="flex justify-between items-center">
          <div>
            <h2 class="text-lg font-medium text-text">{{ $item->name }}</h2>

            <p class="text-sm text-muted">
              {{ $item->category?->name ?? '—' }} &middot; {{ $item->rarity?->name ?? '—' }}
            </p>

            @if($item->baseItem)
              <p class="text-xs text-muted">
                Variant of
                <a href="{{ route('items.show', $item->baseItem) }}" class="underline text-accent hover:text-accent-hover">
                  {{ $item->baseItem->name }}
                </a>
              </p>
            @endif
          </div>

          <div class="flex items-center gap-3">

            {{-- View --}}
            <a href="{{ route('items.show', $item) }}"
               class="text-accent hover:text-accent-hover font-medium">
              View
            </a>

            @auth
              @if($item->is_srd)
                <a href="{{ route('items.custom.create', ['base_item_id' => $item->id, 'from' => request('from') ?? session('items.last_index')]) }}"
                   class="text-accent hover:text-accent-hover font-medium">
                  Clone
                </a>
              @else
                @can('update', $item)
                  <a href="{{ route('items.edit', $item) }}?from={{ request('from') ?? session('items.last_index') }}"
                     class="text-yellow-500 hover:text-yellow-600 font-medium">
                    Edit
                  </a>
                @endcan

                @can('delete', $item)
                  <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline"
                        onsubmit="return confirm('Delete this item?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="text-danger hover:text-red-600 font-medium">
                      Delete
                    </button>
                  </form>
                @endcan
              @endif
            @endauth

          </div>
        </div>
      </div>
    @empty
      <p class="text-center text-muted">No items found.</p>
    @endforelse
  </div>

  {{-- Pagination --}}
  @if ($items->hasPages())
    <div id="pagination-links" class="mt-4" aria-label="Pagination">
      {!! $items->withQueryString()->links('pagination::tailwind') !!}
    </div>
  @endif

</div>
