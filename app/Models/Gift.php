<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'badge',
        'category',
        'description',
        'price',
        'external_url',
        'market_price',
        'image_path',
        'icon',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'price' => 'decimal:2',
        'market_price' => 'decimal:2',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order', 'asc');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }
        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }
        if (file_exists(public_path('storage/' . $this->image_path))) {
            return asset('storage/' . $this->image_path);
        }
        if (file_exists(public_path('images/' . $this->image_path))) {
            return asset('images/' . $this->image_path);
        }
        if (file_exists(public_path($this->image_path))) {
            return asset($this->image_path);
        }

        return null;
    }
}
