<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, HasUniqueSlug, SoftDeletes;

    protected $attributes = [
        'stock_quantity' => 0,
        'manage_stock' => true,
        'is_featured' => false,
        'is_active' => true,
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'manage_stock' => 'boolean',
            'is_featured' => 'boolean',
            'is_top' => 'boolean',
            'is_active' => 'boolean',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function specialOffer(): BelongsTo
    {
        return $this->belongsTo(SpecialOffer::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderByDesc('is_primary')->orderBy('sort_order');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getSalePriceAttribute(): string
    {
        return $this->discount_price ?? $this->price;
    }

    public function getInStockAttribute(): bool
    {
        return ! $this->manage_stock || $this->stock_quantity > 0;
    }

    public function getOgImageUrlAttribute(): ?string
    {
        if (! $this->og_image) {
            return $this->images->first()?->url;
        }

        return Str::startsWith($this->og_image, 'uploads/')
            ? route('media.show', ['path' => $this->og_image])
            : asset(ltrim($this->og_image, '/'));
    }
}
