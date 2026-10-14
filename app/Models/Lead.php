<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'district',
        'source',
        'prize_won',
        'coupon_code',
        'status',
        'notes',
        'ip_address',
    ];

    public function getCleanPhoneAttribute()
    {
        return preg_replace('/[^0-9]/', '', $this->phone);
    }
}
