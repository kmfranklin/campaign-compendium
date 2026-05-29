<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model): void {
            $model->ensureSlug();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null): mixed
    {
        $field ??= $this->getRouteKeyName();

        $resolved = $this->newQuery()->where($field, $value)->first();

        if ($resolved !== null || ! ctype_digit((string) $value)) {
            return $resolved;
        }

        return $this->newQuery()->whereKey((int) $value)->first();
    }

    protected function ensureSlug(): void
    {
        if (! empty($this->slug)) {
            return;
        }

        $base = Str::slug($this->slugSource());

        if ($base === '') {
            $base = Str::slug(class_basename(static::class)) . '-' . Str::lower(Str::random(6));
        }

        $slug = $base;
        $suffix = 2;

        while ($this->slugExists($slug)) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        $this->slug = $slug;
    }

    protected function slugExists(string $slug): bool
    {
        $query = static::query();

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class), true)) {
            $query->withTrashed();
        }

        if ($this->exists) {
            $query->whereKeyNot($this->getKey());
        }

        return $query->where('slug', $slug)->exists();
    }

    abstract protected function slugSource(): string;
}
