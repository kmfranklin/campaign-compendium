<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCustomItemRequest;
use App\Http\Requests\UpdateCustomItemRequest;
use App\Models\DamageType;
use App\Models\ItemCategory;
use App\Models\ItemRarity;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public function __construct()
    {
        // Apply policy checks for item resource routes (update/delete etc.)
        $this->authorizeResource(Item::class, 'item');
    }

    /**
     * Display a listing of all items.
     */
    public function index(Request $request)
    {
        $query = Item::with(['weapon.damageType', 'armor', 'category', 'rarity']);
        $query->where(function ($builder) use ($request) {
            $builder->where('is_srd', true);

            if ($request->user()) {
                $builder->orWhere('user_id', $request->user()->id);
            }
        });
        $query = $query->orderByRaw('LOWER(`name`) ASC');
        $items = $this->applyFilters($request, $query)
            ->paginate(15)
            ->appends($request->except('page'));

        session(['items.last_index' => 'all']);
        session(['items.last_index_url' => request()->fullUrl()]);
        return $this->renderItems($request, $items, 'items.index');
    }

    /**
     * Display SRD items only.
     */
    public function srdIndex(Request $request)
    {
        $query = Item::where('is_srd', true)
            ->with(['category', 'rarity', 'weapon.damageType', 'armor']);
        $query = $query->orderByRaw('LOWER(`name`) ASC');

        $items = $this->applyFilters($request, $query)
            ->paginate(15)
            ->appends($request->except('page'));

        session(['items.last_index' => 'srd']);
        session(['items.last_index_url' => request()->fullUrl()]);
        return $this->renderItems($request, $items, 'items.srd');
    }

    /**
     * Display custom items for the authenticated user.
     */
    public function customIndex(Request $request)
    {
        $query = Item::where('is_srd', false)
            ->where('user_id', auth()->id())
            ->with(['category', 'rarity', 'weapon.damageType', 'armor']);
        $query = $query->orderByRaw('LOWER(`name`) ASC');

        $items = $this->applyFilters($request, $query)
            ->paginate(15)
            ->appends($request->except('page'));

        session(['items.last_index' => 'custom']);
        session(['items.last_index_url' => request()->fullUrl()]);
        return $this->renderItems($request, $items, 'items.custom');
    }

    /**
     * Display a single item.
     */
    public function show(Item $item)
    {
        // eager load relations we use
        $item->load([
            'weapon',
            'weapon.damageType',
            'armor',
            'category',
            'rarity',
            'baseItem.weapon',
            'baseItem.weapon.damageType',
            'user',
        ]);

        // pick the weapon source: prefer item->weapon, fallback to baseItem->weapon
        $weaponSource = $item->weapon ?? optional($item->baseItem)->weapon ?? null;

        $magicBonus = $item->magic_bonus ?? null;
        if (is_null($magicBonus) && preg_match('/\+(\d+)/', $item->name, $m)) {
            $magicBonus = intval($m[1]);
        }

        // build $displayWeapon only when a weapon source exists
        $displayWeapon = null;
        if ($weaponSource) {
            $baseDamageDice = $weaponSource->damage_dice ?? null;
            $damageTypeName = optional($weaponSource->damageType)->name ?? null;

            // combined damage string like "1d10 +1 Piercing"
            $damageString = null;
            if ($baseDamageDice) {
                $damageString = trim($baseDamageDice . ($magicBonus ? " +{$magicBonus}" : '') . ($damageTypeName ? "{$damageTypeName}" : ''));
            }

            $displayWeapon = [
                'base_damage_dice' => $baseDamageDice,
                'damage_type' => $damageTypeName,
                'damageString' => $damageString,
                'attackBonus' => $magicBonus ? "+{$magicBonus}" : null,
                'range' => $weaponSource->range ?? null,
                'long_range' => $weaponSource->long_range ?? null,
                'distance_unit' => $weaponSource->distance_unit ?? 'ft',
                'is_improvised' => (bool) ($weaponSource->is_improvised ?? false),
                'is_simple' => (bool) ($weaponSource->is_simple ?? false),
                'source' => $item->weapon ? 'item' : (optional($item->baseItem)->weapon ? 'base' : null),
            ];
        }

        return view('items.show', compact('item', 'displayWeapon'));
    }

    /**
     * Show the create form for a new custom item.
     */
    public function create(Request $request)
    {
        $prefill = [];
        $baseId = $request->query('base_item_id');

        if ($baseId) {
            $source = Item::with(['weapon', 'armor', 'baseItem'])->findOrFail($baseId);

            // Authorization:
            // - Any signed-in user can clone SRD items
            // - Only the owner can clone their custom items
            if (!$source->is_srd && $source->user_id !== auth()->id()) {
                abort(403);
            }

            $prefill = [
                'name' => $source->name,
                'item_category_id' => $source->item_category_id,
                'item_rarity_id' => $source->item_rarity_id,
                'description' => $source->description,
                'base_item_id' => $source->id,
            ];

            $weaponSource = $source->weapon ?? $source->baseItem?->weapon;
            $armorSource  = $source->armor ?? $source->baseItem?->armor;

            if ($weaponSource) {
                $prefill += [
                    'damage_dice'     => $weaponSource->damage_dice,
                    'damage_type_id'  => $weaponSource->damage_type_id,
                    'range'           => $weaponSource->range,
                    'long_range'      => $weaponSource->long_range,
                    'distance_unit'   => $weaponSource->distance_unit,
                    'is_improvised'   => (bool) $weaponSource->is_improvised,
                    'is_simple'       => (bool) $weaponSource->is_simple,
                ];
            }

            if ($armorSource) {
                $prefill += [
                    'base_ac'                      => $armorSource->base_ac,
                    'adds_dex_mod'                 => (bool) $armorSource->adds_dex_mod,
                    'dex_mod_cap'                  => $armorSource->dex_mod_cap,
                    'imposes_stealth_disadvantage' => (bool) $armorSource->imposes_stealth_disadvantage,
                    'strength_requirement'         => $armorSource->strength_requirement,
                ];
            }
        }

        $categories = ItemCategory::orderBy('name')->get();
        $rarities   = ItemRarity::orderBy('name')->get();
        $damageTypes = DamageType::orderBy('name')->get();

        return view('items.custom.create', compact('prefill', 'categories', 'rarities', 'damageTypes'));
    }

    /**
     * Store a new custom item.
     */
    public function store(StoreCustomItemRequest $request)
    {
        $data = $request->validated();

        $item = Item::create($data + [
            'is_srd' => false,
            'user_id' => auth()->id(),
            'item_key' => Str::uuid()->toString(),
        ]);

        if (($data['type'] ?? null) === 'Weapon') {
            $item->weapon()->create($request->only([
                'damage_dice', 'damage_type_id', 'range', 'long_range',
                'distance_unit', 'is_improvised', 'is_simple'
            ]));
        }

        if (($data['type'] ?? null) === 'Armor') {
            $item->armor()->create($request->only([
                'base_ac', 'adds_dex_mod', 'dex_mod_cap',
                'imposes_stealth_disadvantage', 'strength_requirement'
            ]));
        }

        $from = $request->input('from') ?: route('customItems.index');

        return redirect($from)
            ->with('status', 'Custom item created.');
    }

    /**
     * Show the edit form for a custom item.
     */
    public function edit(Request $request, Item $item)
    {
        // Policy (authorizeResource) already ensures owner can edit; ensure it's custom
        if ($item->is_srd) {
            abort(403);
        }

        $from = $request->query('from', url()->previous());

        $categories = ItemCategory::orderBy('name')->get();
        $rarities   = ItemRarity::orderBy('name')->get();
        $damageTypes = DamageType::orderBy('name')->get();

        return view('items.custom.edit', compact('item', 'from', 'categories', 'rarities', 'damageTypes'));
    }

    /**
     * Update a custom item.
     */
    public function update(UpdateCustomItemRequest $request, Item $item)
    {
        // authorizeResource ensures owner, but double-check SRD status
        if ($item->is_srd) {
            abort(403);
        }

        $data = $request->validated();

        // Prevent tampering with item_key or is_srd/user_id
        $item->update(collect($data)->except(['item_key', 'is_srd', 'user_id'])->all());

        if (($data['type'] ?? null) === 'Weapon') {
            $item->weapon()->updateOrCreate(
                ['item_id' => $item->id],
                $request->only([
                    'damage_dice', 'damage_type_id', 'range', 'long_range',
                    'distance_unit', 'is_improvised', 'is_simple'
                ])
            );
            // remove armor if previously present? Optional: keep as-is
            $item->armor()->delete();
        } elseif (($data['type'] ?? null) === 'Armor') {
            $item->armor()->updateOrCreate(
                ['item_id' => $item->id],
                $request->only([
                    'base_ac', 'adds_dex_mod', 'dex_mod_cap',
                    'imposes_stealth_disadvantage', 'strength_requirement'
                ])
            );
            $item->weapon()->delete();
        } else {
            // neither weapon nor armor -> remove related records
            $item->weapon()->delete();
            $item->armor()->delete();
        }

        $from = $request->input('from') ?: route('customItems.index');

        return redirect($from)->with('status', 'Custom item updated.');
    }

    /**
     * Destroy (delete) a custom item.
     */
    public function destroy(Request $request, Item $item)
    {
        // authorizeResource ensures owner, verify not SRD
        if ($item->is_srd) {
            abort(403);
        }

        // Soft delete if model uses SoftDeletes, otherwise delete
        $item->delete();

        $from = $request->input('from') ?: route('customItems.index');

        return redirect($from)->with('status', 'Custom item deleted.');
    }

    /**
     * Apply search and filter parameters to the query.
     */
    private function applyFilters(Request $request, $query)
    {
        if ($search = $request->q) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($category = $request->category) {
            $query->whereHas('category', fn($q) => $q->where('slug', $category));
        }

        if ($rarity = $request->rarity) {
            $query->whereHas('rarity', fn($q) => $q->where('slug', $rarity));
        }

        return $query;
    }

    /**
     * Render either the full view or just the results partial for AJAX.
     */
    private function renderItems(Request $request, $items, $view)
    {
        if ($request->ajax()) {
            return response()
                ->view('items.partials.results', compact('items'))
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Vary', 'X-Requested-With');
        }

        return view($view, compact('items'));
    }
}
