<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <i class="fas fa-search-dollar text-emerald-600"></i> Google SEO, Meta &amp; Takip Kodları
                </h2>
                <p class="text-xs text-gray-500 mt-1">Google aramalarında üst sıralara çıkmak için meta başlıkları, kelime bulutu ve analitik kodlarını yönetin</p>
            </div>
            <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-800 text-xs font-black px-3.5 py-1.5 rounded-full border border-emerald-200 shadow-sm">
                <i class="fas fa-shield-alt"></i> SEO 100% Uyumlu
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 text-emerald-800 rounded-r-2xl shadow-sm text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.marketing.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- 1. GOOGLE SERP META BİLGİLERİ -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm space-y-6"
                     x-data="{
                        title: '{{ addslashes($settings['seo_title'] ?? config('app.name', 'Yiğit Ajans') . ' | Resmi Web Sitesi') }}',
                        desc: '{{ addslashes($settings['seo_description'] ?? 'En kaliteli ürünler, profesyonel hizmet ve müşteri memnuniyeti garantisi.') }}'
                     }">
                    
                    <div class="border-b border-gray-100 pb-4">
                        <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                            <i class="fab fa-google text-blue-600"></i> Google Arama Sonucu Meta Bilgileri
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">Google arama sonuçlarında kullanıcıların göreceği başlık ve tanıtım yazısı.</p>
                    </div>

                    <!-- Canlı SERP Önizlemesi -->
                    <div class="p-5 bg-gray-50 rounded-2xl border border-gray-200">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-2">
                            <i class="fab fa-google text-blue-500 mr-1"></i> Google Arama Sonucu Canlı Önizlemesi:
                        </span>
                        <div class="font-sans max-w-2xl">
                            <div class="flex items-center gap-2 text-xs text-gray-700 mb-1">
                                <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-[10px] font-bold"><i class="fas fa-globe"></i></span>
                                <span class="text-gray-800 font-medium truncate">{{ url('/') }}</span>
                            </div>
                            <h4 class="text-blue-800 hover:underline text-lg sm:text-xl font-medium leading-snug cursor-pointer mb-1 line-clamp-1" x-text="title || 'Site Başlığı Buraya Gelecek'"></h4>
                            <p class="text-gray-600 text-xs sm:text-sm leading-relaxed line-clamp-2" x-text="desc || 'Site açıklaması burada görünecek.'"></p>
                        </div>
                    </div>

                    <!-- Title & Description -->
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-bold text-gray-800">Site Meta Başlığı (SEO Title)</label>
                                <span class="text-xs font-bold text-gray-400">
                                    <span x-text="title.length"></span> / 60 Karakter
                                </span>
                            </div>
                            <input type="text" name="seo_title" x-model="title"
                                   class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-medium focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-bold text-gray-800">Site Meta Açıklaması (Meta Description)</label>
                                <span class="text-xs font-bold text-gray-400">
                                    <span x-text="desc.length"></span> / 160 Karakter
                                </span>
                            </div>
                            <textarea name="seo_description" rows="3" x-model="desc"
                                      class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-medium focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-800 mb-1">Anahtar Kelimeler (Meta Keywords)</label>
                                <input type="text" name="seo_keywords" value="{{ $settings['seo_keywords'] ?? '' }}"
                                       class="shadow-sm border border-gray-300 rounded-2xl w-full py-2 px-3 text-gray-900 text-xs focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-800 mb-1">Robots Arama İzni</label>
                                <select name="seo_robots" class="shadow-sm border border-gray-300 rounded-2xl w-full py-2 px-3 text-gray-900 text-xs font-bold focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="index, follow" {{ ($settings['seo_robots'] ?? 'index, follow') == 'index, follow' ? 'selected' : '' }}>index, follow (Açık - Önerilen)</option>
                                    <option value="noindex, nofollow" {{ ($settings['seo_robots'] ?? '') == 'noindex, nofollow' ? 'selected' : '' }}>noindex, nofollow (Gizle)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- 2. ANAHTAR KELİME BULUTU -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm space-y-4 mt-6"
                     x-data="{
                        tagsText: '{{ addslashes($settings['seo_footer_tags'] ?? 'su arıtma cihazı, istanbul su arıtma, su arıtma servisi, tezgah altı su arıtma, pompalı su arıtma, kapalı kasa su arıtma, filtre değişimi, su arıtma montajı, kapıda ödemeli su arıtma, ücretsiz montaj, orijinal filtre, lg membran, 5 aşamalı filtre, alkali filtre, su yumuşatma, bina girişi arıtma, kadıköy su arıtma, ümraniye su arıtma, esenyurt su arıtma, pendik su arıtma, beylikdüzü su arıtma, üsküdar su arıtma') }}',
                        newTag: '',
                        get tags() {
                            return this.tagsText.split(',').map(t => t.trim()).filter(t => t.length > 0);
                        },
                        addTag() {
                            let tag = this.newTag.trim();
                            if (tag && !this.tags.includes(tag)) {
                                this.tagsText = this.tagsText ? this.tagsText + ', ' + tag : tag;
                                this.newTag = '';
                            }
                        },
                        removeTag(tagToRemove) {
                            this.tagsText = this.tags.filter(t => t !== tagToRemove).join(', ');
                        },
                        addPreset(words) {
                            let current = this.tags;
                            let newWords = words.filter(w => !current.includes(w));
                            if (newWords.length > 0) {
                                this.tagsText = (this.tagsText ? this.tagsText + ', ' : '') + newWords.join(', ');
                            }
                        }
                     }">

                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 pb-3">
                        <div>
                            <h4 class="text-sm font-black text-gray-900 flex items-center gap-2">
                                <i class="fas fa-tags text-blue-600"></i> Sayfa Anahtar Kelime Bulutu (Keyword Density)
                            </h4>
                            <p class="text-xs text-gray-500">Footer alanında Google botları için arama yoğunluğu oluşturur.</p>
                        </div>
                        <span class="text-xs font-bold bg-blue-50 text-blue-700 px-3 py-1 rounded-xl">
                            Toplam <strong x-text="tags.length"></strong> Kelime
                        </span>
                    </div>

                    <div class="flex flex-wrap gap-2 p-4 bg-gray-50 rounded-2xl border border-gray-200/70 min-h-[60px]">
                        <template x-for="(tag, index) in tags" :key="index">
                            <span class="inline-flex items-center gap-1.5 bg-white text-gray-800 text-xs font-semibold px-3 py-1.5 rounded-xl border border-gray-200 shadow-sm">
                                <span x-text="tag"></span>
                                <button type="button" @click="removeTag(tag)" class="text-gray-400 hover:text-red-600 text-sm ml-1">&times;</button>
                            </span>
                        </template>
                    </div>

                    <div class="space-y-3">
                        <div class="flex gap-2">
                            <input type="text" x-model="newTag" @keydown.enter.prevent="addTag()" 
                                   placeholder="Yeni kelime yazıp ekleyin (Örn: Beşiktaş Su Arıtma)..." 
                                   class="shadow-sm border border-gray-300 rounded-2xl flex-1 py-2 px-3 text-xs">
                            <button type="button" @click="addTag()" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-2xl shadow-sm transition-colors flex items-center gap-1.5">
                                <i class="fas fa-plus"></i> Ekle
                            </button>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="font-bold text-gray-500">Hızlı Paket:</span>
                            <button type="button" @click="addPreset(['En İyi Su Arıtma', 'Tezgah Altı Arıtma Fiyatları', 'Alkali Su Arıtma Cihazı', 'Pompalı Arıtma Cihazı'])" 
                                    class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold rounded-lg border border-blue-200">
                                + Popüler Cihazlar
                            </button>
                            <button type="button" @click="addPreset(['Kadıköy Su Arıtma', 'Ümraniye Filtre Değişimi', 'Ataşehir Arıtma Servisi', 'Kartal Su Arıtma', 'Maltepe Su Arıtma'])" 
                                    class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold rounded-lg border border-emerald-200">
                                + İstanbul İlçeleri
                            </button>
                            <button type="button" @click="addPreset(['5li Filtre Seti', 'LG Membran Filtre Değişimi', 'Post Karbon Filtre', 'Sediment Tortu Filtresi'])" 
                                    class="px-2.5 py-1 bg-purple-50 hover:bg-purple-100 text-purple-700 font-semibold rounded-lg border border-purple-200">
                                + Filtreler
                            </button>
                        </div>

                        <input type="hidden" name="seo_footer_tags" :value="tagsText">
                    </div>

                </div>

                <!-- 3. GOOGLE MERCHANT CENTER (GOOGLE ALIŞVERİŞ) XML FEED -->
                <div class="bg-gradient-to-br from-amber-500/10 via-orange-500/5 to-white rounded-3xl p-6 sm:p-8 border border-amber-200/80 shadow-sm space-y-4 mt-6"
                     x-data="{ copiedFeed: false, feedUrl: '{{ url('/google-shopping.xml') }}' }">
                    
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-amber-200/60 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-lg shadow-md">
                                <i class="fas fa-shopping-bag"></i>
                            </span>
                            <div>
                                <h3 class="text-base font-black text-amber-950">
                                    Google Merchant Center (Google Alışveriş) XML Ürün Feed'i
                                </h3>
                                <p class="text-xs text-amber-800/80 mt-0.5">Ürünlerinizin Google Alışveriş sekmesinde ve Google reklamlarında çıkması için gereken canlı XML akışı.</p>
                            </div>
                        </div>
                        <a href="{{ url('/google-shopping.xml') }}" target="_blank" class="bg-amber-100 hover:bg-amber-200 text-amber-900 text-xs font-black px-3.5 py-1.5 rounded-full transition-colors flex items-center gap-1.5">
                            <i class="fas fa-external-link-alt"></i> Canlı XML'i İncele
                        </a>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-1.5">Google Merchant Center Feed URL'niz:</label>
                        <div class="flex gap-2">
                            <input type="text" readonly :value="feedUrl"
                                   class="shadow-sm border border-amber-300 rounded-2xl flex-1 py-2.5 px-3.5 text-xs font-mono font-bold bg-white text-gray-900 select-all">
                            <button type="button" @click="navigator.clipboard.writeText(feedUrl); copiedFeed = true; setTimeout(() => copiedFeed = false, 2500);"
                                    class="bg-amber-600 hover:bg-amber-700 text-white font-black text-xs px-5 py-2.5 rounded-2xl shadow-sm transition-all flex items-center gap-1.5 flex-shrink-0">
                                <i class="fas fa-copy" x-show="!copiedFeed"></i>
                                <i class="fas fa-check" x-show="copiedFeed"></i>
                                <span x-text="copiedFeed ? 'Kopyalandı!' : 'Linki Kopyala'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="p-4 bg-white/80 rounded-2xl border border-amber-100 text-xs text-gray-600 space-y-2">
                        <span class="font-black text-gray-900 block flex items-center gap-1.5"><i class="fas fa-info-circle text-amber-600"></i> Google Merchant Center'a Nasıl Eklenir?</span>
                        <ol class="list-decimal list-inside space-y-1 text-[11px] leading-relaxed">
                            <li><a href="https://merchants.google.com" target="_blank" class="text-blue-600 font-bold underline">merchants.google.com</a> adresine giderek oturum açın.</li>
                            <li>Sol menüden <strong>Ürünler &gt; Feed'ler &gt; Birincil Feed Ekle</strong> butonuna basın (Hedef Ülke: Türkiye, Dil: Türkçe).</li>
                            <li>Giriş yöntemi olarak <strong>Zamanlanmış Çekim (Scheduled Fetch)</strong> seçin ve yukarıdaki linki yapıştırın.</li>
                            <li>Google her 24 saatte bir ürünlerinizi, güncel fiyatlarınızı, stoklarınızı ve fotoğraflarınızı otomatik olarak çekecek ve Google Alışveriş'te yayınlayacaktır.</li>
                        </ol>
                    </div>

                </div>

                <!-- 4. DOĞRULAMA & ANALİTİK KODLARI -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm space-y-6 mt-6">
                    
                    <div class="border-b border-gray-100 pb-4">
                        <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                            <i class="fas fa-chart-line text-purple-600"></i> Arama Motoru Konsolları &amp; Analitik Kodları
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">Google Search Console, Google Analytics (GA4) ve Meta Pixel takip kodları.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Google Console Kodu</label>
                            <input type="text" name="google_verification" value="{{ $settings['google_verification'] ?? '' }}"
                                   class="shadow-sm border border-gray-300 rounded-2xl w-full py-2 px-3 text-gray-900 text-xs font-mono" placeholder="google-site-verification">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Yandex Webmaster Kodu</label>
                            <input type="text" name="yandex_verification" value="{{ $settings['yandex_verification'] ?? '' }}"
                                   class="shadow-sm border border-gray-300 rounded-2xl w-full py-2 px-3 text-gray-900 text-xs font-mono" placeholder="yandex-verification">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Bing / Microsoft Kodu</label>
                            <input type="text" name="bing_verification" value="{{ $settings['bing_verification'] ?? '' }}"
                                   class="shadow-sm border border-gray-300 rounded-2xl w-full py-2 px-3 text-gray-900 text-xs font-mono" placeholder="msvalidate.01">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Google Analytics ID (GA4)</label>
                            <input type="text" name="google_analytics_id" value="{{ $settings['google_analytics_id'] ?? '' }}"
                                   class="shadow-sm border border-gray-300 rounded-2xl w-full py-2 px-3 text-gray-900 text-xs font-mono" placeholder="G-XXXXXXXXXX">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Meta Pixel ID (Facebook Ads)</label>
                            <input type="text" name="meta_pixel_id" value="{{ $settings['meta_pixel_id'] ?? '' }}"
                                   class="shadow-sm border border-gray-300 rounded-2xl w-full py-2 px-3 text-gray-900 text-xs font-mono" placeholder="123456789012345">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Özel Header Scriptleri (&lt;head&gt; içine - Google Tag Manager 1. Kod)</label>
                        <textarea name="header_scripts" rows="3"
                                  class="shadow-sm border border-gray-300 rounded-2xl w-full py-2 px-3 text-gray-900 text-xs font-mono" placeholder="<!-- Google Tag Manager (head) veya özel takip kodları -->">{{ $settings['header_scripts'] ?? '' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Özel Body Scriptleri (&lt;body&gt; hemen başına - Google Tag Manager 2. Kod)</label>
                        <textarea name="body_scripts" rows="3"
                                  class="shadow-sm border border-gray-300 rounded-2xl w-full py-2 px-3 text-gray-900 text-xs font-mono" placeholder="<!-- Google Tag Manager (noscript) veya canlı chat kodları -->">{{ $settings['body_scripts'] ?? '' }}</textarea>
                    </div>

                </div>

                <div class="pt-6 flex justify-end">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs px-6 py-3 rounded-2xl shadow-lg transition-all transform active:scale-95 flex items-center gap-2">
                        <i class="fas fa-save"></i> SEO &amp; Takip Ayarlarını Kaydet
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>
