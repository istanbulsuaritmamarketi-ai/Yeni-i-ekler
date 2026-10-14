<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;

class SitemapController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)->latest()->get();
        $categories = Category::where('is_active', true)->get();
        $posts = Post::where('is_published', true)->latest()->get();
        $pages = Page::where('is_active', true)->get();
        $districts = DistrictController::getActiveDistricts();

        $content = view('sitemap', compact('products', 'categories', 'posts', 'pages', 'districts'))->render();

        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }

    public function robots()
    {
        $sitemapUrl = url('/sitemap.xml');
        $robots = "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /dashboard\nDisallow: /profile\n\nSitemap: {$sitemapUrl}\n";

        return response($robots, 200)
            ->header('Content-Type', 'text/plain');
    }
}
