@php
    $banners = \App\Models\Banner::where('is_active', true)->get()->keyBy('position');

    $b1 = $banners->get('grid_1');
    $b2 = $banners->get('grid_2');
    $b3 = $banners->get('grid_3');
    $b4 = $banners->get('grid_4');
    $b5 = $banners->get('grid_5');
    $b6 = $banners->get('grid_6');
    $b7 = $banners->get('grid_7');

    $resolveBannerImg = function($banner, $fallback = 'images/placeholder-flower.svg') {
        if (!$banner || !$banner->image_path) {
            return asset($fallback);
        }
        $path = $banner->image_path;
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        if (file_exists(public_path('storage/' . $path))) {
            return asset('storage/' . $path);
        }
        if (file_exists(public_path('images/' . $path))) {
            return asset('images/' . $path);
        }
        if (file_exists(public_path($path))) {
            return asset($path);
        }
        return asset($fallback);
    };

    $card1Img = $resolveBannerImg($b1, 'images/placeholder-flower.svg');
    $card2Img = $resolveBannerImg($b2, 'images/placeholder-flower.svg');
    $card3Img = $resolveBannerImg($b3, 'images/placeholder-flower.svg');
    $card4Img = $resolveBannerImg($b4, 'images/placeholder-flower.svg');
    $card5Img = $resolveBannerImg($b5, 'images/placeholder-flower.svg');
    $card6Img = $resolveBannerImg($b6, 'images/placeholder-flower.svg');
    $card7Img = $resolveBannerImg($b7, 'images/placeholder-flower.svg');
@endphp

