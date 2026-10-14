<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getImageUrlAttribute(): string
    {
        if (empty($this->image_path)) {
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
}
