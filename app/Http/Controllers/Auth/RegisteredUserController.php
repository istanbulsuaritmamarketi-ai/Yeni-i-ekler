<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('auth.register', compact('settings'));
    }

    /**
     * Handle an incoming registration request.
     * SADECE: İsim, Telefon ve Şifre
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'name.required' => 'Lütfen adınızı ve soyadınızı giriniz.',
            'phone.required' => 'Lütfen telefon numaranızı giriniz.',
            'password.required' => 'Lütfen bir şifre belirleyiniz.',
            'password.min' => 'Şifreniz en az 6 karakter olmalıdır.',
        ]);

        // Telefonu temizle (05xxxxxxxxx formatı)
        $cleanPhone = preg_replace('/[^0-9]/', '', $request->phone);
        if (str_starts_with($cleanPhone, '90') && strlen($cleanPhone) === 12) {
            $cleanPhone = '0'.substr($cleanPhone, 2);
        } elseif (! str_starts_with($cleanPhone, '0') && strlen($cleanPhone) === 10) {
            $cleanPhone = '0'.$cleanPhone;
        }

        if (strlen($cleanPhone) < 10) {
            throw ValidationException::withMessages([
                'phone' => 'Lütfen geçerli bir cep telefonu numarası giriniz.',
            ]);
        }

        // Telefon benzersizlik kontrolü
        if (User::where('phone', $cleanPhone)->exists()) {
            throw ValidationException::withMessages([
                'phone' => 'Bu telefon numarası ile kayıtlı bir hesap zaten bulunmaktadır. Lütfen giriş yapınız.',
            ]);
        }

        $email = $cleanPhone.'@musteri.tazecicekler.com';

        $user = User::create([
            'name' => trim($request->name),
            'phone' => $cleanPhone,
            'email' => $email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'is_admin' => false,
        ]);

        // 🎁 Admin Ayarlarında Hoş Geldin Kuponu Aktif mi?
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $isWelcomePromoActive = ($settings['welcome_coupon_enabled'] ?? '1') == '1';

        if ($isWelcomePromoActive) {
            $couponCode = strtoupper(trim($settings['welcome_coupon_code'] ?? 'HOSGELDIN150'));
            $couponValue = (float) ($settings['welcome_coupon_discount'] ?? 150);
            $couponMinOrder = (float) ($settings['welcome_coupon_min_order'] ?? 500);
            $couponType = ($settings['welcome_coupon_type'] ?? 'fixed');

            $coupon = Coupon::firstOrCreate(
                ['code' => $couponCode],
                [
                    'type' => $couponType,
                    'value' => $couponValue,
                    'min_order_amount' => $couponMinOrder,
                    'is_active' => true,
                    'usage_limit' => null,
                ]
            );

            $user->assignCoupon($coupon, 'welcome');
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('customer.account')->with('success', '🎉 Hoş geldiniz! Üyeliğiniz başarıyla oluşturuldu ve indirim kuponunuz hesabınıza tanımlandı.');
    }
}
