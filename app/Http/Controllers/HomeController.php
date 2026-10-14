<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Product;
use App\Models\Project;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $siteMode = env('SITE_MODE', 'core');

        $siteMatrix = config('site_matrix.sites', [
            [
                'name' => 'İstanbul Su Arıtma Marketi',
                'domain' => 'istanbulsuaritmamarketi.com',
                'type' => 'E-Ticaret & Yerel Servis',
                'server' => 'Cenuta cPanel 1',
                'status' => 'Canlıda (Aktif)',
                'badge' => 'bg-emerald-100 text-emerald-800',
            ],
            [
                'name' => 'Onur İnşaat Mimarlık',
                'domain' => 'onurinsaat.com.tr',
                'type' => 'Kurumsal & Danışmanlık',
                'server' => 'Cenuta cPanel 2',
                'status' => 'Geliştirme Aşamasında',
                'badge' => 'bg-blue-100 text-blue-800',
            ],
            [
                'name' => 'İTÜ Petek',
                'domain' => 'itupetek.com',
                'type' => 'Mobil App & API',
                'server' => 'Cenuta cPanel 2',
                'status' => 'Aktif Geliştirme',
                'badge' => 'bg-purple-100 text-purple-800',
            ],
            [
                'name' => 'Esenyurt Çiçekçisi',
                'domain' => 'esenyurtcicekcisi.com',
                'type' => 'E-Ticaret Vitrini',
                'server' => 'Cenuta cPanel 1',
                'status' => 'Hazırlık Aşamasında',
                'badge' => 'bg-amber-100 text-amber-800',
            ],
            [
                'name' => 'Reform İnşaat',
                'domain' => 'reforminsaat.com',
                'type' => 'Kurumsal Portföy',
                'server' => 'Cenuta cPanel 2',
                'status' => 'Hazırlık Aşamasında',
                'badge' => 'bg-blue-100 text-blue-800',
            ],
            [
                'name' => 'Yiğit Dijital Ajans',
                'domain' => 'yigitdijital.com',
                'type' => 'Danışmanlık & Web Servisleri',
                'server' => 'Cenuta cPanel 2',
                'status' => 'Planlama',
                'badge' => 'bg-slate-100 text-slate-800',
            ],
        ]);

        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        $productQuery = Product::where('is_active', true)->latest();
        if ($request->filled('search')) {
            $productQuery->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('short_description', 'like', '%'.$request->search.'%');
        }
        $featuredProducts = $productQuery->take(12)->get();

        $settings = Setting::all()->pluck('value', 'key');
        $topDealIds = array_values(array_filter([
            $settings['top_deal_1'] ?? null,
            $settings['top_deal_2'] ?? null,
            $settings['top_deal_3'] ?? null,
            $settings['top_deal_4'] ?? null,
        ]));

        if (! empty($topDealIds)) {
            $topDealProducts = Product::whereIn('id', $topDealIds)
                ->where('is_active', true)
                ->get()
                ->sortBy(function ($product) use ($topDealIds) {
                    return array_search($product->id, $topDealIds);
                })
                ->values();
        } else {
            $topDealProducts = Product::where('is_active', true)->take(4)->get();
        }

        $banners = Banner::where('is_active', true)->orderBy('order')->get();
        $faqs = Faq::where('is_active', true)->orderBy('sort_order')->get();

        $homeCat1Id = $settings['home_cat_1'] ?? null;
        $homeCat2Id = $settings['home_cat_2'] ?? null;

        $homeCat1 = $homeCat1Id ? Category::find($homeCat1Id) : $categories->first();
        $homeCat2 = $homeCat2Id ? Category::find($homeCat2Id) : $categories->skip(1)->first();

        $featuredProjects = Project::where('is_active', true)->orderBy('sort_order')->take(6)->get();

        $upsellGifts = \App\Models\Gift::where('is_active', true)->orderBy('sort_order')->take(4)->get();
        $districts = \App\Models\Region::where('is_active', true)->orderBy('name')->get();
        $sameDayFlowers = Product::where('is_active', true)->where('custom_badge', 'like', '%AYNI GÜN%')->take(6)->get();
        if ($sameDayFlowers->isEmpty()) {
            $sameDayFlowers = $featuredProducts->take(6);
        }

        $googleReviews = \App\Models\Review::where('is_approved', true)
            ->orderByRaw("CASE WHEN source = 'google' THEN 0 ELSE 1 END")
            ->latest()
            ->take(6)
            ->get();
        $googleRating = $settings['google_reviews_rating'] ?? '4.9';
        $googleReviewsTotal = $settings['google_reviews_total_count'] ?? '486';
        $googleMapsUrl = $settings['google_maps_url'] ?? 'https://maps.google.com';

        return view('welcome', compact('categories', 'homeCat1', 'homeCat2', 'featuredProducts', 'topDealProducts', 'banners', 'faqs', 'featuredProjects', 'siteMatrix', 'siteMode', 'upsellGifts', 'districts', 'sameDayFlowers', 'googleReviews', 'googleRating', 'googleReviewsTotal', 'googleMapsUrl'));
    }
}
