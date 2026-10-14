@php
    $promoActive = ($siteSettings['gift_promo_active'] ?? '1') == '1';
    $dbGifts = collect();
    
    // 1. Ürün detay sayfasındaysa ($product tanımlıysa)
    if (isset($product) && $product) {
        if (!$product->has_gift) {
            $promoActive = false;
        } elseif ($product->gifts && $product->gifts->count() > 0) {
            $dbGifts = $product->gifts()->where('is_active', true)->orderBy('sort_order')->get();
        }
    } 
    // 2. Sepet veya Ödeme sayfasındaysa (Sepetteki ürünlerin has_gift durumunu kontrol et)
    else {
        $cartItems = $cart ?? session('cart', []);
        if (empty($cartItems)) {
            $promoActive = false;
        } else {
            $cartProductIds = array_column($cartItems, 'id');
            if (empty($cartProductIds)) {
                $cartProductIds = array_keys($cartItems);
            }
            
            // Sepette hediye promosyonu açık (has_gift = true) en az bir ürün var mı?
            $eligibleProducts = \App\Models\Product::whereIn('id', $cartProductIds)
                ->where('has_gift', true)
                ->get();
                
            if ($eligibleProducts->isEmpty()) {
                // Sepetteki hiçbir ürünün hediye özelliği açık değil -> Hediye kutusunu gizle!
                $promoActive = false;
            } else {
                // Hediye hakkı olan ürünlere özel atanmış hediyeler varsa onları topla
                $specificGifts = collect();
                foreach ($eligibleProducts as $ep) {
                    if ($ep->gifts && $ep->gifts->count() > 0) {
                        $specificGifts = $specificGifts->merge($ep->gifts()->where('is_active', true)->get());
                    }
                }
                if ($specificGifts->isNotEmpty()) {
                    $dbGifts = $specificGifts->unique('id')->sortBy('sort_order');
                }
            }
        }
    }

    if ($promoActive && $dbGifts->isEmpty()) {
        $dbGifts = \App\Models\Gift::where('is_active', true)->orderBy('sort_order')->get();
    }
@endphp

