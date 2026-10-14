<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function category($slug)
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $products = $category->products()->where('is_active', true)->latest()->paginate(12);

        return view('category', compact('category', 'products'));
    }

    public function product($slug)
    {
        $product = Product::with(['category', 'images', 'reviews' => fn ($q) => $q->where('is_approved', true)])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        $upsellGifts = \App\Models\Gift::where('is_active', true)->orderBy('sort_order')->get();
        $districts = \App\Models\Region::where('is_active', true)->orderBy('name')->get();

        $readyNotes = [
            'Aşk & Romantizm' => [
                'Hayatımın anlamı, varlığınla dünyama bahar getirdin. Seni çok seviyorum.',
                'Gözlerinin güzelliği bu çiçeklerin hepsinden daha parlak. İyi ki varsın birtanem.',
                'Her geçen gün sana olan sevgim daha da büyüyor. Sonsuza kadar seninle...',
                'Kalbimin tek sahibine, en taze çiçekler gibi solmayan sonsuz bir aşkla...',
            ],
            'Doğum Günü' => [
                'Yeni yaşın sana sağlık, mutluluk ve kalbinin dilediği tüm güzellikleri getirsin. İyi ki doğdun!',
                'Gülüşün gibi aydınlık, çiçekler kadar renkli bir yaş dilerim. Doğum günün kutlu olsun!',
                'Dünyanın en harika insanına en tatlı yaş dileklerimle... Nice mutlu senelere!',
            ],
            'Yıldönümü' => [
                'Seninle geçen her gün ilk günkü gibi taze ve heyecan verici. Yıldönümümüz kutlu olsun sevgilim.',
                'Birlikte yazdığımız bu güzel hikayenin nice mutlu sayfalarında el ele olmak dileğiyle...',
            ],
            'Tebrik & Başarı' => [
                'Yeni işinde ve hayatında üstün başarılar dilerim. Hak ettiğin tüm güzellikler seninle olsun!',
                'Başarılarının devamını diler, gururla tebrik ederim. Yolun her zaman açık olsun.',
            ],
            'Özür Dilerim' => [
                'Seni kırdığım için çok üzgünüm. Lütfen bu çiçekleri barışmamızın ilk adımı kabul et...',
                'Kalbimdeki yerini hiçbir şey değiştiremez. Beni affetmen dileğiyle, çok özür dilerim.',
            ],
            'Geçmiş Olsun' => [
                'En kısa zamanda sağlığına kavuşup aramıza dönmen dileğiyle. Çok geçmiş olsun!',
                'Dualarımız ve sevgimiz seninle. Çiçekler gibi hızla canlanıp iyileşmen dileğiyle...',
            ],
        ];

        return view('product', compact('product', 'relatedProducts', 'upsellGifts', 'districts', 'readyNotes'));
    }

    public function trackOrder(Request $request)
    {
        $order = null;
        $error = null;

        if ($request->filled('order_number') || $request->filled('phone')) {
            $query = Order::query()->with('items.product');

            if ($request->filled('order_number')) {
                $query->where('order_number', trim($request->order_number));
            }
            if ($request->filled('phone')) {
                $cleanPhone = preg_replace('/[^0-9]/', '', $request->phone);
                $query->where(function ($q) use ($cleanPhone) {
                    $q->where('customer_phone', 'like', '%'.$cleanPhone.'%')
                        ->orWhere('recipient_phone', 'like', '%'.$cleanPhone.'%');
                });
            }

            $order = $query->latest()->first();

            if (! $order) {
                $error = 'Girdiğiniz bilgilere ait bir sipariş kaydı bulunamadı. Lütfen sipariş numaranızı kontrol ediniz.';
            }
        }

        return view('track-order', compact('order', 'error'));
    }

    public function page($slug)
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return view('page', compact('page'));
    }

    public function social()
    {
        return view('social');
    }

    public function contact()
    {
        return view('contact');
    }

    public function storeContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        ContactMessage::create($request->all());

        return back()->with('success', 'Mesajınız başarıyla alınmıştır. En kısa sürede size dönüş yapacağız.');
    }

    public function categories()
    {
        $categories = Category::where('is_active', true)->withCount(['products' => function ($q) {
            $q->where('is_active', true);
        }])->orderBy('sort_order')->get();

        return view('categories', compact('categories'));
    }

    public function calculator()
    {
        $topProducts = Product::where('is_active', true)->take(4)->get();

        return view('calculator', compact('topProducts'));
    }

    public function wizard()
    {
        return view('wizard');
    }

    public function filterFinder(Request $request)
    {
        // Ana markaları ve alt modellerini yükle
        $brands = Brand::with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $selectedBrand = null;
        $selectedSubBrand = null;

        if ($request->filled('brand')) {
            $selectedBrand = $brands->firstWhere('slug', $request->brand);
            if (! $selectedBrand) {
                // Belki doğrudan alt marka slug'ı girilmiştir
                $foundSub = Brand::with('parent')->whereNotNull('parent_id')->where('slug', $request->brand)->first();
                if ($foundSub) {
                    $selectedBrand = $foundSub->parent;
                    $selectedSubBrand = $foundSub;
                }
            }
        }

        if ($selectedBrand && $request->filled('sub')) {
            $selectedSubBrand = $selectedBrand->children->firstWhere('slug', $request->sub);
        }

        if ($selectedSubBrand) {
            // Spesifik alt model seçilmişse: Sadece bu modele atanmış ürünler
            $targetBrandIds = [$selectedSubBrand->id];
            $searchTerm = $selectedSubBrand->search_query ?: $selectedSubBrand->name;

            $products = Product::where('is_active', true)
                ->where(function ($q) use ($targetBrandIds, $searchTerm) {
                    $q->whereIn('brand_id', $targetBrandIds)
                        ->orWhereHas('brands', fn ($bq) => $bq->whereIn('brands.id', $targetBrandIds));
                    if (! empty($searchTerm)) {
                        $q->orWhere('name', 'like', '%'.$searchTerm.'%')
                            ->orWhere('short_description', 'like', '%'.$searchTerm.'%');
                    }
                })->latest()->get();

        } elseif ($selectedBrand) {
            // Ana marka seçilmişse: Kendisine VE bağlı tüm alt modellerine atanmış ürünler
            $targetBrandIds = array_merge([$selectedBrand->id], $selectedBrand->children->pluck('id')->toArray());
            $searchTerm = $selectedBrand->search_query ?: $selectedBrand->name;

            $products = Product::where('is_active', true)
                ->where(function ($q) use ($targetBrandIds, $searchTerm) {
                    $q->whereIn('brand_id', $targetBrandIds)
                        ->orWhereHas('brands', fn ($bq) => $bq->whereIn('brands.id', $targetBrandIds));
                    if (! empty($searchTerm)) {
                        $q->orWhere('name', 'like', '%'.$searchTerm.'%')
                            ->orWhere('short_description', 'like', '%'.$searchTerm.'%');
                    }
                })->latest()->get();

            // Eğer doğrudan eşleşen yoksa üniversal filtreleri getir
            if ($products->isEmpty()) {
                $products = Product::where('is_active', true)
                    ->where(function ($q) {
                        $q->where('name', 'like', '%filtre%')
                            ->orWhere('short_description', 'like', '%filtre%')
                            ->orWhere('description', 'like', '%filtre%');
                    })->latest()->get();
            }
        } else {
            // Hiçbir marka seçilmemişse tüm filtre setlerini göster
            $products = Product::where('is_active', true)
                ->where(function ($q) {
                    $q->where('name', 'like', '%filtre%')
                        ->orWhere('short_description', 'like', '%filtre%')
                        ->orWhere('description', 'like', '%filtre%');
                })->latest()->get();

            if ($products->isEmpty()) {
                $products = Product::where('is_active', true)->latest()->take(12)->get();
            }
        }

        // Eğer marka seçilerek geldiyse doğrudan markanın kendi özel sayfasına yönlendir
        if ($request->filled('brand')) {
            $b = Brand::where('slug', $request->brand)->first();
            if ($b) {
                return redirect()->route('brand.show', array_filter(['brand' => $b->slug, 'model' => $request->sub]));
            }
        }

        return view('filter-finder', compact('brands', 'selectedBrand', 'selectedSubBrand', 'products'));
    }

    /**
     * Markaya Özel Sayfa (Örn: /marka/ihlas-aura-cebilon)
     */
    public function brandShow(Request $request, Brand $brand)
    {
        $brand->load(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')]);

        $selectedSubBrand = null;
        if ($request->filled('model')) {
            $selectedSubBrand = $brand->children->firstWhere('slug', $request->model);
        }

        if ($selectedSubBrand) {
            $targetBrandIds = [$selectedSubBrand->id];
            $searchTerm = $selectedSubBrand->search_query ?: $selectedSubBrand->name;

            $products = Product::where('is_active', true)
                ->where(function ($q) use ($targetBrandIds, $searchTerm) {
                    $q->whereIn('brand_id', $targetBrandIds)
                        ->orWhereHas('brands', fn ($bq) => $bq->whereIn('brands.id', $targetBrandIds));
                    if (! empty($searchTerm)) {
                        $q->orWhere('name', 'like', '%'.$searchTerm.'%')
                            ->orWhere('short_description', 'like', '%'.$searchTerm.'%');
                    }
                })->latest()->get();
        } else {
            $targetBrandIds = array_merge([$brand->id], $brand->children->pluck('id')->toArray());
            $searchTerm = $brand->search_query ?: $brand->name;

            $products = Product::where('is_active', true)
                ->where(function ($q) use ($targetBrandIds, $searchTerm) {
                    $q->whereIn('brand_id', $targetBrandIds)
                        ->orWhereHas('brands', fn ($bq) => $bq->whereIn('brands.id', $targetBrandIds));
                    if (! empty($searchTerm)) {
                        $q->orWhere('name', 'like', '%'.$searchTerm.'%')
                            ->orWhere('short_description', 'like', '%'.$searchTerm.'%');
                    }
                })->latest()->get();

            if ($products->isEmpty()) {
                $products = Product::where('is_active', true)
                    ->where(function ($q) {
                        $q->where('name', 'like', '%filtre%')
                            ->orWhere('short_description', 'like', '%filtre%')
                            ->orWhere('description', 'like', '%filtre%');
                    })->latest()->take(12)->get();
            }
        }

        $otherBrands = Brand::whereNull('parent_id')
            ->where('is_active', true)
            ->where('id', '!=', $brand->id)
            ->orderBy('sort_order')
            ->get();

        return view('brand', compact('brand', 'selectedSubBrand', 'products', 'otherBrands'));
    }
}
