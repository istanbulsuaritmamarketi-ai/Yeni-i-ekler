<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\ImageOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('order')->orderBy('id')->get();

        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'position' => 'required|string|max:50',
            'image' => 'nullable|mimes:jpeg,png,jpg,webp,gif,svg,jfif,avif,bmp|max:25600',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'badge' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:255',
            'link_url' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
        ], [
            'image.mimes' => 'Banner görseli yalnızca JPG, PNG, WEBP, GIF, SVG veya AVIF formatında olmalıdır.',
            'image.max' => 'Banner görseli en fazla 25 MB olabilir.',
        ]);

        $data = $request->except('image');
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $data['image_path'] = ImageOptimizerService::optimizeAndStore($request->file('image'), 'banners', 1200, 1200, 90);
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner başarıyla eklendi.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'position' => 'required|string|max:50',
            'image' => 'nullable|mimes:jpeg,png,jpg,webp,gif,svg,jfif,avif,bmp|max:25600',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'badge' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:255',
            'link_url' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
        ], [
            'image.mimes' => 'Banner görseli yalnızca JPG, PNG, WEBP, GIF, SVG veya AVIF formatında olmalıdır.',
            'image.max' => 'Banner görseli en fazla 25 MB olabilir.',
        ]);

        $data = $request->except('image');
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $data['image_path'] = ImageOptimizerService::optimizeAndStore($request->file('image'), 'banners', 1200, 1200, 90);
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner başarıyla güncellendi.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
            Storage::disk('public')->delete($banner->image_path);
        }
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner silindi.');
    }
}
