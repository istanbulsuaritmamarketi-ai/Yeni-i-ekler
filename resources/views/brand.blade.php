<x-frontend-layout 
    title="{{ $brand->name }} Su Arıtma Cihazları ve Uyumlu Filtreleri | İstanbul Su Arıtma"
    description="{{ $brand->description ? Str::limit(strip_tags($brand->description), 160) : ($brand->name . ' su arıtma cihazları ve %100 uyumlu orijinal yedek filtre takımları. İstanbul içi aynı gün teslimat ve ücretsiz servis imkanı.') }}">

    <!-- Breadcrumb & Marka Başlık Şeridi -->
    <div class="bg-gradient-to-r from-purple-900 via-indigo-900 to-slate-900 text-white py-8 sm:py-12 px-4 sm:px-6 lg:px-8 border-b border-purple-800/40">
        <div class="max-w-7xl mx-auto">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs text-purple-200/70 mb-4 font-medium">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">Ana Sayfa</a>
                <span>/</span>
                <a href="{{ route('filter.finder') }}" class="hover:text-white transition-colors">Markalar &amp; Filtre Bulucu</a>
                <span>/</span>
                <span class="text-white font-bold">{{ $brand->name }}</span>
                @if($selectedSubBrand)
                    <span>/</span>
                    <span class="text-amber-300 font-bold">{{ $selectedSubBrand->name }}</span>
                @endif
            </nav>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2 max-w-3xl">
                    <div class="inline-flex items-center gap-2 bg-purple-500/20 text-purple-300 text-xs font-black px-3.5 py-1.5 rounded-full border border-purple-400/30 shadow-inner">
                        <i class="fas fa-shield-alt text-purple-400"></i> %100 Orijinal Uyumlu Parça Garantisi
                    </div>
                    <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight leading-tight">
                        {{ $selectedSubBrand ? $brand->name . ' - ' . $selectedSubBrand->name : $brand->name }} Su Arıtma Cihazı &amp; Filtreleri
                    </h1>
                    <p class="text-xs sm:text-sm text-purple-100/90 leading-relaxed">
                        {{ ($selectedSubBrand && $selectedSubBrand->description) ? $selectedSubBrand->description : ($brand->description ?: $brand->name . ' su arıtma cihazınız için %100 uyumlu sediment, aktif karbon, blok karbon, membran ve tatlandırıcı filtre takımları. İstanbul genelinde aynı gün servis ve montaj desteği.') }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905013798236' }}?text=Merhaba,%20{{ urlencode($selectedSubBrand ? $brand->name . ' ' . $selectedSubBrand->name : $brand->name) }}%20cihaz%C4%B1m%20i%C3%A7in%20filtre%20bak%C4%B1yorum.%20Uyumlu%20modeli%20teyit%20etmek%20istiyorum." 
                       target="_blank" 
                       class="bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs sm:text-sm px-6 py-3.5 rounded-2xl shadow-lg transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center gap-2 whitespace-nowrap">
                        <i class="fab fa-whatsapp text-lg"></i> WhatsApp'tan Teyit Alın
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Ana İçerik -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-10">

        <!-- Alt Modeller (Varsa Tab/Buton Barı) -->
        @if($brand->children->count() > 0)
            <div class="p-5 rounded-3xl bg-white border border-purple-200/80 shadow-xs space-y-3">
                <div class="text-xs font-black text-gray-700 flex items-center gap-1.5">
                    <i class="fas fa-code-branch text-purple-600"></i> {{ $brand->name }} Modellerine Göre Filtreleyin:
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('brand.show', $brand->slug) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-black transition-all {{ !$selectedSubBrand ? 'bg-purple-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-purple-50 hover:text-purple-700' }}">
                        Tüm {{ $brand->name }} Modelleri ({{ $products->count() }})
                    </a>
                    @foreach($brand->children as $sub)
                        <a href="{{ route('brand.show', ['brand' => $brand->slug, 'model' => $sub->slug]) }}" 
                           class="px-4 py-2 rounded-xl text-xs font-black transition-all {{ ($selectedSubBrand && $selectedSubBrand->id == $sub->id) ? 'bg-purple-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-purple-50 hover:text-purple-700' }}">
                            {{ $sub->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Ürünler Vitrini -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-black text-gray-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-sm">
                        <i class="fas fa-boxes"></i>
                    </span>
                    {{ $selectedSubBrand ? $selectedSubBrand->name . ' İçin Uyumlu Ürünler' : $brand->name . ' Uyumlu Ürünler ve Filtre Setleri' }}
                    <span class="text-xs font-bold text-gray-500">({{ $products->count() }} Ürün)</span>
                </h2>
            </div>

            @if($products->isEmpty())
                <div class="text-center py-16 bg-white rounded-3xl border border-dashed border-gray-300 space-y-4">
                    <div class="w-16 h-16 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center mx-auto text-2xl">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div class="space-y-1">
                        <h3 class="font-black text-base text-gray-800">Bu modele özel ürün henüz eklenmedi.</h3>
                        <p class="text-xs text-gray-500 max-w-md mx-auto">
                            Cihazınız standart 10 inç veya inline filtre kullanıyorsa üniversal filtrelerimiz %100 uyumludur. Detaylı bilgi için WhatsApp hattımıza yazabilirsiniz.
                        </p>
                    </div>
                    <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905013798236' }}?text=Merhaba,%20{{ urlencode($brand->name) }}%20cihaz%C4%B1m%20i%C3%A7in%20filtre%20bilgisi%20almak%20istiyorum." 
                       target="_blank" 
                       class="inline-flex items-center gap-2 bg-emerald-600 text-white font-black text-xs px-5 py-3 rounded-2xl shadow-sm">
                        <i class="fab fa-whatsapp"></i> WhatsApp'tan Destek Al
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">
                    @foreach($products as $product)
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
                                            <span class="text-base sm:text-xl font-black text-blue-600 font-mono">{{ number_format($product->price, 0, ',', '.') }} TL</span>
                                        @endif
                                    </div>

                                    <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                title="Sepete Ekle"
                                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-purple-600 hover:bg-purple-700 text-white flex items-center justify-center shadow-md transition-all transform active:scale-90 cursor-pointer">
                                            <i class="fas fa-cart-plus text-xs sm:text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- WHATSAPP HIZLI DESTEK BANTI (Tek Renk & Sade) -->
        <div class="bg-emerald-50 border border-emerald-200/90 rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-5 shadow-sm">
            <div class="flex items-center gap-4 text-center sm:text-left">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-2xl sm:text-3xl flex-shrink-0 shadow-md shadow-emerald-600/30">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <div>
                    <h3 class="text-base sm:text-xl font-black text-emerald-950">
                        {{ $brand->name }} Cihazınızın Modelini Tam Bilmiyor musunuz?
                    </h3>
                    <p class="text-xs sm:text-sm text-emerald-800/90 mt-1 max-w-xl leading-relaxed">
                        Cihazınızın veya mevcut filtrelerinizin fotoğrafını WhatsApp hattımıza gönderin. Uzman ekibimiz 2 dakika içinde cihazınıza %100 uyan filtre takımını tespit edip adresinize göndersin.
                    </p>
                </div>
            </div>
            <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905013798236' }}?text=Merhaba,%20{{ urlencode($brand->name) }}%20su%20ar%C4%B1tma%20cihaz%C4%B1m%C4%B1n%20foto%C4%9Fraf%C4%B1n%C4%B1%20g%C3%B6nderip%20uyumlu%20filtreyi%20%C3%B6%C4%9Frenmek%20istiyorum." 
               target="_blank" 
               class="flex-shrink-0 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-xs transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center gap-1.5">
                <i class="fab fa-whatsapp text-sm"></i> Fotoğraf Gönder &amp; Sor
            </a>
        </div>

        <!-- DİĞER POPÜLER MARKALAR -->
        @if($otherBrands->isNotEmpty())
            <div class="pt-6 border-t border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm sm:text-base font-black text-gray-900 flex items-center gap-2">
                        <i class="fas fa-shield-alt text-purple-600"></i> Diğer Popüler Su Arıtma Markaları:
                    </h3>
                    <a href="{{ route('filter.finder') }}" class="text-xs font-bold text-purple-600 hover:underline">
                        Tüm Markaları Gör &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach($otherBrands->take(6) as $ob)
                        <a href="{{ route('brand.show', $ob->slug) }}" 
                           class="p-3.5 rounded-2xl bg-white border border-gray-200 hover:border-purple-300 hover:bg-purple-50/40 text-center transition-all transform hover:-translate-y-0.5 active:scale-95 shadow-2xs flex flex-col items-center justify-between">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center mb-1 overflow-hidden">
                                @if($ob->image_path)
                                    <img src="{{ asset('storage/' . $ob->image_path) }}" alt="{{ $ob->name }}" class="w-full h-full object-contain">
                                @else
                                    <i class="fas fa-shield-alt text-base text-purple-600"></i>
                                @endif
                            </div>
                            <span class="text-xs font-black text-gray-900 line-clamp-1">{{ $ob->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</x-frontend-layout>
