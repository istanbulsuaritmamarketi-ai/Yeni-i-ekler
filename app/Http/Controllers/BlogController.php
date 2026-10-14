<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::where('is_published', true);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('excerpt', 'like', '%'.$request->search.'%')
                    ->orWhere('content', 'like', '%'.$request->search.'%');
            });
        }

        $posts = $query->latest()->paginate(9)->withQueryString();
        $recentPosts = Post::where('is_published', true)->latest()->take(5)->get();

        return view('blog.index', compact('posts', 'recentPosts'));
    }

    public function show(string $slug)
    {
        $post = Post::where('slug', $slug)->where('is_published', true)->firstOrFail();

        // Increase view count
        $post->increment('views_count');

        $recentPosts = Post::where('is_published', true)
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(4)
            ->get();

        $featuredProducts = Product::where('is_active', true)->latest()->take(3)->get();

        return view('blog.show', compact('post', 'recentPosts', 'featuredProducts'));
    }
}
