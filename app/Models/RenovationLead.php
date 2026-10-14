<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RenovationLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'contractor_name',
        'customer_name',
        'phone',
        'district',
        'service_type',
        'property_size_sqm',
        'estimated_budget',
        'notes',
        'status',
    ];

    protected $casts = [
        'property_size_sqm' => 'integer',
        'estimated_budget' => 'decimal:2',
    ];

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'new' => 'Yeni Talep',
            'contacted' => 'Müşteri Arandı',
            'appointment_set' => 'Keşif Randevusu Verildi',
            'proposal_sent' => 'Teklif Gönderildi',
            'completed' => 'Anlaşma Sağlandı / Bitti',
            'cancelled' => 'İptal Edildi',
            default => 'Bekliyor',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'new' => 'bg-amber-100 text-amber-800',
            'contacted' => 'bg-blue-100 text-blue-800',
            'appointment_set' => 'bg-purple-100 text-purple-800',
            'proposal_sent' => 'bg-indigo-100 text-indigo-800',
            'completed' => 'bg-emerald-100 text-emerald-800',
            'cancelled' => 'bg-rose-100 text-rose-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
