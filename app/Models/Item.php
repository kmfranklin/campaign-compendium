<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    protected $fillable = [
        'item_key',
        'slug',
        'name',
        'description',
        'cost',
        'weight',
        'is_magic_item',
        'requires_attunement',
        'attunement_requirements',
        'item_category_id',
        'item_rarity_id',
        'base_item_id',
        'is_srd',
        'user_id',
    ];

    protected $casts = [
        'is_magic_item' => 'boolean',
        'requires_attunement' => 'boolean',
        'is_srd' => 'boolean',
        'cost' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    // Relationships
    public function weapon()
    {
        return $this->hasOne(Weapon::class);
    }

    public function armor()
    {
        return $this->hasOne(Armor::class);
    }

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id');
    }

    public function rarity()
    {
        return $this->belongsTo(ItemRarity::class, 'item_rarity_id');
    }

    public function baseItem()
    {
        return $this->belongsTo(self::class, 'base_item_id');
    }

    public function variants()
    {
        return $this->hasMany(self::class, 'base_item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function slugSource(): string
    {
        return $this->name;
    }
}
