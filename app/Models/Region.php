<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = ['city', 'name', 'is_active'];

    public function getFullNameAttribute(): string
    {
        return ($this->city ? $this->city . ' / ' : '') . $this->name;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
