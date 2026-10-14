<x-frontend-layout 
    :title="$post->title . ' - Su Arıtma Blog & Rehber'" 
    :description="$post->meta_description ?: Str::limit(strip_tags($post->content), 155)"
    :image="$post->image_path ? asset('storage/' . $post->image_path) : asset('images/logo.png')"
    ogType="article">
    <!-- Google Schema (BlogPosting JSON-LD) -->
    @php
        $blogSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'image' => $post->image_path ? asset('storage/' . $post->image_path) : asset('images/logo.png'),
            'datePublished' => $post->created_at->toIso8601String(),
            'dateModified' => $post->updated_at->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => config('app.name', 'İstanbul Su Arıtma Marketi')
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name', 'İstanbul Su Arıtma Marketi')
            ],
            'description' => $post->meta_description ?: Str::limit(strip_tags($post->content), 160)
        ];
    @endphp
    <script type="application/ld+json">
    {!! json_encode($blogSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    <!-- Breadcrumb -->
    <div class="bg-gray-100 py-3 border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-xs sm:text-sm text-gray-500 font-medium overflow-x-auto whitespace-nowrap">
                <a href="{{ route('home') }}" class="hover:text-blue-600">Ana Sayfa</a>
                <span class="mx-2">/</span>
                <a href="{{ route('blog.index') }}" class="hover:text-blue-600">Bilgi Rehberi</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900 truncate">{{ $post->title }}</span>
            </nav>
        </div>
    </div>

    <!-- Makale İçeriği -->
    <div class="bg-white py-10 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

                <!-- Sol Ana Makale -->
                <main class="lg:col-span-8">
                    <article>
                        <!-- Başlık & Meta -->
                        <header class="mb-8">
                            <div class="flex items-center gap-3 text-xs sm:text-sm text-gray-500 mb-3">
                                <span class="bg-blue-50 text-blue-700 font-semibold px-2.5 py-0.5 rounded-full border border-blue-100">
                                    <i class="fas fa-tag mr-1 text-xs"></i> Su Arıtma Rehberi
                                </span>
                                <span><i class="far fa-calendar-alt mr-1"></i> {{ $post->created_at->translatedFormat('d F Y') }}</span>
                                <span><i class="far fa-clock mr-1"></i> {{ $post->estimated_reading_time }} dk okuma</span>
                                <span><i class="far fa-eye mr-1"></i> {{ $post->views_count }} görüntülenme</span>
                            </div>

                            <h1 class="text-2xl sm:text-4xl font-extrabold text-gray-900 leading-tight mb-4">
                                {{ $post->title }}
                            </h1>

                            @if($post->excerpt)
                                <p class="text-base sm:text-lg text-gray-600 leading-relaxed font-medium bg-gray-50 p-4 rounded-xl border-l-4 border-blue-600">
                                    {{ $post->excerpt }}
                                </p>
                            @endif
                        </header>

                        <!-- Kapak Görseli -->
                        @if($post->image)
                            <div class="mb-8 rounded-2xl overflow-hidden shadow-sm aspect-[16/9] bg-gray-100">
                                <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                            </div>
                        @endif

                        <!-- İçerik -->
                        <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed space-y-4">
                            {!! $post->content !!}
                        </div>

                        <!-- Sosyal Paylaşım & CTA -->
                        <div class="mt-12 pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50 p-6 rounded-2xl">
                            <div>
                                <h4 class="font-bold text-gray-900 text-sm">Bu rehberi paylaşın:</h4>
                                <p class="text-xs text-gray-500">Arkadaşlarınızın da sağlıklı suya ulaşmasına yardımcı olun.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' - ' . url()->current()) }}" target="_blank" class="w-10 h-10 rounded-full bg-green-500 hover:bg-green-600 text-white flex items-center justify-center transition-colors">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="w-10 h-10 rounded-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center transition-colors">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" target="_blank" class="w-10 h-10 rounded-full bg-sky-500 hover:bg-sky-600 text-white flex items-center justify-center transition-colors">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Hızlı Servis / Destek Kutusu -->
                        <div class="mt-8 bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-6 sm:p-8 rounded-2xl shadow-md flex flex-col sm:flex-row items-center justify-between gap-6">
                            <div>
                                <h3 class="text-xl font-bold mb-1">Cihazınız İçin Bakım veya Filtre Mi Lazım?</h3>
                                <p class="text-blue-100 text-sm">İstanbul geneli dilediğiniz gün ve saatte randevulu servis ve orijinal filtre değişimi için hemen randevu alın.</p>
                            </div>
                            <div class="flex gap-3 whitespace-nowrap">
                                <a href="{{ route('service.create') }}" class="px-5 py-2.5 bg-white text-blue-700 font-bold text-sm rounded-xl hover:bg-blue-50 transition-colors shadow">
                                    Servis Talebi Aç
                                </a>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSettings['whatsapp'] ?? '905555555555') }}" target="_blank" class="px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white font-bold text-sm rounded-xl transition-colors shadow">
                                    <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                                </a>
                            </div>
                        </div>
                    </article>
                </main>

                <!-- Sağ Kenar Çubuğu (Sidebar) -->
                <aside class="lg:col-span-4 space-y-8">
                    <!-- Blog Arama Kutusu -->
                    <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 shadow-2xs">
                        <h3 class="font-black text-gray-900 text-xs uppercase tracking-wider mb-3 flex items-center gap-2">
                            <i class="fas fa-search text-blue-600"></i> Rehberde Ara
                        </h3>
                        <form action="{{ route('blog.index') }}" method="GET" class="relative">
                            <input type="text" name="search" placeholder="Filtre, makale veya konu ara..."
                                class="w-full text-xs py-2.5 pl-3.5 pr-10 border border-gray-200 rounded-xl bg-white focus:ring-blue-500 focus:border-blue-500 font-medium">
                            <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 w-7 h-7 bg-blue-600 text-white rounded-lg flex items-center justify-center hover:bg-blue-700 transition-colors cursor-pointer">
                                <i class="fas fa-search text-[10px]"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Öne Çıkan Ürünler -->
                    @if(isset($featuredProducts) && $featuredProducts->count() > 0)
                        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                            <h3 class="font-extrabold text-gray-900 text-base mb-4 flex items-center gap-2">
                                <i class="fas fa-star text-amber-500"></i> Popüler Su Arıtma Cihazları
                            </h3>
                            <div class="space-y-4">
                                @foreach($featuredProducts as $item)
                                    <div class="flex gap-3 bg-white p-3 rounded-xl border border-gray-100 hover:border-blue-200 transition-all group">
                                        <a href="{{ route('product.show', $item->slug) }}" class="w-16 h-16 rounded-lg bg-gray-50 flex-shrink-0 overflow-hidden flex items-center justify-center">
                                            @if($item->image_path)
                                                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                            @else
                                                <i class="fas fa-tint text-blue-300 text-xl"></i>
                                            @endif
                                        </a>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-xs font-bold text-gray-900 truncate group-hover:text-blue-600">
                                                <a href="{{ route('product.show', $item->slug) }}">{{ $item->name }}</a>
                                            </h4>
                                            <div class="mt-1 text-sm font-extrabold text-blue-600">
                                                {{ number_format($item->price, 0, ',', '.') }} TL
                                            </div>
                                            <a href="{{ route('product.show', $item->slug) }}" class="text-[11px] text-gray-500 hover:text-blue-600 font-semibold inline-flex items-center gap-1 mt-1">
                                                İncele <i class="fas fa-chevron-right text-[9px]"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Diğer Yazılar -->
                    @if(isset($recentPosts) && $recentPosts->count() > 0)
                        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                            <h3 class="font-extrabold text-gray-900 text-base mb-4 flex items-center gap-2">
                                <i class="fas fa-book-reader text-blue-600"></i> Diğer Rehber Yazıları
                            </h3>
                            <ul class="divide-y divide-gray-100">
                                @foreach($recentPosts as $rPost)
                                    <li class="py-3 first:pt-0 last:pb-0">
                                        <a href="{{ route('blog.show', $rPost->slug) }}" class="group block">
                                            <h4 class="text-sm font-semibold text-gray-800 group-hover:text-blue-600 transition-colors leading-snug">
                                                {{ $rPost->title }}
                                            </h4>
                                            <div class="text-[11px] text-gray-400 mt-1 flex items-center gap-2">
                                                <span>{{ $rPost->created_at->translatedFormat('d M Y') }}</span>
                                                <span>•</span>
                                                <span>{{ $rPost->estimated_reading_time }} dk</span>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </aside>

            </div>
        </div>
    </div>
</x-frontend-layout>
