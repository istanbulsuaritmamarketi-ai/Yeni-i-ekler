<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Müşteri Paneli (Kuponlarım, Siparişlerim ve Profil)
     */
    public function account()
    {
        $user = Auth::user();

        // Kullanıcıya ait kuponlar (Hoş geldin, Çark, vb.)
        $userCoupons = $user->userCoupons()
            ->with('coupon')
            ->get()
            ->filter(function ($uc) {
                return $uc->coupon !== null;
            });

        // Kullanıcının siparişleri (User ID veya Telefon ile eşleşenler)
        $cleanPhone = $user->phone ? preg_replace('/[^0-9]/', '', $user->phone) : null;
        $orders = Order::where(function ($q) use ($user, $cleanPhone) {
            $q->where('user_id', $user->id);
            if ($cleanPhone) {
                $last10 = substr($cleanPhone, -10);
                $q->orWhere('customer_phone', 'like', "%{$last10}%");
            }
        })
            ->latest()
            ->get();

        return view('customer.account', compact('user', 'userCoupons', 'orders'));
    }

    /**
     * Kuponu tek tıkla sepete uygular ve sepete yönlendirir
     */
    public function applyMyCoupon(string $code)
    {
        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (! $coupon || ! $coupon->is_active) {
            return redirect()->route('cart.index')->with('error', 'Kupon bulunamadı veya süresi dolmuş.');
        }

        session()->put('coupon', [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'min_order_amount' => $coupon->min_order_amount,
        ]);

        return redirect()->route('cart.index')->with('success', "✨ '{$coupon->code}' kuponunuz sepete başarıyla tanımlandı!");
    }

    /**
     * Müşteri profil bilgilerini ve şifresini günceller
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'current_password' => ['nullable', 'string'],
            'new_password' => ['nullable', 'string', 'min:6'],
        ], [
            'name.required' => 'Lütfen adınızı giriniz.',
            'new_password.min' => 'Yeni şifreniz en az 6 karakter olmalıdır.',
        ]);

        $user->name = trim($request->name);

        if ($request->filled('new_password')) {
            if (! $request->filled('current_password') || ! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Mevcut şifreniz hatalı.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Profil bilgileriniz başarıyla güncellendi.');
    }
}
