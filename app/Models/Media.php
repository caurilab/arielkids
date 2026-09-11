<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'product_id',
        'path',
        'alt',
        'position',
    ];

    protected $appends = ['url'];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (Media $media) {
            if (! str_starts_with($media->path, 'http')) {
                Storage::disk('public')->delete($media->path);
            }
        });
    }

    public function url(): string
    {
        return str_starts_with($this->path, 'http')
            ? $this->path
            : Storage::disk('public')->url($this->path);
    }

    public function getUrlAttribute(): string
    {
        return $this->url();
    }
}
