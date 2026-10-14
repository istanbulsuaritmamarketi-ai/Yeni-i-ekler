<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'usage_limit',
        'used_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'float',
        'min_order_amount' => 'float',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isValidForAmount(float $amount): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($amount < $this->min_order_amount) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if ($this->type === 'percent') {
            $discount = ($amount * $this->value) / 100;
        } else {
            $discount = $this->value;
        }

        return min($amount, max(0, $discount));
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_coupons')
            ->withPivot(['source', 'is_used', 'used_at'])
            ->withTimestamps();
    }
}
