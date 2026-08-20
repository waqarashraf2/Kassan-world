<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SpecialOffer extends Model
{
    use HasFactory, HasUniqueSlug;

    protected $attributes = [
        'is_active' => true,
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'discount_percentage' => 'integer',
            'is_active' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getBannerImageUrlAttribute(): ?string
    {
        if (! $this->banner_image) {
            return null;
        }

        return Str::startsWith($this->banner_image, ['http://', 'https://'])
            ? $this->banner_image
            : asset('storage/' . $this->banner_image);
    }
}
