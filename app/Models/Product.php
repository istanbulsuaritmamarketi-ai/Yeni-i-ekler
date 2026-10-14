<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'short_description',
        'description',
        'price',
        'campaign_price',
        'cost_price',
        'image_path',
        'is_active',
        'in_stock',
        'has_free_installation',
        'warranty_period',
        'has_cash_on_delivery',
        'custom_badge',
        'has_gift',
        'gift_note',
        'technical_features',
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'campaign_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'is_active' => 'boolean',
        'in_stock' => 'boolean',
        'has_free_installation' => 'boolean',
        'has_cash_on_delivery' => 'boolean',
        'has_gift' => 'boolean',
    ];

    public function getDiscountPercentAttribute(): ?int
    {
        if ($this->campaign_price && $this->campaign_price < $this->price && $this->price > 0) {
            return (int) round((($this->price - $this->campaign_price) / $this->price) * 100);
        }

        return null;
    }

    public function getEffectivePriceAttribute(): float
    {
        return ($this->campaign_price && $this->campaign_price < $this->price) ? (float) $this->campaign_price : (float) $this->price;
    }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image_path) {
            return asset('images/placeholder-flower.svg');
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

        return asset('images/placeholder-flower.svg');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_product');
    }

    public function gifts()
    {
        return $this->belongsToMany(Gift::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true)->latest();
    }

    public function getAverageRatingAttribute(): float
    {
        return round((float) ($this->approvedReviews()->avg('rating') ?: 5.0), 1);
    }

    public function getReviewCountAttribute(): int
    {
        return (int) $this->approvedReviews()->count();
    }
}
