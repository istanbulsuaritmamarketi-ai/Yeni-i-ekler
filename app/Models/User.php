<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'phone', 'password', 'role', 'is_admin'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function userCoupons()
    {
        return $this->hasMany(UserCoupon::class)->latest();
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'user_coupons')
            ->withPivot(['source', 'is_used', 'used_at'])
            ->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(Order::class)->latest();
    }

    /**
     * Kullanıcıya kupon tanımlar
     */
    public function assignCoupon($coupon, string $source = 'welcome')
    {
        $couponId = is_numeric($coupon) ? $coupon : $coupon->id;

        return UserCoupon::firstOrCreate([
            'user_id' => $this->id,
            'coupon_id' => $couponId,
        ], [
            'source' => $source,
            'is_used' => false,
        ]);
    }
}
