<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $defaultCoupons = [
            ['code' => 'ARITMA500', 'type' => 'fixed', 'value' => 500, 'min_order_amount' => 0, 'is_active' => true],
            ['code' => 'FILTREHEDIYE', 'type' => 'fixed', 'value' => 350, 'min_order_amount' => 0, 'is_active' => true],
            ['code' => 'INDIRIM15', 'type' => 'percent', 'value' => 15, 'min_order_amount' => 0, 'is_active' => true],
            ['code' => 'UCRETSIZMONTAJ', 'type' => 'fixed', 'value' => 250, 'min_order_amount' => 0, 'is_active' => true],
            ['code' => 'ALKALIPH', 'type' => 'fixed', 'value' => 300, 'min_order_amount' => 0, 'is_active' => true],
            ['code' => 'EKSTRA300', 'type' => 'fixed', 'value' => 300, 'min_order_amount' => 0, 'is_active' => true],
        ];

        foreach ($defaultCoupons as $c) {
            Coupon::firstOrCreate(['code' => $c['code']], $c);
        }
    }
}
