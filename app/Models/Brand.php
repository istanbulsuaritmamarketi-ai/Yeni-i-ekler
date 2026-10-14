<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'search_query',
        'description',
        'image_path',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'parent_id' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            if (empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(Brand::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Brand::class, 'parent_id')->orderBy('sort_order');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'brand_product');
    }

    /**
     * Get all product IDs attached to this brand OR its children sub-brands
     */
    public function getAllProductIds(): array
    {
        $brandIds = array_merge([$this->id], $this->children()->pluck('id')->toArray());

        return \DB::table('brand_product')
            ->whereIn('brand_id', $brandIds)
            ->pluck('product_id')
            ->unique()
            ->toArray();
    }
}
