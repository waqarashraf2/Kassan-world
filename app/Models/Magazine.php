<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Magazine extends Model
{
    use HasFactory, HasUniqueSlug, SoftDeletes;

    protected string $slugSource = 'title';

    protected $attributes = [
        'price' => 0,
        'is_free' => false,
        'allow_download' => true,
        'is_active' => true,
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_free' => 'boolean',
            'allow_download' => 'boolean',
            'is_active' => 'boolean',
            'issue_date' => 'date',
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

    public function purchases(): HasMany
    {
        return $this->hasMany(MagazinePurchase::class);
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        if (! $this->cover_image) {
            return null;
        }

        if (Str::startsWith($this->cover_image, ['http://', 'https://'])) {
            return $this->cover_image;
        }

        $path = $this->normalizeCoverImagePath($this->cover_image);
        $this->copyLegacyCoverIfNeeded($path);

        return asset($path);
    }

    private function normalizeCoverImagePath(string $path): string
    {
        $path = str_replace('\\', '/', trim($path));
        $path = parse_url($path, PHP_URL_PATH) ?: $path;
        $path = ltrim($path, '/');

        foreach ([
            'public/storage/uploads/magazines/' => 'storage/uploads/magazines/',
            'storage/app/public/uploads/magazines/' => 'storage/uploads/magazines/',
            'app/public/uploads/magazines/' => 'storage/uploads/magazines/',
            'uploads/magazines/' => 'storage/uploads/magazines/',
        ] as $prefix => $replacement) {
            if (Str::startsWith($path, $prefix)) {
                return $replacement.Str::after($path, $prefix);
            }
        }

        return $path;
    }

    private function copyLegacyCoverIfNeeded(string $publicPath): void
    {
        if (! Str::startsWith($publicPath, 'storage/uploads/magazines/')) {
            return;
        }

        $filename = basename($publicPath);
        $root = dirname(__DIR__, 2);
        $target = $root.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $publicPath);

        if (File::exists($target)) {
            return;
        }

        $legacy = $root.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'magazines'.DIRECTORY_SEPARATOR.$filename;

        if (! File::exists($legacy)) {
            return;
        }

        File::ensureDirectoryExists(dirname($target), 0755, true);
        File::copy($legacy, $target);
    }
}
