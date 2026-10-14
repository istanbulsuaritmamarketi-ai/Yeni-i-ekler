<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_path',
        'is_active',
        'sort_order',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image_path) {
            return asset('images/placeholder-category.svg');
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

        return asset('images/placeholder-category.svg');
    }
}
