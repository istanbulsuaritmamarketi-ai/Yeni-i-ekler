<x-frontend-layout 
    :title="$category->name . ' Çiçek Modelleri & Fiyatları — Tazecicekler.com'" 
    :description="$category->description ?: ($category->name . ' kategorisindeki en taze çiçek aranjmanları, butik buketler ve hediye setleri. Türkiye geneline aynı gün hızlı teslimat ve fotoğraflı onay garantisiyle.')">

    <!-- Kategori Başlığı (Sade & Hızlı) -->
    <div class="bg-rose-700 text-white py-5 sm:py-7 border-b border-rose-800">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-12">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs text-rose-200 mb-2 sm:mb-3 font-bold">
                <a href="{{ route('home') }}" class="hover:text-white transition">Ana Sayfa</a>
                <i class="fas fa-chevron-right text-[9px] opacity-70"></i>
                <a href="{{ route('categories.index') }}" class="hover:text-white transition">Kategoriler</a>
                <i class="fas fa-chevron-right text-[9px] opacity-70"></i>
                <span class="text-white">{{ $category->name }}</span>
            </nav>

            <div>
                <h1 class="text-xl sm:text-3xl font-black text-white tracking-tight leading-tight">
                    {{ $category->name }}
                </h1>
                @if($category->description)
                    <p class="mt-1 text-xs sm:text-sm text-rose-100 max-w-2xl leading-relaxed">
                        {{ $category->description }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Ürün Listesi Bölümü -->
    <div class="bg-slate-50 py-10 sm:py-14 min-h-screen">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-12">
            
            <!-- Üst Bar: Ürün Sayısı & Hızlı Geçiş -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-4 border-b border-rose-100">
                <div class="flex items-center gap-3">
                    <span class="text-xs sm:text-sm font-black text-slate-800">
                        {{ $products->total() }} Tasarım Listeleniyor
                    </span>
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                    <span class="text-xs text-slate-500 font-medium">Günlük Taze Çiçekler</span>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('products.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 hover:underline flex items-center gap-1">
                        <i class="fas fa-th"></i> Tüm Çiçekleri Gör
                    </a>
                </div>
            </div>

            <!-- Çiçek Ürün Izgarası -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 sm:gap-6">
                @forelse($products as $product)
                    <div class="bg-white rounded-3xl overflow-hidden border border-rose-100/80 shadow-2xs hover:shadow-xl transition-all duration-300 flex flex-col group relative">
                        
                        <!-- Rozetler -->
                        <div class="absolute top-3 left-3 z-10 flex flex-col gap-1.5 items-start">
                            @if($product->custom_badge)
                                <span class="bg-rose-600 text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow-xs uppercase">
                                    {{ $product->custom_badge }}
                                </span>
                            @elseif($product->is_featured)
                                <span class="bg-amber-500 text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow-xs flex items-center gap-1">
                                    <i class="fas fa-star text-[9px]"></i>
                                    <span>Popüler</span>
                                </span>
                            @endif

                            @if($product->discount_percent)
                                <span class="bg-emerald-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-xs">
                                    %{{ $product->discount_percent }} İndirim
                                </span>
                            @endif
                        </div>

                        <!-- Görsel -->
                        <a href="{{ route('product.show', $product->slug) }}" class="relative aspect-[4/5] bg-rose-50 overflow-hidden block">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                 loading="lazy"
                                 decoding="async"
                                 class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500">
                        </a>

                        <!-- Bilgi -->
                        <div class="p-3.5 sm:p-4 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] font-bold text-rose-500 uppercase tracking-wider block">
                                        {{ $category->name }}
                                    </span>
                                    <div class="flex text-amber-400 text-[10px]">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>

                                <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 line-clamp-2 group-hover:text-rose-600 transition-colors">
                                    <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                                </h3>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <div>
                                    @if($product->campaign_price && $product->campaign_price < $product->price)
                                        <div class="text-[10px] text-slate-400 line-through">₺{{ number_format($product->price, 0, ',', '.') }}</div>
                                        <div class="text-base sm:text-lg font-black text-rose-600 leading-tight">₺{{ number_format($product->campaign_price, 0, ',', '.') }}</div>
                                    @else
                                        <div class="text-base sm:text-lg font-black text-slate-900 leading-tight">₺{{ number_format($product->price, 0, ',', '.') }}</div>
                                    @endif
                                </div>

                                <a href="{{ route('product.show', $product->slug) }}" 
                                   class="px-3 py-2 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white rounded-xl text-xs font-black transition-all flex items-center gap-1 shadow-xs">
                                    <span>Gönder</span>
                                    <i class="fas fa-chevron-right text-[9px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-rose-100 max-w-md mx-auto my-8">
                        <div class="w-16 h-16 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center text-3xl mx-auto mb-3">
                            <i class="fas fa-spa"></i>
                        </div>
                        <h3 class="text-base font-black text-slate-900">Bu Kategoride Çiçek Bulunmuyor</h3>
                        <p class="text-xs text-slate-500 mt-1 mb-4">Şu anda bu kategoride ürün kalmadı, diğer özel tasarımlarımıza göz atabilirsiniz.</p>
                        <a href="{{ route('products.index') }}" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl inline-block shadow transition">
                            Tüm Çiçekleri Göster
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Sayfalama (Pagination) -->
            <div class="mt-10">
                {{ $products->links() }}
            </div>

        </div>
    </div>
</x-frontend-layout>
