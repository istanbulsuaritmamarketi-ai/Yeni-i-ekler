<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'email',
        'rating',
        'comment',
        'is_approved',
        'source',
        'location',
        'author_photo',
        'google_review_id',
        'ip_address',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'rating' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
