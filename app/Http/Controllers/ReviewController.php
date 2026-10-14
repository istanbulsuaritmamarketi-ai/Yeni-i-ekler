<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:150',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:1000',
        ], [
            'name.required' => 'Lütfen adınızı ve soyadınızı giriniz.',
            'rating.required' => 'Lütfen 1 ile 5 arasında bir puan seçiniz.',
            'comment.required' => 'Lütfen yorumunuzu yazınız.',
            'comment.min' => 'Yorumunuz en az 5 karakter olmalıdır.',
        ]);

        $validated['product_id'] = $product->id;
        $validated['ip_address'] = $request->ip();
        $validated['is_approved'] = false; // Requires admin moderation

        Review::create($validated);

        return back()->with('review_success', 'Değerlendirmeniz ve yorumunuz başarıyla alındı! Yönetici onayının ardından ürün sayfasında yayınlanacaktır.');
    }
}
