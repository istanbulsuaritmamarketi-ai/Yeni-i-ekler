<x-frontend-layout 
    :title="$product->name . ' — Tazecicekler.com'" 
    :description="Str::limit(strip_tags($product->short_description ?: $product->description), 155)"
    :image="$product->image_url"
    ogType="product">

    <!-- Schema.org JSON-LD -->
    @php
        $productSchema = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => $product->image_url,
            'description' => Str::limit(strip_tags($product->short_description ?: $product->description), 160),
            'sku' => 'FLW-' . $product->id,
            'brand' => [
                '@type' => 'Brand',
                'name' => 'Tazecicekler.com'
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => route('product.show', $product->slug),
                'priceCurrency' => 'TRY',
                'price' => (string) $product->effective_price,
                'availability' => $product->in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'itemCondition' => 'https://schema.org/NewCondition'
            ]
        ];
    @endphp
    <script type="application/ld+json">
    {!! json_encode($productSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    <!-- Breadcrumb -->
    <div class="bg-rose-50/50 py-3 border-b border-rose-100">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-12">
            <nav class="flex text-xs text-slate-500 font-bold overflow-x-auto whitespace-nowrap items-center gap-2">
                <a href="{{ route('home') }}" class="hover:text-rose-600">Ana Sayfa</a>
                <span class="text-rose-200">/</span>
                <a href="{{ route('products.index') }}" class="hover:text-rose-600">Çiçekler</a>
                <span class="text-rose-200">/</span>
                @if($product->category)
                    <a href="{{ route('category.show', $product->category->slug) }}" class="hover:text-rose-600">{{ $product->category->name }}</a>
                    <span class="text-rose-200">/</span>
                @endif
                <span class="text-slate-900 font-extrabold truncate">{{ $product->name }}</span>
            </nav>
        </div>
    </div>

    @php
        $galleryList = collect();
        if (!empty($product->image_url)) {
            $galleryList->push($product->image_url);
        }
        foreach ($product->images as $gal) {
            $galUrl = null;
            if (file_exists(public_path('storage/' . $gal->image_path))) {
                $galUrl = asset('storage/' . $gal->image_path);
            } elseif (file_exists(public_path('images/' . $gal->image_path))) {
                $galUrl = asset('images/' . $gal->image_path);
            } elseif (file_exists(public_path($gal->image_path))) {
                $galUrl = asset($gal->image_path);
            }
            if (!empty($galUrl) && !$galleryList->contains($galUrl)) {
                $galleryList->push($galUrl);
            }
        }
        if ($galleryList->isEmpty()) {
            $galleryList->push(asset('images/placeholder-flower.svg'));
        }
    @endphp

    <!-- ÇİÇEK DETAY ANA ALANI -->
    <div class="py-8 sm:py-12"
         x-data="{
             allImages: @js($galleryList->values()->all()),
             currentIndex: 0,
             touchStartX: 0,
             lightboxOpen: false,
             get mainImg() {
                 return this.allImages[this.currentIndex] || this.allImages[0];
             },
             nextImage() {
                 if (this.allImages.length <= 1) return;
                 this.currentIndex = (this.currentIndex + 1) % this.allImages.length;
             },
             prevImage() {
                 if (this.allImages.length <= 1) return;
                 this.currentIndex = (this.currentIndex - 1 + this.allImages.length) % this.allImages.length;
             },
             deliveryDate: '{{ now()->format('Y-m-d') }}',
             deliverySlot: '12:00 - 15:00 (Öğle)',
             noteModal: false,
             cardNote: '',
             senderName: '',
             isAnonymous: false,
             selectedGifts: [],
             basePrice: {{ (float) $product->effective_price }},
             activeNoteTab: 'ask',

             selectNote(text) {
                 this.cardNote = text;
                 this.noteModal = false;
             },
             toggleGift(id, price) {
                 if (this.selectedGifts.includes(id)) {
                     this.selectedGifts = this.selectedGifts.filter(g => g !== id);
                 } else {
                     this.selectedGifts.push(id);
                 }
             },
             getTotalPrice() {
                 let total = this.basePrice;
                 @foreach($upsellGifts as $g)
                     if (this.selectedGifts.includes({{ $g->id }})) {
                         total += {{ (float) ($g->price ?: 150) }};
                     }
                 @endforeach
                 return total;
             }
         }">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
                
                <!-- SOL SÜTUN: GÖRSEL GALERİSİ -->
                <div class="lg:col-span-6 space-y-4">
                    <!-- Büyük Görsel -->
                    <div class="relative aspect-[4/5] bg-rose-50 rounded-3xl overflow-hidden border border-rose-100 shadow-xl group select-none"
                         @touchstart="touchStartX = $event.touches[0].clientX"
                         @touchend="if ($event.changedTouches[0].clientX < touchStartX - 40) nextImage(); else if ($event.changedTouches[0].clientX > touchStartX + 40) prevImage();"
                         @keydown.window.arrow-left="prevImage()"
                         @keydown.window.arrow-right="nextImage()">
                        
                        <img :src="mainImg" 
                             alt="{{ $product->name }}" 
                             @click="lightboxOpen = true"
                             class="w-full h-full object-cover transition-all duration-300 cursor-zoom-in">
                        
                        <!-- Rozetler -->
                        <div class="absolute top-4 left-4 z-10 flex flex-col gap-2 pointer-events-none">
                            @if($product->custom_badge)
                                <span class="bg-rose-600 text-white text-xs font-black px-3 py-1.5 rounded-full shadow-md uppercase tracking-wider">
                                    {{ $product->custom_badge }}
                                </span>
                            @endif
                            <span class="bg-emerald-600 text-white text-xs font-black px-3 py-1.5 rounded-full shadow-md flex items-center gap-1.5">
                                <i class="fas fa-camera"></i> Fotoğraflı Onay
                            </span>
                        </div>

                        <!-- Fotoğraf Değiştirme Butonları (Önceki & Sonraki) -->
                        <template x-if="allImages.length > 1">
                            <div>
                                <!-- Sol / Önceki Butonu -->
                                <button type="button" 
                                        @click.stop="prevImage()" 
                                        aria-label="Önceki Fotoğraf"
                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/95 hover:bg-white text-slate-800 shadow-lg shadow-black/15 flex items-center justify-center transition-all duration-200 transform hover:scale-105 active:scale-90 z-20 border border-slate-200 cursor-pointer">
                                    <i class="fas fa-chevron-left text-sm sm:text-base"></i>
                                </button>

                                <!-- Sağ / Sonraki Butonu -->
                                <button type="button" 
                                        @click.stop="nextImage()" 
                                        aria-label="Sonraki Fotoğraf"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/95 hover:bg-white text-slate-800 shadow-lg shadow-black/15 flex items-center justify-center transition-all duration-200 transform hover:scale-105 active:scale-90 z-20 border border-slate-200 cursor-pointer">
                                    <i class="fas fa-chevron-right text-sm sm:text-base"></i>
                                </button>

                                <!-- Fotoğraf Sayacı / İndikatör -->
                                <div class="absolute bottom-4 right-4 z-20 bg-slate-900/75 backdrop-blur-md text-white text-[11px] font-black px-3 py-1 rounded-full shadow-md flex items-center gap-1.5 pointer-events-none">
                                    <i class="fas fa-images text-[10px] text-rose-300"></i>
                                    <span x-text="(currentIndex + 1) + ' / ' + allImages.length"></span>
                                </div>

                                <!-- Alt Nokta / Bullet İndikatörleri -->
                                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex items-center gap-1.5 bg-slate-900/40 backdrop-blur-xs px-2.5 py-1 rounded-full pointer-events-none">
                                    <template x-for="(img, idx) in allImages" :key="idx">
                                        <div class="rounded-full transition-all duration-300"
                                             :class="currentIndex === idx ? 'w-5 h-2 bg-white' : 'w-2 h-2 bg-white/50'"></div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Küçük Thumbnail'lar -->
                    <template x-if="allImages.length > 1">
                        <div class="flex items-center gap-3 overflow-x-auto no-scrollbar pb-2">
                            <template x-for="(imgUrl, idx) in allImages" :key="idx">
                                <button type="button" 
                                        @click="currentIndex = idx"
                                        class="w-20 h-20 rounded-2xl overflow-hidden border-2 transition-all shrink-0 cursor-pointer"
                                        :class="currentIndex === idx ? 'border-rose-600 ring-2 ring-rose-500/30 opacity-100 scale-102' : 'border-slate-200 opacity-60 hover:opacity-100'">
                                    <img :src="imgUrl" :alt="'Fotoğraf ' + (idx + 1)" class="w-full h-full object-cover">
                                </button>
                            </template>
                        </div>
                    </template>

                    <!-- Çiçek Güvence Kutusu -->
                    <div class="bg-white rounded-2xl p-4 border border-rose-100 shadow-2xs grid grid-cols-2 gap-3 text-xs">
                        <div class="flex items-center gap-2.5 text-slate-700">
                            <i class="fas fa-check-circle text-emerald-500 text-base"></i>
                            <span>%100 Taze İthal Çiçek</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-slate-700">
                            <i class="fas fa-truck-fast text-rose-500 text-base"></i>
                            <span>Aynı Gün Özel Çiçek Kuryesi</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-slate-700">
                            <i class="fas fa-envelope-open-text text-amber-500 text-base"></i>
                            <span>Mühürlü Zarfta Kart Notu</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-slate-700">
                            <i class="fas fa-camera text-blue-500 text-base"></i>
                            <span>Kurye Öncesi Fotoğraflı Onay</span>
                        </div>
                    </div>
                </div>

                <!-- SAĞ SÜTUN: BAŞLIK, FİYAT, TESLİMAT ZAMANLAYICI & AKSİYONLAR -->
                <div class="lg:col-span-6 space-y-6">
                    
                    <!-- Başlık & Kategori & Puan -->
                    <div>
                        <span class="text-xs font-black text-rose-600 uppercase tracking-widest">
                            {{ $product->category->name ?? 'Taze Çiçek Tasarımı' }}
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 leading-snug">
                            {{ $product->name }}
                        </h1>
                        <div class="flex items-center gap-3 mt-2 text-xs text-slate-500">
                            <div class="flex items-center text-amber-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <span class="font-bold text-slate-700 ml-1.5">5.0 (38 Değerlendirme)</span>
                            </div>
                            <span>•</span>
                            <span class="text-emerald-600 font-bold"><i class="fas fa-shield-alt"></i> Stokta Var</span>
                        </div>
                    </div>

                    <!-- Fiyat Bloğu -->
                    <div class="p-4 rounded-2xl bg-rose-50/70 border border-rose-100 flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold text-slate-500 block uppercase">Satış Fiyatı</span>
                            <div class="flex items-baseline gap-2">
                                @if($product->campaign_price)
                                    <span class="text-sm text-slate-400 line-through">₺{{ number_format($product->price, 0, ',', '.') }}</span>
                                    <span class="text-3xl font-black text-rose-600">₺{{ number_format($product->campaign_price, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-3xl font-black text-slate-900">₺{{ number_format($product->price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-block bg-rose-600 text-white text-[10px] font-black px-2.5 py-1 rounded-full uppercase">
                                Ücretsiz Kurye
                            </span>
                            <span class="text-[11px] text-slate-500 block mt-1">İstanbul İçi Aynı Gün</span>
                        </div>
                    </div>

                    <form action="{{ route('cart.add', $product->id) }}" method="POST" id="flowerOrderForm" class="space-y-6">
                        @csrf
                        <input type="hidden" name="delivery_date" :value="deliveryDate">
                        <input type="hidden" name="delivery_time_slot" :value="deliverySlot">
                        <input type="hidden" name="card_note" :value="cardNote">
                        <input type="hidden" name="sender_name" :value="senderName">
                        <input type="hidden" name="is_anonymous" :value="isAnonymous ? '1' : '0'">

                        <!-- TESLİMAT GÜNÜ & SAATİ SEÇİCİ -->
                        <div class="bg-white rounded-3xl p-5 border border-rose-100 shadow-xs space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-black text-slate-900 flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-rose-600"></i> Teslimat Tarihi ve Saati
                                </span>
                                <span class="text-[11px] text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-md">
                                    Aynı Gün Teslimat Açık
                                </span>
                            </div>

                            <!-- Gün Tabları -->
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" @click="deliveryDate = '{{ now()->format('Y-m-d') }}'"
                                        class="py-2.5 px-3 rounded-2xl text-center border-2 transition-all"
                                        :class="deliveryDate === '{{ now()->format('Y-m-d') }}' ? 'border-rose-600 bg-rose-50/70 text-rose-700 font-black ring-2 ring-rose-500/20' : 'border-slate-200 bg-white text-slate-700 font-bold hover:bg-slate-50'">
                                    <div class="text-[10px] uppercase text-rose-500">Bugün</div>
                                    <div class="text-xs mt-0.5">{{ now()->locale('tr')->isoFormat('D MMMM') }}</div>
                                </button>
                                <button type="button" @click="deliveryDate = '{{ now()->addDay()->format('Y-m-d') }}'"
                                        class="py-2.5 px-3 rounded-2xl text-center border-2 transition-all"
                                        :class="deliveryDate === '{{ now()->addDay()->format('Y-m-d') }}' ? 'border-rose-600 bg-rose-50/70 text-rose-700 font-black ring-2 ring-rose-500/20' : 'border-slate-200 bg-white text-slate-700 font-bold hover:bg-slate-50'">
                                    <div class="text-[10px] uppercase text-slate-400">Yarın</div>
                                    <div class="text-xs mt-0.5">{{ now()->addDay()->locale('tr')->isoFormat('D MMMM') }}</div>
                                </button>
                                <button type="button" @click="deliveryDate = '{{ now()->addDays(2)->format('Y-m-d') }}'"
                                        class="py-2.5 px-3 rounded-2xl text-center border-2 transition-all"
                                        :class="deliveryDate === '{{ now()->addDays(2)->format('Y-m-d') }}' ? 'border-rose-600 bg-rose-50/70 text-rose-700 font-black ring-2 ring-rose-500/20' : 'border-slate-200 bg-white text-slate-700 font-bold hover:bg-slate-50'">
                                    <div class="text-[10px] uppercase text-slate-400">{{ now()->addDays(2)->locale('tr')->isoFormat('dddd') }}</div>
                                    <div class="text-xs mt-0.5">{{ now()->addDays(2)->locale('tr')->isoFormat('D MMMM') }}</div>
                                </button>
                            </div>

                            <!-- Saat Dilimi Radyo Butonları -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 mb-2">Teslimat Saat Aralığı:</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" @click="deliverySlot = '09:00 - 12:00 (Sabah)'"
                                            class="p-2.5 rounded-xl border text-left text-xs font-bold transition flex items-center justify-between"
                                            :class="deliverySlot.includes('09:00 - 12:00') ? 'border-rose-600 bg-rose-50 text-rose-800' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                                        <span>09:00 - 12:00</span>
                                        <i class="fas fa-check text-rose-600 text-xs" x-show="deliverySlot.includes('09:00 - 12:00')"></i>
                                    </button>
                                    <button type="button" @click="deliverySlot = '12:00 - 15:00 (Öğle)'"
                                            class="p-2.5 rounded-xl border text-left text-xs font-bold transition flex items-center justify-between"
                                            :class="deliverySlot.includes('12:00 - 15:00') ? 'border-rose-600 bg-rose-50 text-rose-800' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                                        <span>12:00 - 15:00</span>
                                        <i class="fas fa-check text-rose-600 text-xs" x-show="deliverySlot.includes('12:00 - 15:00')"></i>
                                    </button>
                                    <button type="button" @click="deliverySlot = '15:00 - 18:00 (Öğleden Sonra)'"
                                            class="p-2.5 rounded-xl border text-left text-xs font-bold transition flex items-center justify-between"
                                            :class="deliverySlot.includes('15:00 - 18:00') ? 'border-rose-600 bg-rose-50 text-rose-800' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                                        <span>15:00 - 18:00</span>
                                        <i class="fas fa-check text-rose-600 text-xs" x-show="deliverySlot.includes('15:00 - 18:00')"></i>
                                    </button>
                                    <button type="button" @click="deliverySlot = '18:00 - 22:00 (Akşam)'"
                                            class="p-2.5 rounded-xl border text-left text-xs font-bold transition flex items-center justify-between"
                                            :class="deliverySlot.includes('18:00 - 22:00') ? 'border-rose-600 bg-rose-50 text-rose-800' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                                        <span>18:00 - 22:00</span>
                                        <i class="fas fa-check text-rose-600 text-xs" x-show="deliverySlot.includes('18:00 - 22:00')"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- ZARFLI KART NOTU ALANI -->
                        <div class="bg-rose-50/80 rounded-3xl p-5 border border-rose-200/80 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center text-sm shadow-xs">
                                        <i class="fas fa-envelope-open-text"></i>
                                    </span>
                                    <div>
                                        <h3 class="text-xs sm:text-sm font-black text-rose-950">Özel Çiçek Kart Notu</h3>
                                        <p class="text-[11px] text-rose-600">Mühürlü zarfta çiçeğinize iliştirilir</p>
                                    </div>
                                </div>
                                <button type="button" @click="noteModal = true" 
                                        class="px-3 py-1.5 bg-white hover:bg-rose-100 text-rose-700 border border-rose-300 rounded-xl text-xs font-extrabold shadow-2xs transition">
                                    <span x-text="cardNote ? 'Notu Düzenle' : 'Not Yaz / Şablon Seç'"></span>
                                </button>
                            </div>

                            <!-- Seçilen Not Önizlemesi -->
                            <div x-show="cardNote" class="bg-white p-3.5 rounded-2xl border border-rose-200 text-xs text-slate-700 italic relative">
                                <div class="text-[10px] text-rose-500 font-bold uppercase mb-1">Mühürlü Kart Mesajınız:</div>
                                <p x-text="cardNote"></p>
                                <button type="button" @click="cardNote = ''" class="absolute top-2 right-2 text-slate-400 hover:text-rose-600">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- BİRLİKTE GÖNDERİN (UPSELL ÇAPRAZ SATIŞ) -->
                        @if(isset($upsellGifts) && $upsellGifts->count() > 0)
                            <div class="bg-white rounded-3xl p-5 border border-rose-100 shadow-xs space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-black text-slate-900 flex items-center gap-1.5">
                                        <i class="fas fa-gift text-rose-600"></i> Birlikte İyi Gider (Ek Sürpriz)
                                    </span>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold">1-Tıkla Ekle</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    @foreach($upsellGifts as $gift)
                                        <label class="p-3 rounded-2xl border transition-all cursor-pointer flex items-center gap-3 relative select-none"
                                               :class="selectedGifts.includes({{ $gift->id }}) ? 'border-rose-600 bg-rose-50/60 ring-2 ring-rose-500/20' : 'border-slate-200 bg-slate-50/50 hover:bg-slate-50'">
                                            <input type="checkbox" name="upsell_products[]" value="{{ $gift->id }}"
                                                   class="hidden"
                                                   :checked="selectedGifts.includes({{ $gift->id }})"
                                                   @change="toggleGift({{ $gift->id }}, {{ (float) ($gift->price ?: 150) }})">
                                            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-lg shrink-0">
                                                <i class="{{ $gift->icon ?? 'fas fa-gift' }}"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="text-[11px] font-extrabold text-slate-900 truncate">{{ $gift->name }}</div>
                                                <div class="text-[11px] font-black text-rose-600">+₺{{ number_format($gift->price ?: 150, 0, ',', '.') }}</div>
                                            </div>
                                            <i class="fas fa-check-circle text-rose-600 absolute top-2 right-2 text-xs" x-show="selectedGifts.includes({{ $gift->id }})"></i>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- SİPARİŞ VER BUTONU (Tek & Sade) -->
                        <div class="pt-2">
                            <button type="submit" 
                                    class="w-full py-4 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl font-black text-base shadow-lg shadow-rose-600/25 transition-all transform hover:-translate-y-0.5 active:scale-98 flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-shopping-bag"></i>
                                <span>Sipariş Ver</span>
                                <span class="text-rose-200 text-sm font-normal">•</span>
                                <span class="text-white font-extrabold" x-text="'₺' + getTotalPrice().toLocaleString('tr-TR')"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ALT SEKMELER: ÖZELLİKLER & ÇİÇEK BAKIM REHBERİ -->
            <div class="mt-16 pt-10 border-t border-rose-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Buket Özellikleri -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-rose-100 shadow-2xs">
                        <h3 class="text-base font-black text-slate-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-rose-600"></i> Çiçek ve Buket Bilgisi
                        </h3>
                        <div class="prose prose-sm text-slate-600 leading-relaxed">
                            {!! $product->description !!}
                        </div>
                        @if($product->technical_features)
                            <div class="mt-6 pt-4 border-t border-slate-100">
                                <h4 class="text-xs font-black uppercase text-slate-500 mb-2">Teknik Ölçüler &amp; İçerik</h4>
                                <pre class="font-sans text-xs text-slate-700 whitespace-pre-wrap leading-relaxed">{{ $product->technical_features }}</pre>
                            </div>
                        @endif
                    </div>

                    <!-- Çiçek Bakım Kılavuzu -->
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-3xl p-6 sm:p-8 border border-emerald-100 shadow-2xs">
                        <h3 class="text-base font-black text-emerald-950 mb-4 flex items-center gap-2">
                            <i class="fas fa-seedling text-emerald-600"></i> Çiçek Bakım Kılavuzu (Ömrünü Uzatın)
                        </h3>
                        <div class="space-y-4 text-xs text-emerald-900">
                            <div class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-black shrink-0">1</span>
                                <div>
                                    <strong class="block text-emerald-950">Vazo Suyu &amp; Besin:</strong>
                                    Buket teslim alındıktan sonra temiz bir vazoya soğuk musluk suyu koyun ve çiçeğin yanında gelen özel besin tozunu suya ekleyin.
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-black shrink-0">2</span>
                                <div>
                                    <strong class="block text-emerald-950">Sapları 45° Açıyla Kesin:</strong>
                                    Her 2 günde bir sapların ucundan 1-2 cm verev (çapraz) keserek su çekme kanallarını açık tutun.
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-black shrink-0">3</span>
                                <div>
                                    <strong class="block text-emerald-950">Doğrudan Güneşten Koruyun:</strong>
                                    Çiçeğinizi kalorifer, klima rüzgarı veya yakıcı güneş ışığı yerine aydınlık ve serin bir ortamda konumlandırın.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BENZER ÇİÇEK TASARIMLARI -->
            @if(isset($relatedProducts) && $relatedProducts->count() > 0)
            <div class="mt-16">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6">
                    <h3 class="text-xl font-black text-slate-900">Bunu Beğenenler Bunlara da Baktı</h3>
                    <a href="{{ route('products.index') }}" class="text-xs font-bold text-rose-600 hover:underline">Tümünü Gör →</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5 sm:gap-4">
                    @foreach($relatedProducts as $rel)
                        <div class="bg-white rounded-3xl overflow-hidden border border-rose-100 shadow-2xs hover:shadow-lg transition flex flex-col group">
                            <a href="{{ route('product.show', $rel->slug) }}" class="aspect-[4/5] bg-rose-50 overflow-hidden block">
                                <img src="{{ $rel->image_url }}" alt="{{ $rel->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                            </a>
                            <div class="p-3 flex-1 flex flex-col justify-between">
                                <h4 class="text-xs font-bold text-slate-900 line-clamp-1 group-hover:text-rose-600">
                                    <a href="{{ route('product.show', $rel->slug) }}">{{ $rel->name }}</a>
                                </h4>
                                <div class="mt-2 text-sm font-black text-rose-600">
                                    ₺{{ number_format($rel->effective_price, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        <!-- ZARFLI KART NOTU SEÇİM MODALI -->
        <div x-show="noteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="noteModal" x-transition.opacity @click="noteModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-show="noteModal" x-transition class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-rose-100 p-6 sm:p-8">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-lg">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-black text-slate-900">Çiçek Kart Notu Ekle</h3>
                                <p class="text-xs text-slate-500">Mühürlü zarfta el yazısıyla yazılacak mesajınızı belirleyin</p>
                            </div>
                        </div>
                        <button type="button" @click="noteModal = false" class="text-slate-400 hover:text-slate-600 p-2">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <!-- Kendi Notunu Yaz -->
                    <div class="mt-4 space-y-3">
                        <label class="block text-xs font-extrabold text-slate-700">Mesajınız (Maks. 250 Karakter):</label>
                        <textarea x-model="cardNote" maxlength="250" rows="3"
                                  placeholder="Buraya sevgi dolu mesajınızı yazabilir veya aşağıdaki hazır şablonlardan tek tıkla seçebilirsiniz..."
                                  class="w-full p-3.5 rounded-2xl border border-rose-200 bg-rose-50/30 text-xs font-medium focus:bg-white focus:outline-none focus:border-rose-500"></textarea>
                        <div class="flex items-center justify-between text-[11px] text-slate-400">
                            <span x-text="(250 - cardNote.length) + ' karakter kaldı'"></span>
                            <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-600">
                                <input type="checkbox" x-model="isAnonymous" class="rounded text-rose-600 focus:ring-rose-500">
                                <span>İsmim Gizli Kalsın (Anonim Gönderim)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Hazır Şablonlar Tabı -->
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <h4 class="text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Hazır Mesaj Şablonları (Tek Tıkla Seçin):</h4>
                        
                        <div class="flex gap-2 overflow-x-auto no-scrollbar pb-2 text-xs font-bold">
                            @foreach($readyNotes as $categoryTitle => $messages)
                                <button type="button" @click="activeNoteTab = '{{ Str::slug($categoryTitle) }}'"
                                        class="px-3 py-1.5 rounded-xl transition shrink-0"
                                        :class="activeNoteTab === '{{ Str::slug($categoryTitle) }}' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                                    {{ $categoryTitle }}
                                </button>
                            @endforeach
                        </div>

                        <div class="mt-3 space-y-2 max-h-56 overflow-y-auto pr-1">
                            @foreach($readyNotes as $categoryTitle => $messages)
                                <div x-show="activeNoteTab === '{{ Str::slug($categoryTitle) }}'" class="space-y-2">
                                    @foreach($messages as $msg)
                                        <button type="button" @click="selectNote('{{ addslashes($msg) }}')"
                                                class="w-full text-left p-3 rounded-xl border border-slate-100 hover:border-rose-300 hover:bg-rose-50/50 text-xs text-slate-700 transition flex items-center justify-between group">
                                            <span class="line-clamp-2">"{{ $msg }}"</span>
                                            <span class="text-[10px] font-black text-rose-600 shrink-0 ml-2 group-hover:translate-x-0.5 transition-transform">Seç →</span>
                                        </button>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Butonlar -->
                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="noteModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900">
                            Kapat
                        </button>
                        <button type="button" @click="noteModal = false" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-black rounded-xl shadow-md transition">
                            Notu Kaydet
                        </button>
                    </div>
                </div>
            </div>
        <!-- TAM EKRAN FOTOĞRAF GÖRÜNTÜLEME MODALI (Lightbox) -->
        <div x-show="lightboxOpen" x-cloak 
             class="fixed inset-0 z-50 overflow-hidden flex items-center justify-center bg-slate-950/90 backdrop-blur-md"
             @keydown.escape.window="lightboxOpen = false">
            
            <!-- Kapat Butonu -->
            <button type="button" @click="lightboxOpen = false" 
                    aria-label="Kapat"
                    class="absolute top-4 right-4 z-50 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer">
                <i class="fas fa-times text-lg"></i>
            </button>

            <!-- Sol Ok -->
            <button type="button" x-show="allImages.length > 1" @click="prevImage()" 
                    aria-label="Önceki Fotoğraf"
                    class="absolute left-4 top-1/2 -translate-y-1/2 z-50 w-12 h-12 rounded-full bg-white/15 hover:bg-white/30 text-white flex items-center justify-center transition cursor-pointer">
                <i class="fas fa-chevron-left text-lg"></i>
            </button>

            <!-- Sağ Ok -->
            <button type="button" x-show="allImages.length > 1" @click="nextImage()" 
                    aria-label="Sonraki Fotoğraf"
                    class="absolute right-4 top-1/2 -translate-y-1/2 z-50 w-12 h-12 rounded-full bg-white/15 hover:bg-white/30 text-white flex items-center justify-center transition cursor-pointer">
                <i class="fas fa-chevron-right text-lg"></i>
            </button>

            <!-- Büyük Fotoğraf -->
            <div class="max-w-4xl max-h-[85vh] p-4 flex items-center justify-center">
                <img :src="mainImg" alt="{{ $product->name }}" class="max-w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl">
            </div>

            <!-- Sayaç -->
            <div x-show="allImages.length > 1" class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white/80 font-bold text-xs bg-black/50 px-3 py-1 rounded-full">
                <span x-text="(currentIndex + 1) + ' / ' + allImages.length"></span>
            </div>
        </div>

    </div>

</x-frontend-layout>
