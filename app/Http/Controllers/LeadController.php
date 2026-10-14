<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:30',
            'district' => 'nullable|string|max:100',
            'source' => 'nullable|string|max:50',
            'prize_won' => 'nullable|string|max:255',
            'coupon_code' => 'nullable|string|max:50',
        ]);

        $validated['ip_address'] = $request->ip();

        $lead = Lead::create($validated);

        // Kullanıcı giriş yapmışsa veya telefon numarasıyla kayıtlıysa kuponu hesabına da ekle
        $user = auth()->user();
        if (! $user && $request->filled('phone')) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $request->phone);
            if (str_starts_with($cleanPhone, '90') && strlen($cleanPhone) === 12) {
                $cleanPhone = '0'.substr($cleanPhone, 2);
            }
            $user = User::where('phone', $cleanPhone)->first();
        }

        if ($user && $lead->coupon_code) {
            $coupon = Coupon::firstOrCreate(
                ['code' => strtoupper($lead->coupon_code)],
                [
                    'type' => 'fixed',
                    'value' => 100,
                    'is_active' => true,
                ]
            );
            $user->assignCoupon($coupon, 'spin_wheel');
        }

        return response()->json([
            'success' => true,
            'message' => 'Tebrikler! İndirim kuponunuz kaydedildi.',
            'lead_id' => $lead->id,
            'coupon_code' => $lead->coupon_code,
        ]);
    }
}
