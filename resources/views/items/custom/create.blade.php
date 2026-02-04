@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-surface border border-border shadow rounded-lg p-6">

    {{-- Back link --}}
    <a href="{{ route('customItems.index') }}"
       class="inline-flex items-center text-sm text-accent hover:text-accent-hover mb-4 font-medium">
      <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
           viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 19l-7-7 7-7"/>
      </svg>
      Back to Items
    </a>

    <h1 class="text-2xl font-bold text-text mb-6">Create Custom Item</h1>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-danger/10 border border-danger text-danger rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('items.store') }}">
        @csrf

        {{-- Name --}}
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-text">Name</label>
            <input id="name" name="name" type="text"
                   value="{{ old('name', $prefill['name'] ?? '') }}"
                   class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                          focus:border-accent focus:ring-accent sm:text-sm"
                   required>
        </div>

        {{-- Cost / Weight --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="cost" class="block text-sm font-medium text-text">Cost (gp)</label>
                <input id="cost" name="cost" type="number" step="0.01"
                       value="{{ old('cost', $prefill['cost'] ?? '') }}"
                       class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                              focus:border-accent focus:ring-accent sm:text-sm">
            </div>

            <div>
                <label for="weight" class="block text-sm font-medium text-text">Weight (lb)</label>
                <input id="weight" name="weight" type="number" step="0.01"
                       value="{{ old('weight', $prefill['weight'] ?? '') }}"
                       class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                              focus:border-accent focus:ring-accent sm:text-sm">
            </div>
        </div>

        {{-- Category / Rarity --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="item_category_id" class="block text-sm font-medium text-text">Category</label>
                <select id="item_category_id" name="item_category_id"
                        class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                               focus:border-accent focus:ring-accent sm:text-sm"
                        required>
                    <option value="">Choose item category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            @selected(old('item_category_id', $prefill['item_category_id'] ?? '') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="item_rarity_id" class="block text-sm font-medium text-text">Rarity</label>
                <select id="item_rarity_id" name="item_rarity_id"
                        class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                               focus:border-accent focus:ring-accent sm:text-sm">
                    <option value="">Choose item rarity</option>
                    @foreach ($rarities as $rarity)
                        <option value="{{ $rarity->id }}"
                            @selected(old('item_rarity_id', $prefill['item_rarity_id'] ?? '') == $rarity->id)>
                            {{ $rarity->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Description --}}
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-text">Description</label>
            <textarea id="description" name="description" rows="4"
                      class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                             focus:border-accent focus:ring-accent sm:text-sm">{{ old('description', $prefill['description'] ?? '') }}</textarea>
        </div>

        {{-- Magic / Attunement --}}
        <div class="flex items-center space-x-6 mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_magic_item" value="1"
                       @checked(old('is_magic_item', $prefill['is_magic_item'] ?? false))
                       class="rounded border-border text-accent focus:ring-accent">
                <span class="ml-2 text-text">Magic Item</span>
            </label>

            <label class="inline-flex items-center">
                <input type="checkbox" id="requires_attunement" name="requires_attunement" value="1"
                       @checked(old('requires_attunement', $prefill['requires_attunement'] ?? false))
                       class="rounded border-border text-accent focus:ring-accent">
                <span class="ml-2 text-text">Requires Attunement</span>
            </label>
        </div>

        {{-- Attunement Requirements --}}
        <div id="attunement-requirements-wrapper" class="mt-2 hidden">
            <label for="attunement_requirements" class="block text-sm font-medium text-text">
                Attunement Requirements
            </label>
            <input id="attunement_requirements" name="attunement_requirements" type="text"
                   value="{{ old('attunement_requirements', $prefill['attunement_requirements'] ?? '') }}"
                   class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                          focus:border-accent focus:ring-accent sm:text-sm">
        </div>

        {{-- Weapon Fields --}}
        <div id="weapon-fields" class="space-y-4 hidden">
            <h2 class="text-lg font-semibold text-text">Weapon Details</h2>

            {{-- Damage Dice + Type --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="damage_dice" class="block text-sm font-medium text-text">Damage Dice</label>
                    <input id="damage_dice" name="damage_dice" type="text"
                           value="{{ old('damage_dice', $prefill['damage_dice'] ?? '') }}"
                           class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                                  focus:border-accent focus:ring-accent sm:text-sm">
                </div>

                <div>
                    <label for="damage_type_id" class="block text-sm font-medium text-text">Damage Type</label>
                    <select id="damage_type_id" name="damage_type_id"
                            class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                                   focus:border-accent focus:ring-accent sm:text-sm">
                        <option value="">Choose damage type</option>
                        @foreach ($damageTypes as $dt)
                            <option value="{{ $dt->id }}"
                                @selected(old('damage_type_id', $prefill['damage_type_id'] ?? '') == $dt->id)>
                                {{ $dt->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Range Fields --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="range" class="block text-sm font-medium text-text">Normal Range</label>
                    <input id="range" name="range" type="number" step="1"
                           value="{{ old('range', $prefill['range'] ?? '') }}"
                           class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                                  focus:border-accent focus:ring-accent sm:text-sm">
                </div>

                <div>
                    <label for="long_range" class="block text-sm font-medium text-text">Long Range</label>
                    <input id="long_range" name="long_range" type="number" step="1"
                           value="{{ old('long_range', $prefill['long_range'] ?? '') }}"
                           class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                                  focus:border-accent focus:ring-accent sm:text-sm">
                </div>

                <div>
                    <label for="distance_unit" class="block text-sm font-medium text-text">Distance Unit</label>
                    <input id="distance_unit" name="distance_unit" type="text"
                           value="{{ old('distance_unit', $prefill['distance_unit'] ?? 'ft') }}"
                           class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                                  focus:border-accent focus:ring-accent sm:text-sm">
                </div>
            </div>

            {{-- Weapon Flags --}}
            <div class="flex items-center space-x-6 mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_improvised" value="1"
                           @checked(old('is_improvised', $prefill['is_improvised'] ?? false))
                           class="rounded border-border text-accent focus:ring-accent">
                    <span class="ml-2 text-text">Improvised</span>
                </label>

                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_simple" value="1"
                           @checked(old('is_simple', $prefill['is_simple'] ?? false))
                           class="rounded border-border text-accent focus:ring-accent">
                    <span class="ml-2 text-text">Simple</span>
                </label>
            </div>
        </div>

        {{-- Armor Fields --}}
        <div id="armor-fields" class="space-y-4 hidden">
            <h2 class="text-lg font-semibold text-text">Armor Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="base_ac" class="block text-sm font-medium text-text">Base AC</label>
                    <input id="base_ac" name="base_ac" type="number"
                           value="{{ old('base_ac', $prefill['base_ac'] ?? '') }}"
                           class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                                  focus:border-accent focus:ring-accent sm:text-sm">
                </div>

                <div>
                    <label for="dex_mod_cap" class="block text-sm font-medium text-text">Dex Mod Cap</label>
                    <input id="dex_mod_cap" name="dex_mod_cap" type="number"
                           value="{{ old('dex_mod_cap', $prefill['dex_mod_cap'] ?? '') }}"
                           class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                                  focus:border-accent focus:ring-accent sm:text-sm">
                </div>

                <div>
                    <label for="strength_requirement" class="block text-sm font-medium text-text">Strength Requirement</label>
                    <input id="strength_requirement" name="strength_requirement" type="number"
                           value="{{ old('strength_requirement', $prefill['strength_requirement'] ?? '') }}"
                           class="mt-1 block w-full rounded-md border border-border bg-surface text-text shadow-sm
                                  focus:border-accent focus:ring-accent sm:text-sm">
                </div>
            </div>

            {{-- Armor Flags --}}
            <div class="flex items-center space-x-6 mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="adds_dex_mod" value="1"
                           @checked(old('adds_dex_mod', $prefill['adds_dex_mod'] ?? true))
                           class="rounded border-border text-accent focus:ring-accent">
                    <span class="ml-2 text-text">Adds Dex Modifier</span>
                </label>

                <label class="inline-flex items-center">
                    <input type="checkbox" name="imposes_stealth_disadvantage" value="1"
                           @checked(old('imposes_stealth_disadvantage', $prefill['imposes_stealth_disadvantage'] ?? false))
                           class="rounded border-border text-accent focus:ring-accent">
                    <span class="ml-2 text-text">Stealth Disadvantage</span>
                </label>
            </div>
        </div>

        {{-- Variant link (when cloning) --}}
        @if(!empty($prefill['base_item_id']))
            <input type="hidden" name="base_item_id" value="{{ $prefill['base_item_id'] }}">
        @endif

        {{-- Footer --}}
        <div class="pt-4 border-t border-border flex justify-between">
            <a href="{{ route('customItems.index') }}"
               class="px-4 py-2 bg-bg text-text rounded hover:bg-hover">
                Cancel
            </a>

            <button type="submit"
                    class="px-6 py-2 bg-accent text-on-accent font-semibold rounded hover:bg-accent-hover
                           focus:outline-none focus:ring-2 focus:ring-accent">
                Create Item
            </button>
        </div>
    </form>
</div>
@endsection
