@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-surface border border-border shadow rounded-lg p-6">

    {{-- Back link --}}
    <a href="{{ route('compendium.npcs.index') }}"
       class="inline-flex items-center text-sm text-accent hover:text-accent-hover mb-4 font-medium">
        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Compendium
    </a>

    <h1 class="text-2xl font-bold text-text mb-6">Edit NPC</h1>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-surface border border-danger text-danger rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('compendium.npcs.update', $npc) }}"
          method="POST"
          class="space-y-10">
        @csrf
        @method('PUT')

        {{-- Core Identity --}}
        <section>
            <h2 class="text-lg font-semibold text-text mb-4 border-b border-border pb-2">Core Identity</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form.field label="Name" name="name" :value="$npc->name" required />
                <x-form.field label="Alias" name="alias" :value="$npc->alias" />

                <x-form.select
                    name="race"
                    label="Race/Species"
                    :options="\App\Models\Npc::raceOptions()"
                    :selected="$npc->race"
                    placeholder="Choose Race/Species" />

                <x-form.select
                    name="class"
                    label="Class"
                    :options="\App\Models\Npc::classOptions()"
                    :selected="$npc->class"
                    placeholder="Choose Class" />

                <x-form.select
                    name="role"
                    label="Role"
                    :options="\App\Models\Npc::socialRoleOptions()"
                    :selected="$npc->role"
                    placeholder="Choose Role" />

                <x-form.select
                    name="alignment"
                    label="Alignment"
                    :options="\App\Models\Npc::alignmentOptions()"
                    :selected="$npc->alignment"
                    placeholder="Choose Alignment" />

                <x-form.field label="Location" name="location" :value="$npc->location" />

                <x-form.select
                    name="status"
                    label="Status"
                    :options="\App\Models\Npc::statusOptions()"
                    :selected="$npc->status"
                    placeholder="Choose Status" />

                <x-form.field label="Portrait Path" name="portrait_path" :value="$npc->portrait_path" />
            </div>
        </section>

        {{-- Descriptive --}}
        <section>
            <h2 class="text-lg font-semibold text-text mb-4 border-b border-border pb-2">Descriptive</h2>

            <div class="space-y-4">
                <x-form.field label="Description" name="description" type="textarea" rows="3" :value="$npc->description" />
                <x-form.field label="Personality" name="personality" type="textarea" rows="3" :value="$npc->personality" />
                <x-form.field label="Quirks" name="quirks" type="textarea" rows="3" :value="$npc->quirks" />
            </div>
        </section>

        {{-- Combat Stats --}}
        <section>
            <h2 class="text-lg font-semibold text-text mb-4 border-b border-border pb-2">Abilities + Stats</h2>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach (['strength','dexterity','constitution','intelligence','wisdom','charisma'] as $stat)
                    <x-form.field
                        label="{{ ucfirst($stat) }}"
                        name="{{ $stat }}"
                        type="number"
                        min="1"
                        max="30"
                        :value="$npc->$stat" />
                @endforeach

                <x-form.field label="Armor Class" name="armor_class" type="number" min="0" max="50" :value="$npc->armor_class" />
                <x-form.field label="Hit Points" name="hit_points" type="number" min="0" :value="$npc->hit_points" />
                <x-form.field label="Speed" name="speed" :value="$npc->speed" />
                <x-form.field label="Challenge Rating" name="challenge_rating" :value="$npc->challenge_rating" />
                <x-form.field label="Proficiency Bonus" name="proficiency_bonus" type="number" min="0" max="10" :value="$npc->proficiency_bonus" />
            </div>
        </section>

        {{-- Submit --}}
        <div class="pt-4 border-t border-border flex justify-between">
            <a href="{{ route('compendium.npcs.show', $npc) }}"
               class="px-4 py-2 bg-bg text-text border border-border rounded hover:bg-hover">
                Cancel
            </a>

            <button type="submit"
                    class="px-6 py-2 bg-accent text-on-accent font-semibold rounded shadow
                           hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent">
                Update NPC
            </button>
        </div>
    </form>
</div>
@endsection
