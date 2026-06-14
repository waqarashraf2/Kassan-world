<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUniqueSlug
{
    protected static function bootHasUniqueSlug(): void
    {
        static::saving(function (Model $model): void {
            if (! $model->getAttribute('slug')) {
                $model->setAttribute('slug', static::uniqueSlug(
                    (string) $model->getAttribute($model->slugSource()),
                    $model->getKey()
                ));
            }
        });
    }

    protected function slugSource(): string
    {
        return property_exists($this, 'slugSource') ? $this->slugSource : 'name';
    }

    protected static function uniqueSlug(string $value, mixed $ignoreId = null): string
    {
        $base = Str::slug($value) ?: Str::lower(Str::random(8));
        $slug = $base;
        $suffix = 2;

        while (static::query()
            ->withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
