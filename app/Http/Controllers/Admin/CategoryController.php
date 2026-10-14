<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ImageOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'image' => 'nullable|mimes:jpeg,png,jpg,webp,gif,svg,jfif,avif,bmp|max:25600',
            'sort_order' => 'integer',
        ], [
            'image.mimes' => 'Kategori görseli yalnızca JPG, PNG, WEBP, GIF, SVG veya AVIF formatında olmalıdır.',
            'image.max' => 'Kategori görseli en fazla 25 MB olabilir.',
        ]);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $data['image_path'] = ImageOptimizerService::optimizeAndStore($request->file('image'), 'categories', 800, 800, 85);
        }
        unset($data['image']);

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori başarıyla eklendi.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,'.$category->id,
            'description' => 'nullable|string',
            'image' => 'nullable|mimes:jpeg,png,jpg,webp,gif,svg,jfif,avif,bmp|max:25600',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ], [
            'image.mimes' => 'Kategori görseli yalnızca JPG, PNG, WEBP, GIF, SVG veya AVIF formatında olmalıdır.',
            'image.max' => 'Kategori görseli en fazla 25 MB olabilir.',
        ]);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($category->image_path && Storage::disk('public')->exists($category->image_path)) {
                Storage::disk('public')->delete($category->image_path);
            }
            $data['image_path'] = ImageOptimizerService::optimizeAndStore($request->file('image'), 'categories', 800, 800, 85);
        }
        unset($data['image']);

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori güncellendi.');
    }

    public function deleteImage(Category $category)
    {
        if ($category->image_path && Storage::disk('public')->exists($category->image_path)) {
            Storage::disk('public')->delete($category->image_path);
        }
        $category->update(['image_path' => null]);

        return back()->with('success', 'Kategori görseli başarıyla kaldırıldı.');
    }

    public function destroy(Category $category)
    {
        if ($category->image_path && Storage::disk('public')->exists($category->image_path)) {
            Storage::disk('public')->delete($category->image_path);
        }
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori silindi.');
    }
}