<div class="max-w-[1536px] mx-auto px-3 sm:px-6 lg:px-8 xl:px-12 py-4 sm:py-6 w-full overflow-hidden">
    <!-- 4 Sütunlu Banner Vitrini (1-6 Kutu + 7 Dikey Banner) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-5 items-stretch w-full">

        <!-- 1. SÜTUN: BANNER 1 (Üst) & BANNER 2 (Alt) -->
        <div class="grid grid-cols-2 sm:flex sm:flex-col gap-3 sm:gap-4 lg:gap-5 justify-between">
            <!-- BANNER 1 (Kutu 1) -->
            <a href="{{ $b1->link_url ?? route('category.show', 'gul-buketleri') }}" 
               class="flex-1 bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 hover:border-rose-400 shadow-2xs hover:shadow-xl transition-all duration-300 p-3.5 sm:p-4.5 flex flex-col justify-between group relative overflow-hidden">
                <div class="relative z-10">
                    @if($b1 && $b1->badge)
                        <span class="inline-block text-[10px] font-black uppercase tracking-wider text-rose-700 bg-rose-50 border border-rose-100 px-2 py-0.5 rounded-md mb-1.5">
                            {{ $b1->badge }}
                        </span>
                    @endif
                    <h3 class="text-xs xs:text-sm sm:text-base font-black text-slate-800 leading-tight group-hover:text-rose-600 transition-colors line-clamp-1">
                        {{ $b1->title ?? 'Gül Buketleri' }}
                    </h3>
                    @if($b1 && $b1->subtitle)
                        <p class="text-[10px] sm:text-xs text-slate-500 font-medium line-clamp-1 mt-0.5">
                            {{ $b1->subtitle }}
                        </p>
                    @endif
                </div>

                <!-- Büyük Çiçek Görseli -->
                <div class="relative z-10 w-full flex-1 flex items-center justify-center my-2 sm:my-3 min-h-[170px] sm:min-h-[210px] lg:min-h-[230px] xl:min-h-[260px]">
                    <img src="{{ $card1Img }}" 
                         alt="{{ $b1->title ?? 'Gül Buketleri' }}" 
                         class="h-[160px] xs:h-[180px] sm:h-[200px] lg:h-[225px] xl:h-[255px] w-auto max-w-[96%] object-contain filter drop-shadow-md group-hover:scale-108 group-hover:-translate-y-1.5 transition-all duration-300">
                </div>

                <div class="relative z-10 pt-2 flex items-center justify-between border-t border-slate-100">
                    <span class="text-[11px] sm:text-xs font-black text-rose-600 group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                        <span>{{ $b1->button_text ?? 'Hemen İncele' }}</span>
                        <i class="fas fa-arrow-right text-[9px]"></i>
                    </span>
                </div>
            </a>

            <!-- BANNER 2 (Kutu 2) -->
            <a href="{{ $b2->link_url ?? route('category.show', 'vazoda-ve-kutuda-tasarimlar') }}" 
               class="flex-1 bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 hover:border-rose-400 shadow-2xs hover:shadow-xl transition-all duration-300 p-3.5 sm:p-4.5 flex flex-col justify-between group relative overflow-hidden">
                <div class="relative z-10">
                    @if($b2 && $b2->badge)
                        <span class="inline-block text-[10px] font-black uppercase tracking-wider text-rose-700 bg-rose-50 border border-rose-100 px-2 py-0.5 rounded-md mb-1.5">
                            {{ $b2->badge }}
                        </span>
                    @endif
                    <h3 class="text-xs xs:text-sm sm:text-base font-black text-slate-800 leading-tight group-hover:text-rose-600 transition-colors line-clamp-1">
                        {{ $b2->title ?? 'Kutuda Tasarımlar' }}
                    </h3>
                    @if($b2 && $b2->subtitle)
                        <p class="text-[10px] sm:text-xs text-slate-500 font-medium line-clamp-1 mt-0.5">
                            {{ $b2->subtitle }}
                        </p>
                    @endif
                </div>

                <!-- Büyük Çiçek Görseli -->
                <div class="relative z-10 w-full flex-1 flex items-center justify-center my-2 sm:my-3 min-h-[170px] sm:min-h-[210px] lg:min-h-[230px] xl:min-h-[260px]">
                    <img src="{{ $card2Img }}" 
                         alt="{{ $b2->title ?? 'Kutuda Tasarımlar' }}" 
                         class="h-[160px] xs:h-[180px] sm:h-[200px] lg:h-[225px] xl:h-[255px] w-auto max-w-[96%] object-contain filter drop-shadow-md group-hover:scale-108 group-hover:-translate-y-1.5 transition-all duration-300">
                </div>

                <div class="relative z-10 pt-2 flex items-center justify-between border-t border-slate-100">
                    <span class="text-[11px] sm:text-xs font-black text-rose-600 group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                        <span>{{ $b2->button_text ?? 'Keşfet' }}</span>
                        <i class="fas fa-arrow-right text-[9px]"></i>
                    </span>
                </div>
            </a>
        </div>

        <!-- 2. SÜTUN: BANNER 3 (Üst) & BANNER 4 (Alt) -->
        <div class="grid grid-cols-2 sm:flex sm:flex-col gap-3 sm:gap-4 lg:gap-5 justify-between">
            <!-- BANNER 3 (Kutu 3) -->
            <a href="{{ $b3->link_url ?? route('category.show', 'orkide-ve-saksi-cicekleri') }}" 
               class="flex-1 bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 hover:border-rose-400 shadow-2xs hover:shadow-xl transition-all duration-300 p-3.5 sm:p-4.5 flex flex-col justify-between group relative overflow-hidden">
                <div class="relative z-10">
                    @if($b3 && $b3->badge)
                        <span class="inline-block text-[10px] font-black uppercase tracking-wider text-rose-700 bg-rose-50 border border-rose-100 px-2 py-0.5 rounded-md mb-1.5">
                            {{ $b3->badge }}
                        </span>
                    @endif
                    <h3 class="text-xs xs:text-sm sm:text-base font-black text-slate-800 leading-tight group-hover:text-rose-600 transition-colors line-clamp-1">
                        {{ $b3->title ?? 'Asil Orkideler' }}
                    </h3>
                    @if($b3 && $b3->subtitle)
                        <p class="text-[10px] sm:text-xs text-slate-500 font-medium line-clamp-1 mt-0.5">
                            {{ $b3->subtitle }}
                        </p>
                    @endif
                </div>

                <!-- Büyük Çiçek Görseli -->
                <div class="relative z-10 w-full flex-1 flex items-center justify-center my-2 sm:my-3 min-h-[170px] sm:min-h-[210px] lg:min-h-[230px] xl:min-h-[260px]">
                    <img src="{{ $card3Img }}" 
                         alt="{{ $b3->title ?? 'Asil Orkideler' }}" 
                         class="h-[160px] xs:h-[180px] sm:h-[200px] lg:h-[225px] xl:h-[255px] w-auto max-w-[96%] object-contain filter drop-shadow-md group-hover:scale-108 group-hover:-translate-y-1.5 transition-all duration-300">
                </div>

                <div class="relative z-10 pt-2 flex items-center justify-between border-t border-slate-100">
                    <span class="text-[11px] sm:text-xs font-black text-rose-600 group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                        <span>{{ $b3->button_text ?? 'Hemen İncele' }}</span>
                        <i class="fas fa-arrow-right text-[9px]"></i>
                    </span>
                </div>
            </a>

            <!-- BANNER 4 (Kutu 4) -->
            <a href="{{ $b4->link_url ?? route('category.show', 'mevsim-cicekleri-ve-lilyum') }}" 
               class="flex-1 bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 hover:border-rose-400 shadow-2xs hover:shadow-xl transition-all duration-300 p-3.5 sm:p-4.5 flex flex-col justify-between group relative overflow-hidden">
                <div class="relative z-10">
                    @if($b4 && $b4->badge)
                        <span class="inline-block text-[10px] font-black uppercase tracking-wider text-rose-700 bg-rose-50 border border-rose-100 px-2 py-0.5 rounded-md mb-1.5">
                            {{ $b4->badge }}
                        </span>
                    @endif
                    <h3 class="text-xs xs:text-sm sm:text-base font-black text-slate-800 leading-tight group-hover:text-rose-600 transition-colors line-clamp-1">
                        {{ $b4->title ?? 'Mevsim Çiçekleri' }}
                    </h3>
                    @if($b4 && $b4->subtitle)
                        <p class="text-[10px] sm:text-xs text-slate-500 font-medium line-clamp-1 mt-0.5">
                            {{ $b4->subtitle }}
                        </p>
                    @endif
                </div>

                <!-- Büyük Çiçek Görseli -->
                <div class="relative z-10 w-full flex-1 flex items-center justify-center my-2 sm:my-3 min-h-[170px] sm:min-h-[210px] lg:min-h-[230px] xl:min-h-[260px]">
                    <img src="{{ $card4Img }}" 
                         alt="{{ $b4->title ?? 'Mevsim Çiçekleri' }}" 
                         class="h-[160px] xs:h-[180px] sm:h-[200px] lg:h-[225px] xl:h-[255px] w-auto max-w-[96%] object-contain filter drop-shadow-md group-hover:scale-108 group-hover:-translate-y-1.5 transition-all duration-300">
                </div>

                <div class="relative z-10 pt-2 flex items-center justify-between border-t border-slate-100">
                    <span class="text-[11px] sm:text-xs font-black text-rose-600 group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                        <span>{{ $b4->button_text ?? 'İncele' }}</span>
                        <i class="fas fa-arrow-right text-[9px]"></i>
                    </span>
                </div>
            </a>
        </div>

        <!-- 3. SÜTUN: BANNER 5 (Üst) & BANNER 6 (Alt) -->
        <div class="grid grid-cols-2 sm:flex sm:flex-col gap-3 sm:gap-4 lg:gap-5 justify-between">
            <!-- BANNER 5 (Kutu 5) -->
            <a href="{{ $b5->link_url ?? route('category.show', 'dogum-gunu-cicekleri') }}" 
               class="flex-1 bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 hover:border-rose-400 shadow-2xs hover:shadow-xl transition-all duration-300 p-3.5 sm:p-4.5 flex flex-col justify-between group relative overflow-hidden">
                <div class="relative z-10">
                    @if($b5 && $b5->badge)
                        <span class="inline-block text-[10px] font-black uppercase tracking-wider text-rose-700 bg-rose-50 border border-rose-100 px-2 py-0.5 rounded-md mb-1.5">
                            {{ $b5->badge }}
                        </span>
                    @endif
                    <h3 class="text-xs xs:text-sm sm:text-base font-black text-slate-800 leading-tight group-hover:text-rose-600 transition-colors line-clamp-1">
                        {{ $b5->title ?? 'Doğum Günü' }}
                    </h3>
                    @if($b5 && $b5->subtitle)
                        <p class="text-[10px] sm:text-xs text-slate-500 font-medium line-clamp-1 mt-0.5">
                            {{ $b5->subtitle }}
                        </p>
                    @endif
                </div>

                <!-- Büyük Çiçek Görseli -->
                <div class="relative z-10 w-full flex-1 flex items-center justify-center my-2 sm:my-3 min-h-[170px] sm:min-h-[210px] lg:min-h-[230px] xl:min-h-[260px]">
                    <img src="{{ $card5Img }}" 
                         alt="{{ $b5->title ?? 'Doğum Günü' }}" 
                         class="h-[160px] xs:h-[180px] sm:h-[200px] lg:h-[225px] xl:h-[255px] w-auto max-w-[96%] object-contain filter drop-shadow-md group-hover:scale-108 group-hover:-translate-y-1.5 transition-all duration-300">
                </div>

                <div class="relative z-10 pt-2 flex items-center justify-between border-t border-slate-100">
                    <span class="text-[11px] sm:text-xs font-black text-rose-600 group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                        <span>{{ $b5->button_text ?? 'Göz At' }}</span>
                        <i class="fas fa-arrow-right text-[9px]"></i>
                    </span>
                </div>
            </a>

            <!-- BANNER 6 (Kutu 6) -->
            <a href="{{ $b6->link_url ?? route('category.show', 'yildonumu-ve-sevgiliye') }}" 
               class="flex-1 bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 hover:border-rose-400 shadow-2xs hover:shadow-xl transition-all duration-300 p-3.5 sm:p-4.5 flex flex-col justify-between group relative overflow-hidden">
                <div class="relative z-10">
                    @if($b6 && $b6->badge)
                        <span class="inline-block text-[10px] font-black uppercase tracking-wider text-rose-700 bg-rose-50 border border-rose-100 px-2 py-0.5 rounded-md mb-1.5">
                            {{ $b6->badge }}
                        </span>
                    @endif
                    <h3 class="text-xs xs:text-sm sm:text-base font-black text-slate-800 leading-tight group-hover:text-rose-600 transition-colors line-clamp-1">
                        {{ $b6->title ?? 'Romantik Seri' }}
                    </h3>
                    @if($b6 && $b6->subtitle)
                        <p class="text-[10px] sm:text-xs text-slate-500 font-medium line-clamp-1 mt-0.5">
                            {{ $b6->subtitle }}
                        </p>
                    @endif
                </div>

                <!-- Büyük Çiçek Görseli -->
                <div class="relative z-10 w-full flex-1 flex items-center justify-center my-2 sm:my-3 min-h-[170px] sm:min-h-[210px] lg:min-h-[230px] xl:min-h-[260px]">
                    <img src="{{ $card6Img }}" 
                         alt="{{ $b6->title ?? 'Romantik Seri' }}" 
                         class="h-[160px] xs:h-[180px] sm:h-[200px] lg:h-[225px] xl:h-[255px] w-auto max-w-[96%] object-contain filter drop-shadow-md group-hover:scale-108 group-hover:-translate-y-1.5 transition-all duration-300">
                </div>

                <div class="relative z-10 pt-2 flex items-center justify-between border-t border-slate-100">
                    <span class="text-[11px] sm:text-xs font-black text-rose-600 group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                        <span>{{ $b6->button_text ?? 'Seçimi Gör' }}</span>
                        <i class="fas fa-arrow-right text-[9px]"></i>
                    </span>
                </div>
            </a>
        </div>

        <!-- 4. SÜTUN: BANNER 7 (DİKEY BOYDAN BOYA BÜYÜK BANNER) -->
        <div class="flex flex-col h-full col-span-1 sm:col-span-2 lg:col-span-1">
            <a href="{{ $b7->link_url ?? route('products.index') }}" 
               class="h-full flex-1 bg-rose-700 hover:bg-rose-800 text-white rounded-2xl sm:rounded-3xl p-5 sm:p-6 lg:p-7 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col justify-between group relative overflow-hidden min-h-[460px] sm:min-h-[580px] lg:min-h-[660px] xl:min-h-[720px]">
                
                <!-- Üst Bilgi Grubu -->
                <div class="relative z-10">
                    @if($b7 && $b7->badge)
                        <span class="inline-block text-[10px] sm:text-xs font-black uppercase tracking-widest text-amber-300 bg-black/25 backdrop-blur-xs px-3 py-1 rounded-full mb-3 self-start border border-amber-300/30">
                            {{ $b7->badge }}
                        </span>
                    @endif

                    <h2 class="text-xl sm:text-2xl lg:text-3xl font-black text-white leading-tight tracking-tight uppercase">
                        {{ $b7->title ?? 'Haftanın Özel Çiçek Fırsatları' }}
                    </h2>

                    @if($b7 && $b7->subtitle)
                        <p class="text-xs sm:text-sm text-rose-100/90 font-medium mt-1.5 leading-snug">
                            {{ $b7->subtitle }}
                        </p>
                    @endif
                </div>

                <!-- ÇOK BÜYÜK ÇİÇEK GÖRSELİ (Vitrin Odak Noktası) -->
                <div class="relative z-10 w-full flex-1 flex items-center justify-center my-4 sm:my-6 overflow-hidden min-h-[260px] sm:min-h-[340px] lg:min-h-[400px]">
                    <img src="{{ $card7Img }}" 
                         alt="{{ $b7->title ?? 'Haftanın Özel Çiçek Fırsatları' }}" 
                         class="max-h-[260px] xs:max-h-[300px] sm:max-h-[360px] lg:max-h-[420px] xl:max-h-[460px] w-auto max-w-[96%] object-contain filter drop-shadow-2xl group-hover:scale-108 group-hover:-translate-y-2 transition-all duration-500">
                </div>

                <!-- Alt Aksiyon Butonu -->
                <div class="relative z-10 pt-2">
                    <div class="w-full py-3 px-5 bg-white text-rose-700 hover:bg-rose-50 font-black text-xs sm:text-sm rounded-xl sm:rounded-2xl shadow-md transition-all text-center flex items-center justify-center gap-2 group-hover:shadow-xl">
                        <span>{{ $b7->button_text ?? 'Tüm Fırsatları Gör' }}</span>
                        <i class="fas fa-arrow-right text-xs group-hover:translate-x-1.5 transition-transform"></i>
                    </div>
                </div>
            </a>
        </div>

    </div>
</div>
