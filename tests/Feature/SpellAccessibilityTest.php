<?php

namespace Tests\Feature;

use App\Models\Spell;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpellAccessibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_spell_show_uses_full_component_names_and_material_details(): void
    {
        $spell = Spell::create([
            'name' => 'Burning Hands',
            'level' => 1,
            'verbal' => true,
            'somatic' => true,
            'material' => true,
            'material_specified' => 'A tiny ball of bat guano and sulfur.',
            'description' => 'A sheet of flames shoots forth.',
            'is_srd' => true,
        ]);

        $this->get(route('spells.show', $spell))
            ->assertOk()
            ->assertSee('Verbal, Somatic, Material')
            ->assertSee('(A tiny ball of bat guano and sulfur.)');
    }
}
