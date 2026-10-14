<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class DistrictController extends Controller
{
    /**
     * Tüm İstanbul / Bölge İlçe Havuzu (Yedek Referans)
     */
    public static array $districts = [
        'kadikoy' => ['name' => 'Kadıköy', 'side' => 'Anadolu', 'code' => 3401],
        'umraniye' => ['name' => 'Ümraniye', 'side' => 'Anadolu', 'code' => 3402],
        'uskudar' => ['name' => 'Üsküdar', 'side' => 'Anadolu', 'code' => 3403],
        'atasehir' => ['name' => 'Ataşehir', 'side' => 'Anadolu', 'code' => 3404],
        'maltepe' => ['name' => 'Maltepe', 'side' => 'Anadolu', 'code' => 3405],
        'kartal' => ['name' => 'Kartal', 'side' => 'Anadolu', 'code' => 3406],
        'pendik' => ['name' => 'Pendik', 'side' => 'Anadolu', 'code' => 3407],
        'tuzla' => ['name' => 'Tuzla', 'side' => 'Anadolu', 'code' => 3408],
        'beykoz' => ['name' => 'Beykoz', 'side' => 'Anadolu', 'code' => 3409],
        'cekmekoy' => ['name' => 'Çekmeköy', 'side' => 'Anadolu', 'code' => 3410],
        'sancaktepe' => ['name' => 'Sancaktepe', 'side' => 'Anadolu', 'code' => 3411],
        'sultanbeyli' => ['name' => 'Sultanbeyli', 'side' => 'Anadolu', 'code' => 3412],
        'sile' => ['name' => 'Şile', 'side' => 'Anadolu', 'code' => 3413],
        'adalar' => ['name' => 'Adalar', 'side' => 'Anadolu', 'code' => 3414],
        'besiktas' => ['name' => 'Beşiktaş', 'side' => 'Avrupa', 'code' => 3415],
        'sisli' => ['name' => 'Şişli', 'side' => 'Avrupa', 'code' => 3416],
        'sariyer' => ['name' => 'Sarıyer', 'side' => 'Avrupa', 'code' => 3417],
        'beyoglu' => ['name' => 'Beyoğlu', 'side' => 'Avrupa', 'code' => 3418],
        'fatih' => ['name' => 'Fatih', 'side' => 'Avrupa', 'code' => 3419],
        'bakirkoy' => ['name' => 'Bakırköy', 'side' => 'Avrupa', 'code' => 3420],
        'bahcelievler' => ['name' => 'Bahçelievler', 'side' => 'Avrupa', 'code' => 3421],
        'gungoren' => ['name' => 'Güngören', 'side' => 'Avrupa', 'code' => 3422],
        'zeytinburnu' => ['name' => 'Zeytinburnu', 'side' => 'Avrupa', 'code' => 3423],
        'bayrampasa' => ['name' => 'Bayrampaşa', 'side' => 'Avrupa', 'code' => 3424],
        'gaziosmanpasa' => ['name' => 'Gaziosmanpaşa', 'side' => 'Avrupa', 'code' => 3425],
        'eyupsultan' => ['name' => 'Eyüpsultan', 'side' => 'Avrupa', 'code' => 3426],
        'kagithane' => ['name' => 'Kağıthane', 'side' => 'Avrupa', 'code' => 3427],
        'sultangazi' => ['name' => 'Sultangazi', 'side' => 'Avrupa', 'code' => 3428],
        'esenler' => ['name' => 'Esenler', 'side' => 'Avrupa', 'code' => 3429],
        'bagcilar' => ['name' => 'Bağcılar', 'side' => 'Avrupa', 'code' => 3430],
        'kucukcekmece' => ['name' => 'Küçükçekmece', 'side' => 'Avrupa', 'code' => 3431],
        'avcilar' => ['name' => 'Avcılar', 'side' => 'Avrupa', 'code' => 3432],
        'beylikduzu' => ['name' => 'Beylikdüzü', 'side' => 'Avrupa', 'code' => 3433],
        'esenyurt' => ['name' => 'Esenyurt', 'side' => 'Avrupa', 'code' => 3434],
        'basaksehir' => ['name' => 'Başakşehir', 'side' => 'Avrupa', 'code' => 3435],
        'buyukcekmece' => ['name' => 'Büyükçekmece', 'side' => 'Avrupa', 'code' => 3436],
        'catalca' => ['name' => 'Çatalca', 'side' => 'Avrupa', 'code' => 3437],
        'silivri' => ['name' => 'Silivri', 'side' => 'Avrupa', 'code' => 3438],
        'arnavutkoy' => ['name' => 'Arnavutköy', 'side' => 'Avrupa', 'code' => 3439],
    ];

    /**
     * Sitenin sadece aktif seçilen ilçelerini döndürür.
     * config('districts.active') veya .env ACTIVE_DISTRICTS kontrol edilir.
     * Örneğin: 'all' veya 'kadikoy,umraniye,besiktas'
     */
    public static function getActiveDistricts(): array
    {
        $all = config('districts.list', self::$districts);

        try {
            $inactiveNames = \App\Models\Region::where('is_active', false)
                ->pluck('name')
                ->map(fn($n) => \Illuminate\Support\Str::slug($n))
                ->toArray();

            if (!empty($inactiveNames)) {
                $all = array_filter($all, function ($key) use ($inactiveNames) {
                    return !in_array($key, $inactiveNames);
                }, ARRAY_FILTER_USE_KEY);
            }
        } catch (\Throwable $e) {
            // DB fallback
        }

        $activeSetting = config('districts.active', 'all');

        if ($activeSetting === 'all' || empty($activeSetting)) {
            return $all;
        }

        if (is_string($activeSetting)) {
            $keys = array_map('trim', explode(',', strtolower($activeSetting)));
        } elseif (is_array($activeSetting)) {
            $keys = array_map('strtolower', $activeSetting);
        } else {
            return $all;
        }

        return array_filter($all, function ($key) use ($keys) {
            return in_array($key, $keys);
        }, ARRAY_FILTER_USE_KEY);
    }

    public function show($slug)
    {
        $slug = strtolower($slug);
        $activeDistricts = self::getActiveDistricts();

        if (! isset($activeDistricts[$slug])) {
            abort(404, 'Bu bölge için henüz hizmet verilmemektedir.');
        }

        $district = $activeDistricts[$slug];
        $districtName = $district['name'];
        $districtSide = $district['side'];

        $products = Product::where('is_active', true)->latest()->take(8)->get();
        $categories = Category::where('is_active', true)->get();
        $allDistricts = $activeDistricts;

        return view('district', compact('district', 'districtName', 'districtSide', 'slug', 'products', 'categories', 'allDistricts'));
    }

    public function index()
    {
        $districts = self::getActiveDistricts();

        return view('districts', compact('districts'));
    }
}
