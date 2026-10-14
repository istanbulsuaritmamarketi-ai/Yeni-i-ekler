<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\AiBlogService;
use App\Services\ArticleScraperService;
use App\Services\ImageOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $posts = $query->latest()->paginate(15)->withQueryString();

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function scrapeUrl(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $scraped = ArticleScraperService::scrape($request->url);

        if (! $scraped['success']) {
            return response()->json([
                'success' => false,
                'message' => $scraped['message'] ?? 'Makale bilgileri çekilemedi.',
            ], 422);
        }

        $data = $scraped['data'];

        // Kapak görselini hemen indirip depolayalım
        if (! empty($data['image_url'])) {
            $localPath = ArticleScraperService::downloadAndSaveImage($data['image_url']);
            if ($localPath) {
                $data['local_image_path'] = $localPath;
                $data['local_image_asset'] = asset('storage/'.$localPath);
            }
        }

        // Yapay zeka ile kurumsal blog formatına uyarlama talep edilmişse
        if ($request->boolean('use_ai')) {
            $aiData = AiBlogService::generateOrRewritePost([
                'title' => $data['title'] ?? '',
                'excerpt' => $data['excerpt'] ?? '',
                'content' => $data['content'] ?? '',
            ], $request->input('custom_instruction'), 'all');

            $data['title'] = $aiData['title'] ?? $data['title'];
            $data['slug'] = $aiData['slug'] ?? $data['slug'];
            $data['excerpt'] = $aiData['excerpt'] ?? $data['excerpt'];
            $data['content'] = $aiData['content'] ?? $data['content'];
            $data['meta_title'] = $aiData['meta_title'] ?? ($data['meta_title'] ?? '');
            $data['meta_description'] = $aiData['meta_description'] ?? ($data['meta_description'] ?? '');
            $data['ai_notice'] = $aiData['notice'] ?? null;
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $request->boolean('use_ai')
                ? 'Makale bilgileri çekildi ve yapay zeka ile firmanıza özel yeniden yazıldı!'
                : 'Makale bilgileri ve görseli başarıyla çekildi!',
        ]);
    }

    /**
     * ✨ Blog Makalesini Yapay Zeka ile Sıfırdan Yazar veya Mevcut Alanı Geliştirir (AJAX)
     */
    public function aiGenerate(Request $request)
    {
        $request->validate([
            'topic' => 'nullable|string',
            'title' => 'nullable|string',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'custom_instruction' => 'nullable|string',
            'target_field' => 'nullable|string|in:all,title,excerpt,content,seo',
        ]);

        $targetField = $request->input('target_field', 'all');

        $aiData = AiBlogService::generateOrRewritePost([
            'topic' => $request->topic,
            'title' => $request->title,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
        ], $request->custom_instruction, $targetField);

        $messages = [
            'all' => 'Blog makalesi tüm başlık, içerik ve SEO detaylarıyla başarıyla hazırlandı!',
            'title' => 'Yapay zeka ile dikkat çekici ve SEO uyumlu makale başlığı üretildi!',
            'excerpt' => 'Kısa spot özet yapay zeka ile başarıyla oluşturuldu!',
            'content' => 'Detaylı makale içeriği zengin HTML formatında hazırlandı!',
            'seo' => 'SEO başlığı ve açıklaması başarıyla optimize edildi!',
        ];

        return response()->json([
            'success' => true,
            'target_field' => $targetField,
            'data' => $aiData,
            'message' => $messages[$targetField] ?? 'İçerik başarıyla üretildi!',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|mimes:jpeg,png,jpg,webp,gif,svg,jfif,avif,bmp|max:25600',
            'scraped_image_path' => 'nullable|string',
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
        ], [
            'image.mimes' => 'Blog görseli yalnızca JPG, PNG, WEBP, GIF, SVG veya AVIF formatında olmalıdır.',
            'image.max' => 'Blog görseli en fazla 25 MB olabilir.',
        ]);

        $validated['slug'] = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['is_published'] = $request->has('is_published');

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['image'] = ImageOptimizerService::optimizeAndStore($request->file('image'), 'posts', 1200, 800, 85);
        } elseif (! empty($validated['scraped_image_path'])) {
            $validated['image'] = $validated['scraped_image_path'];
        }

        unset($validated['scraped_image_path']);

        Post::create($validated);

        return redirect()->route('admin.posts.index')->with('success', 'Blog makalesi başarıyla oluşturuldu.');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug,'.$post->id,
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|mimes:jpeg,png,jpg,webp,gif,svg,jfif,avif,bmp|max:25600',
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
        ], [
            'image.mimes' => 'Blog görseli yalnızca JPG, PNG, WEBP, GIF, SVG veya AVIF formatında olmalıdır.',
            'image.max' => 'Blog görseli en fazla 25 MB olabilir.',
        ]);

        $validated['slug'] = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['is_published'] = $request->has('is_published');

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
            $validated['image'] = ImageOptimizerService::optimizeAndStore($request->file('image'), 'posts', 1200, 800, 85);
        }

        $post->update($validated);

        return redirect()->route('admin.posts.index')->with('success', 'Blog makalesi başarıyla güncellendi.');
    }

    public function destroy(Post $post)
    {
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Blog makalesi silindi.');
    }
}
