<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiFinanceController extends Controller
{
    public function stats(Request $request)
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $dailyServiceRevenue = ServiceRequest::whereDate('created_at', $today)
            ->where('status', 'Tamamlandı')
            ->sum('service_price') ?? 0;

        $monthlyServiceRevenue = ServiceRequest::where('created_at', '>=', $startOfMonth)
            ->where('status', 'Tamamlandı')
            ->sum('service_price') ?? 0;

        $totalServiceRevenue = ServiceRequest::where('status', 'Tamamlandı')
            ->sum('service_price') ?? 0;

        $dailyOrderRevenue = Order::whereDate('created_at', $today)
            ->whereNotIn('status', ['İptal Edildi', 'İade Edildi'])
            ->sum('total_amount') ?? 0;

        $monthlyOrderRevenue = Order::where('created_at', '>=', $startOfMonth)
            ->whereNotIn('status', ['İptal Edildi', 'İade Edildi'])
            ->sum('total_amount') ?? 0;

        $totalOrderRevenue = Order::whereNotIn('status', ['İptal Edildi', 'İade Edildi'])
            ->sum('total_amount') ?? 0;

        return response()->json([
            'daily_revenue' => (float) ($dailyServiceRevenue + $dailyOrderRevenue),
            'monthly_revenue' => (float) ($monthlyServiceRevenue + $monthlyOrderRevenue),
            'total_revenue' => (float) ($totalServiceRevenue + $totalOrderRevenue),
        ]);
    }
}
