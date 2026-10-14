<x-frontend-layout title="Cihazınızın Markasına Göre Uyumlu Filtre Bulucu | İstanbul Su Arıtma" description="İhlas, LG, BMS, Conax, Waterbox ve tüm su arıtma markalarıyla %100 uyumlu orijinal yedek filtre takımları.">
    <!-- Breadcrumb & Başlık (Tek Renk & Sade) -->
    <div class="bg-rose-700 text-white py-8 sm:py-12 px-4 sm:px-6 lg:px-8 border-b border-rose-800">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight leading-tight mb-2">
                Su Arıtma Cihazınızın Markasını Seçin
            </h1>
            <p class="text-xs sm:text-sm text-purple-100/90 max-w-2xl mx-auto">
                Cihazınızın markasına veya kasa tipine tıklayın; %100 uyumlu orijinal filtre setlerini ve yedek kartuşları anında listeleyelim.
            </p>
        </div>
    </div>

    <!-- Ana İçerik -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">
        
        <!-- TEK BUTON: CİHAZ VE FİLTRE SORMA -->
        <div class="mb-6 flex items-center justify-center">
            <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905013798236' }}?text=Merhaba,%20su%20ar%C4%B1tma%20cihaz%C4%B1m%C4%B1n%20veya%20filtremin%20modelini%20tam%20bilmiyorum.%20Foto%C4%9Fraf%C4%B1n%C4%B1%20at%C4%B1p%20uyumlu%20filtreyi%20%C3%B6%C4%9Frenmek%20istiyorum." 
               target="_blank" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 sm:gap-2.5 px-5 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white shadow-2xs hover:shadow transition-all transform hover:-translate-y-0.5 active:scale-95 text-center group">
                <i class="fab fa-whatsapp text-lg sm:text-xl text-emerald-200 group-hover:text-white transition-colors flex-shrink-0"></i>
                <span class="text-xs sm:text-sm font-bold tracking-tight">
                    Arıtmanızı ve filtrenizi bilmiyorsanız WhatsApp'tan sorun, hemen söyleyelim
                </span>
                <i class="fas fa-arrow-right text-[10px] sm:text-xs opacity-75 group-hover:translate-x-1 transition-transform flex-shrink-0"></i>
            </a>
        </div>

        <!-- HIZLI MARKA VE MODEL ARAMA KUTUSU -->
        <div class="mb-8">
            <form action="{{ route('products.index') }}" method="GET" class="relative max-w-2xl mx-auto">
                <div class="relative flex items-center shadow-md rounded-2xl group focus-within:shadow-xl transition-all">
                    <input type="text" name="search" placeholder="Markanızı veya modelinizi yazın (Örn: İhlas, Cebilon, LG, BMS, Conax...)" 
                           class="w-full pl-11 sm:pl-12 pr-28 py-3.5 sm:py-4 rounded-2xl border-2 border-purple-200 focus:border-purple-600 focus:ring-4 focus:ring-purple-100 bg-white text-xs sm:text-sm font-bold text-gray-900 shadow-sm transition-all">
                    <div class="absolute left-4 text-purple-600 text-base pointer-events-none group-focus-within:scale-110 transition-transform">
                        <i class="fas fa-search"></i>
                    </div>
                    <button type="submit" class="absolute right-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-black text-xs px-5 py-2.5 rounded-xl shadow-md transition-all transform active:scale-95 cursor-pointer">
                        Markaya Git &rarr;
                    </button>
                </div>
            </form>
        </div>

        <!-- Marka Seçim Kartları / Butonları -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm sm:text-base font-black text-gray-900 flex items-center gap-2">
                    <i class="fas fa-filter text-purple-600"></i> Popüler Su Arıtma Markaları:
                </h3>
                @if($selectedBrand)
                    <a href="{{ route('filter.finder') }}" class="text-xs font-bold text-gray-500 hover:text-purple-600 flex items-center gap-1 bg-gray-100 hover:bg-purple-50 px-3 py-1.5 rounded-xl transition-colors">
                        <i class="fas fa-times"></i> Filtreyi Temizle
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-2.5 sm:gap-3">
                <!-- Tümü Butonu -->
                <a href="{{ route('filter.finder') }}" 
                   class="p-3 rounded-2xl border text-center transition-all transform hover:-translate-y-0.5 active:scale-95 shadow-2xs flex flex-col items-center justify-center {{ !$selectedBrand ? 'bg-purple-600 text-white border-purple-600 shadow-md' : 'bg-white text-gray-800 border-gray-200 hover:border-purple-300' }}">
                    <i class="fas fa-th-large text-lg mb-1 {{ !$selectedBrand ? 'text-white' : 'text-purple-600' }}"></i>
                    <span class="text-xs font-black">Tüm Filtreler</span>
                </a>

                @foreach($brands as $b)
                    <a href="{{ route('brand.show', $b->slug) }}" 
                       class="p-3 rounded-2xl border text-center transition-all transform hover:-translate-y-0.5 active:scale-95 shadow-2xs flex flex-col items-center justify-between {{ ($selectedBrand && $selectedBrand->id == $b->id) ? 'bg-purple-600 text-white border-purple-600 shadow-md' : 'bg-white text-gray-800 border-gray-200 hover:border-purple-300 hover:bg-purple-50/40' }}">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center mb-1 overflow-hidden">
                            @if($b->image_path)
                                <img src="{{ asset('storage/' . $b->image_path) }}" alt="{{ $b->name }}" class="w-full h-full object-contain">
                            @else
                                <i class="fas fa-shield-alt text-base {{ ($selectedBrand && $selectedBrand->id == $b->id) ? 'text-white' : 'text-purple-600' }}"></i>
                            @endif
                        </div>
                        <span class="text-xs font-black line-clamp-1">{{ $b->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Alt Model Filtreleme Butonları (Varsa) -->
        @if($selectedBrand && $selectedBrand->children->count() > 0)
            <div class="mb-6 p-4 rounded-2xl bg-white border border-purple-200/80 shadow-2xs">
                <div class="text-xs font-black text-gray-700 mb-2.5 flex items-center gap-1.5">
                    <i class="fas fa-code-branch text-purple-600"></i> {{ $selectedBrand->name }} Modelleri:
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('filter.finder', ['brand' => $selectedBrand->slug]) }}" 
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ !$selectedSubBrand ? 'bg-purple-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-purple-50 hover:text-purple-700' }}">
                        Tüm {{ $selectedBrand->name }} Modelleri
                    </a>
                    @foreach($selectedBrand->children as $sub)
                        <a href="{{ route('filter.finder', ['brand' => $selectedBrand->slug, 'sub' => $sub->slug]) }}" 
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ ($selectedSubBrand && $selectedSubBrand->id == $sub->id) ? 'bg-purple-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-purple-50 hover:text-purple-700' }}">
                            {{ $sub->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Seçilen Marka Bilgi Şeridi -->
        @if($selectedBrand)
            <div class="mb-8 p-5 sm:p-6 rounded-3xl bg-purple-50 border border-purple-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4 text-center sm:text-left">
                    <span class="w-12 h-12 rounded-2xl bg-purple-600 text-white flex items-center justify-center flex-shrink-0 text-xl shadow-sm">
                        <i class="fas fa-check-circle"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-black text-gray-900">
                            {{ $selectedSubBrand ? $selectedBrand->name . ' - ' . $selectedSubBrand->name : $selectedBrand->name }} İle %100 Uyumlu Filtre Setleri
                        </h3>
                        <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                            {{ ($selectedSubBrand && $selectedSubBrand->description) ? $selectedSubBrand->description : ($selectedBrand->description ?: 'Aşağıdaki orijinal filtre takımları cihazınıza tam uyar ve kolayca monte edilir.') }}
                        </p>
                    </div>
                </div>
                <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905013798236' }}?text=Merhaba,%20{{ urlencode($selectedSubBrand ? $selectedBrand->name . ' ' . $selectedSubBrand->name : $selectedBrand->name) }}%20cihaz%C4%B1m%20i%C3%A7in%20filtre%20bak%C4%B1yorum.%20Uyumlu%20modeli%20teyit%20etmek%20istiyorum." target="_blank" class="flex-shrink-0 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs px-5 py-3 rounded-2xl shadow transition-all flex items-center gap-2">
                    <i class="fab fa-whatsapp text-sm"></i> WhatsApp'tan Teyit Alın
                </a>
            </div>
        @endif

        <!-- Ürünler Vitrini -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">
            @forelse($products as $product)
                <div class="group relative border border-gray-200/80 rounded-2xl sm:rounded-3xl overflow-hidden bg-white shadow-2xs hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col justify-between">
                    
                    <!-- Rozetler -->
                    <div class="absolute top-2 left-2 sm:top-3 sm:left-3 flex flex-col gap-1 z-10">
                        @if($product->discount_percent)
                            <div class="bg-gradient-to-r from-rose-600 to-red-600 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full shadow">
                                %{{ $product->discount_percent }} İNDİRİM
                            </div>
                        @else
                            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full shadow">
                                UYUMLU FİLTRE
                            </div>
                        @endif
                    </div>

                    <a href="{{ route('product.show', $product->slug) }}" class="block h-36 sm:h-52 bg-white p-3 sm:p-5 overflow-hidden relative flex items-center justify-center">
                        @if($product->image_path)
                            <img src="{{ asset('storage/'.$product->image_path) }}" 
                                 alt="{{ $product->name }}" 
                                 loading="lazy" 
                                 class="object-contain h-full w-full mix-blend-multiply group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="flex items-center justify-center h-full w-full text-gray-300">
                                <i class="fas fa-filter text-3xl sm:text-4xl text-purple-300"></i>
                            </div>
                        @endif
                    </a>

                    <div class="p-3 sm:p-4 flex-grow flex flex-col border-t border-gray-100 bg-gray-50/40 justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[9px] sm:text-[10px] text-purple-700 font-black uppercase tracking-wider truncate">
                                    {{ $product->category->name ?? 'Filtre Seti' }}
                                </span>
                                <div class="flex text-amber-400 text-[8px] sm:text-[10px]">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                            </div>

                            <a href="{{ route('product.show', $product->slug) }}">
                                <h3 class="text-xs sm:text-sm font-black text-gray-900 mb-1 group-hover:text-purple-600 transition-colors line-clamp-2 leading-snug">
                                    {{ $product->name }}
                                </h3>
                            </a>
                        </div>
                        
                        <div class="flex items-center justify-between mt-2 pt-2 sm:pt-3 border-t border-gray-200/60">
                            <div>
                                @if($product->campaign_price && $product->campaign_price < $product->price)
                                    <div class="flex flex-col sm:flex-row sm:items-baseline sm:gap-1.5">
                                        <span class="text-base sm:text-xl font-black text-rose-600 font-mono">{{ number_format($product->campaign_price, 0, ',', '.') }} TL</span>
                                        <span class="text-[10px] sm:text-xs font-bold text-gray-400 line-through">{{ number_format($product->price, 0, ',', '.') }} TL</span>
                                    </div>
                                @else
                                    <span class="text-base sm:text-xl font-black text-blue-700 font-mono">{{ number_format($product->price, 0, ',', '.') }} TL</span>
                                @endif
                            </div>
                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white p-2 sm:p-2.5 px-2.5 sm:px-3 rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center gap-1 text-[11px] sm:text-xs active:scale-95 cursor-pointer" title="Sepete Ekle">
                                    <i class="fas fa-cart-plus"></i> <span class="hidden sm:inline">Ekle</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-gray-500 py-12 bg-white rounded-3xl border border-gray-200/80">
                    <i class="fas fa-filter text-4xl text-gray-300 mb-3"></i>
                    <p class="text-sm font-bold text-gray-600">Bu marka için şu an listelenen ürün bulunamadı.</p>
                    <p class="text-xs text-gray-400 mt-1">Cihazınızın modelini WhatsApp üzerinden uzmanımıza ileterek uyumlu filtreyi hemen öğrenebilirsiniz.</p>
                </div>
            @endforelse
        </div>

    </div>
</x-frontend-layout>
