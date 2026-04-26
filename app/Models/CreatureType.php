<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreatureType extends Model
{
    protected $fillable = ['slug', 'name', 'description'];

    /**
     * All creatures of this type.
     */
    public function creatures(): HasMany
    {
        return $this->hasMany(Creature::class, 'creature_type_slug', 'slug');
    }
}
