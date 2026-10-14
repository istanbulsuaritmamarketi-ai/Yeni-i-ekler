<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BouquetPackage;
use App\Models\BouquetRibbon;
use App\Models\BouquetSize;
use App\Models\BouquetStem;
use App\Models\Setting;
use App\Services\ImageOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BouquetItemController extends Controller
{
    /**
     * Buket Malzemeleri Yönetim Ana Ekranı
     */
    public function index()
    {
        $stems = BouquetStem::orderBy('sort_order')->orderBy('id')->get();
        $packages = BouquetPackage::orderBy('sort_order')->orderBy('id')->get();
        $ribbons = BouquetRibbon::orderBy('sort_order')->orderBy('id')->get();
        $sizes = BouquetSize::orderBy('sort_order')->orderBy('id')->get();

        $minStems = (int) Setting::get('custom_bouquet_min_stems', 7);
        $isEnabled = Setting::get('custom_bouquet_enabled', '1') !== '0';

        return view('admin.bouquet-items.index', compact('stems', 'packages', 'ribbons', 'sizes', 'minStems', 'isEnabled'));
    }

    // ==========================================
    // 🌸 1. TEKLİ DAL ÇİÇEKLER (STEMS)
    // ==========================================

    public function storeStem(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'meaning' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'icon' => 'nullable|string|max:10',
            'color_hex' => 'nullable|string|max:25',
            'sort_order' => 'nullable|integer',
            'image' => 'nullable|image|max:10240',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = ImageOptimizerService::optimizeAndStore($request->file('image'), 'bouquet_items/stems', 800, 800, 85);
        }

        BouquetStem::create([
            'name' => $validated['name'],
            'meaning' => $validated['meaning'] ?? null,
            'price' => $validated['price'],
            'icon' => $validated['icon'] ?: '🌸',
            'color_hex' => $validated['color_hex'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'image_path' => $imagePath,
            'is_active' => true,
        ]);

        return redirect()->route('admin.bouquet-items.index', ['tab' => 'stems'])
            ->with('success', '🌸 Yeni tekli dal çiçek başarıyla eklendi.');
    }

    public function updateStem(Request $request, BouquetStem $stem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'meaning' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'icon' => 'nullable|string|max:10',
            'color_hex' => 'nullable|string|max:25',
            'sort_order' => 'nullable|integer',
            'image' => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('image')) {
            if ($stem->image_path && Storage::disk('public')->exists($stem->image_path)) {
                Storage::disk('public')->delete($stem->image_path);
            }
            $stem->image_path = ImageOptimizerService::optimizeAndStore($request->file('image'), 'bouquet_items/stems', 800, 800, 85);
        }

        $stem->update([
            'name' => $validated['name'],
            'meaning' => $validated['meaning'] ?? null,
            'price' => $validated['price'],
            'icon' => $validated['icon'] ?: '🌸',
            'color_hex' => $validated['color_hex'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'image_path' => $stem->image_path,
        ]);

        return redirect()->route('admin.bouquet-items.index', ['tab' => 'stems'])
            ->with('success', '🌸 Çiçek bilgileri güncellendi.');
    }

    public function destroyStem(BouquetStem $stem)
    {
        if ($stem->image_path && Storage::disk('public')->exists($stem->image_path)) {
            Storage::disk('public')->delete($stem->image_path);
        }
        $stem->delete();

        return redirect()->route('admin.bouquet-items.index', ['tab' => 'stems'])
            ->with('success', 'Çiçek listeden silindi.');
    }

    public function toggleStem(BouquetStem $stem)
    {
        $stem->is_active = ! $stem->is_active;
        $stem->save();

        return response()->json([
            'success' => true,
            'is_active' => $stem->is_active,
            'message' => $stem->is_active ? 'Çiçek aktif edildi.' : 'Çiçek pasife alındı (stok kapalı).',
        ]);
    }

    // ==========================================
    // 🎁 2. AMBALAJ & PAKET KAĞITLARI (PACKAGES)
    // ==========================================

    public function storePackage(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'color_hex' => 'nullable|string|max:25',
            'sort_order' => 'nullable|integer',
            'image' => 'nullable|image|max:10240',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = ImageOptimizerService::optimizeAndStore($request->file('image'), 'bouquet_items/packages', 800, 800, 85);
        }

        BouquetPackage::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'] ?? 0,
            'color_hex' => $validated['color_hex'] ?? '#fbcfe8',
            'sort_order' => $validated['sort_order'] ?? 0,
            'image_path' => $imagePath,
            'is_active' => true,
        ]);

        return redirect()->route('admin.bouquet-items.index', ['tab' => 'packages'])
            ->with('success', '🎁 Yeni ambalaj kağıdı başarıyla eklendi.');
    }

    public function updatePackage(Request $request, BouquetPackage $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'color_hex' => 'nullable|string|max:25',
            'sort_order' => 'nullable|integer',
            'image' => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('image')) {
            if ($package->image_path && Storage::disk('public')->exists($package->image_path)) {
                Storage::disk('public')->delete($package->image_path);
            }
            $package->image_path = ImageOptimizerService::optimizeAndStore($request->file('image'), 'bouquet_items/packages', 800, 800, 85);
        }

        $package->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'] ?? 0,
            'color_hex' => $validated['color_hex'] ?? '#fbcfe8',
            'sort_order' => $validated['sort_order'] ?? 0,
            'image_path' => $package->image_path,
        ]);

        return redirect()->route('admin.bouquet-items.index', ['tab' => 'packages'])
            ->with('success', '🎁 Ambalaj kağıdı güncellendi.');
    }

    public function destroyPackage(BouquetPackage $package)
    {
        if ($package->image_path && Storage::disk('public')->exists($package->image_path)) {
            Storage::disk('public')->delete($package->image_path);
        }
        $package->delete();

        return redirect()->route('admin.bouquet-items.index', ['tab' => 'packages'])
            ->with('success', 'Ambalaj seçeneği silindi.');
    }

    public function togglePackage(BouquetPackage $package)
    {
        $package->is_active = ! $package->is_active;
        $package->save();

        return response()->json([
            'success' => true,
            'is_active' => $package->is_active,
            'message' => $package->is_active ? 'Ambalaj aktif edildi.' : 'Ambalaj pasife alındı.',
        ]);
    }

    // ==========================================
    // 🎀 3. SÜSLEME KURDELELERİ (RIBBONS)
    // ==========================================

    public function storeRibbon(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'color_hex' => 'nullable|string|max:25',
            'sort_order' => 'nullable|integer',
            'image' => 'nullable|image|max:10240',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = ImageOptimizerService::optimizeAndStore($request->file('image'), 'bouquet_items/ribbons', 800, 800, 85);
        }

        BouquetRibbon::create([
            'name' => $validated['name'],
            'price' => $validated['price'] ?? 0,
            'color_hex' => $validated['color_hex'] ?? '#e11d48',
            'sort_order' => $validated['sort_order'] ?? 0,
            'image_path' => $imagePath,
            'is_active' => true,
        ]);

        return redirect()->route('admin.bouquet-items.index', ['tab' => 'ribbons'])
            ->with('success', '🎀 Yeni kurdele seçeneği başarıyla eklendi.');
    }

    public function updateRibbon(Request $request, BouquetRibbon $ribbon)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'color_hex' => 'nullable|string|max:25',
            'sort_order' => 'nullable|integer',
            'image' => 'nullable|image|max:10240',
        ]);

        if ($request->hasFile('image')) {
            if ($ribbon->image_path && Storage::disk('public')->exists($ribbon->image_path)) {
                Storage::disk('public')->delete($ribbon->image_path);
            }
            $ribbon->image_path = ImageOptimizerService::optimizeAndStore($request->file('image'), 'bouquet_items/ribbons', 800, 800, 85);
        }

        $ribbon->update([
            'name' => $validated['name'],
            'price' => $validated['price'] ?? 0,
            'color_hex' => $validated['color_hex'] ?? '#e11d48',
            'sort_order' => $validated['sort_order'] ?? 0,
            'image_path' => $ribbon->image_path,
        ]);

        return redirect()->route('admin.bouquet-items.index', ['tab' => 'ribbons'])
            ->with('success', '🎀 Kurdele güncellendi.');
    }

    public function destroyRibbon(BouquetRibbon $ribbon)
    {
        if ($ribbon->image_path && Storage::disk('public')->exists($ribbon->image_path)) {
            Storage::disk('public')->delete($ribbon->image_path);
        }
        $ribbon->delete();

        return redirect()->route('admin.bouquet-items.index', ['tab' => 'ribbons'])
            ->with('success', 'Kurdele silindi.');
    }

    public function toggleRibbon(BouquetRibbon $ribbon)
    {
        $ribbon->is_active = ! $ribbon->is_active;
        $ribbon->save();

        return response()->json([
            'success' => true,
            'is_active' => $ribbon->is_active,
            'message' => $ribbon->is_active ? 'Kurdele aktif edildi.' : 'Kurdele pasife alındı.',
        ]);
    }

    // ==========================================
    // 💐 4. BUKET BOYUTLARI (SIZES)
    // ==========================================

    public function storeSize(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'badge' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'icon' => 'nullable|string|max:10',
            'sort_order' => 'nullable|integer',
            'image' => 'nullable|image|max:10240',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = ImageOptimizerService::optimizeAndStore($request->file('image'), 'bouquet_items/sizes', 800, 800, 85);
        }

        BouquetSize::create([
            'name' => $validated['name'],
            'badge' => $validated['badge'] ?? null,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'icon' => $validated['icon'] ?: '💐',
            'sort_order' => $validated['sort_order'] ?? 0,
            'image_path' => $imagePath,
            'is_active' => true,
        ]);

        return redirect()->route('admin.bouquet-items.index', ['tab' => 'sizes'])
            ->with('success', '💐 Yeni buket boyutu başarıyla eklendi.');
    }

    public function updateSize(Request $request, BouquetSize $size)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'badge' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'icon' => 'nullable|string|max:10',
            'sort_order' => 'nullable|integer',
            'image' => 'nullable|image|max:10240',
        ]);

        $imagePath = $size->image_path;
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = ImageOptimizerService::optimizeAndStore($request->file('image'), 'bouquet_items/sizes', 800, 800, 85);
        }

        $size->update([
            'name' => $validated['name'],
            'badge' => $validated['badge'] ?? null,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'icon' => $validated['icon'] ?: '💐',
            'sort_order' => $validated['sort_order'] ?? 0,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('admin.bouquet-items.index', ['tab' => 'sizes'])
            ->with('success', '💐 Buket boyutu güncellendi.');
    }

    public function destroySize(BouquetSize $size)
    {
        if ($size->image_path && Storage::disk('public')->exists($size->image_path)) {
            Storage::disk('public')->delete($size->image_path);
        }
        $size->delete();

        return redirect()->route('admin.bouquet-items.index', ['tab' => 'sizes'])
            ->with('success', 'Buket boyutu silindi.');
    }

    public function toggleSize(BouquetSize $size)
    {
        $size->is_active = ! $size->is_active;
        $size->save();

        return response()->json([
            'success' => true,
            'is_active' => $size->is_active,
            'message' => $size->is_active ? 'Buket boyutu aktif edildi.' : 'Buket boyutu pasife alındı.',
        ]);
    }

    // ==========================================
    // ⚙️ 5. GENEL ATÖLYE AYARLARI
    // ==========================================

    public function saveSettings(Request $request)
    {
        $minStems = max(1, (int) $request->input('custom_bouquet_min_stems', 7));
        $enabled = $request->boolean('custom_bouquet_enabled') ? '1' : '0';

        Setting::updateOrCreate(['key' => 'custom_bouquet_min_stems'], ['value' => $minStems]);
        Setting::updateOrCreate(['key' => 'custom_bouquet_enabled'], ['value' => $enabled]);

        return redirect()->route('admin.bouquet-items.index', ['tab' => 'settings'])
            ->with('success', '⚙️ Buket atölyesi kuralları güncellendi.');
    }
}
