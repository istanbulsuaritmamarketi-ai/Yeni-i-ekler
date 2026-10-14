<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'address',
        'region_id',
        'total_amount',
        'total_cost',
        'status',
        'wants_service',
        'notes',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'customer_ip',
        'customer_user_agent',
        'kvkk_accepted_at',
        'contract_accepted_at',
        'delivery_date',
        'delivery_time_slot',
        'recipient_name',
        'recipient_phone',
        'recipient_district',
        'card_note',
        'card_template_id',
        'sender_name',
        'is_anonymous',
        'preparation_photo',
        'delivery_fee',
        'payment_method',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'is_anonymous' => 'boolean',
        'delivery_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::updated(function ($order) {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Sipariş Güncellendi',
                'model_type' => 'Order',
                'model_id' => $order->id,
                'details' => 'Sipariş durumu "'.$order->status.'" olarak güncellendi.',
                'ip_address' => request()->ip(),
            ]);
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
