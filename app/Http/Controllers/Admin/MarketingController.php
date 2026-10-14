<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CookieConsent;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Setting;
use App\Services\ImageOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class MarketingController extends Controller
{
    /**
     * ⚡ Günün Fırsatı & Canlı Geri Sayım Kampanyası
     */
    public function flashDeal()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.marketing.flash-deal', compact('settings', 'products'));
    }

    /**
     * 💧 Akıllı Cihaz Bulucu Sihirbazı (Quiz) Ürün Eşleştirmeleri
     */
    public function quiz()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.marketing.quiz', compact('settings', 'products'));
    }

    /**
     * 🎡 Şans Çarkı (Lucky Wheel) Ödül & Kupon Ayarları
     */
    public function wheel()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $coupons = Coupon::orderBy('code')->get();

        return view('admin.marketing.wheel', compact('settings', 'coupons'));
    }

    /**
     * 🎯 Google SEO & Takip Kodları Ayarları
     */
    public function seo()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('admin.marketing.seo', compact('settings'));
    }

    /**
     * 🎁 Ücretsiz Promosyon & Hediye Ürün Seçenekleri (Oyuncak, Matara vb.)
     */
    public function gifts()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('admin.marketing.gifts', compact('settings'));
    }

    /**
     * 💳 PayTR & Online Kredi Kartı Ödeme Ayarları
     */
    public function payment()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('admin.marketing.payment', compact('settings'));
    }

    /**
     * 📋 KVKK & Çerez Açık Rıza Kayıtları (Proof of Consent Logs)
     */
    public function cookieConsents()
    {
        $consents = CookieConsent::latest()->paginate(20);
        $totalConsents = CookieConsent::count();
        $acceptedCount = CookieConsent::where('consent_type', 'all')->count();
        $customCount = CookieConsent::where('consent_type', 'custom')->count();
        $rejectedCount = CookieConsent::where('consent_type', 'rejected')->count();

        return view('admin.marketing.cookie-consents', compact('consents', 'totalConsents', 'acceptedCount', 'customCount', 'rejectedCount'));
    }

    /**
     * KVKK Çerez İspat Kütüğünü Excel (CSV) Formatında Dışa Aktar
     */
    public function exportCookieConsents()
    {
        $consents = CookieConsent::latest()->get();
        $fileName = 'KVKK_Cerez_Riza_Kutugu_'.date('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        return response()->stream(function () use ($consents) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Rıza ID / Token',
                'IP Adresi',
                'Onay Türü',
                'Zorunlu Çerezler',
                'Analitik Çerezleri',
                'Pazarlama Çerezleri',
                'Tarayıcı / User Agent',
                'Zaman Damgası (Tarih & Saat)',
            ], ';');

            foreach ($consents as $c) {
                fputcsv($handle, [
                    $c->consent_token,
                    $c->ip_address,
                    $c->consent_type === 'all' ? 'Tümünü Kabul Etti' : ($c->consent_type === 'custom' ? 'Özel Tercih' : 'Yalnızca Zorunlular'),
                    $c->necessary ? 'Evet' : 'Hayır',
                    $c->analytics ? 'Evet' : 'Hayır',
                    $c->marketing ? 'Evet' : 'Hayır',
                    $c->user_agent,
                    $c->created_at->format('d.m.Y H:i:s'),
                ], ';');
            }
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * ⚡ Site Hızlandırma & Sistem Performans Merkezi
     */
    public function performance()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        // Sistem Tanılama Verileri
        $phpVersion = phpversion();
        $opcacheEnabled = function_exists('opcache_get_status') && is_array(@opcache_get_status()) && ! empty(@opcache_get_status()['opcache_enabled']);
        $memoryLimit = ini_get('memory_limit');
        $maxExecutionTime = ini_get('max_execution_time');

        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalImages = ProductImage::count();

        // Önbellek Durumları
        $isConfigCached = app()->configurationIsCached();
        $isRoutesCached = app()->routesAreCached();
        $isEventsCached = app()->eventsAreCached();

        return view('admin.marketing.performance', compact(
            'settings',
            'phpVersion',
            'opcacheEnabled',
            'memoryLimit',
            'maxExecutionTime',
            'totalProducts',
            'totalOrders',
            'totalImages',
            'isConfigCached',
            'isRoutesCached',
            'isEventsCached'
        ));
    }

    /**
     * 1-Tıkla Önbellek Temizleme ve Optimizasyon Motoru
     */
    public function clearCache(Request $request)
    {
        $action = $request->input('action', 'full_optimize');

        try {
            switch ($action) {
                case 'all_clear':
                    Artisan::call('optimize:clear');
                    $msg = 'Tüm sistem, görünüm ve rota önbellekleri başarıyla sıfırlandı.';
                    break;
                case 'view_clear':
                    Artisan::call('view:clear');
                    $msg = 'Blade şablon görünüm önbelleği temizlendi.';
                    break;
                case 'route_cache':
                    Artisan::call('route:cache');
                    $msg = 'Tüm URL rotaları maksimum hız için belleğe alındı (Route Cached).';
                    break;
                case 'config_cache':
                    Artisan::call('config:cache');
                    $msg = 'Sistem yapılandırma ayarları belleğe alındı (Config Cached).';
                    break;
                case 'optimize_images':
                    $res = ImageOptimizerService::optimizeExistingStoredImages();
                    $msg = $res['message'] ?? 'Görseller optimize edildi.';
                    break;
                case 'full_optimize':
                default:
                    Artisan::call('optimize:clear');
                    Artisan::call('config:cache');
                    Artisan::call('route:cache');
                    Artisan::call('view:cache');
                    $msg = '🚀 Tam Sistem Optimizasyonu Tamamlandı! Rotalar, görünümler ve ayarlar maksimum hız moduna alındı.';
                    break;
            }

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', 'Önbellek işlemi sırasında bir hata oluştu: '.$e->getMessage());
        }
    }

    /**
     * Ortak Ayarları Kaydetme Metodu
     */
    public function store(Request $request)
    {
        $data = $request->except('_token');

        $fileKeys = ['logo', 'favicon', 'og_image', 'gift_1_image', 'gift_2_image', 'gift_3_image', 'gift_4_image'];
        foreach ($fileKeys as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $maxWidth = ($fileKey === 'favicon') ? 256 : 1000;
                $maxHeight = ($fileKey === 'favicon') ? 256 : 1000;
                $path = ImageOptimizerService::optimizeAndStore($request->file($fileKey), 'settings', $maxWidth, $maxHeight, 88);
                Setting::updateOrCreate(['key' => $fileKey], ['value' => $path]);
                unset($data[$fileKey]);
            }
        }

        foreach ($data as $key => $value) {
            if (! in_array($key, $fileKeys)) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        return redirect()->back()->with('success', 'Ayarlar başarıyla kaydedildi.');
    }
}
