<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <i class="fas fa-bolt text-amber-500"></i> Site Hızlandırma &amp; Sistem Performans Merkezi
                </h2>
                <p class="text-xs text-gray-500 mt-1">Sitenin açılış hızını, önbelleklerini (Cache) ve sunucu kaynaklarını tek tıkla yönetin</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="https://pagespeed.web.dev/analysis?url=https%3A%2F%2Fistanbulsuaritmamarketi.com" target="_blank"
                   class="bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 font-bold text-xs px-4 py-2 rounded-2xl shadow-sm transition-all flex items-center gap-2">
                    <i class="fab fa-google text-blue-600"></i> Google PageSpeed Testi Yap <i class="fas fa-external-link-alt text-[10px]"></i>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Bildirimler -->
            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 text-emerald-800 rounded-r-2xl shadow-sm text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 text-red-800 rounded-r-2xl shadow-sm text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                    {{ session('error') }}
                </div>
            @endif

            <!-- 1. CANLI SİSTEM TEŞHİS KARTLARI -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <!-- PHP Sürümü -->
                <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-sm">
                    <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">PHP Motoru</div>
                    <div class="text-xl font-black text-gray-900 mt-1 flex items-center gap-2">
                        <i class="fab fa-php text-indigo-600"></i> v{{ $phpVersion }}
                    </div>
                    <div class="text-[11px] text-emerald-600 font-bold mt-1">Yüksek Performans</div>
                </div>

                <!-- OPcache Durumu -->
                <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-sm">
                    <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">OPcache (RAM Önbellek)</div>
                    <div class="text-xl font-black text-gray-900 mt-1 flex items-center gap-2">
                        @if($opcacheEnabled)
                            <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-emerald-700">Aktif</span>
                        @else
                            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                            <span class="text-amber-700">Pasif / Yerel</span>
                        @endif
                    </div>
                    <div class="text-[11px] text-gray-500 mt-1">cPanel PHP Seçici</div>
                </div>

                <!-- Rota & Config Önbelleği -->
                <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-sm">
                    <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Üretim Önbelleği</div>
                    <div class="text-xl font-black text-gray-900 mt-1 flex items-center gap-2">
                        @if($isRoutesCached && $isConfigCached)
                            <i class="fas fa-bolt text-emerald-500"></i>
                            <span class="text-emerald-700 text-base font-black">Ultra Hızlı</span>
                        @else
                            <i class="fas fa-code text-amber-500"></i>
                            <span class="text-amber-700 text-base font-bold">Geliştirici Modu</span>
                        @endif
                    </div>
                    <div class="text-[11px] text-gray-500 mt-1">{{ $isRoutesCached ? 'Rotalar Önbellekte' : 'Dinamik Çalışıyor' }}</div>
                </div>

                <!-- Bellek Limiti -->
                <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-sm">
                    <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Sunucu Bellek Limiti</div>
                    <div class="text-xl font-black text-gray-900 mt-1 flex items-center gap-2">
                        <i class="fas fa-memory text-purple-600"></i> {{ $memoryLimit }}
                    </div>
                    <div class="text-[11px] text-gray-500 mt-1">Zaman Sınırı: {{ $maxExecutionTime }}s</div>
                </div>
            </div>

            <!-- 2. HIZLANDIRMA & ÖNBELLEK KONTROL PANELİ -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-200/80 shadow-sm">
                <div class="border-b border-gray-100 pb-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 flex items-center gap-2">
                            <i class="fas fa-rocket text-amber-500"></i> 1-Tıkla Canlı Hızlandırma &amp; Önbellek Yönetimi
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Sitede değişiklik yaptıktan veya hız testi öncesinde bu butonları kullanabilirsiniz.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    
                    <!-- 1. FULL OPTİMİZASYON (AÇ / KAPAT DİNAMİK ANAHTARI) -->
                    @if($isRoutesCached && $isConfigCached)
                        <!-- Hızlandırma AÇIK Durumda -> Durdurma / Güncelleme Modu Butonu Çıkar -->
                        <form action="{{ route('admin.marketing.performance.clearCache') }}" method="POST" class="h-full">
                            @csrf
                            <input type="hidden" name="action" value="all_clear">
                            <div class="bg-gradient-to-br from-emerald-600 via-teal-700 to-emerald-900 rounded-2xl p-5 text-white h-full flex flex-col justify-between shadow-md border-2 border-emerald-400">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[11px] font-black uppercase tracking-wider bg-white text-emerald-800 px-2.5 py-1 rounded-full flex items-center gap-1.5 shadow-sm">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span> HIZLANDIRMA AKTİF
                                        </span>
                                        <i class="fas fa-check-circle text-2xl text-emerald-300"></i>
                                    </div>
                                    <h4 class="text-base font-black mb-1">Maksimum Hız Devrede</h4>
                                    <p class="text-xs text-emerald-100 leading-relaxed">
                                        Siteniz şu an RAM önbellekte ve 3-5 kat daha hızlı çalışıyor. Sitede yeni bir güncelleme veya kod değişikliği yapacaksanız hızlandırmayı geçici olarak buradan kapatabilirsiniz.
                                    </p>
                                </div>
                                <button type="submit" class="mt-4 w-full bg-red-500 hover:bg-red-600 text-white font-black text-xs py-3 px-4 rounded-xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2">
                                    <i class="fas fa-power-off"></i> Hızlandırmayı Durdur (Güncelleme Modu)
                                </button>
                            </div>
                        </form>
                    @else
                        <!-- Hızlandırma KAPALI Durumda -> Başlatma Butonu Çıkar -->
                        <form action="{{ route('admin.marketing.performance.clearCache') }}" method="POST" class="h-full">
                            @csrf
                            <input type="hidden" name="action" value="full_optimize">
                            <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-blue-950 rounded-2xl p-5 text-white h-full flex flex-col justify-between shadow-md border border-indigo-800/60">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[11px] font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/40 px-2.5 py-1 rounded-full flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-amber-400"></span> GÜNCELLEME MODU (KAPALI)
                                        </span>
                                        <i class="fas fa-tachometer-alt text-2xl opacity-80 text-amber-400"></i>
                                    </div>
                                    <h4 class="text-base font-black mb-1">Maksimum Hızlandırma Yap</h4>
                                    <p class="text-xs text-blue-200 leading-relaxed">
                                        Şu an anlık geliştirici modundasınız. Sitenizi canlı yayına alıp 3-5 kat hızlandırmak için önbellekleri RAM belleğe kilitleyin.
                                    </p>
                                </div>
                                <button type="submit" class="mt-4 w-full bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-black text-xs py-3.5 px-4 rounded-xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2">
                                    <i class="fas fa-bolt text-amber-300"></i> Maksimum Hızlandırmayı Başlat
                                </button>
                            </div>
                        </form>
                    @endif

                    <!-- 2. TÜM ÖNBELLEĞİ TEMİZLE -->
                    <form action="{{ route('admin.marketing.performance.clearCache') }}" method="POST" class="h-full">
                        @csrf
                        <input type="hidden" name="action" value="all_clear">
                        <div class="bg-gradient-to-br from-blue-900 to-indigo-950 rounded-2xl p-5 text-white h-full flex flex-col justify-between shadow-md">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-black uppercase tracking-wider bg-white/20 px-2.5 py-1 rounded-full">Sıfırlama</span>
                                    <i class="fas fa-broom text-2xl opacity-80 text-cyan-300"></i>
                                </div>
                                <h4 class="text-base font-black mb-1">Tüm Önbelleği Sıfırla</h4>
                                <p class="text-xs text-blue-200 leading-relaxed">
                                    Sitede yaptığınız bir değişiklik ekranda hemen görünmüyorsa veya tasarım güncellendiyse tüm hafızayı temizler.
                                </p>
                            </div>
                            <button type="submit" class="mt-4 w-full bg-cyan-400 hover:bg-cyan-300 text-gray-950 font-black text-xs py-3 px-4 rounded-xl shadow transition-all active:scale-95 flex items-center justify-center gap-2">
                                <i class="fas fa-sync-alt"></i> Önbelleği Komple Temizle
                            </button>
                        </div>
                    </form>

                    <!-- 3. GÖRÜNÜM & BLADE ÖNBELLEĞİ -->
                    <form action="{{ route('admin.marketing.performance.clearCache') }}" method="POST" class="h-full">
                        @csrf
                        <input type="hidden" name="action" value="view_clear">
                        <div class="bg-gradient-to-br from-purple-900 to-slate-900 rounded-2xl p-5 text-white h-full flex flex-col justify-between shadow-md">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-black uppercase tracking-wider bg-white/20 px-2.5 py-1 rounded-full">Şablonlar</span>
                                    <i class="fas fa-layer-group text-2xl opacity-80 text-purple-300"></i>
                                </div>
                                <h4 class="text-base font-black mb-1">Görünüm (HTML) Önbelleği</h4>
                                <p class="text-xs text-purple-200 leading-relaxed">
                                    Yalnızca derlenmiş HTML ve Blade şablon dosyalarını temizler. Sayfa tasarımları yeniden derlenir.
                                </p>
                            </div>
                            <button type="submit" class="mt-4 w-full bg-purple-500 hover:bg-purple-400 text-white font-black text-xs py-3 px-4 rounded-xl shadow transition-all active:scale-95 flex items-center justify-center gap-2">
                                <i class="fas fa-eraser"></i> Şablon Önbelleğini Sil
                            </button>
                        </div>
                    </form>

                    <!-- 4. TÜM ESKİ RESİMLERİ WEBP'YE DÖNÜŞTÜR & SIKIŞTIR (772 KB -> 25 KB) -->
                    <form action="{{ route('admin.marketing.performance.clearCache') }}" method="POST" class="h-full sm:col-span-2 lg:col-span-3">
                        @csrf
                        <input type="hidden" name="action" value="optimize_images">
                        <div class="bg-gradient-to-r from-amber-500 via-orange-600 to-red-600 rounded-2xl p-5 text-white shadow-md flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-2xl shrink-0">
                                    <i class="fas fa-file-image text-amber-200"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-base font-black">Mevcut Tüm Resimleri WebP'ye Dönüştür &amp; Sıkıştır</h4>
                                        <span class="bg-white text-orange-700 text-[10px] font-black px-2 py-0.5 rounded-full uppercase">Google PageSpeed Çözümü</span>
                                    </div>
                                    <p class="text-xs text-orange-100 mt-1 leading-relaxed max-w-2xl">
                                        Daha önce yüklenmiş olan tüm ağır PNG ve JPEG ürün fotoğraflarını tek tıkla tarar, en-boy oranını bozmadan modern <strong>WebP</strong> formatına dönüştürür ve boyutlarını <strong>772 KB'tan ~25 KB'a</strong> indirir.
                                    </p>
                                </div>
                            </div>
                            <button type="submit" class="bg-white hover:bg-orange-50 text-orange-700 font-black text-xs py-3.5 px-6 rounded-xl shadow-lg transition-all active:scale-95 whitespace-nowrap flex items-center gap-2 shrink-0">
                                <i class="fas fa-magic text-amber-500"></i> Tüm Resimleri Şimdi Optimize Et
                            </button>
                        </div>
                    </form>

                </div>
            </div>

            <!-- 3. AKTİF OPTİMİZASYON STANDARTLARI (KONTROL LİSTESİ) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Sitemizde Devrede Olan Optimizasyonlar -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-200/80 shadow-sm space-y-4">
                    <div class="border-b border-gray-100 pb-3">
                        <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                            <i class="fas fa-shield-check text-emerald-600"></i> Otomatik Aktif Hız Kalkanları
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Sistem kodlarında arka planda 7/24 çalışan hız kuralları</p>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-start gap-3 p-3 bg-emerald-50/50 rounded-2xl border border-emerald-100">
                            <i class="fas fa-check-circle text-emerald-600 text-base mt-0.5"></i>
                            <div>
                                <h5 class="text-xs font-black text-gray-900">GZIP / Deflate Sıkıştırma (Aktif)</h5>
                                <p class="text-[11px] text-gray-600 mt-0.5">HTML, CSS, JS ve SVG dosyaları sunucuda %80 sıkıştırılarak istemciye iletilir.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3 bg-emerald-50/50 rounded-2xl border border-emerald-100">
                            <i class="fas fa-check-circle text-emerald-600 text-base mt-0.5"></i>
                            <div>
                                <h5 class="text-xs font-black text-gray-900">1 Yıllık Tarayıcı Önbelleği (Browser Cache)</h5>
                                <p class="text-[11px] text-gray-600 mt-0.5">Resimler, logolar ve fontlar tekrar eden ziyaretçiler için 0 ms sürede hafızadan gelir.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3 bg-emerald-50/50 rounded-2xl border border-emerald-100">
                            <i class="fas fa-check-circle text-emerald-600 text-base mt-0.5"></i>
                            <div>
                                <h5 class="text-xs font-black text-gray-900">Bloklamayan Asenkron FontAwesome &amp; Fonts</h5>
                                <p class="text-[11px] text-gray-600 mt-0.5">İlk ekran boyaması (FCP) kilitlenmez, yazı tipleri ve ikonlar arka planda bağlanır.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3 bg-emerald-50/50 rounded-2xl border border-emerald-100">
                            <i class="fas fa-check-circle text-emerald-600 text-base mt-0.5"></i>
                            <div>
                                <h5 class="text-xs font-black text-gray-900">Vite 8 CSS &amp; JavaScript Minification</h5>
                                <p class="text-[11px] text-gray-600 mt-0.5">Tüm stil ve betikler tek parça, gereksiz boşluklardan arındırılmış olarak sunulur.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PageSpeed İpuçları & Hızı Düşüren Unsurlar -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-200/80 shadow-sm space-y-4">
                    <div class="border-b border-gray-100 pb-3">
                        <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                            <i class="fas fa-lightbulb text-amber-500"></i> Hızı Düşüren Şeyler &amp; İpuçları
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Ürün ve resim eklerken dikkat edilmesi gereken altın kurallar</p>
                    </div>

                    <div class="space-y-3">
                        <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100">
                            <div class="flex items-center gap-2 text-xs font-black text-gray-900">
                                <i class="fas fa-image text-purple-600"></i> 1. Ürün Fotoğraflarının Boyutu
                            </div>
                            <p class="text-[11px] text-gray-600 mt-1">
                                Telefonda çekilmiş 5 MB - 10 MB'lık ham fotoğrafları doğrudan yüklemek yerine, TinyPNG veya WebP formatında 100-300 KB civarında yüklemek açılışı uçurur.
                            </p>
                        </div>

                        <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100">
                            <div class="flex items-center gap-2 text-xs font-black text-gray-900">
                                <i class="fas fa-code text-blue-600"></i> 2. Aşırı Üçüncü Parti JS Kodları
                            </div>
                            <p class="text-[11px] text-gray-600 mt-1">
                                Canlı destek araçları (Tawk.to, WhatsApp botları) veya aşırı reklam pikselleri sayfa hızını 10-20 puan düşürebilir. Sadece gerekli olanları ekleyin.
                            </p>
                        </div>

                        <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100">
                            <div class="flex items-center gap-2 text-xs font-black text-gray-900">
                                <i class="fas fa-server text-emerald-600"></i> 3. cPanel OPcache Açıklığı
                            </div>
                            <p class="text-[11px] text-gray-600 mt-1">
                                Cenuta cPanel'de PHP Extensions bölümünden <code class="bg-gray-200 px-1 py-0.5 rounded text-gray-800 font-mono text-[10px]">opcache</code> modülünün işaretli olması şarttır.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
