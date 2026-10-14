<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BouquetRibbon extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'image_path',
        'color_hex',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image_path)) {
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
