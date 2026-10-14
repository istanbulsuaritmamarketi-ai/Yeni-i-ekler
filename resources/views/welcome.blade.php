<x-frontend-layout>

    <!-- 1. KAMPANYA & ÖNE ÇIKANLAR BANNER VİTRİNİ -->
    <x-hero-banner-grid />

    <!-- 3. KATEGORİ VİTRİNİ -->
    <section class="max-w-[1536px] mx-auto px-3 sm:px-6 lg:px-8 xl:px-12 py-8 sm:py-12 w-full overflow-hidden">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Kategorileri Keşfedin</h2>
                <p class="text-xs text-slate-500 mt-0.5">En çok tercih edilen taze çiçek koleksiyonları</p>
            </div>
            <a href="{{ route('products.index') }}" class="text-xs font-extrabold text-rose-600 hover:underline flex items-center gap-1">
                Tümünü Gör <i class="fas fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
            @foreach($categories->take(6) as $cat)
                <a href="{{ route('category.show', $cat->slug) }}" 
                   class="group bg-white rounded-3xl p-3 sm:p-4 text-center border border-rose-100 hover:border-rose-300 shadow-2xs hover:shadow-lg transition-all duration-300 flex flex-col items-center">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden bg-rose-50 mb-3 group-hover:scale-110 transition-transform duration-300 shadow-xs">
                        <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" class="w-full h-full object-cover">
                    </div>
                    <span class="text-xs font-black text-slate-800 group-hover:text-rose-600 transition-colors line-clamp-1">
                        {{ $cat->name }}
                    </span>
                    <span class="text-[10px] text-slate-400 mt-0.5">Taze Çiçekler</span>
                </a>
            @endforeach
        </div>
    </section>

    <!-- 4. GÜNÜN ÇOK SATAN ÇİÇEKLERİ (PRODUCT GRID) -->
    <section class="max-w-[1536px] mx-auto px-3 sm:px-6 lg:px-8 xl:px-12 py-6 sm:py-8 w-full overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6">
            <div>
                <div class="inline-flex items-center gap-1.5 text-xs font-black text-rose-600 uppercase tracking-wider mb-1">
                    <i class="fas fa-fire"></i> En Çok Satanlar
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Popüler Çiçek Tasarımları</h2>
            </div>
            <a href="{{ route('products.index') }}" class="text-xs font-extrabold text-rose-600 hover:underline flex items-center gap-1 self-start sm:self-auto">
                Kataloğu Aç <i class="fas fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 sm:gap-6">
            @foreach($featuredProducts->take(8) as $prod)
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

                    <!-- Görsel Alanı (Hover Zoom) -->
                    <a href="{{ route('product.show', $prod->slug) }}" class="relative aspect-[4/5] bg-rose-50 overflow-hidden block">
                        <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" 
                             class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500">
                    </a>

                    <!-- İçerik Alanı -->
                    <div class="p-4 sm:p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-rose-500 uppercase tracking-wider block">
                                {{ $prod->category->name ?? 'Taze Çiçek' }}
                            </span>
                            <h3 class="text-sm sm:text-sm font-extrabold text-slate-900 mt-1 line-clamp-2 group-hover:text-rose-600 transition-colors">
                                <a href="{{ route('product.show', $prod->slug) }}">{{ $prod->name }}</a>
                            </h3>
                        </div>

                        <!-- Fiyat ve Aksiyon -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                @if($prod->campaign_price)
                                    <div class="text-[10px] text-slate-400 line-through">₺{{ number_format($prod->price, 0, ',', '.') }}</div>
                                    <div class="text-lg sm:text-lg font-black text-rose-600 leading-tight">₺{{ number_format($prod->campaign_price, 0, ',', '.') }}</div>
                                @else
                                    <div class="text-lg sm:text-lg font-black text-slate-900 leading-tight">₺{{ number_format($prod->price, 0, ',', '.') }}</div>
                                @endif
                            </div>

                            <a href="{{ route('product.show', $prod->slug) }}" 
                               class="px-4 py-2.5 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white rounded-xl text-xs font-black transition-all flex items-center gap-1.5 shadow-2xs">
                                <span>Gönder</span>
                                <i class="fas fa-chevron-right text-[9px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- 4.5. KENDİ BUKETİNİ KENDİN OLUŞTUR (ÖZEL ATÖLYE BANNERI) -->
    <section class="max-w-[1536px] mx-auto px-3 sm:px-6 lg:px-8 xl:px-12 my-6 sm:my-10 w-full overflow-hidden">
        <div class="relative bg-gradient-to-br from-rose-950 via-pink-900 to-slate-900 rounded-3xl p-6 sm:p-12 text-white shadow-xl overflow-hidden border border-rose-500/20">
            <div class="absolute -right-16 -top-16 w-80 h-80 bg-rose-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-80 h-80 bg-pink-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-7 space-y-4 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-rose-200 text-xs font-black uppercase tracking-wider border border-white/10">
                        <i class="fas fa-magic text-rose-300"></i>
                        <span>Atölye Tasarım Sihirbazı</span>
                    </div>

                    <h2 class="text-2xl sm:text-4xl font-black tracking-tight leading-tight">
                        Kendi Buketini Kendin Tasarla!
                    </h2>

                    <p class="text-xs sm:text-sm text-rose-100/90 font-medium max-w-xl mx-auto lg:mx-0 leading-relaxed">
                        Sevdiğiniz çiçekleri tek tek dal olarak seçin, dilediğiniz buket paket kağıdını ve kurdele rengini belirleyin. Usta çiçekçilerimiz hayalinizdeki buketi taptaze hazırlasın!
                    </p>

                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-2 text-xs font-bold text-rose-200">
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-check-circle text-rose-400"></i> Tekli Dal Çiçek Seçimi
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-check-circle text-rose-400"></i> 8 Farklı Paket Rengi
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-check-circle text-rose-400"></i> Canlı Fiyatlandırma
                        </span>
                    </div>

                    <div class="pt-3">
                        <a href="{{ route('custom-bouquet.index') }}" 
                           class="inline-flex items-center gap-2.5 bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-600 hover:to-pink-600 text-white font-black text-sm px-7 py-3.5 rounded-2xl shadow-lg shadow-rose-600/30 transition-all hover:scale-105 active:scale-95">
                            <i class="fas fa-paint-brush"></i>
                            <span>Buketi Tasarlamaya Başla &rarr;</span>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5 flex justify-center">
                    <div class="relative w-full max-w-sm bg-white/10 backdrop-blur-md rounded-3xl p-5 border border-white/20 shadow-2xl text-center space-y-4">
                        <div class="w-full aspect-[4/3] rounded-2xl overflow-hidden bg-rose-50/20 relative shadow-inner">
                            <img src="{{ asset('images/custom-bouquet-placeholder.svg') }}" alt="Özel Tasarım Buket" class="w-full h-full object-cover">
                            <span class="absolute top-3 right-3 bg-rose-600 text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow-md uppercase">
                                Kişiye Özel
                            </span>
                        </div>
                        <div class="flex items-center justify-around text-xs text-white font-bold border-t border-white/10 pt-3">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-8 h-8 rounded-full bg-white/10 border border-white/20 flex items-center justify-center text-white mb-1 shadow-xs">
                                    <i class="fas fa-spa text-xs"></i>
                                </div>
                                <span class="text-[11px] text-slate-200 font-semibold">Çiçeklerini Seç</span>
                            </div>
                            <i class="fas fa-arrow-right text-[10px] text-white/40 mb-3"></i>
                            <div class="flex flex-col items-center text-center">
                                <div class="w-8 h-8 rounded-full bg-white/10 border border-white/20 flex items-center justify-center text-white mb-1 shadow-xs">
                                    <i class="fas fa-box-open text-xs"></i>
                                </div>
                                <span class="text-[11px] text-slate-200 font-semibold">Ambalajını Seç</span>
                            </div>
                            <i class="fas fa-arrow-right text-[10px] text-white/40 mb-3"></i>
                            <div class="flex flex-col items-center text-center">
                                <div class="w-8 h-8 rounded-full bg-white/10 border border-white/20 flex items-center justify-center text-white mb-1 shadow-xs">
                                    <i class="fas fa-shopping-bag text-xs"></i>
                                </div>
                                <span class="text-[11px] text-slate-200 font-semibold">Hemen Satın Al</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. AYNI GÜN TESLİMAT ÇİÇEKLERİ (Sade, Ferah, Büyük Kartlar & Akıcı Kaydırma) -->
    <section class="max-w-[1536px] mx-auto px-3 sm:px-6 lg:px-8 xl:px-12 my-6 sm:my-10 w-full overflow-hidden"
             x-data="{
                 scrollLeft() { $refs.slider.scrollBy({ left: -320, behavior: 'smooth' }); },
                 scrollRight() { $refs.slider.scrollBy({ left: 320, behavior: 'smooth' }); }
             }">
        <div class="bg-white border border-rose-100 rounded-3xl p-4 sm:p-8 shadow-xs relative overflow-hidden w-full max-w-full">
            <!-- Üst Başlık & Küçültülmüş Buton & Kaydırma Kontrolleri -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-4 border-b border-rose-100/70">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold text-rose-700 bg-rose-100/70 border border-rose-200/50 mb-1.5">
                        <i class="fas fa-truck-fast text-xs text-rose-600"></i>
                        <span>Ekspres Teslimat</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                        Aynı Gün Hızlı Teslimat
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">81 il ve tüm ilçelerimize taptaze çiçekler aynı gün kapınızda.</p>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <!-- Küçültülmüş Zarif Aynı Gün Butonu -->
                    <a href="{{ route('products.index', ['badge' => 'AYNI GÜN']) }}" 
                       class="inline-flex items-center gap-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-3 py-1.5 rounded-xl shadow-xs transition active:scale-95">
                        <span>Aynı Gün Çiçekler</span>
                        <i class="fas fa-chevron-right text-[9px]"></i>
                    </a>

                    <!-- Kaydırma Okları -->
                    <div class="flex items-center gap-1">
                        <button type="button" @click="scrollLeft()" title="Geri"
                                class="w-8 h-8 rounded-xl bg-white hover:bg-rose-50 text-slate-700 hover:text-rose-600 border border-rose-200/80 flex items-center justify-center text-xs transition shadow-2xs">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button type="button" @click="scrollRight()" title="İleri"
                                class="w-8 h-8 rounded-xl bg-white hover:bg-rose-50 text-slate-700 hover:text-rose-600 border border-rose-200/80 flex items-center justify-center text-xs transition shadow-2xs">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Büyük Ürün Kartları & Akıcı Yatay Dokunmatik Kaydırma -->
            <div x-ref="slider" 
                 class="flex gap-3 sm:gap-6 overflow-x-auto snap-x snap-mandatory scroll-smooth pb-3 pt-1 no-scrollbar touch-pan-x w-full max-w-full">
                @foreach($sameDayFlowers as $sdf)
                    <div class="w-[80vw] max-w-[310px] sm:w-[290px] lg:w-[310px] shrink-0 snap-start bg-white rounded-2xl p-4 border border-rose-100/90 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                        
                        <!-- Büyük Ürün Fotoğrafı -->
                        <a href="{{ route('product.show', $sdf->slug) }}" class="block relative h-64 sm:h-72 w-full rounded-xl overflow-hidden bg-slate-50 mb-3.5">
                            <img src="{{ $sdf->image_url }}" alt="{{ $sdf->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            
                            <!-- Rozetler -->
                            <span class="absolute top-2.5 left-2.5 bg-amber-400 text-slate-950 font-black text-[10px] px-2.5 py-1 rounded-lg uppercase tracking-wider shadow-xs">
                                2 Saatte Teslim
                            </span>

                            @if($sdf->campaign_price)
                                <span class="absolute top-2.5 right-2.5 bg-rose-600 text-white font-extrabold text-[10px] px-2 py-0.5 rounded-lg shadow-xs">
                                    İndirim
                                </span>
                            @endif
                        </a>

                        <!-- Ürün Detayları -->
                        <div>
                            <a href="{{ route('product.show', $sdf->slug) }}" class="block">
                                <h4 class="text-sm sm:text-base font-extrabold text-slate-900 group-hover:text-rose-600 transition-colors line-clamp-1">
                                    {{ $sdf->name }}
                                </h4>
                            </a>
                            
                            <p class="text-xs text-slate-500 line-clamp-1 mt-1">
                                {{ $sdf->short_description ?: 'Taze ithal çiçekler ve özel tasarım sunumuyla.' }}
                            </p>

                            <!-- Fiyat & Buton -->
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <div>
                                    @if($sdf->campaign_price)
                                        <div class="text-[10px] text-slate-400 line-through">₺{{ number_format($sdf->price, 0, ',', '.') }}</div>
                                        <div class="text-base sm:text-lg font-black text-rose-600 leading-tight">₺{{ number_format($sdf->campaign_price, 0, ',', '.') }}</div>
                                    @else
                                        <div class="text-[10px] text-slate-400 font-bold uppercase">Teslimat Fiyatı</div>
                                        <div class="text-base sm:text-lg font-black text-slate-900 leading-tight">₺{{ number_format($sdf->price, 0, ',', '.') }}</div>
                                    @endif
                                </div>

                                <a href="{{ route('product.show', $sdf->slug) }}" 
                                   class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-black transition-all flex items-center gap-1.5 shadow-xs group-hover:scale-105 active:scale-95">
                                    <span>Gönder</span>
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            <!-- Mobilde Sağa Kaydır Bilgisi / İpucu -->
            <div class="sm:hidden mt-2 text-center text-[11px] text-slate-400 flex items-center justify-center gap-1.5">
                <i class="fas fa-arrows-left-right text-[10px] text-rose-400"></i>
                <span>Diğer çiçekleri görmek için yana kaydırın</span>
            </div>
        </div>
    </section>


    <!-- 7. MÜŞTERİ YORUMLARI & GOOGLE HARİTALAR CANLI ENTEGRASYONU -->
    <section class="max-w-[1536px] mx-auto px-3 sm:px-6 lg:px-8 xl:px-12 py-8 sm:py-14 my-4 w-full overflow-hidden">
        <div class="bg-white border border-slate-200/90 rounded-3xl p-4 sm:p-10 shadow-xs w-full max-w-full overflow-hidden">
            
            <!-- Google Başlık & Canlı Değerlendirme Rozeti -->
            <div class="text-center max-w-2xl mx-auto mb-10">
                <!-- Google Resmi Renkli Logosu & Rozeti -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white text-slate-800 text-xs font-bold shadow-xs border border-slate-200 mb-3">
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.665-5.18 3.665-9.15z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.27v3.15C3.33 21.46 7.37 24 12 24z"/>
                        <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.27C.46 8.2 0 10.05 0 12s.46 3.8 1.27 5.42l4.01-3.15z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.37 0 3.33 2.54 1.27 6.58l4.01 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                    </svg>
                    <span>Google Haritalar Doğrulanmış Değerlendirmeleri</span>
                </div>
                
                <!-- Google Yıldız Skoru -->
                <div class="flex items-center justify-center gap-1.5 text-amber-400 text-sm mb-1.5">
                    <span class="text-slate-900 font-black text-2xl mr-1">{{ $googleRating ?? '4.9' }}</span>
                    <i class="fas fa-star text-base"></i><i class="fas fa-star text-base"></i><i class="fas fa-star text-base"></i><i class="fas fa-star text-base"></i><i class="fas fa-star text-base"></i>
                    <span class="text-xs text-slate-500 font-bold ml-1.5">({{ $googleReviewsTotal ?? '486' }}+ Google İncelemesi)</span>
                </div>
                
                <h2 class="text-xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Müşterilerimizin Deneyimleri
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Google Haritalar profilimiz üzerinden teslim edilen yüzlerce mutlu tebessüm ve şeffaf müşteri yorumları.
                </p>
            </div>

            <!-- Google Yorum Kartları Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 lg:gap-6">
                @forelse($googleReviews as $review)
                    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                        <div>
                            <!-- Müşteri Başlığı & Google Rozeti -->
                            <div class="flex items-center justify-between mb-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-slate-800 to-slate-600 text-white font-black text-xs flex items-center justify-center shadow-xs">
                                        {{ mb_substr($review->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-extrabold text-xs text-slate-900 leading-tight">{{ $review->name }}</div>
                                        <div class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5">
                                            <i class="fas fa-map-marker-alt text-[9px] text-rose-500"></i>
                                            <span>{{ $review->location ?: 'İstanbul' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <span class="text-[10px] text-slate-700 font-bold bg-slate-50 px-2 py-1 rounded-lg flex items-center gap-1.5 border border-slate-200">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24">
                                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.665-5.18 3.665-9.15z"/>
                                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.27v3.15C3.33 21.46 7.37 24 12 24z"/>
                                        <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.27C.46 8.2 0 10.05 0 12s.46 3.8 1.27 5.42l4.01-3.15z"/>
                                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.37 0 3.33 2.54 1.27 6.58l4.01 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                                    </svg>
                                    <span>Google</span>
                                </span>
                            </div>

                            <!-- Yıldızlar -->
                            <div class="flex items-center text-amber-400 gap-0.5 mb-2.5">
                                @for($i = 1; $i <= ($review->rating ?: 5); $i++)
                                    <i class="fas fa-star text-xs"></i>
                                @endfor
                                <span class="text-[10px] font-black text-slate-700 ml-1.5">5.0</span>
                            </div>

                            <!-- Yorum Metni -->
                            <p class="text-xs text-slate-600 leading-relaxed italic">
                                "{{ $review->comment }}"
                            </p>
                        </div>

                        <!-- Alt Doğrulama Bilgisi -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                            <span class="flex items-center gap-1 text-emerald-600 font-bold">
                                <i class="fas fa-check-circle text-xs"></i>
                                <span>Doğrulanmış Teslimat</span>
                            </span>
                            <span class="text-[10px] text-slate-400">Google Maps</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 text-slate-400 text-xs">
                        Henüz gösterilecek Google yorumu bulunamadı.
                    </div>
                @endforelse
            </div>

            <!-- Alt Google Maps Aksiyon Barı -->
            <div class="mt-8 pt-6 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <i class="fas fa-shield-alt text-emerald-500 text-sm"></i>
                    <span>Tüm yorumlar Google Haritalar işletme hesabımızdan doğrulanarak aktarılmaktadır.</span>
                </div>

                <a href="{{ $googleMapsUrl ?? 'https://maps.google.com' }}" target="_blank" 
                   class="inline-flex items-center gap-2 text-xs font-bold text-slate-800 hover:text-blue-600 bg-white hover:bg-slate-100 border border-slate-300/80 px-4 py-2 rounded-xl transition shadow-xs">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.665-5.18 3.665-9.15z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.27v3.15C3.33 21.46 7.37 24 12 24z"/>
                        <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.27C.46 8.2 0 10.05 0 12s.46 3.8 1.27 5.42l4.01-3.15z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.37 0 3.33 2.54 1.27 6.58l4.01 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                    </svg>
                    <span>Google Haritalar'da Bizi Değerlendirin</span>
                    <i class="fas fa-external-link-alt text-[9px] text-slate-400"></i>
                </a>
            </div>

        </div>
    </section>

</x-frontend-layout>
