<?php

namespace App\Models;

use App\Enums\Audience;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'audience',
        'sku',
        'stock',
        'is_active',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'audience' => Audience::class,
            'price' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class)->orderBy('position');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeChildren(Builder $query): Builder
    {
        return $query->whereIn('audience', array_map(
            fn (Audience $a) => $a->value,
            Audience::children(),
        ));
    }

    public function scopeAdults(Builder $query): Builder
    {
        return $query->whereIn('audience', array_map(
            fn (Audience $a) => $a->value,
            Audience::adults(),
        ));
    }

    public function firstImageUrl(): ?string
    {
        $media = $this->relationLoaded('media')
            ? $this->media->first()
            : $this->media()->first();

        return $media?->url();
    }
}
