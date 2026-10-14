<x-frontend-layout 
    title="Tüm Çiçekler &amp; Özel Hediye Buketleri — Tazecicekler.com" 
    description="Gül buketleri, kraliyet orkideleri, saksı çiçekleri, açılış çelenkleri ve hediyeler. Türkiye geneli aynı gün hızlı teslimat.">
    
    <div class="bg-slate-50 min-h-screen py-8 sm:py-12">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-12">

            <!-- Başlık & Arama Bilgisi -->
            <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4 pb-6 border-b border-rose-100">
                <div>
                    <span class="text-xs font-black uppercase text-rose-600 tracking-wider">Taze Çiçek Kataloğu</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">
                        @if(request('search'))
                            "<span class="text-rose-600">{{ request('search') }}</span>" İçin Çiçekler
                        @elseif(request('category'))
                            {{ \App\Models\Category::where('slug', request('category'))->value('name') ?? 'Çiçekler' }}
                        @elseif(request('badge'))
                            {{ request('badge') }} Çiçekler
                        @else
                            Tüm Çiçek &amp; Hediye Koleksiyonu
                        @endif
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">Toplam {{ $products->total() }} taze tasarım listeleniyor</p>
                </div>

                <!-- Hızlı Sıralama Seçici -->
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-400 font-bold whitespace-nowrap">Sırala:</span>
                    <form action="{{ route('products.index') }}" method="GET" class="flex gap-2">
                        @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                        @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                        @if(request('badge'))<input type="hidden" name="badge" value="{{ request('badge') }}">@endif
                        <select name="sort" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 focus:outline-none focus:border-rose-500">
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>En Çok Satanlar</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>En Yeniler</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Fiyat (Artan)</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Fiyat (Azalan)</option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- SOL FİLTRE PANELİ -->
                <aside class="lg:col-span-3 space-y-6">
                    <form action="{{ route('products.index') }}" method="GET" class="space-y-6">
                        @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif

                        <!-- Arama -->
                        <div class="bg-white rounded-3xl p-5 border border-rose-100 shadow-2xs">
                            <h3 class="text-xs font-black uppercase text-slate-700 tracking-wider mb-3">Çiçek Ara</h3>
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}"
                                       placeholder="Gül, orkide, papatya..."
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-3 pr-8 py-2 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500">
                                <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-rose-500">
                                    <i class="fas fa-search text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Kategoriler -->
                        <div class="bg-white rounded-3xl p-5 border border-rose-100 shadow-2xs">
                            <h3 class="text-xs font-black uppercase text-slate-700 tracking-wider mb-3">Kategoriler</h3>
                            <ul class="space-y-1.5 text-xs">
                                <li>
                                    <a href="{{ route('products.index', array_merge(request()->except('category'), [])) }}"
                                       class="flex items-center justify-between p-2 rounded-xl font-bold transition {{ !request('category') ? 'bg-rose-50 text-rose-600' : 'text-slate-600 hover:bg-slate-50' }}">
                                        <span>Tüm Çiçekler</span>
                                        <span class="text-[10px] bg-slate-100 px-2 py-0.5 rounded-full">{{ \App\Models\Product::where('is_active', true)->count() }}</span>
                                    </a>
                                </li>
                                @foreach($categories as $cat)
                                    <li>
                                        <a href="{{ route('products.index', array_merge(request()->except('category'), ['category' => $cat->slug])) }}"
                                           class="flex items-center justify-between p-2 rounded-xl font-bold transition {{ request('category') == $cat->slug ? 'bg-rose-50 text-rose-600' : 'text-slate-600 hover:bg-slate-50' }}">
                                            <span>{{ $cat->name }}</span>
                                            <span class="text-[10px] bg-slate-100 px-2 py-0.5 rounded-full">{{ $cat->products()->where('is_active', true)->count() }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Hızlı Rozet Filtreleri -->
                        <div class="bg-white rounded-3xl p-5 border border-rose-100 shadow-2xs">
                            <h3 class="text-xs font-black uppercase text-slate-700 tracking-wider mb-3">Öne Çıkanlar</h3>
                            <div class="space-y-2 text-xs">
                                <a href="{{ route('products.index', ['badge' => 'AYNI GÜN']) }}" 
                                   class="block p-2 rounded-xl font-bold transition {{ request('badge') == 'AYNI GÜN' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                                    <i class="fas fa-bolt text-amber-500 mr-1.5"></i> Aynı Gün Teslimat
                                </a>
                                <a href="{{ route('products.index', ['badge' => 'FOTOĞRAFLI']) }}" 
                                   class="block p-2 rounded-xl font-bold transition {{ request('badge') == 'FOTOĞRAFLI' ? 'bg-rose-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                    <i class="fas fa-camera text-emerald-600 mr-1.5"></i> Fotoğraflı Onay
                                </a>
                                <a href="{{ route('products.index', ['badge' => 'ÇOK SATAN']) }}" 
                                   class="block p-2 rounded-xl font-bold transition {{ request('badge') == 'ÇOK SATAN' ? 'bg-rose-600 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                                    <i class="fas fa-fire text-amber-500 mr-1.5"></i> Çok Satanlar
                                </a>
                            </div>
                        </div>

                        <!-- Temizle Butonu -->
                        @if(request('search') || request('category') || request('badge') || request('sort'))
                            <a href="{{ route('products.index') }}" 
                               class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-extrabold rounded-xl transition flex items-center justify-center gap-1.5">
                                <i class="fas fa-times"></i> Filtreleri Temizle
                            </a>
                        @endif
                    </form>
                </aside>

                <!-- SAĞ ÜRÜN IZGARASI -->
                <main class="lg:col-span-9 space-y-8">
                    @if($products->isEmpty())
                        <div class="bg-white rounded-3xl p-12 text-center border border-rose-100 max-w-md mx-auto">
                            <div class="w-16 h-16 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center text-3xl mx-auto mb-3">
                                <i class="fas fa-search"></i>
                            </div>
                            <h3 class="text-base font-black text-slate-900">Aradığınız Kriterde Çiçek Bulunamadı</h3>
                            <p class="text-xs text-slate-500 mt-1 mb-4">Lütfen farklı bir arama terimi deneyin veya filtreleri temizleyin.</p>
                            <a href="{{ route('products.index') }}" class="px-5 py-2.5 bg-rose-600 text-white text-xs font-bold rounded-xl inline-block shadow">
                                Tüm Çiçekleri Göster
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5 sm:gap-6">
                            @foreach($products as $prod)
                                <div class="bg-white rounded-3xl overflow-hidden border border-rose-100/80 shadow-2xs hover:shadow-xl transition-all duration-300 flex flex-col group relative">
                                    
                                    <!-- Rozetler -->
                                    <div class="absolute top-3 left-3 z-10 flex flex-col gap-1.5 items-start">
                                        @if($prod->custom_badge)
                                            <span class="bg-rose-600 text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow-xs uppercase">
                                                {{ $prod->custom_badge }}
                                            </span>
                                        @endif
                                        @if($prod->discount_percent)
                                            <span class="bg-emerald-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-xs">
                                                %{{ $prod->discount_percent }} İndirim
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Görsel -->
                                    <a href="{{ route('product.show', $prod->slug) }}" class="relative aspect-[4/5] bg-rose-50 overflow-hidden block">
                                        <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" 
                                             class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500">
                                    </a>

                                    <!-- Bilgi -->
                                    <div class="p-3.5 sm:p-4 flex-1 flex flex-col justify-between">
                                        <div>
                                            <span class="text-[10px] font-bold text-rose-500 uppercase tracking-wider block">
                                                {{ $prod->category->name ?? 'Taze Çiçek' }}
                                            </span>
                                            <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 mt-1 line-clamp-2 group-hover:text-rose-600 transition-colors">
                                                <a href="{{ route('product.show', $prod->slug) }}">{{ $prod->name }}</a>
                                            </h3>
                                        </div>

                                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                            <div>
                                                @if($prod->campaign_price)
                                                    <div class="text-[10px] text-slate-400 line-through">₺{{ number_format($prod->price, 0, ',', '.') }}</div>
                                                    <div class="text-base sm:text-lg font-black text-rose-600 leading-tight">₺{{ number_format($prod->campaign_price, 0, ',', '.') }}</div>
                                                @else
                                                    <div class="text-base sm:text-lg font-black text-slate-900 leading-tight">₺{{ number_format($prod->price, 0, ',', '.') }}</div>
                                                @endif
                                            </div>

                                            <a href="{{ route('product.show', $prod->slug) }}" 
                                               class="px-3 py-2 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white rounded-xl text-xs font-black transition-all flex items-center gap-1">
                                                <span>Gönder</span>
                                                <i class="fas fa-chevron-right text-[9px]"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Sayfalama -->
                        <div class="pt-6">
                            {{ $products->links() }}
                        </div>
                    @endif
                </main>

            </div>

        </div>
    </div>
</x-frontend-layout>
