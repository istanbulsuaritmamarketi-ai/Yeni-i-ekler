<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <i class="fas fa-robot text-theme-primary"></i> Otonom Site Kalite &amp; Buton Denetçisi
                </h2>
                <p class="text-xs text-gray-500 mt-1">Sitemap sayfalarını gezen, buton ve linkleri tıklayan ve geliştirici kod kasasını sunan otonom ajan</p>
            </div>
            
            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.siteAudit.index') }}" class="px-4 py-2.5 rounded-xl bg-theme-primary hover:bg-theme-primary-hover text-white text-xs font-black transition-all shadow-md flex items-center gap-2">
                    <i class="fas fa-redo-alt"></i>
                    <span>Tüm Butonları &amp; Sayfaları Yeniden Tara</span>
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $score = $audit['score'] ?? 100;
        $level = $audit['status_level'] ?? 'excellent';
        $badgeColor = match($level) {
            'excellent' => 'bg-emerald-500/20 text-emerald-300 border-emerald-400/30',
            'good' => 'bg-amber-500/20 text-amber-300 border-amber-400/30',
            default => 'bg-red-500/20 text-red-300 border-red-400/30',
        };
        $scoreBg = match($level) {
            'excellent' => 'text-emerald-400',
            'good' => 'text-amber-400',
            default => 'text-red-400',
        };
        $scoreDesc = match($level) {
            'excellent' => 'Sistem Mükemmel Durumda • Sıfır Kritik Hata',
            'good' => 'Site Yayına Uygun • Ufak Eksikler Var',
            default => 'Müdahale Gerekiyor • Kritik Eksikler Mevcut',
        };
        $urlAudit = $audit['url_audit'] ?? null;
    @endphp

    <div class="space-y-8" x-data="siteAuditApp()">
        
        <!-- ========================================== -->
        <!-- 1. EXECUTIVE SAĞLIK SKORU & ÖZET KARTLARI -->
        <!-- ========================================== -->
        <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-800 relative overflow-hidden">
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                
                <div class="flex items-center gap-6">
                    <!-- Büyük Skor Rozeti -->
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl bg-slate-900/90 border-2 border-slate-700 flex flex-col items-center justify-center shrink-0 shadow-inner">
                        <span class="text-3xl sm:text-4xl font-black {{ $scoreBg }} leading-none">%{{ $score }}</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Sağlık Puanı</span>
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black border {{ $badgeColor }}">
                                <span class="w-2 h-2 rounded-full bg-current animate-pulse"></span>
                                {{ $scoreDesc }}
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-white">
                            {{ config('app.name', 'Master Engine') }} Sistem Denetimi
                        </h1>
                        <p class="text-xs text-slate-400">
                            Son tarama: <span class="font-mono text-slate-300">{{ $audit['checked_at'] }}</span> • Toplam <strong class="text-white">{{ $audit['total_checks'] }}</strong> sistem kontrolü • <strong class="text-indigo-400">{{ $urlAudit['scanned_urls_count'] ?? 0 }} URL</strong> &amp; <strong class="text-emerald-400">{{ $urlAudit['total_buttons_found'] ?? 0 }} Buton</strong> tarandı.
                        </p>
                    </div>
                </div>

                <!-- 3 Özet Sayaç -->
                <div class="grid grid-cols-3 gap-3 shrink-0">
                    <button type="button" @click="mainTab = 'system'; systemSubTab = 'bugs'" class="bg-white/5 hover:bg-white/10 p-3.5 sm:p-4 rounded-2xl border border-white/10 text-center transition cursor-pointer">
                        <div class="text-2xl font-black {{ count($audit['bugs']) > 0 ? 'text-red-400' : 'text-emerald-400' }}">{{ count($audit['bugs']) }}</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Kritik Bug</div>
                    </button>

                    <button type="button" @click="mainTab = 'crawler'" class="bg-white/5 hover:bg-white/10 p-3.5 sm:p-4 rounded-2xl border border-white/10 text-center transition cursor-pointer">
                        <div class="text-2xl font-black {{ ($urlAudit['total_dummy'] ?? 0) > 0 ? 'text-amber-400' : 'text-emerald-400' }}">{{ $urlAudit['total_dummy'] ?? 0 }}</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Sahte (#) Buton</div>
                    </button>

                    <button type="button" @click="mainTab = 'snippets'" class="bg-white/5 hover:bg-white/10 p-3.5 sm:p-4 rounded-2xl border border-white/10 text-center transition cursor-pointer">
                        <div class="text-2xl font-black text-indigo-400" x-text="stickyNotes.length"></div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Yapışkan Not</div>
                    </button>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- 2. ANA SEKMELER (SİSTEM / CRAWLER / NOTLAR) -->
        <!-- ========================================== -->
        <div class="flex flex-wrap items-center bg-gray-100 p-1.5 rounded-2xl border border-gray-200 w-fit gap-1">
            <button type="button" 
                    @click="mainTab = 'system'" 
                    :class="mainTab === 'system' ? 'bg-slate-900 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                    class="px-5 py-2.5 rounded-xl text-xs font-black transition flex items-center gap-2">
                <i class="fas fa-shield-alt text-indigo-400"></i>
                <span>1. Sistem Sağlığı &amp; Ayarlar</span>
            </button>

            <button type="button" 
                    @click="mainTab = 'crawler'" 
                    :class="mainTab === 'crawler' ? 'bg-slate-900 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                    class="px-5 py-2.5 rounded-xl text-xs font-black transition flex items-center gap-2">
                <i class="fas fa-network-wired text-emerald-400"></i>
                <span>2. Derin URL &amp; Buton Taraması ({{ $urlAudit['scanned_urls_count'] ?? 0 }})</span>
            </button>

            <button type="button" 
                    @click="mainTab = 'snippets'" 
                    :class="mainTab === 'snippets' ? 'bg-slate-900 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                    class="px-5 py-2.5 rounded-xl text-xs font-black transition flex items-center gap-2">
                <i class="fas fa-terminal text-amber-400"></i>
                <span>3. ⚡ Kod Kasası &amp; Yapışkan Notlar</span>
            </button>
        </div>

        <!-- ================================================================= -->
        <!-- ANA SEKME 1: 📊 SİSTEM SAĞLIĞI & AYAR DENETİMLERİ                 -->
        <!-- ================================================================= -->
        <div x-show="mainTab === 'system'" x-transition:enter="transition ease-out duration-200" class="space-y-6">
            
            <!-- Alt Sekmeler (Kritik, Uyarı, Fikir, Geçenler) -->
            <div class="flex items-center bg-gray-200/70 p-1 rounded-xl w-fit">
                <button type="button" 
                        @click="systemSubTab = 'bugs'" 
                        :class="systemSubTab === 'bugs' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                        class="px-4 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fas fa-bug {{ count($audit['bugs']) > 0 ? 'text-red-500' : 'text-emerald-500' }}"></i>
                    <span>Kritik Hatalar ({{ count($audit['bugs']) }})</span>
                </button>

                <button type="button" 
                        @click="systemSubTab = 'warnings'" 
                        :class="systemSubTab === 'warnings' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                        class="px-4 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fas fa-exclamation-triangle text-amber-500"></i>
                    <span>Uyarılar ({{ count($audit['warnings']) }})</span>
                </button>

                <button type="button" 
                        @click="systemSubTab = 'ideas'" 
                        :class="systemSubTab === 'ideas' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                        class="px-4 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fas fa-lightbulb text-indigo-500"></i>
                    <span>Büyüme Tavsiyeleri ({{ count($audit['ideas']) }})</span>
                </button>

                <button type="button" 
                        @click="systemSubTab = 'passed'" 
                        :class="systemSubTab === 'passed' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                        class="px-4 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span>Geçen Kontroller ({{ count($audit['passed']) }})</span>
                </button>
            </div>

            <!-- 1.1 Kritik Hatalar -->
            <div x-show="systemSubTab === 'bugs'" class="space-y-3">
                @if(count($audit['bugs']) === 0)
                    <div class="bg-emerald-50 border border-emerald-200 rounded-3xl p-8 text-center space-y-3">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-2xl mx-auto shadow-md">
                            <i class="fas fa-shield-check"></i>
                        </div>
                        <h3 class="text-base font-black text-emerald-950">Harika! Sitede Sıfır Kritik Sistem Hatası</h3>
                        <p class="text-xs text-emerald-800 max-w-md mx-auto">
                            Tüm yasal KVKK sayfaları, iletişim numaraları ve temel rotalar eksiksiz çalışıyor.
                        </p>
                    </div>
                @else
                    @foreach($audit['bugs'] as $bug)
                        <div class="bg-white rounded-3xl p-5 border-l-4 border-l-red-500 border border-gray-200/80 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center text-lg shrink-0 mt-0.5">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                                <div>
                                    <h4 class="font-black text-gray-900 text-sm">{{ $bug['title'] }}</h4>
                                    <p class="text-xs text-gray-600 mt-0.5">{{ $bug['desc'] }}</p>
                                </div>
                            </div>

                            @if(!empty($bug['fix_url']))
                                <a href="{{ $bug['fix_url'] }}" class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs transition shrink-0 flex items-center gap-1.5 shadow-xs">
                                    <span>{{ $bug['fix_text'] ?? 'Düzelt' }}</span>
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- 1.2 Uyarılar -->
            <div x-show="systemSubTab === 'warnings'" class="space-y-3">
                @if(count($audit['warnings']) === 0)
                    <div class="bg-gray-50 border border-gray-200 rounded-3xl p-8 text-center space-y-2">
                        <i class="fas fa-check-double text-gray-400 text-2xl"></i>
                        <h3 class="text-sm font-black text-gray-700">Tebrikler, hiçbir uyarı veya eksik bulunmuyor!</h3>
                    </div>
                @else
                    @foreach($audit['warnings'] as $warning)
                        <div class="bg-white rounded-3xl p-5 border-l-4 border-l-amber-500 border border-gray-200/80 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg shrink-0 mt-0.5">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div>
                                    <h4 class="font-black text-gray-900 text-sm">{{ $warning['title'] }}</h4>
                                    <p class="text-xs text-gray-600 mt-0.5">{{ $warning['desc'] }}</p>
                                </div>
                            </div>

                            @if(!empty($warning['fix_url']))
                                <a href="{{ $warning['fix_url'] }}" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-black text-xs transition shrink-0 flex items-center gap-1.5 shadow-xs">
                                    <span>{{ $warning['fix_text'] ?? 'İncele' }}</span>
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- 1.3 Tavsiyeler -->
            <div x-show="systemSubTab === 'ideas'" class="space-y-4">
                <div class="bg-indigo-50/70 border border-indigo-200/80 rounded-3xl p-5 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-lg shrink-0">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-indigo-950 text-sm">Akıllı Büyüme &amp; Satış Tavsiyeleri</h4>
                        <p class="text-xs text-indigo-800 mt-0.5 leading-relaxed">
                            Bu öneriler teknik bir hata değildir. Ancak sitenize eklendiğinde Google sıralamasını, müşteri güvenini ve satış dönüşüm oranlarını doğrudan artıracak adımlardır.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($audit['ideas'] as $idea)
                        <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-xs flex flex-col justify-between group hover:shadow-md transition">
                            <div>
                                <div class="w-10 h-10 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center text-lg mb-3 shadow-xs">
                                    <i class="fas fa-rocket"></i>
                                </div>
                                <h4 class="font-black text-gray-900 text-base mb-1.5">{{ $idea['title'] }}</h4>
                                <p class="text-xs text-gray-600 leading-relaxed">{{ $idea['desc'] }}</p>
                            </div>

                            @if(!empty($idea['fix_url']))
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <a href="{{ $idea['fix_url'] }}" class="inline-flex items-center gap-1.5 text-xs font-black text-indigo-600 hover:text-indigo-800">
                                        <span>{{ $idea['fix_text'] ?? 'Uygula' }}</span>
                                        <i class="fas fa-chevron-right text-[10px]"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 1.4 Geçenler -->
            <div x-show="systemSubTab === 'passed'" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($audit['passed'] as $pass)
                        <div class="p-3.5 rounded-2xl bg-white border border-gray-200/80 shadow-2xs flex items-center gap-3 text-xs text-gray-800">
                            <i class="fas fa-check-circle text-emerald-500 text-base shrink-0"></i>
                            <span class="font-medium">{{ $pass }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- ================================================================= -->
        <!-- ANA SEKME 2: 🌐 DERİN SİTEMAP & BUTON SAĞLIK TARAMASI             -->
        <!-- ================================================================= -->
        <div x-show="mainTab === 'crawler'" x-transition:enter="transition ease-out duration-200" class="space-y-6">
            
            @if($urlAudit)
                <!-- 4 KPI Rozeti -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-xs text-center">
                        <div class="text-3xl font-black text-indigo-600">{{ $urlAudit['scanned_urls_count'] }}</div>
                        <div class="text-xs font-bold text-gray-500 uppercase mt-1">Taranan Sayfa</div>
                    </div>
                    <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-xs text-center">
                        <div class="text-3xl font-black text-slate-800">{{ $urlAudit['total_buttons_found'] }}</div>
                        <div class="text-xs font-bold text-gray-500 uppercase mt-1">Toplam Buton &amp; Link</div>
                    </div>
                    <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-xs text-center">
                        <div class="text-3xl font-black text-emerald-600">{{ $urlAudit['total_healthy'] }}</div>
                        <div class="text-xs font-bold text-gray-500 uppercase mt-1">Sağlıklı / Çalışan</div>
                    </div>
                    <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-xs text-center">
                        <div class="text-3xl font-black {{ $urlAudit['total_dummy'] > 0 ? 'text-amber-500' : 'text-gray-400' }}">{{ $urlAudit['total_dummy'] }}</div>
                        <div class="text-xs font-bold text-gray-500 uppercase mt-1">Sahte (#) Buton</div>
                    </div>
                </div>

                <!-- Taranan Sayfaların ve Butonlarının Detaylı Listesi -->
                <div class="bg-white rounded-3xl border border-gray-200/80 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-gray-50/50">
                        <div>
                            <h3 class="font-black text-gray-900 text-sm flex items-center gap-2">
                                <i class="fas fa-list-check text-theme-primary"></i> Taranan Rotalar ve Sayfa İçi Buton Raporu
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">Her sayfa için HTTP durum kodu, açılış süresi ve sayfadaki butonların analizi</p>
                        </div>

                        <div class="relative w-full sm:w-64">
                            <input type="text" x-model="crawlerSearch" placeholder="Sayfa veya rota ara..." 
                                   class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 pl-8 pr-3 py-1.5">
                            <i class="fas fa-search text-gray-400 text-xs absolute left-2.5 top-2.5"></i>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach($urlAudit['pages'] as $idx => $page)
                            <div class="p-5 hover:bg-gray-50/60 transition" 
                                 x-data="{ openButtons: false }"
                                 x-show="!crawlerSearch || '{{ strtolower($page['url']) }}'.includes(crawlerSearch.toLowerCase()) || '{{ strtolower($page['label']) }}'.includes(crawlerSearch.toLowerCase())">
                                
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="space-y-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider {{ $page['status_code'] == 200 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $page['status_code'] }} OK
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700">
                                                {{ $page['category'] }}
                                            </span>
                                            <span class="text-xs font-mono text-gray-400">
                                                ⚡ {{ $page['time_ms'] }}ms
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <h4 class="font-black text-gray-900 text-sm">{{ $page['label'] }}</h4>
                                            <a href="{{ $page['url'] }}" target="_blank" class="text-xs font-mono text-indigo-600 hover:underline flex items-center gap-1">
                                                <span>{{ $page['url'] }}</span>
                                                <i class="fas fa-external-link-alt text-[9px]"></i>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 shrink-0">
                                        <div class="text-right">
                                            <div class="text-xs font-black text-gray-800">
                                                {{ $page['analysis']['total'] }} Buton / Link
                                            </div>
                                            <div class="text-[11px] {{ $page['analysis']['dummy_count'] > 0 ? 'text-amber-600 font-bold' : 'text-emerald-600' }}">
                                                {{ $page['analysis']['healthy_count'] }} Sağlıklı • {{ $page['analysis']['dummy_count'] }} Sahte (#)
                                            </div>
                                        </div>

                                        <button type="button" @click="openButtons = !openButtons" 
                                                class="px-3 py-1.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-100 transition flex items-center gap-1.5">
                                            <span x-text="openButtons ? 'Kapat' : 'İncele'"></span>
                                            <i class="fas fa-chevron-down text-[10px] transition-transform" :class="openButtons ? 'rotate-180' : ''"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Sayfa İçi Buton Listesi (Akordeon) -->
                                <div x-show="openButtons" x-transition class="mt-4 pt-4 border-t border-gray-100 space-y-3">
                                    <div class="text-xs font-bold text-gray-600 flex items-center gap-1.5">
                                        <i class="fas fa-fingerprint text-indigo-500"></i>
                                        <span>Sayfada Ayrıştırılan Buton ve Linkler (Örnek İlk 20 Adet):</span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                                        @foreach($page['analysis']['items'] as $item)
                                            <div class="p-2.5 rounded-xl border {{ $item['status'] === 'warning' ? 'bg-amber-50/60 border-amber-200 text-amber-950' : ($item['status'] === 'danger' ? 'bg-red-50 border-red-200 text-red-950' : 'bg-gray-50/80 border-gray-200 text-gray-800') }} flex items-start justify-between gap-2">
                                                <div class="space-y-0.5">
                                                    <div class="flex items-center gap-1.5 font-bold">
                                                        @if($item['type'] === 'whatsapp')
                                                            <i class="fab fa-whatsapp text-emerald-500 text-sm"></i>
                                                        @elseif($item['type'] === 'tel')
                                                            <i class="fas fa-phone-alt text-blue-500 text-xs"></i>
                                                        @elseif($item['type'] === 'dummy_hash')
                                                            <i class="fas fa-exclamation-triangle text-amber-500 text-xs"></i>
                                                        @elseif($item['type'] === 'form_submit')
                                                            <i class="fas fa-paper-plane text-purple-500 text-xs"></i>
                                                        @else
                                                            <i class="fas fa-link text-gray-400 text-xs"></i>
                                                        @endif
                                                        <span class="truncate max-w-[200px]">{{ $item['text'] }}</span>
                                                    </div>
                                                    <p class="text-[10px] text-gray-500 font-mono truncate max-w-[240px]">{{ $item['target'] }}</p>
                                                    <p class="text-[10px] {{ $item['status'] === 'warning' ? 'text-amber-700 font-medium' : 'text-gray-400' }}">{{ $item['note'] }}</p>
                                                </div>

                                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase shrink-0 {{ $item['status'] === 'warning' ? 'bg-amber-200 text-amber-800' : ($item['status'] === 'danger' ? 'bg-red-200 text-red-800' : 'bg-emerald-100 text-emerald-800') }}">
                                                    {{ $item['type'] }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        <!-- ================================================================= -->
        <!-- ANA SEKME 3: ⚡ GELİŞTİRİCİ KOD KASASI & YAPIŞKAN NOTLAR          -->
        <!-- ================================================================= -->
        <div x-show="mainTab === 'snippets'" x-transition:enter="transition ease-out duration-200" class="space-y-8">
            
            <!-- 3.1: 1-TIKLA KOPYALA KOD KASASI (DEVELOPER SNIPPETS) -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                            <i class="fas fa-bolt text-amber-500"></i> 1-Tıkla Kopyalanabilir Geliştirici Kod Kasası
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">Ezberlemek zorunda kalmadan tek tıkla panoya kopyalayabileceğiniz hazır Cenuta, Git ve Artisan komutları</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    
                    <!-- Kart 1: Cenuta Canlı Dağıtım -->
                    <div class="bg-white rounded-3xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between group hover:border-indigo-300 transition">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-[10px] font-black uppercase">Cenuta cPanel</span>
                                <i class="fab fa-git-alt text-gray-400"></i>
                            </div>
                            <h4 class="font-black text-gray-900 text-sm">Canlı Projeyi Çek &amp; Önbellek Sıfırla</h4>
                            <p class="text-xs text-gray-500">Git'ten son master commit'leri çeker ve web sitesini anında günceller.</p>
                            <div class="bg-slate-900 rounded-xl p-3 font-mono text-[11px] text-amber-300 break-all select-all">
                                git pull origin main &amp;&amp; php artisan optimize:clear
                            </div>
                        </div>
                        <button type="button" @click="copySnippet('git pull origin main && php artisan optimize:clear')" 
                                class="mt-4 w-full py-2 rounded-xl bg-slate-100 hover:bg-indigo-600 hover:text-white text-gray-700 text-xs font-black transition flex items-center justify-center gap-1.5">
                            <i class="fas fa-copy"></i>
                            <span x-text="copiedText === 'git pull origin main && php artisan optimize:clear' ? '✓ Kopyalandı!' : 'Komutu Kopyala'"></span>
                        </button>
                    </div>

                    <!-- Kart 2: Otonom Kalite Botu -->
                    <div class="bg-white rounded-3xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between group hover:border-emerald-300 transition">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase">Otonom Bot</span>
                                <i class="fas fa-robot text-gray-400"></i>
                            </div>
                            <h4 class="font-black text-gray-900 text-sm">Terminalden Buton &amp; Kalite Taraması</h4>
                            <p class="text-xs text-gray-500">Tüm sitemap URL'lerini ve butonlarını terminal ekranında anında denetler.</p>
                            <div class="bg-slate-900 rounded-xl p-3 font-mono text-[11px] text-emerald-400 break-all select-all">
                                php artisan site:audit
                            </div>
                        </div>
                        <button type="button" @click="copySnippet('php artisan site:audit')" 
                                class="mt-4 w-full py-2 rounded-xl bg-slate-100 hover:bg-emerald-600 hover:text-white text-gray-700 text-xs font-black transition flex items-center justify-center gap-1.5">
                            <i class="fas fa-copy"></i>
                            <span x-text="copiedText === 'php artisan site:audit' ? '✓ Kopyalandı!' : 'Komutu Kopyala'"></span>
                        </button>
                    </div>

                    <!-- Kart 3: Canlı Veritabanı Migration -->
                    <div class="bg-white rounded-3xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between group hover:border-purple-300 transition">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 text-[10px] font-black uppercase">Veritabanı</span>
                                <i class="fas fa-database text-gray-400"></i>
                            </div>
                            <h4 class="font-black text-gray-900 text-sm">Canlıda Veritabanı Tablolarını Güncelle</h4>
                            <p class="text-xs text-gray-500">Cenuta üzerinde yeni eklenen tabloları onay beklemeden doğrudan uygular.</p>
                            <div class="bg-slate-900 rounded-xl p-3 font-mono text-[11px] text-purple-300 break-all select-all">
                                php artisan migrate --force
                            </div>
                        </div>
                        <button type="button" @click="copySnippet('php artisan migrate --force')" 
                                class="mt-4 w-full py-2 rounded-xl bg-slate-100 hover:bg-purple-600 hover:text-white text-gray-700 text-xs font-black transition flex items-center justify-center gap-1.5">
                            <i class="fas fa-copy"></i>
                            <span x-text="copiedText === 'php artisan migrate --force' ? '✓ Kopyalandı!' : 'Komutu Kopyala'"></span>
                        </button>
                    </div>

                    <!-- Kart 4: Müşteri Kaydını Kapat (.env) -->
                    <div class="bg-white rounded-3xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between group hover:border-amber-300 transition">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 text-[10px] font-black uppercase">.ENV Anahtarı</span>
                                <i class="fas fa-user-lock text-gray-400"></i>
                            </div>
                            <h4 class="font-black text-gray-900 text-sm">Müşteri Kaydını Kapat (Kurumsal/İnşaat)</h4>
                            <p class="text-xs text-gray-500">Halka açık kayıt olmayı engeller, header'daki giriş butonunu gizler.</p>
                            <div class="bg-slate-900 rounded-xl p-3 font-mono text-[11px] text-amber-300 break-all select-all">
                                ENABLE_CUSTOMER_REGISTRATION=false
                            </div>
                        </div>
                        <button type="button" @click="copySnippet('ENABLE_CUSTOMER_REGISTRATION=false')" 
                                class="mt-4 w-full py-2 rounded-xl bg-slate-100 hover:bg-amber-600 hover:text-white text-gray-700 text-xs font-black transition flex items-center justify-center gap-1.5">
                            <i class="fas fa-copy"></i>
                            <span x-text="copiedText === 'ENABLE_CUSTOMER_REGISTRATION=false' ? '✓ Kopyalandı!' : 'Anahtarı Kopyala'"></span>
                        </button>
                    </div>

                    <!-- Kart 5: Sadece Seçilen İlçeleri Aç (.env) -->
                    <div class="bg-white rounded-3xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between group hover:border-blue-300 transition">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-[10px] font-black uppercase">.ENV Anahtarı</span>
                                <i class="fas fa-map-marker-alt text-gray-400"></i>
                            </div>
                            <h4 class="font-black text-gray-900 text-sm">Özel İlçe Seçimi (Yerel SEO)</h4>
                            <p class="text-xs text-gray-500">39 ilçe yerine yalnızca hizmet verdiğiniz bölgeleri aktif eder.</p>
                            <div class="bg-slate-900 rounded-xl p-3 font-mono text-[11px] text-blue-300 break-all select-all">
                                ACTIVE_DISTRICTS=kadikoy,umraniye,uskudar
                            </div>
                        </div>
                        <button type="button" @click="copySnippet('ACTIVE_DISTRICTS=kadikoy,umraniye,uskudar')" 
                                class="mt-4 w-full py-2 rounded-xl bg-slate-100 hover:bg-blue-600 hover:text-white text-gray-700 text-xs font-black transition flex items-center justify-center gap-1.5">
                            <i class="fas fa-copy"></i>
                            <span x-text="copiedText === 'ACTIVE_DISTRICTS=kadikoy,umraniye,uskudar' ? '✓ Kopyalandı!' : 'Anahtarı Kopyala'"></span>
                        </button>
                    </div>

                    <!-- Kart 6: Frontend Derleme (Vite) -->
                    <div class="bg-white rounded-3xl p-5 border border-gray-200/80 shadow-xs flex flex-col justify-between group hover:border-rose-300 transition">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 text-[10px] font-black uppercase">Frontend / Vite</span>
                                <i class="fab fa-npm text-gray-400"></i>
                            </div>
                            <h4 class="font-black text-gray-900 text-sm">CSS &amp; JS Asset'lerini Derle</h4>
                            <p class="text-xs text-gray-500">Tailwind ve Vite dosyalarını üretime hazır optimize edilmiş hale getirir.</p>
                            <div class="bg-slate-900 rounded-xl p-3 font-mono text-[11px] text-rose-300 break-all select-all">
                                npm run build
                            </div>
                        </div>
                        <button type="button" @click="copySnippet('npm run build')" 
                                class="mt-4 w-full py-2 rounded-xl bg-slate-100 hover:bg-rose-600 hover:text-white text-gray-700 text-xs font-black transition flex items-center justify-center gap-1.5">
                            <i class="fas fa-copy"></i>
                            <span x-text="copiedText === 'npm run build' ? '✓ Kopyalandı!' : 'Komutu Kopyala'"></span>
                        </button>
                    </div>

                </div>
            </div>

            <!-- 3.2: BANA ÖZEL YAPIŞKAN NOT DEFTERİ (STICKY NOTES) -->
            <div class="space-y-4 pt-6 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                            <i class="fas fa-sticky-note text-amber-500"></i> Yiğit'e Özel Yapışkan Notlar (Sticky Notes)
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">Tarayıcınızın hafızasında (localStorage) saklanır. Sunucu veya Laragon kapansa bile asla silinmez.</p>
                    </div>

                    <button type="button" @click="openNoteModal = true" 
                            class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-black text-xs transition shadow-xs flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        <span>Yeni Yapışkan Not Ekle</span>
                    </button>
                </div>

                <!-- Not Kartları Listesi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="(note, index) in stickyNotes" :key="note.id">
                        <div class="rounded-3xl p-5 shadow-sm border flex flex-col justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-md"
                             :class="{
                                 'bg-amber-100/90 border-amber-300 text-amber-950': note.color === 'yellow',
                                 'bg-blue-100/90 border-blue-300 text-blue-950': note.color === 'blue',
                                 'bg-emerald-100/90 border-emerald-300 text-emerald-950': note.color === 'green',
                                 'bg-purple-100/90 border-purple-300 text-purple-950': note.color === 'purple',
                                 'bg-rose-100/90 border-rose-300 text-rose-950': note.color === 'pink'
                             }">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-black text-sm" x-text="note.title"></h4>
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="copySnippet(note.content)" title="İçeriği Kopyala" class="w-7 h-7 rounded-lg bg-black/10 hover:bg-black/20 flex items-center justify-center text-xs transition">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <button type="button" @click="deleteNote(note.id)" title="Notu Sil" class="w-7 h-7 rounded-lg bg-black/10 hover:bg-red-500 hover:text-white flex items-center justify-center text-xs transition">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-xs whitespace-pre-wrap leading-relaxed opacity-90 font-medium" x-text="note.content"></p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-black/10 flex items-center justify-between text-[10px] opacity-75 font-mono">
                                <span x-text="note.date"></span>
                                <span class="capitalize" x-text="note.color + ' Notu'"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Boş Durum -->
                <div x-show="stickyNotes.length === 0" class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-3xl p-8 text-center space-y-3">
                    <i class="fas fa-sticky-note text-gray-300 text-4xl"></i>
                    <h4 class="text-sm font-black text-gray-700">Henüz Eklenmiş Yapışkan Not Yok</h4>
                    <p class="text-xs text-gray-500 max-w-sm mx-auto">
                        Cenuta şifreleriniz, müşteri istekleri veya unutmak istemediğiniz kodlar için hemen bir not oluşturun.
                    </p>
                    <button type="button" @click="openNoteModal = true" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs">
                        İlk Notunu Oluştur
                    </button>
                </div>
            </div>

            <!-- NOT EKLEME MODALI -->
            <div x-show="openNoteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs" x-transition>
                <div @click.away="openNoteModal = false" class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-gray-100 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-base font-black text-gray-900 flex items-center gap-2">
                            <i class="fas fa-pen-alt text-amber-500"></i> Yeni Yapışkan Not
                        </h4>
                        <button type="button" @click="openNoteModal = false" class="text-gray-400 hover:text-gray-700 text-sm">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Not Başlığı</label>
                            <input type="text" x-model="newNote.title" placeholder="Örn: Cenuta cPanel Notu veya Müşteri İsteği" 
                                   class="w-full rounded-xl border-gray-300 text-xs focus:ring-amber-500 focus:border-amber-500">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Not İçeriği / Kod Parçası</label>
                            <textarea x-model="newNote.content" rows="4" placeholder="Unutmak istemediğiniz bilgileri veya kodları buraya yapıştırın..." 
                                      class="w-full rounded-xl border-gray-300 text-xs focus:ring-amber-500 focus:border-amber-500 font-mono"></textarea>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Kağıt Rengi</label>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="newNote.color = 'yellow'" class="w-7 h-7 rounded-full bg-amber-300 border-2" :class="newNote.color === 'yellow' ? 'border-black' : 'border-transparent'"></button>
                                <button type="button" @click="newNote.color = 'blue'" class="w-7 h-7 rounded-full bg-blue-300 border-2" :class="newNote.color === 'blue' ? 'border-black' : 'border-transparent'"></button>
                                <button type="button" @click="newNote.color = 'green'" class="w-7 h-7 rounded-full bg-emerald-300 border-2" :class="newNote.color === 'green' ? 'border-black' : 'border-transparent'"></button>
                                <button type="button" @click="newNote.color = 'purple'" class="w-7 h-7 rounded-full bg-purple-300 border-2" :class="newNote.color === 'purple' ? 'border-black' : 'border-transparent'"></button>
                                <button type="button" @click="newNote.color = 'pink'" class="w-7 h-7 rounded-full bg-rose-300 border-2" :class="newNote.color === 'pink' ? 'border-black' : 'border-transparent'"></button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button type="button" @click="openNoteModal = false" class="px-4 py-2 rounded-xl text-gray-600 hover:bg-gray-100 text-xs font-bold">
                            Vazgeç
                        </button>
                        <button type="button" @click="saveNote()" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black shadow-xs">
                            Notu Kaydet
                        </button>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Alpine.js Controller -->
    <script>
        function siteAuditApp() {
            return {
                mainTab: 'system',
                systemSubTab: 'bugs',
                crawlerSearch: '',
                copiedText: '',
                openNoteModal: false,
                stickyNotes: [],
                newNote: {
                    title: '',
                    content: '',
                    color: 'yellow'
                },

                init() {
                    const saved = localStorage.getItem('yigit_ajans_sticky_notes');
                    if (saved) {
                        try {
                            this.stickyNotes = JSON.parse(saved);
                        } catch (e) {
                            this.stickyNotes = [];
                        }
                    } else {
                        // Örnek Başlangıç Notları
                        this.stickyNotes = [
                            {
                                id: 1,
                                title: '🚀 Cenuta Canlı Dağıtım',
                                content: 'git pull origin main && php artisan optimize:clear\n\nHer Git push sonrası Cenuta cPanel terminalinde bu komut çalıştırılmalıdır.',
                                color: 'yellow',
                                date: '26.09.2026'
                            },
                            {
                                id: 2,
                                title: '👤 Kurumsal Müşteri Kaydı',
                                content: 'ENABLE_CUSTOMER_REGISTRATION=false\n\nİnşaat ve kurumsal sitelerde gereksiz üye kayıt butonunu gizler.',
                                color: 'blue',
                                date: '26.09.2026'
                            }
                        ];
                        this.persistNotes();
                    }

                    // URL parametresinde tab varsa aç
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.get('tab') === 'crawler') {
                        this.mainTab = 'crawler';
                    } else if (urlParams.get('tab') === 'snippets') {
                        this.mainTab = 'snippets';
                    }
                },

                copySnippet(text) {
                    navigator.clipboard.writeText(text);
                    this.copiedText = text;
                    setTimeout(() => {
                        this.copiedText = '';
                    }, 2500);
                },

                saveNote() {
                    if (!this.newNote.title.trim() && !this.newNote.content.trim()) return;

                    const now = new Date();
                    const dateStr = now.toLocaleDateString('tr-TR') + ' ' + now.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' });

                    this.stickyNotes.unshift({
                        id: Date.now(),
                        title: this.newNote.title.trim() || 'Başlıksız Not',
                        content: this.newNote.content.trim(),
                        color: this.newNote.color || 'yellow',
                        date: dateStr
                    });

                    this.persistNotes();
                    this.newNote = { title: '', content: '', color: 'yellow' };
                    this.openNoteModal = false;
                },

                deleteNote(id) {
                    if (confirm('Bu yapışkan notu silmek istediğinize emin misiniz?')) {
                        this.stickyNotes = this.stickyNotes.filter(n => n.id !== id);
                        this.persistNotes();
                    }
                },

                persistNotes() {
                    localStorage.setItem('yigit_ajans_sticky_notes', JSON.stringify(this.stickyNotes));
                }
            }
        }
    </script>
</x-app-layout>
