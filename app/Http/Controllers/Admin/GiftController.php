<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gift;
use App\Services\ImageOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gifts = Gift::orderBy('sort_order', 'asc')->latest()->paginate(15);
        $totalGifts = Gift::count();
        $activeGifts = Gift::where('is_active', true)->count();

        return view('admin.gifts.index', compact('gifts', 'totalGifts', 'activeGifts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.gifts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'badge' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'external_url' => 'nullable|url|max:1000',
            'market_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|mimes:jpeg,png,jpg,webp,gif,svg,jfif,avif,bmp|max:25600',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ], [
            'image.mimes' => 'Hediye görseli yalnızca JPG, PNG, WEBP, GIF, SVG veya AVIF formatında olmalıdır.',
            'image.max' => 'Hediye görseli en fazla 25 MB olabilir.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = ImageOptimizerService::optimizeAndStore($request->file('image'), 'gifts', 600, 600, 88);
        }

        Gift::create([
            'name' => $validated['name'],
            'badge' => $validated['badge'] ?? null,
            'description' => $validated['description'] ?? null,
            'external_url' => $validated['external_url'] ?? null,
            'market_price' => $validated['market_price'] ?? null,
            'image_path' => $imagePath,
            'icon' => $validated['icon'] ?: 'fas fa-gift',
            'color' => $validated['color'] ?: 'purple',
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
        ]);

        return redirect()->route('admin.gifts.index')->with('success', 'Hediye seçeneği başarıyla oluşturuldu.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gift $gift)
    {
        return view('admin.gifts.edit', compact('gift'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gift $gift)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'badge' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'external_url' => 'nullable|url|max:1000',
            'market_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|mimes:jpeg,png,jpg,webp,gif,svg,jfif,avif,bmp|max:25600',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ], [
            'image.mimes' => 'Hediye görseli yalnızca JPG, PNG, WEBP, GIF, SVG veya AVIF formatında olmalıdır.',
            'image.max' => 'Hediye görseli en fazla 25 MB olabilir.',
        ]);

        $imagePath = $gift->image_path;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($gift->image_path && Storage::disk('public')->exists($gift->image_path)) {
                Storage::disk('public')->delete($gift->image_path);
            }
            $imagePath = ImageOptimizerService::optimizeAndStore($request->file('image'), 'gifts', 600, 600, 88);
        }

        $gift->update([
            'name' => $validated['name'],
            'badge' => $validated['badge'] ?? null,
            'description' => $validated['description'] ?? null,
            'external_url' => $validated['external_url'] ?? null,
            'market_price' => $validated['market_price'] ?? null,
            'image_path' => $imagePath,
            'icon' => $validated['icon'] ?: 'fas fa-gift',
            'color' => $validated['color'] ?: 'purple',
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : false,
        ]);

        return redirect()->route('admin.gifts.index')->with('success', 'Hediye seçeneği başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gift $gift)
    {
        if ($gift->image_path && Storage::disk('public')->exists($gift->image_path)) {
            Storage::disk('public')->delete($gift->image_path);
        }

        $gift->delete();

        return redirect()->route('admin.gifts.index')->with('success', 'Hediye seçeneği başarıyla silindi.');
    }

    /**
     * Hızlı Aktif/Pasif Durumu Değiştirme
     */
    public function toggle(Gift $gift)
    {
        $gift->is_active = ! $gift->is_active;
        $gift->save();

        return redirect()->back()->with('success', 'Hediye durumu güncellendi: '.($gift->is_active ? 'Aktif' : 'Pasif'));
    }
}
