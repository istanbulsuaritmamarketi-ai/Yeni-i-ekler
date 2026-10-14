<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Services\ImageOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::with(['parent', 'children'])
            ->withCount('products')
            ->orderByRaw('CASE WHEN parent_id IS NULL THEN id ELSE parent_id END')
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->get();

        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        $parentBrands = Brand::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.brands.create', compact('parentBrands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_id' => 'nullable|exists:brands,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:brands',
            'search_query' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|mimes:jpeg,png,jpg,webp,gif,svg,jfif,avif,bmp|max:25600',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ], [
            'image.mimes' => 'Marka görseli yalnızca JPG, PNG, WEBP, GIF, SVG veya AVIF formatında olmalıdır.',
            'image.max' => 'Marka görseli en fazla 25 MB olabilir.',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (empty($data['search_query'])) {
            $data['search_query'] = $data['name'];
        }

        $data['is_active'] = $request->has('is_active') ? (bool) $request->is_active : true;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $data['image_path'] = ImageOptimizerService::optimizeAndStore($request->file('image'), 'brands', 800, 800, 85);
        }
        unset($data['image']);

        $brand = Brand::create($data);

        return redirect()->route('admin.brands.index')->with('success', 'Filtre markası/alt markası başarıyla eklendi.');
    }

    public function edit(Brand $brand)
    {
        $parentBrands = Brand::whereNull('parent_id')->where('id', '!=', $brand->id)->orderBy('name')->get();

        return view('admin.brands.edit', compact('brand', 'parentBrands'));
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'parent_id' => 'nullable|exists:brands,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:brands,slug,'.$brand->id,
            'search_query' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|mimes:jpeg,png,jpg,webp,gif,svg,jfif,avif,bmp|max:25600',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ], [
            'image.mimes' => 'Marka görseli yalnızca JPG, PNG, WEBP, GIF, SVG veya AVIF formatında olmalıdır.',
            'image.max' => 'Marka görseli en fazla 25 MB olabilir.',
        ]);

        // Prevent setting self as parent
        if ($data['parent_id'] == $brand->id) {
            $data['parent_id'] = null;
        }

        $data['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if (empty($data['search_query'])) {
            $data['search_query'] = $data['name'];
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($brand->image_path && Storage::disk('public')->exists($brand->image_path)) {
                Storage::disk('public')->delete($brand->image_path);
            }
            $data['image_path'] = ImageOptimizerService::optimizeAndStore($request->file('image'), 'brands', 800, 800, 85);
        }
        unset($data['image']);

        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('success', 'Filtre markası bilgileri güncellendi.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->image_path && Storage::disk('public')->exists($brand->image_path)) {
            Storage::disk('public')->delete($brand->image_path);
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', 'Filtre markası silindi.');
    }

    /**
     * Markaya ait uyumlu ürünleri aktif ürünler arasından tek tek seçme ekranı
     */
    public function manageProducts(Brand $brand)
    {
        $products = Product::with('category')->where('is_active', true)->orderBy('name')->get();
        $selectedProductIds = $brand->products()->pluck('products.id')->toArray();

        return view('admin.brands.products', compact('brand', 'products', 'selectedProductIds'));
    }

    /**
     * Seçilen ürünleri markaya bağlama/senkronize etme
     */
    public function syncProducts(Request $request, Brand $brand)
    {
        $productIds = $request->input('product_ids', []);
        $brand->products()->sync($productIds);

        return redirect()->route('admin.brands.index')->with('success', "'{$brand->name}' markası için ".count($productIds).' ürün başarıyla eşleştirildi.');
    }
}
