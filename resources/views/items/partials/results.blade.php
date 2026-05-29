<div id="item-results" class="space-y-6" role="region" aria-live="polite" aria-atomic="true">
  @php
    $itemsIndexFrom = request('from') ?? session('items.last_index');
  @endphp

  {{-- Desktop Table --}}
  <div class="hidden sm:block">
    <div class="ui-table-panel ui-table-shell sm:rounded-lg">
      <table class="ui-table">
        <thead class="ui-table-head">
          <tr>
            <th class="ui-table-header">Name</th>
            <th class="ui-table-header">Category</th>
            <th class="ui-table-header">Rarity</th>
            <th class="ui-table-header">Details</th>
            <th class="ui-table-header">Actions</th>
          </tr>
        </thead>

        <tbody>
          @forelse($items as $item)
            <tr class="ui-table-row">
              <td class="ui-table-cell-strong whitespace-normal break-words max-w-xs">
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

              <td class="ui-table-cell whitespace-nowrap">
                {{ $item->category?->name ?? '—' }}
              </td>

              <td class="ui-table-cell whitespace-nowrap">
                {{ $item->rarity?->name ?? '—' }}
              </td>

              <td class="ui-table-cell whitespace-normal break-words max-w-md">
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

              <td class="ui-table-cell whitespace-nowrap">
                <div class="ui-table-action-row">

                {{-- View --}}
                <a href="{{ route('items.show', $item) }}"
                   class="ui-table-action-view">
                  View
                </a>

                @auth
                  @if($item->is_srd)
                    {{-- Clone --}}
                    <a href="{{ route('items.custom.create', ['base_item_id' => $item->id, 'from' => $itemsIndexFrom]) }}"
                       class="ui-table-action-primary">
                      Clone
                    </a>
                  @else
                    {{-- Edit --}}
                    @can('update', $item)
                      <a href="{{ route('items.edit', $item) }}?from={{ request('from') ?? session('items.last_index') }}"
                         class="ui-table-action-edit">
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
                                class="ui-table-action-danger">
                          Delete
                        </button>
                      </form>
                    @endcan
                  @endif
                @else
                  @if($item->is_srd)
                    <a href="{{ route('login') }}"
                       class="ui-table-action-primary">
                      Sign in to clone
                    </a>
                  @endif
                @endauth
                </div>

              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="ui-table-empty">
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
      <div class="ui-card p-4">
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
               class="ui-table-action-view">
              View
            </a>

            @auth
              @if($item->is_srd)
                <a href="{{ route('items.custom.create', ['base_item_id' => $item->id, 'from' => $itemsIndexFrom]) }}"
                   class="ui-table-action-primary">
                  Clone
                </a>
              @else
                @can('update', $item)
                  <a href="{{ route('items.edit', $item) }}?from={{ request('from') ?? session('items.last_index') }}"
                     class="ui-table-action-edit">
                    Edit
                  </a>
                @endcan

                @can('delete', $item)
                  <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline"
                        onsubmit="return confirm('Delete this item?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="ui-table-action-danger">
                      Delete
                    </button>
                  </form>
                @endcan
              @endif
            @else
              @if($item->is_srd)
                <a href="{{ route('login') }}"
                   class="ui-table-action-primary">
                  Sign in to clone
                </a>
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
