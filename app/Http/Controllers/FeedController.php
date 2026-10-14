<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    /**
     * Google Merchant Center (Google Alışveriş) XML Ürün Feed'i
     */
    public function googleMerchant(): Response
    {
        $products = Product::where('is_active', true)
            ->with(['category', 'images'])
            ->orderBy('id', 'desc')
            ->get();

        $settings = Setting::all()->pluck('value', 'key')->toArray();

        $xml = view('feeds.google-merchant', compact('products', 'settings'))->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
