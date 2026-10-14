<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RegionController extends Controller
{
    /**
     * 30 Büyükşehir Listesi
     */
    protected array $metropolitanCities = [
        'Adana', 'Ankara', 'Antalya', 'Aydın', 'Balıkesir', 'Bursa', 'Denizli', 'Diyarbakır',
        'Erzurum', 'Eskişehir', 'Gaziantep', 'Hatay', 'İstanbul', 'İzmir', 'Kahramanmaraş',
        'Kayseri', 'Kocaeli', 'Konya', 'Malatya', 'Manisa', 'Mardin', 'Mersin', 'Muğla',
        'Ordu', 'Sakarya', 'Samsun', 'Şanlıurfa', 'Tekirdağ', 'Trabzon', 'Van',
    ];

    /**
     * 81 İl ve İlçe Teslimat Yönetimi
     */
    public function index(Request $request)
    {
        // 81 İlin toplam ve aktif ilçe istatistikleri
        $cityStats = Region::select(
            'city',
            DB::raw('COUNT(*) as total_count'),
            DB::raw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_count')
        )
            ->groupBy('city')
            ->orderBy('city')
            ->get()
            ->keyBy('city');

        // Genel sayaçlar
        $totalCities = $cityStats->count();
        $totalDistricts = Region::count();
        $activeDistricts = Region::where('is_active', true)->count();
        $inactiveDistricts = $totalDistricts - $activeDistricts;

        // Filtre parametreleri
        $search = trim($request->input('q', ''));
        $statusFilter = $request->input('status', 'all');
        $selectedCity = $request->input('city');

        // Varsayılan olarak eğer arama yoksa ve il seçilmemişse İstanbul göster
        if (! $selectedCity && ! $search) {
            $selectedCity = 'İstanbul';
        }

        $query = Region::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        } elseif ($selectedCity && $selectedCity !== 'all') {
            $query->where('city', $selectedCity);
        }

        if ($statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        $regions = $query->orderBy('city')->orderBy('name')->get();

        return view('admin.regions.index', compact(
            'regions',
            'cityStats',
            'selectedCity',
            'search',
            'statusFilter',
            'totalCities',
            'totalDistricts',
            'activeDistricts',
            'inactiveDistricts'
        ));
    }

    /**
     * Tekil İlçe Aç/Kapat (AJAX ve Klasik İstek Desteği)
     */
    public function toggle(Request $request, Region $region)
    {
        if ($request->has('status')) {
            $region->is_active = $request->boolean('status');
        } else {
            $region->is_active = ! $region->is_active;
        }

        $region->save();

        Cache::forget('turkey_locations_all');

        $statusText = $region->is_active ? 'teslimata açıldı' : 'teslimata kapatıldı';
        $message = "{$region->city} / {$region->name} ilçesi {$statusText}.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_active' => $region->is_active,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Belirli Bir İlin Tüm İlçelerini Toplu Aç/Kapat
     */
    public function toggleCity(Request $request)
    {
        $request->validate([
            'city' => 'required|string|max:100',
            'status' => 'required|boolean',
        ]);

        $city = $request->input('city');
        $status = $request->boolean('status');

        $affected = Region::where('city', $city)->update(['is_active' => $status]);

        Cache::forget('turkey_locations_all');

        $statusText = $status ? 'teslimata açıldı' : 'teslimata kapatıldı';

        return back()->with('success', "{$city} iline ait {$affected} ilçe başarıyla {$statusText}.");
    }

    /**
     * Tüm Türkiye Genelinde Hızlı Hazır Ayarlar (Toplu İşlem)
     */
    public function toggleAll(Request $request)
    {
        $request->validate([
            'action' => 'required|in:all_active,all_inactive,metropolitan',
        ]);

        $action = $request->input('action');

        switch ($action) {
            case 'all_active':
                Region::query()->update(['is_active' => true]);
                $message = 'Tüm Türkiye genelindeki 81 il ve tüm ilçeler teslimata açıldı.';
                break;

            case 'all_inactive':
                Region::query()->update(['is_active' => false]);
                $message = 'Tüm Türkiye genelindeki ilçeler teslimata kapatıldı.';
                break;

            case 'metropolitan':
                Region::whereIn('city', $this->metropolitanCities)->update(['is_active' => true]);
                Region::whereNotIn('city', $this->metropolitanCities)->update(['is_active' => false]);
                $message = '30 Büyükşehir teslimata açıldı, diğer iller kapatıldı.';
                break;
        }

        Cache::forget('turkey_locations_all');

        return back()->with('success', $message);
    }

    /**
     * Yeni İlçe Ekle
     */
    public function store(Request $request)
    {
        $request->validate([
            'city' => 'required|string|max:100',
            'name' => 'required|string|max:100',
        ]);

        $region = Region::firstOrCreate(
            ['city' => $request->city, 'name' => $request->name],
            ['is_active' => true]
        );

        if (! $region->wasRecentlyCreated) {
            $region->update(['is_active' => true]);
        }

        Cache::forget('turkey_locations_all');

        return back()->with('success', "{$request->city} - {$request->name} ilçesi sisteme eklendi.");
    }

    /**
     * Eski Update Metodu (Geriye Uyumluluk İçin)
     */
    public function update(Request $request, Region $region)
    {
        $region->update([
            'is_active' => $request->has('is_active'),
        ]);

        Cache::forget('turkey_locations_all');

        return redirect()->route('admin.regions.index', ['city' => $region->city])->with('success', 'Bölge durumu güncellendi.');
    }

    /**
     * Hızlı Kurye / VIP Teslimat Ayarları ve Ek Ücretini Kaydet
     */
    public function saveExpressSettings(Request $request)
    {
        $request->validate([
            'express_courier_fee' => 'required|numeric|min:0',
            'express_courier_title' => 'required|string|max:255',
            'express_courier_subtitle' => 'nullable|string|max:500',
        ]);

        \App\Models\Setting::set('express_courier_active', $request->has('express_courier_active') ? '1' : '0');
        \App\Models\Setting::set('express_courier_fee', (string) $request->express_courier_fee);
        \App\Models\Setting::set('express_courier_title', $request->express_courier_title);
        \App\Models\Setting::set('express_courier_subtitle', $request->express_courier_subtitle ?? '');

        return redirect()->route('admin.regions.index')->with('success', 'Hızlı Kurye / VIP Teslimat ayarları ve ek ücreti başarıyla güncellendi.');
    }

    /**
     * İlçe Sil
     */
    public function destroy(Region $region)
    {
        $city = $region->city;
        $name = $region->name;

        $region->delete();

        Cache::forget('turkey_locations_all');

        return redirect()->route('admin.regions.index', ['city' => $city])->with('success', "{$city} / {$name} silindi.");
    }
}

