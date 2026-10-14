<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        $query = Product::where('is_active', true)->with('category');

        // Arama (İsim, Çiçek Türü, Açıklama ve Kategori Arama)
        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('short_description', 'like', '%'.$searchTerm.'%')
                    ->orWhere('description', 'like', '%'.$searchTerm.'%')
                    ->orWhere('custom_badge', 'like', '%'.$searchTerm.'%')
                    ->orWhereHas('category', fn ($cq) => $cq->where('name', 'like', '%'.$searchTerm.'%'));
            });
        }

        // Kategori filtresi
        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        // Hızlı Rozet Filtresi (Aynı Gün Teslimat, vb.)
        if ($request->filled('badge')) {
            $query->where('custom_badge', 'like', '%'.$request->badge.'%');
        }

        // Fiyat filtresi
        if ($request->filled('min_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('campaign_price', '>=', $request->min_price)
                    ->orWhere(function ($sub) use ($request) {
                        $sub->whereNull('campaign_price')->where('price', '>=', $request->min_price);
                    });
            });
        }
        if ($request->filled('max_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('campaign_price', '<=', $request->max_price)
                    ->orWhere(function ($sub) use ($request) {
                        $sub->whereNull('campaign_price')->where('price', '<=', $request->max_price);
                    });
            });
        }

        // Sıralama
        match ($request->get('sort', 'popular')) {
            'price_asc' => $query->orderByRaw('COALESCE(campaign_price, price) ASC'),
            'price_desc' => $query->orderByRaw('COALESCE(campaign_price, price) DESC'),
            'name' => $query->orderBy('name', 'asc'),
            'newest' => $query->latest(),
            default => $query->latest(),
        };

        $products = $query->paginate(16)->withQueryString();

        return view('products', compact('products', 'categories'));
    }
}
