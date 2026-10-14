<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CookieConsent extends Model
{
    use HasFactory;

    protected $fillable = [
        'consent_token',
        'ip_address',
        'user_agent',
        'consent_type',
        'necessary',
        'analytics',
        'marketing',
    ];

    protected $casts = [
        'necessary' => 'boolean',
        'analytics' => 'boolean',
        'marketing' => 'boolean',
    ];
}
