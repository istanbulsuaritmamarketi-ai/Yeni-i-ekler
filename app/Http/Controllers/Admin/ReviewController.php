<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Setting;
use App\Services\GoogleReviewsSyncService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with('product');

        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $reviews = $query->latest()->paginate(20)->withQueryString();
        $pendingCount = Review::where('is_approved', false)->count();
        $googleReviewsCount = Review::where('source', 'google')->count();

        $settings = Setting::whereIn('key', [
            'google_maps_url',
            'google_maps_place_id',
            'google_maps_api_key',
            'google_reviews_rating',
            'google_reviews_total_count',
        ])->pluck('value', 'key');

        return view('admin.reviews.index', compact('reviews', 'pendingCount', 'googleReviewsCount', 'settings'));
    }

    public function syncGoogle(Request $request, GoogleReviewsSyncService $syncService)
    {
        $request->validate([
            'google_maps_url' => 'nullable|string|max:1000',
            'google_maps_place_id' => 'nullable|string|max:255',
            'google_maps_api_key' => 'nullable|string|max:255',
        ]);

        $result = $syncService->sync(
            $request->google_maps_url,
            $request->google_maps_place_id,
            $request->google_maps_api_key
        );

        return back()->with('success', $result['message']);
    }

    public function storeGoogle(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:2000',
        ]);

        Review::create([
            'name' => $validated['name'],
            'location' => $validated['location'] ?: 'İstanbul',
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'source' => 'google',
            'is_approved' => true,
        ]);

        return back()->with('success', 'Yeni Google Haritalar yorumu başarıyla kaydedildi ve yayına alındı.');
    }

    public function toggleApproval(Review $review)
    {
        $review->update([
            'is_approved' => ! $review->is_approved,
        ]);

        $status = $review->is_approved ? 'onaylandı ve yayına alındı' : 'onayı kaldırıldı';

        return back()->with('success', "Yorum {$status}.");
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Yorum silindi.');
    }
}

