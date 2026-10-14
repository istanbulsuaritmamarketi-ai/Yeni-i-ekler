@php
    $pSmall = !empty($siteSettings['quiz_product_small_id']) ? \App\Models\Product::find($siteSettings['quiz_product_small_id']) : null;
    $pMedium = !empty($siteSettings['quiz_product_medium_id']) ? \App\Models\Product::find($siteSettings['quiz_product_medium_id']) : null;
    $pLarge = !empty($siteSettings['quiz_product_large_id']) ? \App\Models\Product::find($siteSettings['quiz_product_large_id']) : null;
@endphp

<div class="bg-gradient-to-br from-blue-900 via-indigo-900 to-blue-950 text-white rounded-3xl p-6 sm:p-12 shadow-2xl border border-blue-800/80 my-12 relative overflow-hidden"
     x-data="{
        step: 1,
        familySize: 'medium',
        waterPressure: 'low',
        waterType: 'alkaline',
        matchedProduct: null,
        products: [
            {
                id: {{ $pMedium ? $pMedium->id : 1 }},
                name: '{{ $pMedium ? addslashes($pMedium->name) : '12 Aşamalı Pompalı Kapalı Kasa Su Arıtma Cihazı (Alkali + Mineral)' }}',
                tag: 'En Çok Tercih Edilen Model',
                price: '{{ $pMedium ? number_format($pMedium->price, 0, ',', '.') . ' TL' : '4.750 TL' }}',
                desc: '{{ $pMedium ? addslashes($pMedium->short_description ?: \Illuminate\Support\Str::limit(strip_tags($pMedium->description), 140)) : 'Alkali pH 8.5+ filtresi, sessiz hidrofor pompası ve NSF onaylı antibakteriyel tankı ile 4-6 kişilik aileler için ideal seçim.' }}',
                features: ['12 Aşamalı Filtrasyon', 'Sessiz Pompa', 'Alkali & Mineral Filtre', 'Ücretsiz Montaj'],
                link: '{{ $pMedium ? route('product.show', $pMedium->slug) : route('products.index') }}',
                image: '{{ $pMedium && $pMedium->image_path ? asset('storage/' . $pMedium->image_path) : '' }}'
            },
            {
                id: {{ $pSmall ? $pSmall->id : 2 }},
                name: '{{ $pSmall ? addslashes($pSmall->name) : '5 Aşamalı Tezgah Altı Klasik Su Arıtma Cihazı (Pompasız)' }}',
                tag: 'Ekonomik & Kompakt',
                price: '{{ $pSmall ? number_format($pSmall->price, 0, ',', '.') . ' TL' : '3.450 TL' }}',
                desc: '{{ $pSmall ? addslashes($pSmall->short_description ?: \Illuminate\Support\Str::limit(strip_tags($pSmall->description), 140)) : 'Elektrik gerektirmeyen, minimum yer kaplayan 1-3 kişilik çekirdek aileler için en verimli arıtma çözümü.' }}',
                features: ['5 Aşamalı Ters Ozmoz', 'Elektriksiz Tasarım', 'LG Membran Filtre', 'Ücretsiz Montaj'],
                link: '{{ $pSmall ? route('product.show', $pSmall->slug) : route('products.index') }}',
                image: '{{ $pSmall && $pSmall->image_path ? asset('storage/' . $pSmall->image_path) : '' }}'
            },
            {
                id: {{ $pLarge ? $pLarge->id : 3 }},
                name: '{{ $pLarge ? addslashes($pLarge->name) : 'Endüstriyel & Yüksek Kapasiteli Ofis Tipi Su Arıtma Sistemi' }}',
                tag: 'İş Yeri & Kalabalık Kullanım',
                price: '{{ $pLarge ? number_format($pLarge->price, 0, ',', '.') . ' TL' : '6.900 TL' }}',
                desc: '{{ $pLarge ? addslashes($pLarge->short_description ?: \Illuminate\Support\Str::limit(strip_tags($pLarge->description), 140)) : 'Günde 300 litreye kadar kesintisiz temiz su üreten, çay ocakları ve ofisler için ağır hizmet tipi arıtma sistemi.' }}',
                features: ['Günde 300 Litre Üretim', 'Çift Pompa', 'Büyük Paslanmaz Tank', 'Ücretsiz Montaj'],
                link: '{{ $pLarge ? route('product.show', $pLarge->slug) : route('products.index') }}',
                image: '{{ $pLarge && $pLarge->image_path ? asset('storage/' . $pLarge->image_path) : '' }}'
            }
        ],
        calculateResult() {
            if (this.familySize === 'large') {
                this.matchedProduct = this.products[2];
            } else if (this.waterPressure === 'high' && this.waterType === 'standard' && this.familySize === 'small') {
                this.matchedProduct = this.products[1];
            } else {
                this.matchedProduct = this.products[0];
            }
            this.step = 4;
        },
        resetQuiz() {
            this.step = 1;
            this.matchedProduct = null;
        }
     }">

    <!-- Dekoratif Arka Plan Işıkları -->
    <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 max-w-4xl mx-auto">
        
        <!-- Başlık & İlerleme Çubuğu -->
        <div class="text-center mb-8">
            <span class="inline-flex items-center gap-2 bg-blue-500/30 text-cyan-300 text-xs font-black px-4 py-1.5 rounded-full border border-blue-400/30 mb-3 shadow-inner">
                <i class="fas fa-magic"></i> Akıllı Cihaz Bulucu Sihirbazı
            </span>
            <h3 class="text-2xl sm:text-4xl font-black tracking-tight text-white">
                Evinize En Uygun Su Arıtma Cihazını <span class="text-cyan-400">1 Dakikada Bulun</span>
            </h3>
            <p class="text-blue-200/80 text-xs sm:text-sm mt-2 max-w-xl mx-auto">
                3 basit soruya yanıt verin, şebeke basıncınıza ve ailenize en uygun modeli randevulu ücretsiz montaj avantajıyla öğrenin.
            </p>

            <!-- Adım Göstergesi (Step Indicator) -->
            <div class="flex items-center justify-center gap-3 mt-6">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-all"
                     :class="step >= 1 ? 'bg-cyan-400 text-gray-950 shadow-lg shadow-cyan-400/50' : 'bg-blue-800 text-blue-300'">1</div>
                <div class="w-10 sm:w-16 h-1 rounded-full transition-all" :class="step >= 2 ? 'bg-cyan-400' : 'bg-blue-800'"></div>
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-all"
                     :class="step >= 2 ? 'bg-cyan-400 text-gray-950 shadow-lg shadow-cyan-400/50' : 'bg-blue-800 text-blue-300'">2</div>
                <div class="w-10 sm:w-16 h-1 rounded-full transition-all" :class="step >= 3 ? 'bg-cyan-400' : 'bg-blue-800'"></div>
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-all"
                     :class="step >= 3 ? 'bg-cyan-400 text-gray-950 shadow-lg shadow-cyan-400/50' : 'bg-blue-800 text-blue-300'">3</div>
            </div>
        </div>

        <!-- 1. ADIM: Kişi Sayısı & Kullanım Alanı -->
        <div x-show="step === 1" x-transition>
            <h4 class="text-center font-bold text-base sm:text-lg text-blue-100 mb-6">
                1. Cihaz nerede kullanılacak ve kaç kişi su tüketecek?
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <button type="button" @click="familySize = 'small'; step = 2"
                        class="p-6 bg-white/10 hover:bg-blue-600/60 border border-blue-400/30 hover:border-cyan-400 rounded-2xl text-center transition-all group flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-blue-500/20 flex items-center justify-center text-cyan-300 text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="font-extrabold text-sm text-white mb-1">1 - 3 Kişilik Ev</div>
                    <div class="text-xs text-blue-200/70">Çekirdek aile ve bekar evleri için kompakt modeller.</div>
                </button>

                <button type="button" @click="familySize = 'medium'; step = 2"
                        class="p-6 bg-white/10 hover:bg-blue-600/60 border border-blue-400/30 hover:border-cyan-400 rounded-2xl text-center transition-all group flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-blue-500/20 flex items-center justify-center text-cyan-300 text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="font-extrabold text-sm text-white mb-1">4 - 6 Kişilik Aile</div>
                    <div class="text-xs text-blue-200/70">Standart ev kullanımı ve yemek/çay için bol arıtılmış su.</div>
                </button>

                <button type="button" @click="familySize = 'large'; step = 2"
                        class="p-6 bg-white/10 hover:bg-blue-600/60 border border-blue-400/30 hover:border-cyan-400 rounded-2xl text-center transition-all group flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-blue-500/20 flex items-center justify-center text-cyan-300 text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="font-extrabold text-sm text-white mb-1">İş Yeri / Ofis / Kalabalık</div>
                    <div class="text-xs text-blue-200/70">Yoğun çay/kahve tüketimi olan işletmeler ve kalabalık evler.</div>
                </button>
            </div>
        </div>

        <!-- 2. ADIM: Su Basıncı & Kat Durumu -->
        <div x-show="step === 2" x-transition x-cloak>
            <h4 class="text-center font-bold text-base sm:text-lg text-blue-100 mb-6">
                2. Oturduğunuz binada şebeke su basıncı nasıl veya kaçıncı kattasınız?
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <button type="button" @click="waterPressure = 'high'; step = 3"
                        class="p-6 bg-white/10 hover:bg-blue-600/60 border border-blue-400/30 hover:border-cyan-400 rounded-2xl text-center transition-all group flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-blue-500/20 flex items-center justify-center text-cyan-300 text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div class="font-extrabold text-sm text-white mb-1">Normal / Yüksek Basınç</div>
                    <div class="text-xs text-blue-200/70">Giriş/Orta katlar, hidroforlu siteler (Elektriksiz pompasız cihaz uygundur).</div>
                </button>

                <button type="button" @click="waterPressure = 'low'; step = 3"
                        class="p-6 bg-white/10 hover:bg-blue-600/60 border border-blue-400/30 hover:border-cyan-400 rounded-2xl text-center transition-all group flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-blue-500/20 flex items-center justify-center text-cyan-300 text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="font-extrabold text-sm text-white mb-1">Düşük Basınç / Üst Kat</div>
                    <div class="text-xs text-blue-200/70">Üst katlar ve su tazyikinin az olduğu yerler (Pompalı cihaz şarttır).</div>
                </button>

                <button type="button" @click="waterPressure = 'unknown'; step = 3"
                        class="p-6 bg-white/10 hover:bg-blue-600/60 border border-blue-400/30 hover:border-cyan-400 rounded-2xl text-center transition-all group flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-blue-500/20 flex items-center justify-center text-cyan-300 text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div class="font-extrabold text-sm text-white mb-1">Emin Değilim / Bilmiyorum</div>
                    <div class="text-xs text-blue-200/70">Her ihtimale karşı garantili ve kesintisiz arıtma sağlayan modeller.</div>
                </button>
            </div>
            <div class="text-center mt-6">
                <button type="button" @click="step = 1" class="text-xs font-bold text-blue-300 hover:text-white underline">
                    <i class="fas fa-arrow-left mr-1"></i> Önceki Adıma Dön
                </button>
            </div>
        </div>

        <!-- 3. ADIM: Su Tercihi & Mineral -->
        <div x-show="step === 3" x-transition x-cloak>
            <h4 class="text-center font-bold text-base sm:text-lg text-blue-100 mb-6">
                3. Nasıl bir içme suyu lezzeti ve kalitesi arzu ediyorsunuz?
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-2xl mx-auto">
                <button type="button" @click="waterType = 'alkaline'; calculateResult()"
                        class="p-6 bg-white/10 hover:bg-blue-600/60 border border-blue-400/30 hover:border-cyan-400 rounded-2xl text-center transition-all group flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-blue-500/20 flex items-center justify-center text-cyan-300 text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-gem"></i>
                    </div>
                    <div class="font-extrabold text-sm text-white mb-1">Alkali &amp; Zengin Mineralli (pH 8.5+)</div>
                    <div class="text-xs text-blue-200/70">Kalsiyum, magnezyum ve alkali filtreli kaynak suyu lezzeti (Önerilen).</div>
                </button>

                <button type="button" @click="waterType = 'standard'; calculateResult()"
                        class="p-6 bg-white/10 hover:bg-blue-600/60 border border-blue-400/30 hover:border-cyan-400 rounded-2xl text-center transition-all group flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-blue-500/20 flex items-center justify-center text-cyan-300 text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-tint"></i>
                    </div>
                    <div class="font-extrabold text-sm text-white mb-1">Standart Saf &amp; Hafif İçim</div>
                    <div class="text-xs text-blue-200/70">Klasik 5 aşamalı kireç ve klordan %99.9 arındırılmış yumuşak su.</div>
                </button>
            </div>
            <div class="text-center mt-6">
                <button type="button" @click="step = 2" class="text-xs font-bold text-blue-300 hover:text-white underline">
                    <i class="fas fa-arrow-left mr-1"></i> Önceki Adıma Dön
                </button>
            </div>
        </div>

        <!-- 4. ADIM: SONUÇ EKRANI (MATCHED PRODUCT) -->
        <div x-show="step === 4" x-transition x-cloak>
            <div class="bg-white text-gray-900 rounded-3xl p-6 sm:p-8 shadow-2xl border border-gray-100 max-w-2xl mx-auto">
                <div class="flex items-center justify-between gap-4 mb-4 pb-3 border-b border-gray-100">
                    <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-800 text-xs font-black px-3.5 py-1.5 rounded-full">
                        <i class="fas fa-check-circle"></i> %100 Size En Uygun Cihaz
                    </span>
                    <span class="text-xs font-extrabold text-blue-600" x-text="matchedProduct?.tag"></span>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-6 my-4">
                    <div class="w-32 h-32 bg-blue-50 rounded-2xl p-3 flex items-center justify-center flex-shrink-0 overflow-hidden">
                        <template x-if="matchedProduct?.image">
                            <img :src="matchedProduct?.image" :alt="matchedProduct?.name" class="object-contain h-full w-full mix-blend-multiply">
                        </template>
                        <template x-if="!matchedProduct?.image">
                            <i class="fas fa-tint text-5xl text-blue-600"></i>
                        </template>
                    </div>
                    <div class="text-center sm:text-left flex-1">
                        <h4 class="text-lg font-black text-gray-900 leading-snug" x-text="matchedProduct?.name"></h4>
                        <p class="text-xs text-gray-500 mt-2 leading-relaxed" x-text="matchedProduct?.desc"></p>
                        
                        <div class="flex flex-wrap gap-1.5 mt-3 justify-center sm:justify-start">
                            <template x-for="feat in matchedProduct?.features" :key="feat">
                                <span class="bg-gray-100 text-gray-700 text-[10px] font-bold px-2 py-0.5 rounded-md" x-text="feat"></span>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Fiyat ve Sipariş Butonları -->
                <div class="pt-5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <div class="text-xs text-gray-400 font-bold">Kapıda Ödeme Fiyatı:</div>
                        <div class="text-2xl font-black text-blue-600" x-text="matchedProduct?.price"></div>
                        <div class="text-[10px] text-emerald-600 font-extrabold flex items-center"><i class="fas fa-check text-[9px] mr-1"></i> İstanbul İçi Montaj Ücretsiz</div>
                    </div>

                    <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                        <a :href="'https://wa.me/{{ $siteSettings['whatsapp'] ?? '905555555555' }}?text=' + encodeURIComponent('Merhaba, web sitenizdeki test sonucunda bana önerilen ' + matchedProduct?.name + ' cihazı için kapıda ödemeli ücretsiz montaj randevusu almak istiyorum.')"
                           target="_blank"
                           class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black px-4 py-3 rounded-xl shadow transition-colors">
                            <i class="fab fa-whatsapp text-sm"></i> WhatsApp ile Hemen Sipariş Ver
                        </a>
                        <template x-if="matchedProduct?.link">
                            <a :href="matchedProduct?.link" class="p-3 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl text-xs font-bold transition-colors" title="Ürün Detayı">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </template>
                        <button type="button" @click="resetQuiz()" class="p-3 text-gray-400 hover:text-gray-600 text-xs font-bold" title="Yeniden Başla">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
