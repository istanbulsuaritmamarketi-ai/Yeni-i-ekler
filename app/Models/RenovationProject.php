<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RenovationProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'contractor_name',
        'category',
        'location',
        'description',
        'before_image_path',
        'after_image_path',
        'gallery_images',
        'duration',
        'budget_approx',
        'has_free_purifier_gift',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'has_free_purifier_gift' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'budget_approx' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order', 'asc')->latest();
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_active', true)->where('is_featured', true)->orderBy('sort_order', 'asc');
    }
}
