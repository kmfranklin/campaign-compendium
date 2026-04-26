<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpellSchool extends Model
{
    protected $fillable = ['slug', 'name', 'description'];

    /**
     * All spells that belong to this school.
     */
    public function spells(): HasMany
    {
        return $this->hasMany(Spell::class, 'spell_school_slug', 'slug');
    }
}
