@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
  @php
    use Illuminate\Support\Facades\Route;

    $expected = [
      'all'    => 'items.index',
      'custom' => 'customItems.index',
      'srd'    => 'srdItems.index',
    ];

    $getRouteUrl = function(?string $routeName, $fallback = null) {
        if ($routeName && Route::has($routeName)) {
            return route($routeName);
        }
        return $fallback;
    };

    $fromKey = request()->query('from');
    if ($fromKey && isset($expected[$fromKey]) && Route::has($expected[$fromKey])) {
        $backUrl = route($expected[$fromKey]);
    } else {
        $previous = url()->previous();
        $prevPath = parse_url($previous, PHP_URL_PATH) ?: null;

        $allowedPaths = [];
        foreach ($expected as $key => $routeName) {
            if (Route::has($routeName)) {
                $allowedPaths[] = parse_url(route($routeName), PHP_URL_PATH);
            }
        }

        if ($prevPath && in_array($prevPath, $allowedPaths, true)) {
            $backUrl = $previous;
        } else {
            $lastUrl = session('items.last_index_url');
            if ($lastUrl) {
                $backUrl = $lastUrl;
            } else {
                $lastKey = session('items.last_index');
                $backUrl = $getRouteUrl($expected[$lastKey] ?? null, route('items.index'));
            }
        }
    }
  @endphp

  <a href="{{ $backUrl }}"
     class="inline-flex items-center text-sm text-accent hover:text-accent-hover mb-4 font-medium">
    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
         viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 19l-7-7 7-7"/>
    </svg>
    Back to Items
  </a>

  <div class="bg-surface border border-border shadow-md rounded-lg overflow-hidden">
    <div class="flex items-start p-6 border-b border-border">
      <div class="flex-1">
        <div class="flex items-start">
          <div>
            <h1 class="text-3xl font-bold text-text">{{ $item->name }}</h1>

            @php
              $itemTags = [];
              if (optional($item->category)->name) {
                  $itemTags[] = [
                      'label' => optional($item->category)->name,
                      'bg'    => 'bg-bg',
                      'text'  => 'text-text',
                  ];
              }
              if (optional($item->rarity)->name) {
                  $itemTags[] = [
                      'label' => optional($item->rarity)->name,
                      'bg'    => 'bg-yellow-500/10',
                      'text'  => 'text-yellow-400',
                  ];
              }
              if (! $item->is_srd) {
                  $itemTags[] = [
                      'label' => 'Custom',
                      'bg'    => 'bg-green-500/10',
                      'text'  => 'text-green-400',
                  ];
              }

              $inlineProps = [];
              if ($item->is_magic_item) {
                  $inlineProps[] = [
                      'label' => 'Magic Item',
                      'bg'    => 'bg-accent/10',
                      'text'  => 'text-accent',
                  ];
              }
              if ($item->requires_attunement) {
                  $inlineProps[] = [
                      'label' => 'Requires Attunement',
                      'bg'    => 'bg-danger/10',
                      'text'  => 'text-danger',
                  ];
              }
              if (optional($item->armor)->adds_dex_mod) {
                  $inlineProps[] = [
                      'label' => 'Adds Dex Mod',
                      'bg'    => 'bg-bg',
                      'text'  => 'text-muted',
                  ];
              }
            @endphp

            <div class="mt-3 flex flex-wrap gap-2 items-center">
              @foreach($itemTags as $tag)
                <span class="{{ $tag['bg'] }} {{ $tag['text'] }} text-xs font-medium px-2 py-1 rounded">
                  {{ $tag['label'] }}
                </span>
              @endforeach

              @foreach($inlineProps as $p)
                <span class="{{ $p['bg'] }} {{ $p['text'] }} text-xs px-2 py-0.5 rounded">
                  {{ $p['label'] }}
                </span>
              @endforeach
            </div>
          </div>

          <div class="ml-auto flex gap-2">
            @can('update', $item)
              <a href="{{ route('items.edit', $item) }}?from=custom"
                 class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-on-accent rounded">
                Edit
              </a>
            @endcan

            @can('delete', $item)
              <form action="{{ route('items.destroy', $item) }}" method="POST"
                    onsubmit="return confirm('Delete this item? This action cannot be undone.');"
                    class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 bg-danger hover:bg-red-600 text-on-accent rounded">
                  Delete
                </button>
              </form>
            @endcan

            <a href="{{ route('items.custom.create', ['base_item_id' => $item->id, 'from' => 'custom']) }}"
               class="px-4 py-2 bg-accent hover:bg-accent-hover text-on-accent rounded">
              Clone
            </a>
          </div>
        </div>
      </div>
    </div>

    @php
      if (!isset($displayWeapon)) {
        $weaponSource = $item->weapon ?? optional($item->baseItem)->weapon ?? null;
        $magicBonus = $item->magic_bonus ?? null;

        if (is_null($magicBonus) && preg_match('/\+(\d+)/', $item->name, $m)) {
          $magicBonus = intval($m[1]);
        }

        $baseDamageDice = optional($weaponSource)->damage_dice;
        $damageTypeName = optional(optional($weaponSource)->damageType)->name;
        $damageString = null;

        if ($baseDamageDice) {
          $damageString = trim(
            $baseDamageDice
            . ($magicBonus ? " +{$magicBonus}" : '')
            . ($damageTypeName ? " {$damageTypeName}" : '')
          );
        }

        $displayWeapon = $weaponSource ? [
          'base_damage_dice' => $baseDamageDice,
          'damage_type'      => $damageTypeName,
          'damageString'     => $damageString,
          'attackBonus'      => $magicBonus ? "+{$magicBonus}" : null,
          'range'            => optional($weaponSource)->range,
          'long_range'       => optional($weaponSource)->long_range,
          'distance_unit'    => optional($weaponSource)->distance_unit ?? 'ft',
          'is_improvised'    => optional($weaponSource)->is_improvised,
          'is_simple'        => optional($weaponSource)->is_simple,
          'source'           => $item->weapon ? 'item' : (optional($item->baseItem)->weapon ? 'base' : null),
        ] : null;
      }
    @endphp

    @php
      $hasMeaningfulNumber = function($v) {
        if (is_null($v) || $v === '') return false;
        if (is_numeric($v)) return floatval($v) !== 0.0;
        return trim((string)$v) !== '0' && trim((string)$v) !== '0.00';
      };

      $quick = [
        'Cost'  => $item->cost,
        'Weight'=> $item->weight,
      ];

      if ($item->is_magic_item) {
        $quick['Magic Item'] = 'Yes';
      }
      if ($item->requires_attunement) {
        $quick['Requires Attunement'] = 'Yes';
      }

      if (optional($item->armor)->exists()) {
        $armor = $item->armor;
        $quick['Base AC'] = $armor->base_ac ?? null;

        if (!is_null($armor->dex_mod_cap)) {
          $quick['Dex Cap'] = $armor->dex_mod_cap;
        }

        if (!is_null($armor->strength_requirement) && $armor->strength_requirement !== '') {
          $quick['Strength Req'] = $armor->strength_requirement;
        }
      }

      $displayQuick = array_filter($quick, function($v, $k) use ($hasMeaningfulNumber) {
        if (in_array($k, ['Magic Item','Requires Attunement','Base AC'])) {
          return !is_null($v) && $v !== '';
        }
        if (in_array($k, ['Cost','Weight','Dex Cap','Strength Req'])) {
          return $hasMeaningfulNumber($v);
        }
        return !is_null($v) && $v !== '';
      }, ARRAY_FILTER_USE_BOTH);
    @endphp

    @if(count($displayQuick))
      <div class="p-4 border-b border-border bg-bg">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
          @foreach($displayQuick as $label => $value)
            <div class="bg-surface border border-border rounded-lg p-3 text-center">
              <div class="text-xs font-medium text-muted uppercase">{{ $label }}</div>
              <div class="mt-1 text-sm font-semibold text-text">{{ $value }}</div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    <div class="md:flex md:items-start">
      <div class="md:flex-1">
        @if($item->description)
          <div class="p-6 bg-surface border-b border-border">
            <h2 class="text-lg font-semibold text-text mb-4">Description</h2>
            <p class="text-text whitespace-pre-line">{{ $item->description }}</p>
          </div>
        @endif

        @if(!empty($displayWeapon))
          <div class="p-6 bg-bg border-b border-border">
            <h2 class="text-lg font-semibold text-text mb-4">Weapon Details</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
              @if(!empty($displayWeapon['base_damage_dice']))
                <div>
                  <dt class="font-medium text-muted">Damage Dice</dt>
                  <dd class="text-text">
                    {{ $displayWeapon['base_damage_dice'] }}
                    @if(!empty($displayWeapon['damage_type']))
                      {{ $displayWeapon['damage_type'] }}
                    @endif
                    @if(!empty($displayWeapon['source']) && $displayWeapon['source'] === 'base')
                      <span class="text-xs text-muted ml-2">(from base)</span>
                    @endif
                  </dd>
                </div>
              @endif

              @if(!empty($displayWeapon['attackBonus']))
                <div>
                  <dt class="font-medium text-muted">Damage Modifier</dt>
                  <dd class="text-text">{{ $displayWeapon['attackBonus'] }}</dd>
                </div>
              @endif

              @php
                $hasRange = (
                  isset($displayWeapon['range']) && $displayWeapon['range'] !== '' && floatval($displayWeapon['range']) !== 0
                ) || (
                  isset($displayWeapon['long_range']) && $displayWeapon['long_range'] !== '' && floatval($displayWeapon['long_range']) !== 0
                );
              @endphp

              @if($hasRange)
                <div>
                  <dt class="font-medium text-muted">Range</dt>
                  <dd class="text-text">
                    {{ $displayWeapon['range'] ?? '—' }}
                    @if(!empty($displayWeapon['long_range']) && floatval($displayWeapon['long_range']) !== 0)
                      / {{ $displayWeapon['long_range'] }} {{ $displayWeapon['distance_unit'] ?? 'ft' }}
                    @endif
                  </dd>
                </div>
              @endif
            </div>
          </div>
        @endif

        @php
          $armorQuickHasBase   = isset($displayQuick['Base AC']) && $displayQuick['Base AC'] !== '';
          $armorQuickHasDexCap = isset($displayQuick['Dex Cap']) && $displayQuick['Dex Cap'] !== '';
        @endphp

        @if(optional($item->armor)->exists() && !($armorQuickHasBase && $armorQuickHasDexCap))
          @php
            $armor = $item->armor;
            $armorProps = [];
            if ($armor->imposes_stealth_disadvantage) {
              $armorProps[] = 'Stealth Disadvantage';
            }
          @endphp

          <div class="p-6 bg-surface border-b border-border">
            <h2 class="text-lg font-semibold text-text mb-4">Armor Details</h2>

            <div class="grid grid-cols-2 gap-4 text-sm">
              @if(!is_null($armor->base_ac) && !empty($armor->base_ac))
                <div>
                  <dt class="font-medium text-muted">Base AC</dt>
                  <dd class="text-text">{{ $armor->base_ac }}</dd>
                </div>
              @endif

              @if($armor->adds_dex_mod)
                <div>
                  <dt class="font-medium text-muted">Dex Cap</dt>
                  <dd class="text-text">
                    {{ is_null($armor->dex_mod_cap) ? 'Uncapped' : $armor->dex_mod_cap }}
                  </dd>
                </div>
              @elseif(!is_null($armor->dex_mod_cap))
                <div>
                  <dt class="font-medium text-muted">Dex Cap</dt>
                  <dd class="text-text">{{ $armor->dex_mod_cap }}</dd>
                </div>
              @endif

              @if(!is_null($armor->strength_requirement) && $armor->strength_requirement !== '')
                <div>
                  <dt class="font-medium text-muted">Strength Requirement</dt>
                  <dd class="text-text">{{ $armor->strength_requirement }}</dd>
                </div>
              @endif

              @if(count($armorProps))
                <div>
                  <dt class="font-medium text-muted">{{ count($armorProps) > 1 ? 'Properties' : '' }}</dt>
                  <dd>
                    @foreach($armorProps as $p)
                      <span class="inline-block bg-bg text-text text-xs px-2 py-0.5 rounded mr-2">
                        {{ $p }}
                      </span>
                    @endforeach
                  </dd>
                </div>
              @endif
            </div>
          </div>
        @endif
      </div>

      @php
        $hasDetails = false;
        if ($item->base_item_id) $hasDetails = true;
        if (! $item->is_srd && $item->created_at) $hasDetails = true;
        if ($item->user_id) $hasDetails = true;
      @endphp

      @if($hasDetails)
        <aside class="md:w-80 border-l border-border bg-bg">
          <div class="p-6">
            <h3 class="text-sm font-semibold text-text mb-3 border-b border-border pb-2">Details</h3>
            <dl class="text-sm text-text space-y-2">
              @if($item->base_item_id)
                <div>
                  <dt class="font-medium text-muted">Base Item</dt>
                  <dd>
                    <a href="{{ route('items.show', $item->baseItem) }}"
                       class="text-accent hover:text-accent-hover hover:underline">
                      {{ optional($item->baseItem)->name }}
                    </a>
                  </dd>
                </div>
              @endif

              @if(! $item->is_srd && $item->created_at)
                <div>
                  <dt class="font-medium text-muted">Created</dt>
                  <dd>{{ $item->created_at?->diffForHumans() }}</dd>
                </div>
              @endif

              @if($item->user_id)
                <div>
                  <dt class="font-medium text-muted">Owner</dt>
                  <dd>{{ optional($item->user)->name }}</dd>
                </div>
              @endif
            </dl>
          </div>
        </aside>
      @endif
    </div>
  </div>
</div>
@endsection
