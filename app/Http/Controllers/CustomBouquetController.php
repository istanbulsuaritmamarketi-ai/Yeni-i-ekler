<?php

namespace App\Http\Controllers;

use App\Models\BouquetPackage;
use App\Models\BouquetRibbon;
use App\Models\BouquetSize;
use App\Models\BouquetStem;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class CustomBouquetController extends Controller
{
    /**
     * Kendi Buketini Oluştur / Kendin Yap Sayfası
     */
    public function index()
    {
        $minStems = self::getMinStems();
        $stems = self::getAvailableStems();
        $sizes = self::getAvailableSizes();
        $packageColors = self::getPackageColors();
        $ribbonColors = self::getRibbonColors();

        return view('custom-bouquet.index', compact('stems', 'sizes', 'packageColors', 'ribbonColors', 'minStems'));
    }

    /**
     * Minimum dal sayısını ayardan veya varsayılan 7 olarak getir
     */
    public static function getMinStems(): int
    {
        $val = Setting::get('custom_bouquet_min_stems');
        return ($val !== null && is_numeric($val) && (int) $val > 0) ? (int) $val : 7;
    }

    /**
     * Hazırlanan Özel Buketi Sepete Ekle
     */
    public function addToCart(Request $request)
    {
        $minStems = self::getMinStems();
        $stemsInput = $request->input('stems', []); // [ '1' => 11, '2' => 5 ]
        $packageColorId = $request->input('package_color');
        $ribbonColorId = $request->input('ribbon_color');
        $sizeId = $request->input('size');
        $instructions = $request->input('instructions');

        $availableStems = collect(self::getAvailableStems())->keyBy('id');
        $availableSizes = collect(self::getAvailableSizes())->keyBy('id');
        $packageColors = collect(self::getPackageColors())->keyBy('id');
        $ribbonColors = collect(self::getRibbonColors())->keyBy('id');

        $chosenStems = [];
        $totalStems = 0;
        $totalPrice = 0;

        foreach ($stemsInput as $stemId => $qty) {
            $qty = (int) $qty;
            if ($qty > 0 && isset($availableStems[$stemId])) {
                $stem = $availableStems[$stemId];
                $subtotal = $qty * (float) $stem['price'];
                $totalStems += $qty;
                $totalPrice += $subtotal;
                $chosenStems[] = [
                    'id' => $stemId,
                    'name' => $stem['name'],
                    'color_name' => $stem['color_name'] ?? $stem['name'],
                    'icon' => $stem['icon'] ?? 'fas fa-spa',
                    'quantity' => $qty,
                    'unit_price' => (float) $stem['price'],
                    'subtotal' => $subtotal,
                ];
            }
        }

        if ($totalStems < $minStems) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Buketinizin dolgun ve estetik durması için lütfen en az {$minStems} dal çiçek seçiniz.");
        }

        $size = $availableSizes[$sizeId] ?? $availableSizes->first() ?? ['name' => 'Standart Boy (Klasik)', 'price' => 0];
        $package = $packageColors[$packageColorId] ?? $packageColors->first() ?? ['name' => 'Standart Ambalaj', 'price' => 0];
        $ribbon = $ribbonColors[$ribbonColorId] ?? $ribbonColors->first() ?? ['name' => 'Standart Kurdele', 'price' => 0];

        $totalPrice += (float) ($size['price'] ?? 0);
        $totalPrice += (float) ($package['price'] ?? 0);

        // Özet Reçete (Atölye ustası, fiş ve sepet için)
        $stemSummaryParts = array_map(fn ($s) => "{$s['quantity']}x {$s['name']}", $chosenStems);
        $summary = implode(', ', $stemSummaryParts)." | Boyut: {$size['name']} | Ambalaj: {$package['name']} | Kurdele: {$ribbon['name']}";

        $customProduct = $this->getOrCreateCustomBouquetProduct();

        $cart = session()->get('cart', []);
        $cartKey = 'custom_'.uniqid();

        $options = [
            'custom_bouquet' => true,
            'total_stems' => $totalStems,
            'stems' => $chosenStems,
            'size_id' => $size['id'] ?? null,
            'size_name' => $size['name'],
            'size_price' => (float) ($size['price'] ?? 0),
            'size_badge' => $size['badge'] ?? '',
            'package_color' => $package['name'],
            'package_color_id' => $packageColorId,
            'ribbon_color' => $ribbon['name'],
            'ribbon_color_id' => $ribbonColorId,
            'summary' => $summary,
            'instructions' => $instructions,
        ];

        $cart[$cartKey] = [
            'id' => $customProduct->id,
            'cart_key' => $cartKey,
            'name' => "Özel Tasarım Buket ({$totalStems} Dal)",
            'price' => $totalPrice,
            'original_price' => $totalPrice,
            'campaign_price' => null,
            'quantity' => 1,
            'image' => $customProduct->image_path,
            'slug' => 'kendi-buketini-olustur',
            'is_custom_bouquet' => true,
            'options' => $options,
        ];

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', "Özel tasarım buketiniz ({$totalStems} Dal) başarıyla oluşturuldu ve sepete eklendi!");
    }

    /**
     * Tekli Dal Çiçek Kataloğu ve Birim Fiyatlar (Veritabanından)
     */
    public static function getAvailableStems(): array
    {
        $dbStems = BouquetStem::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        if ($dbStems->isNotEmpty()) {
            return $dbStems->map(fn ($s) => [
                'id' => (string) $s->id,
                'name' => $s->name,
                'meaning' => $s->meaning ?: 'Taze Dal Çiçek',
                'price' => (float) $s->price,
                'color' => $s->color_hex ?: '#e11d48',
                'color_name' => $s->name,
                'image' => $s->image_url,
                'icon' => $s->icon ?: 'fas fa-spa',
                'is_active' => true,
            ])->toArray();
        }

        return [];
    }

    /**
     * Buket Boyut Seçenekleri (Veritabanından)
     */
    public static function getAvailableSizes(): array
    {
        $dbSizes = BouquetSize::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        if ($dbSizes->isNotEmpty()) {
            return $dbSizes->map(fn ($s) => [
                'id' => (string) $s->id,
                'name' => $s->name,
                'badge' => $s->badge ?: '',
                'description' => $s->description ?: '',
                'price' => (float) $s->price,
                'image' => $s->image_url,
                'icon' => $s->icon ?: 'fas fa-spa',
                'is_active' => true,
            ])->toArray();
        }

        return [
            [
                'id' => '1',
                'name' => 'Standart Boy (Klasik)',
                'badge' => 'Standart Form',
                'description' => 'Zarif ve dengeli standart buket formu',
                'price' => 0.0,
                'image' => asset('images/custom-bouquet-placeholder.svg'),
                'icon' => 'fas fa-spa',
                'is_active' => true,
            ],
            [
                'id' => '2',
                'name' => 'Büyük Boy (Deluxe)',
                'badge' => 'Popüler & Hacimli',
                'description' => 'Ekstra yeşillik ve daha dolgun, geniş aranjman sarımı',
                'price' => 150.0,
                'image' => asset('images/custom-bouquet-placeholder.svg'),
                'icon' => 'fas fa-layer-group',
                'is_active' => true,
            ],
            [
                'id' => '3',
                'name' => 'Mega Boy (King Size)',
                'badge' => 'Gösterişli & Dev',
                'description' => 'Lüks katmanlı sunum, maksimum hacim ve dev boy ambalaj',
                'price' => 300.0,
                'image' => asset('images/custom-bouquet-placeholder.svg'),
                'icon' => 'fas fa-crown',
                'is_active' => true,
            ],
        ];
    }

    /**
     * Buket Ambalaj / Paket Renk Seçenekleri (Veritabanından)
     */
    public static function getPackageColors(): array
    {
        $dbPackages = BouquetPackage::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        if ($dbPackages->isNotEmpty()) {
            return $dbPackages->map(fn ($p) => [
                'id' => (string) $p->id,
                'name' => $p->name,
                'desc' => $p->description ?: 'Lüks buket ambalaj kağıdı',
                'bg_class' => 'border-slate-300 text-slate-800',
                'color_hex' => $p->color_hex ?: '#fbcfe8',
                'image' => $p->image_url,
                'price' => (float) $p->price,
            ])->toArray();
        }

        return [];
    }

    /**
     * Süsleme Kurdelesi Seçenekleri (Veritabanından)
     */
    public static function getRibbonColors(): array
    {
        $dbRibbons = BouquetRibbon::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        if ($dbRibbons->isNotEmpty()) {
            return $dbRibbons->map(fn ($r) => [
                'id' => (string) $r->id,
                'name' => $r->name,
                'color_hex' => $r->color_hex ?: '#e11d48',
                'image' => $r->image_url,
                'price' => (float) $r->price,
            ])->toArray();
        }

        return [];
    }

    /**
     * Sipariş ve Sepet İçin Temel Özel Buket Ürününü Getir veya Oluştur
     */
    protected function getOrCreateCustomBouquetProduct(): Product
    {
        $product = Product::where('slug', 'kendi-buketini-olustur')->first();
        if (! $product) {
            $category = Category::where('slug', 'gul-buketleri')->first() ?? Category::first();
            $product = Product::create([
                'category_id' => $category ? $category->id : null,
                'name' => 'Kişiye Özel Tasarım Çiçek Buketi',
                'slug' => 'kendi-buketini-olustur',
                'short_description' => 'Müşterimizin çiçeklerini ve ambalajını bizzat tasarladığı taze el yapımı buket.',
                'description' => '<p>Kişiye özel seçilen taze çiçekler, ambalaj kağıdı ve saten kurdele ile hazırlanır.</p>',
                'price' => 750.00,
                'campaign_price' => null,
                'image_path' => 'images/custom-bouquet-placeholder.svg',
                'is_active' => true,
                'in_stock' => true,
            ]);
        }

        return $product;
    }
}
