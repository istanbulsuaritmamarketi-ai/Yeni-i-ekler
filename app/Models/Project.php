<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'location',
        'status',
        'summary',
        'description',
        'featured_image',
        'gallery',
        'specifications',
        'client',
        'sort_order',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'gallery' => 'array',
        'specifications' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];
}
