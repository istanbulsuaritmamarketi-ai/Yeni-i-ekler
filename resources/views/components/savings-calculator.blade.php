<div class="bg-gradient-to-br from-slate-900 via-blue-950 to-indigo-950 text-white rounded-3xl p-6 sm:p-12 shadow-2xl border border-blue-800/80 my-14 relative overflow-hidden"
     x-data="{
        weeklyDamacana: 3,
        damacanaPrice: 110,
        devicePrice: 4500,
        filterYearlyCost: 1500,
        
        get yearlyDamacanaCost() {
            return this.weeklyDamacana * this.damacanaPrice * 52;
        },
        get monthlyDamacanaCost() {
            return Math.round(this.yearlyDamacanaCost / 12);
        },
        get yearlyNetSavings() {
            return Math.max(0, this.yearlyDamacanaCost - this.filterYearlyCost);
        },
        get threeYearSavings() {
            return (this.yearlyDamacanaCost * 3) - (this.devicePrice + (this.filterYearlyCost * 2));
        },
        get amortizationMonths() {
            if (this.monthlyDamacanaCost <= 0) return 0;
            return (this.devicePrice / this.monthlyDamacanaCost).toFixed(1);
        },
        get plasticSaved() {
            return this.weeklyDamacana * 52;
        },
        formatTL(num) {
            return new Intl.NumberFormat('tr-TR').format(Math.round(num)) + ' TL';
        }
     }">

    <!-- Dekoratif Arka Plan Efektleri -->
    <div class="absolute -right-20 -top-20 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -left-20 -bottom-20 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 max-w-5xl mx-auto">
        
        <!-- Başlık Bölümü -->
        <div class="text-center mb-10">
            <span class="inline-flex items-center gap-2 bg-emerald-500/20 text-emerald-300 text-xs font-black px-4 py-1.5 rounded-full border border-emerald-500/30 mb-3 shadow-inner">
                <i class="fas fa-coins text-emerald-400"></i> Akıllı Tasarruf Analizi
            </span>
            <h3 class="text-2xl sm:text-4xl font-black tracking-tight text-white leading-tight">
                Damacanaya Servet Ödemeyin: <span class="text-emerald-400">1 Yılda Ne Kadar Kazanırsınız?</span>
            </h3>
            <p class="text-blue-200/80 text-xs sm:text-sm mt-2 max-w-2xl mx-auto leading-relaxed">
                Damacana su fiyatları sürekli artarken, kendi arıtma cihazınızla hem sağlıklı suya kavuşun hem de aile bütçenizde binlerce lira tasarruf edin.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <!-- Sol: İnteraktif Slider Kontrolleri -->
            <div class="lg:col-span-6 bg-white/10 backdrop-blur-md rounded-3xl p-6 sm:p-8 border border-white/10 space-y-6">
                
                <!-- Slider 1: Haftalık Damacana -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label for="weeklyDamacanaInput" class="text-xs sm:text-sm font-extrabold text-blue-100 flex items-center gap-2">
                            <i class="fas fa-tint text-cyan-400"></i> Haftada Kaç Damacana Su Tüketiyorsunuz?
                        </label>
                        <span class="text-lg font-black text-cyan-300 font-mono bg-cyan-950/60 px-3 py-1 rounded-xl border border-cyan-800">
                            <span x-text="weeklyDamacana"></span> Damacana
                        </span>
                    </div>
                    <input id="weeklyDamacanaInput" 
                           aria-label="Haftalık Tüketilen Damacana Sayısı" 
                           type="range" min="1" max="10" step="1" x-model="weeklyDamacana"
                           class="w-full h-2.5 bg-blue-950 rounded-lg appearance-none cursor-pointer accent-cyan-400">
                    <div class="flex justify-between text-[10px] text-blue-300/60 mt-1">
                        <span>1 Adet (Çekirdek Aile)</span>
                        <span>5 Adet</span>
                        <span>10 Adet (İş Yeri / Kalabalık)</span>
                    </div>
                </div>

                <!-- Slider 2: Damacana Birim Fiyatı -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label for="damacanaPriceInput" class="text-xs sm:text-sm font-extrabold text-blue-100 flex items-center gap-2">
                            <i class="fas fa-tag text-emerald-400"></i> 1 Damacana Fiyatı Ne Kadar?
                        </label>
                        <span class="text-lg font-black text-emerald-300 font-mono bg-emerald-950/60 px-3 py-1 rounded-xl border border-emerald-800">
                            <span x-text="damacanaPrice"></span> TL
                        </span>
                    </div>
                    <input id="damacanaPriceInput" 
                           aria-label="1 Damacana Su Fiyatı" 
                           type="range" min="70" max="160" step="5" x-model="damacanaPrice"
                           class="w-full h-2.5 bg-blue-950 rounded-lg appearance-none cursor-pointer accent-emerald-400">
                    <div class="flex justify-between text-[10px] text-blue-300/60 mt-1">
                        <span>70 TL</span>
                        <span>110 TL (Ortalama)</span>
                        <span>160 TL</span>
                    </div>
                </div>

                <!-- Ekolojik Kazanım Rozeti -->
                <div class="p-4 bg-emerald-900/30 border border-emerald-500/30 rounded-2xl flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center flex-shrink-0 text-lg">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <div class="text-xs leading-relaxed text-emerald-100">
                        Yılda tam <strong class="text-white" x-text="plasticSaved"></strong> adet plastik damacana kullanımını engelleyerek doğayı korursunuz.
                    </div>
                </div>

            </div>

            <!-- Sağ: Canlı Tasarruf Kartı -->
            <div class="lg:col-span-6 bg-gradient-to-br from-white to-blue-50 text-gray-900 rounded-3xl p-6 sm:p-8 shadow-2xl border border-white">
                
                <div class="flex items-center justify-between gap-2 mb-6 pb-4 border-b border-gray-200/80">
                    <span class="text-xs font-black uppercase tracking-wider text-gray-500">Maliyet Karşılaştırması</span>
                    <span class="bg-emerald-100 text-emerald-800 text-xs font-black px-3 py-1 rounded-full flex items-center gap-1.5">
                        <i class="fas fa-arrow-trend-down text-emerald-600"></i> %80 Tasarruf
                    </span>
                </div>

                <!-- 2 Sütunlu Kıyaslama -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <!-- Damacana Masrafı -->
                    <div class="p-4 bg-red-50 rounded-2xl border border-red-100 text-center">
                        <span class="text-[11px] font-black text-red-700 uppercase block mb-1">Yıllık Damacana</span>
                        <div class="text-lg sm:text-xl font-black text-red-800 font-mono" x-text="formatTL(yearlyDamacanaCost)"></div>
                        <span class="text-[10px] text-gray-600 font-bold">Çöpe Giden Para</span>
                    </div>

                    <!-- Arıtma Filtre Masrafı -->
                    <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100 text-center">
                        <span class="text-[11px] font-black text-blue-700 uppercase block mb-1">Yıllık Arıtma Filtresi</span>
                        <div class="text-lg sm:text-xl font-black text-blue-800 font-mono" x-text="formatTL(filterYearlyCost)"></div>
                        <span class="text-[10px] text-gray-600 font-bold">Sınırsız Temiz Su</span>
                    </div>
                </div>

                <!-- Ana Net Kazanç Alanı -->
                <div class="p-5 bg-gradient-to-br from-emerald-500 to-teal-700 rounded-2xl text-white text-center shadow-lg mb-6 relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 text-white/10 text-7xl font-black pointer-events-none">₺</div>
                    <span class="text-xs uppercase font-extrabold tracking-wider text-emerald-100 block mb-1">
                        1 Yılda Cebinizde Kalan Net Kâr:
                    </span>
                    <div class="text-3xl sm:text-4xl font-black tracking-tight my-1 font-mono" x-text="formatTL(yearlyNetSavings)"></div>
                    <p class="text-xs text-emerald-100 mt-2 font-medium">
                        Cihaz kendini sadece <strong class="text-white bg-emerald-950/40 px-2 py-0.5 rounded-md" x-text="amortizationMonths + ' Ayda'"></strong> amorti eder!
                    </p>
                </div>

                <!-- 3 Yıllık Toplam Tasarruf -->
                <div class="text-center mb-6">
                    <span class="text-xs text-gray-500 font-medium">3 Yıllık Toplam Net Tasarrufunuz: </span>
                    <strong class="text-sm font-black text-emerald-600" x-text="formatTL(threeYearSavings)"></strong>
                </div>

                <!-- CTA Sipariş Butonu -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('products.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs sm:text-sm py-3.5 px-4 rounded-xl shadow-md transition-all">
                        <i class="fas fa-shopping-bag"></i> Cihazları İncele &amp; Sipariş Ver
                    </a>
                    <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905555555555' }}?text={{ urlencode('Merhaba, sitenizdeki Damacana Tasarruf Hesaplayıcısını inceledim. Yılda büyük kâr sağlayan uygun su arıtma cihazı için ücretsiz montaj randevusu almak istiyorum.') }}" target="_blank" class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs sm:text-sm py-3.5 px-4 rounded-xl shadow-md transition-all">
                        <i class="fab fa-whatsapp text-base"></i> WhatsApp Sipariş
                    </a>
                </div>

            </div>

        </div>

    </div>
</div>