@if($promoActive && $dbGifts->count() > 0)
    <div class="bg-purple-50/60 rounded-3xl p-6 sm:p-7 border border-purple-200/80 shadow-sm space-y-4 my-6"
         x-data="{
            selectedGift: '{{ addslashes($dbGifts->first()->name) }}',
            init() {
                if (localStorage.getItem('user_selected_gift')) {
                    this.selectedGift = localStorage.getItem('user_selected_gift');
                }
            },
            select(giftName) {
                this.selectedGift = giftName;
                localStorage.setItem('user_selected_gift', giftName);
            }
         }">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-purple-200/60">
            <div>
                <span class="inline-flex items-center gap-1.5 bg-purple-600 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider shadow-sm mb-1.5">
                    <i class="fas fa-gift"></i> %100 ÜCRETSİZ SÜRPRİZ HEDİYE
                </span>
                <h3 class="text-base sm:text-lg font-black text-purple-950">
                    {{ (isset($product) && $product->gift_note) ? $product->gift_note : ($siteSettings['gift_promo_title'] ?? 'Bu Siparişinize Özel 1 Adet Ücretsiz Hediyenizi Seçin!') }}
                </h3>
                <p class="text-xs text-purple-800/80 mt-0.5">
                    {{ $siteSettings['gift_promo_subtitle'] ?? 'Tüm cihaz ve filtre siparişlerinizde ücretsiz paketlenip teslim edilir (0 TL).' }}
                </p>
            </div>
            <span class="bg-white text-emerald-600 text-xs font-black px-3 py-1.5 rounded-xl border border-emerald-200 shadow-sm flex items-center gap-1">
                <i class="fas fa-check-circle"></i> 0 TL Ek Ücret Yok
            </span>
        </div>

        <input type="hidden" name="selected_gift" :value="selectedGift">

        <!-- Hediye Seçenek Kartları -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
            @foreach($dbGifts as $g)
                <div @click="select('{{ addslashes($g->name) }}')"
                     class="p-4 rounded-2xl border-2 transition-all cursor-pointer flex items-start gap-3 relative overflow-hidden bg-white hover:shadow-md"
                     :class="selectedGift === '{{ addslashes($g->name) }}' ? 'border-purple-600 ring-2 ring-purple-600/20 bg-purple-50/20' : 'border-gray-200/80 hover:border-purple-300'">
                    
                    <!-- Seçildi Rozeti -->
                    <template x-if="selectedGift === '{{ addslashes($g->name) }}'">
                        <div class="absolute top-0 right-0 bg-purple-600 text-white text-[9px] font-black px-2.5 py-0.5 rounded-bl-xl shadow flex items-center gap-1">
                            <i class="fas fa-check"></i> SEÇİLDİ (0 TL)
                        </div>
                    </template>

                    <!-- Fotoğraf veya İkon -->
                    @if($g->image_path)
                        <div class="w-14 h-14 rounded-2xl bg-white border border-gray-200 flex items-center justify-center flex-shrink-0 p-1 shadow-sm overflow-hidden mt-0.5">
                            <img src="{{ asset('storage/' . $g->image_path) }}" alt="{{ $g->name }}" class="w-full h-full object-contain">
                        </div>
                    @else
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-lg flex-shrink-0 mt-0.5 shadow-sm
                            {{ $g->color === 'purple' ? 'bg-purple-100 text-purple-600' : '' }}
                            {{ $g->color === 'blue' ? 'bg-blue-100 text-blue-600' : '' }}
                            {{ $g->color === 'amber' ? 'bg-amber-100 text-amber-600' : '' }}
                            {{ $g->color === 'emerald' ? 'bg-emerald-100 text-emerald-600' : '' }}
                            {{ $g->color === 'pink' ? 'bg-pink-100 text-pink-600' : '' }}">
                            <i class="{{ $g->icon ?: 'fas fa-gift' }}"></i>
                        </div>
                    @endif

                    <!-- Metinler -->
                    <div class="flex-1 min-w-0 pr-4">
                        <div class="flex flex-wrap items-center gap-1.5">
                            @if($g->badge)
                                <span class="text-[10px] font-extrabold text-purple-700 bg-purple-100/60 px-1.5 py-0.2 rounded">{{ $g->badge }}</span>
                            @endif
                            @if($g->market_price)
                                <span class="text-[10px] text-gray-400 font-semibold line-through">{{ number_format($g->market_price, 0, ',', '.') }} TL</span>
                                <span class="text-[10px] text-emerald-700 font-black bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">0 TL Hediye</span>
                            @endif
                        </div>
                        <h4 class="font-extrabold text-xs text-gray-900 mt-1 leading-snug">{{ $g->name }}</h4>
                        @if($g->description)
                            <p class="text-[11px] text-gray-500 mt-0.5 leading-tight">{{ $g->description }}</p>
                        @endif
                        @if($g->external_url)
                            <div class="mt-1.5">
                                <a href="{{ $g->external_url }}" target="_blank" @click.stop
                                   class="inline-flex items-center gap-1 text-[10px] font-bold text-purple-700 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 border border-purple-200/80 px-2 py-0.5 rounded-lg transition-colors shadow-2xs">
                                    <i class="fas fa-external-link-alt text-[8px]"></i> Hediyeyi İncele / Ürün Linki
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Radio Circle -->
                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 mt-1"
                         :class="selectedGift === '{{ addslashes($g->name) }}' ? 'border-purple-600 bg-purple-600' : 'border-gray-300'">
                        <div class="w-2 h-2 rounded-full bg-white" x-show="selectedGift === '{{ addslashes($g->name) }}'"></div>
                    </div>

                </div>
            @endforeach
        </div>

    </div>
@endif
