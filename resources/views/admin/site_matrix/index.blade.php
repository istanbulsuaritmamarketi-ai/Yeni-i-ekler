<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <i class="fas fa-cubes text-theme-primary"></i> Master Engine &amp; Dijital Ekosistem Komuta Merkezi
                </h2>
                <p class="text-xs text-gray-500 mt-1">14 Core Modülü Sözlüğü, 8 Web Sitesi Matrisi ve Bağımsız Mobil Uygulama (Petek) Takip Paneli</p>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black bg-slate-900 text-white shadow-xs">
                    <i class="fas fa-server text-emerald-400"></i> 2 cPanel • 8 Web Sitesi • 1 Bağımsız App
                </span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8" x-data="{ activeTab: 'dictionary', searchQuery: '', categoryFilter: 'all', filterSector: 'all', copiedCmd: '' }" x-init="if (window.location.hash === '#sync_codes') activeTab = 'sync_codes'">
        
        <!-- ========================================== -->
        <!-- 1. ÜST STRATEJİ & MİMARİ BİLGİ PANOSU      -->
        <!-- ========================================== -->
        <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-800 relative overflow-hidden">
            <div class="relative z-10 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-black border border-emerald-400/30">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            YİĞİT MASTER ENGINE v2.5
                        </span>
                        <span class="text-xs text-slate-400">• Çift cPanel & Çoklu Repo Mimarisi</span>
                    </div>

                    <!-- Sekme Butonları (Pill Tabs) -->
                    <div class="flex items-center bg-slate-800/80 p-1.5 rounded-2xl border border-slate-700">
                        <button type="button" 
                                @click="activeTab = 'dictionary'" 
                                :class="activeTab === 'dictionary' ? 'bg-theme-primary text-white shadow-md' : 'text-slate-300 hover:text-white'"
                                class="px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2">
                            <i class="fas fa-book-open"></i>
                            <span>14 Core Modülü Sözlüğü</span>
                        </button>

                        <button type="button" 
                                @click="activeTab = 'matrix'" 
                                :class="activeTab === 'matrix' ? 'bg-theme-primary text-white shadow-md' : 'text-slate-300 hover:text-white'"
                                class="px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2">
                            <i class="fas fa-table"></i>
                            <span>Çoklu Web Sitesi Matrisi</span>
                        </button>

                        <button type="button" 
                                @click="activeTab = 'apps'" 
                                :class="activeTab === 'apps' ? 'bg-theme-primary text-white shadow-md' : 'text-slate-300 hover:text-white'"
                                class="px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2">
                            <i class="fas fa-mobile-alt"></i>
                            <span>Bağımsız App'ler (İTÜ Petek)</span>
                        </button>

                        <button type="button" 
                                @click="activeTab = 'sync_codes'" 
                                :class="activeTab === 'sync_codes' ? 'bg-amber-500 text-slate-950 shadow-md' : 'text-slate-300 hover:text-white'"
                                class="px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2">
                            <i class="fas fa-terminal"></i>
                            <span>⚡ Terminal &amp; Sync Kodları</span>
                        </button>
                    </div>
                </div>

                <!-- 3 Özet KPI Kartı -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                    <div class="bg-white/5 p-4 rounded-2xl border border-white/10">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Cenuta cPanel 1 (E-Ticaret)</div>
                        <div class="text-lg font-black text-white mt-1">{{ $cpanel1Count }} Ticari Web Sitesi</div>
                        <p class="text-[11px] text-gray-300 mt-1">Su Arıtma Marketi, İstanbul Çiçek Evi, Esenyurt Çiçekçisi</p>
                    </div>

                    <div class="bg-white/5 p-4 rounded-2xl border border-white/10">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Cenuta cPanel 2 (Kurumsal & App)</div>
                        <div class="text-lg font-black text-white mt-1">{{ $cpanel2Count }} Kurumsal / App Projesi</div>
                        <p class="text-[11px] text-gray-300 mt-1">Onur İnşaat, Reform İnşaat, Yiğit Ajans, Çözüm Portalı, İTÜ Petek App</p>
                    </div>

                    <div class="bg-white/5 p-4 rounded-2xl border border-white/10">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Hazır Core Modül Kütüphanesi</div>
                        <div class="text-lg font-black text-theme-primary mt-1">{{ $totalModules }} Çekirdek Modül</div>
                        <p class="text-[11px] text-gray-300 mt-1">Tüm modüller nötr olarak Core'da hazır; siteler ihtiyacına göre çeker</p>
                    </div>
                </div>

                <!-- Basit Dağıtım (Deployment) Kuralı -->
                <div class="p-3.5 bg-slate-800/60 rounded-2xl border border-slate-700/80 flex items-center justify-between gap-4 text-xs">
                    <div class="flex items-center gap-2">
                        <i class="fab fa-github text-white text-base"></i>
                        <span class="text-slate-200"><strong>Çoklu Repo Kuralı:</strong> Her site kendi GitHub reposuna sahiptir. Laragon'da kodla &rarr; Git'e push et &rarr; Cenuta cPanel terminalinde <code class="bg-black/40 px-2 py-0.5 rounded text-amber-300">git pull origin main</code> çalıştır.</span>
                    </div>
                    <span class="text-[11px] text-emerald-400 font-bold hidden sm:inline-block">✅ Sıfır Çakışma Garantisi</span>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- SEKME 1: 📋 14 CORE MODÜLÜ SÖZLÜĞÜ (KART KATALOĞU & HAFIZA REHBERİ)-->
        <!-- ================================================================= -->
        <div x-show="activeTab === 'dictionary'" x-transition:enter="transition ease-out duration-200" class="space-y-6">
            
            <!-- Arama & Kategori Filtre Çubuğu -->
            <div class="bg-white rounded-3xl p-4 sm:p-5 border border-gray-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="relative w-full sm:w-80">
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Modül adı, dosya veya özellik ara..." 
                           class="w-full text-xs pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-theme-primary focus:ring-0">
                    <i class="fas fa-search absolute left-3.5 top-3.5 text-gray-400 text-xs"></i>
                </div>

                <!-- Kategori Butonları -->
                <div class="flex flex-wrap items-center gap-1.5 w-full sm:w-auto">
                    <button type="button" @click="categoryFilter = 'all'" :class="categoryFilter === 'all' ? 'bg-slate-900 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition">Tümü (14)</button>
                    <button type="button" @click="categoryFilter = 'Satış & Ödeme'" :class="categoryFilter === 'Satış & Ödeme' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition">Satış &amp; Ödeme</button>
                    <button type="button" @click="categoryFilter = 'Kurumsal & Pazarlama'" :class="categoryFilter === 'Kurumsal & Pazarlama' ? 'bg-amber-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition">Kurumsal &amp; CRM</button>
                    <button type="button" @click="categoryFilter = 'Yapay Zeka & Büyüme'" :class="categoryFilter === 'Yapay Zeka & Büyüme' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition">AI &amp; Büyüme</button>
                    <button type="button" @click="categoryFilter = 'SEO & Altyapı'" :class="categoryFilter === 'SEO & Altyapı' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition">SEO &amp; Bölgeler</button>
                    <button type="button" @click="categoryFilter = 'Hukuk & Uyum'" :class="categoryFilter === 'Hukuk & Uyum' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition">Hukuk &amp; KVKK</button>
                </div>
            </div>

            <!-- Modül Kartları Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($modules as $mKey => $m)
                    @php
                        $badgeColor = match($m['badge'] ?? '') {
                            'Satış & Ödeme' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'Kurumsal & Pazarlama' => 'bg-amber-100 text-amber-800 border-amber-200',
                            'Yapay Zeka & Büyüme' => 'bg-purple-100 text-purple-800 border-purple-200',
                            'SEO & Altyapı' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'Hukuk & Uyum' => 'bg-teal-100 text-teal-800 border-teal-200',
                            default => 'bg-gray-100 text-gray-800 border-gray-200',
                        };

                        $activeSites = [];
                        foreach(($m['status_map'] ?? []) as $sKey => $sStatus) {
                            if($sStatus === 'active' && isset($sites[$sKey])) {
                                $activeSites[] = $sites[$sKey]['name'];
                            }
                        }
                    @endphp
                    <div x-show="(categoryFilter === 'all' || categoryFilter === '{{ $m['badge'] ?? '' }}') && ('{{ strtolower($m['title'] . ' ' . $m['desc'] . ' ' . ($m['key_files'] ?? '')) }}'.includes(searchQuery.toLowerCase()))"
                         class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-xs hover:shadow-md transition-all flex flex-col justify-between group relative overflow-hidden">
                        
                        <div>
                            <!-- Üst Başlık & Rozet -->
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center text-xl shrink-0 group-hover:bg-theme-primary transition-colors shadow-sm">
                                    <i class="{{ $m['icon'] }}"></i>
                                </div>
                                <span class="px-2.5 py-1 rounded-xl text-[10px] font-black border {{ $badgeColor }}">
                                    {{ $m['badge'] ?? 'Core Modül' }}
                                </span>
                            </div>

                            <h3 class="font-black text-gray-900 text-base mb-2 group-hover:text-theme-primary transition-colors">
                                {{ $m['title'] }}
                            </h3>
                            <p class="text-xs text-gray-600 leading-relaxed mb-4">
                                {{ $m['desc'] }}
                            </p>
                        </div>

                        <div class="space-y-3 pt-3 border-t border-gray-100 text-xs">
                            <!-- Dosya & Rota Bilgisi -->
                            <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-200/60 font-mono text-[11px] text-gray-700">
                                <div class="text-[9px] uppercase font-bold text-gray-400 mb-0.5">Bağlı Çekirdek Dosyalar:</div>
                                <div class="truncate text-slate-800" title="{{ $m['key_files'] ?? '' }}">📁 {{ $m['key_files'] ?? 'Nötr Core Bileşeni' }}</div>
                            </div>

                            <!-- Aktif Olduğu Siteler -->
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Şu Anda Aktif Olduğu Siteler:</span>
                                <div class="flex flex-wrap gap-1">
                                    @forelse($activeSites as $siteName)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 text-[10px] font-bold border border-emerald-200/70">
                                            <i class="fas fa-check-circle text-emerald-500 text-[9px]"></i> {{ $siteName }}
                                        </span>
                                    @empty
                                        <span class="text-[10px] text-gray-400 italic">Henüz sitelerde aktif değil (Core Depoda Hazır)</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

        </div>

        <!-- ================================================================= -->
        <!-- SEKME 2: 📊 ÇOKLU WEB SİTESİ MATRİSİ (İNTERAKTİF TABLO)           -->
        <!-- ================================================================= -->
        <div x-show="activeTab === 'matrix'" x-transition:enter="transition ease-out duration-200" class="space-y-6">
            
            <!-- Filtre Barı -->
            <div class="bg-white rounded-3xl p-4 sm:p-5 border border-gray-200/80 shadow-xs flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black text-gray-700">Sektör Filtresi:</span>
                    <button type="button" @click="filterSector = 'all'" :class="filterSector === 'all' ? 'bg-slate-900 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition">Tüm Siteler ({{ count($sites) }})</button>
                    <button type="button" @click="filterSector = 'ecommerce'" :class="filterSector === 'ecommerce' ? 'bg-theme-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition">E-Ticaret</button>
                    <button type="button" @click="filterSector = 'corporate'" :class="filterSector === 'corporate' ? 'bg-theme-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition">Kurumsal &amp; Ajans</button>
                </div>

                <div class="flex items-center gap-3 text-xs">
                    <span class="inline-flex items-center gap-1.5 font-bold text-emerald-700"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Aktif</span>
                    <span class="inline-flex items-center gap-1.5 font-bold text-amber-700"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Sırada / Bekleniyor (To-Do)</span>
                    <span class="inline-flex items-center gap-1.5 font-bold text-gray-400"><span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span> Pasif / Yok</span>
                </div>
            </div>

            <!-- Matris Tablosu -->
            <div class="bg-white rounded-3xl border border-gray-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-900 text-white uppercase tracking-wider font-bold">
                            <tr>
                                <th class="py-4 px-5 sticky left-0 bg-slate-900 z-20 min-w-[240px] border-r border-slate-800">
                                    Web Sitesi &amp; Sektör
                                </th>
                                <th class="py-4 px-4 min-w-[170px] border-r border-slate-800">Sunucu / cPanel</th>
                                @foreach($modules as $mKey => $m)
                                    <th class="py-4 px-3 text-center min-w-[110px] border-r border-slate-800 text-[10px]" title="{{ $m['title'] }}: {{ $m['desc'] }}">
                                        <div class="flex flex-col items-center gap-1">
                                            <i class="{{ $m['icon'] }} text-xs text-amber-400"></i>
                                            <span class="truncate max-w-[95px]">{{ $m['title'] }}</span>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($sites as $sKey => $s)
                                <tr x-show="filterSector === 'all' || filterSector === '{{ $s['mode'] }}'" class="hover:bg-gray-50/60 transition">
                                    <!-- Site İsmi (Sabit Sol Sütun) -->
                                    <td class="py-4 px-5 sticky left-0 bg-white z-10 border-r border-gray-200 shadow-sm">
                                        <div class="font-black text-gray-900 text-sm">{{ $s['name'] }}</div>
                                        <div class="flex items-center gap-1.5 text-[11px] text-gray-500 mt-0.5">
                                            <span class="font-mono text-theme-primary">{{ $s['domain'] }}</span>
                                            <span>•</span>
                                            <span class="px-1.5 py-0.2 rounded bg-gray-100 text-[10px]">{{ $s['type'] }}</span>
                                        </div>
                                    </td>

                                    <!-- cPanel & Sunucu Bilgisi -->
                                    <td class="py-4 px-4 border-r border-gray-100">
                                        <div class="font-bold text-gray-800 text-[11px]">{{ $s['server'] }}</div>
                                        <div class="text-[10px] text-emerald-600 font-bold mt-0.5 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ $s['status'] }}
                                        </div>
                                    </td>

                                    <!-- 14 Modül Durumları -->
                                    @foreach($modules as $mKey => $m)
                                        @php
                                            $status = $m['status_map'][$sKey] ?? 'passive';
                                        @endphp
                                        <td class="py-4 px-3 text-center border-r border-gray-100">
                                            @if($status === 'active')
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 shadow-2xs font-bold text-xs" title="Aktif ve Çalışıyor">
                                                    <i class="fas fa-check"></i>
                                                </span>
                                            @elseif($status === 'pending')
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-amber-100 text-amber-800 shadow-2xs font-bold text-xs" title="Sırada / Entegrasyon Bekliyor">
                                                    <i class="fas fa-clock"></i>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-gray-100 text-gray-300 font-bold text-xs" title="Pasif (İstenmiyor)">
                                                    <i class="fas fa-minus"></i>
                                                </span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- ================================================================= -->
        <!-- SEKME 3: 📱 BAĞIMSIZ PLATFORMLAR & MOBİL APP'LER (İTÜ PETEK VB.)   -->
        <!-- ================================================================= -->
        <div x-show="activeTab === 'apps'" x-transition:enter="transition ease-out duration-200" class="space-y-6">
            
            <div class="bg-indigo-50/70 border border-indigo-200/80 rounded-3xl p-5 flex items-start gap-4">
                <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-lg shrink-0">
                    <i class="fas fa-network-wired"></i>
                </div>
                <div>
                    <h4 class="font-black text-indigo-950 text-sm">Bağımsız Platform &amp; Mobil Uygulama Mimarisi</h4>
                    <p class="text-xs text-indigo-800 mt-0.5 leading-relaxed">
                        Bu projeler standart e-ticaret veya kurumsal tanıtım sitelerinden farklıdır. Kendine ait <strong>React Native / Expo SPA</strong> arayüzü, <strong>Laravel Sanctum REST API</strong> motoru ve özel veritabanı şemalarıyla tam bağımsız olarak çalışır.
                    </p>
                </div>
            </div>

            <!-- Uygulamalar Listesi -->
            <div class="space-y-6">
                @foreach($independentApps as $appKey => $app)
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-xs space-y-6">
                        
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-5">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-2xl shadow-md">
                                    <i class="fas fa-hexagon-nodes"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-xl font-black text-gray-900">{{ $app['name'] }}</h3>
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">{{ $app['status'] }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5 font-mono">{{ $app['domain'] }} • {{ $app['sector'] }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ $app['local_url'] }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-black text-xs transition flex items-center gap-2">
                                    <i class="fas fa-external-link-alt"></i> Yerel Testi Aç ({{ $app['local_url'] }})
                                </a>
                            </div>
                        </div>

                        <!-- Mimari ve Sunucu Detayları -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-200/60">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Teknoloji &amp; Yazılım Mimarisi:</span>
                                <div class="font-bold text-xs text-gray-900">{{ $app['architecture'] }}</div>
                                <div class="text-[11px] text-gray-500 mt-1">Token bazlı oturum (Sanctum), WebSocket/Pusher anlık akış, Expo Web derlemesi.</div>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-200/60">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Sunucu &amp; Dağıtım:</span>
                                <div class="font-bold text-xs text-gray-900">{{ $app['server'] }}</div>
                                <div class="text-[11px] text-gray-500 mt-1">cPanel Kullanıcısı: <code class="bg-white px-1.5 py-0.5 rounded border">{{ $app['cpanel_user'] }}</code></div>
                            </div>
                        </div>

                        <!-- Özellik Listesi -->
                        <div>
                            <h4 class="font-black text-xs text-gray-700 uppercase tracking-wider mb-3">Bu Uygulamaya Özel Aktif / Geliştirilen Fonksiyonlar:</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                @foreach($app['features'] as $feature)
                                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 text-xs font-semibold text-slate-800 flex items-center gap-2">
                                        <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                                        <span>{{ $feature }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

        </div>

        <!-- ================================================================= -->
        <!-- SEKME 4: ⚡ ÇOKLU SİTE TERMİNAL, KLONLAMA & SYNC KODLARI KONSOLU -->
        <!-- ================================================================= -->
        <div x-show="activeTab === 'sync_codes'" x-transition:enter="transition ease-out duration-200" class="space-y-6">
            
            <!-- Bilgi ve Hızlı Eylem Çubuğu -->
            <div class="bg-slate-900 text-white p-6 rounded-3xl border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-xl">
                <div>
                    <h3 class="text-lg font-black text-white flex items-center gap-2">
                        <i class="fas fa-terminal text-amber-400"></i>
                        <span>Çoklu Site Senkronizasyon &amp; Operasyon Konsolu</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-2xl leading-relaxed">
                        Her site bağımsız bir klasör ve repoya sahiptir. Master Core'da geliştirdiğiniz ortak servisleri, sitemap ve denetçileri diğer sitelere tek tıkla aktarabilir veya yeni bir siteyi saniyeler içinde klonlayabilirsiniz.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <button type="button" 
                            @click="navigator.clipboard.writeText('php artisan site:sync --dry-run'); copiedCmd = 't_sim'; setTimeout(() => copiedCmd = '', 2000)"
                            class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-300 font-bold text-xs transition flex items-center gap-1.5 border border-slate-700">
                        <i class="fas fa-vial text-amber-400"></i>
                        <span x-text="copiedCmd === 't_sim' ? '✓ Kopyalandı' : 'Simülasyon (Dry-Run)'"></span>
                    </button>

                    <button type="button" 
                            @click="navigator.clipboard.writeText('php artisan site:sync all'); copiedCmd = 't_sync_all'; setTimeout(() => copiedCmd = '', 2000)"
                            class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs transition flex items-center gap-1.5 shadow-md">
                        <i class="fas fa-bolt"></i>
                        <span x-text="copiedCmd === 't_sync_all' ? '✓ Kopyalandı' : 'Tüm Siteleri Güncelle (all)'"></span>
                    </button>
                </div>
            </div>

            <!-- Genel Master Core Terminal Komutları -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="text-[10px] font-bold uppercase text-gray-400">Otonom QA Taraması</div>
                        <div class="font-mono text-xs font-bold text-emerald-600 mt-1">php artisan site:audit</div>
                    </div>
                    <button type="button" 
                            @click="navigator.clipboard.writeText('php artisan site:audit'); copiedCmd = 'm_audit'; setTimeout(() => copiedCmd = '', 2000)"
                            class="mt-3 w-full py-1.5 rounded-lg bg-gray-100 hover:bg-emerald-50 hover:text-emerald-700 text-gray-700 font-bold text-[11px] transition">
                        <span x-text="copiedCmd === 'm_audit' ? '✓ Kopyalandı' : 'Kopyala'"></span>
                    </button>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="text-[10px] font-bold uppercase text-gray-400">Kod Standartları (Pint)</div>
                        <div class="font-mono text-xs font-bold text-indigo-600 mt-1">vendor/bin/pint --format agent</div>
                    </div>
                    <button type="button" 
                            @click="navigator.clipboard.writeText('vendor/bin/pint --format agent'); copiedCmd = 'm_pint'; setTimeout(() => copiedCmd = '', 2000)"
                            class="mt-3 w-full py-1.5 rounded-lg bg-gray-100 hover:bg-indigo-50 hover:text-indigo-700 text-gray-700 font-bold text-[11px] transition">
                        <span x-text="copiedCmd === 'm_pint' ? '✓ Kopyalandı' : 'Kopyala'"></span>
                    </button>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="text-[10px] font-bold uppercase text-gray-400">Frontend Derleme (Vite)</div>
                        <div class="font-mono text-xs font-bold text-amber-600 mt-1">npm run build</div>
                    </div>
                    <button type="button" 
                            @click="navigator.clipboard.writeText('npm run build'); copiedCmd = 'm_npm'; setTimeout(() => copiedCmd = '', 2000)"
                            class="mt-3 w-full py-1.5 rounded-lg bg-gray-100 hover:bg-amber-50 hover:text-amber-700 text-gray-700 font-bold text-[11px] transition">
                        <span x-text="copiedCmd === 'm_npm' ? '✓ Kopyalandı' : 'Kopyala'"></span>
                    </button>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex flex-col justify-between">
                    <div>
                        <div class="text-[10px] font-bold uppercase text-gray-400">Birim Testleri (PHPUnit)</div>
                        <div class="font-mono text-xs font-bold text-cyan-600 mt-1">php artisan test --compact</div>
                    </div>
                    <button type="button" 
                            @click="navigator.clipboard.writeText('php artisan test --compact'); copiedCmd = 'm_test'; setTimeout(() => copiedCmd = '', 2000)"
                            class="mt-3 w-full py-1.5 rounded-lg bg-gray-100 hover:bg-cyan-50 hover:text-cyan-700 text-gray-700 font-bold text-[11px] transition">
                        <span x-text="copiedCmd === 'm_test' ? '✓ Kopyalandı' : 'Kopyala'"></span>
                    </button>
                </div>
            </div>

            <!-- Her Site İçin Özel Operasyon Kartları -->
            <div class="space-y-4">
                <h4 class="text-sm font-black text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-layer-group text-theme-primary"></i> Siteye Özel Senkronizasyon &amp; Klonlama Kartları
                </h4>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    @foreach($sites as $code => $site)
                        @php
                            $folder = $site['folder'] ?? $code;
                            $localPath = "C:\\laragon\\www\\" . $folder;
                            $existsLocally = \Illuminate\Support\Facades\File::isDirectory($localPath);
                            $syncCmd = "php artisan site:sync {$code}";
                            $cloneCmd = "robocopy \"C:\\laragon\\www\\yigit-core\" \"{$localPath}\" /E /XD vendor node_modules storage\\logs .git";
                            $cpanelCmd = "cd /home/" . ($site['cpanel_user'] ?? 'cpanel') . "/laravel_core && git pull origin main && php artisan optimize:clear";
                            $envSnippet = "SITE_MODE={$site['mode']}\nTHEME={$site['theme']}\nAPP_URL={$site['local_url']}";
                        @endphp

                        <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-xs space-y-4 hover:border-indigo-300 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h5 class="text-base font-black text-gray-900">{{ $site['name'] }}</h5>
                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-mono font-bold bg-slate-900 text-amber-300">
                                            Kod: {{ $code }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $site['sector'] }}</p>
                                </div>

                                <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider {{ $site['mode'] === 'ecommerce' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $site['mode'] }} • {{ $site['theme'] }}
                                </span>
                            </div>

                            <!-- Yerel Durum & URL -->
                            <div class="p-3 rounded-2xl {{ $existsLocally ? 'bg-emerald-50/70 border border-emerald-200' : 'bg-gray-50 border border-gray-200' }}">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-[10px] font-bold uppercase text-gray-400">Yerel Ortam (Laragon):</span>
                                    @if($existsLocally)
                                        <span class="text-[10px] font-bold text-emerald-700 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Klasör Var ({{ $folder }})
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold text-gray-400 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span> Klonlanmadı ({{ $folder }})
                                        </span>
                                    @endif
                                </div>
                                <div class="mt-1 flex items-center justify-between">
                                    @if($existsLocally)
                                        <a href="{{ $site['local_url'] }}" target="_blank" class="text-xs font-mono font-bold text-emerald-700 hover:text-emerald-900 flex items-center gap-1 hover:underline">
                                            <span>{{ $site['local_url'] }}</span>
                                            <i class="fas fa-external-link-alt text-[9px]"></i>
                                        </a>
                                    @else
                                        <span class="text-xs font-mono text-gray-500">{{ $site['local_url'] }}</span>
                                    @endif
                                    <span class="text-[10px] font-mono text-gray-400">{{ $site['domain'] }}</span>
                                </div>
                            </div>

                            <!-- Komutlar Listesi -->
                            <div class="space-y-2 text-xs">
                                <!-- 1. Sync Komutu -->
                                <div class="bg-slate-950 p-2.5 rounded-xl border border-slate-800 flex items-center justify-between gap-2">
                                    <div class="overflow-hidden">
                                        <div class="text-[9px] font-bold uppercase text-indigo-400">Özel Senkronizasyon (Master Core &rarr; Site)</div>
                                        <div class="font-mono text-xs text-white truncate mt-0.5">{{ $syncCmd }}</div>
                                    </div>
                                    <button type="button" 
                                            @click="navigator.clipboard.writeText('{{ $syncCmd }}'); copiedCmd = 's_{{ $code }}'; setTimeout(() => copiedCmd = '', 2000)"
                                            class="px-2.5 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-[10px] transition shrink-0">
                                        <span x-text="copiedCmd === 's_{{ $code }}' ? '✓' : 'Kopyala'"></span>
                                    </button>
                                </div>

                                <!-- 2. Klonlama Komutu -->
                                <div class="bg-slate-950 p-2.5 rounded-xl border border-slate-800 flex items-center justify-between gap-2">
                                    <div class="overflow-hidden">
                                        <div class="text-[9px] font-bold uppercase text-amber-400">Sıfırdan Klasör Klonlama (robocopy)</div>
                                        <div class="font-mono text-xs text-amber-200 truncate mt-0.5">{{ $cloneCmd }}</div>
                                    </div>
                                    <button type="button" 
                                            @click="navigator.clipboard.writeText('{{ addslashes($cloneCmd) }}'); copiedCmd = 'c_{{ $code }}'; setTimeout(() => copiedCmd = '', 2000)"
                                            class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-amber-500 hover:text-slate-950 text-slate-300 font-bold text-[10px] transition shrink-0">
                                        <span x-text="copiedCmd === 'c_{{ $code }}' ? '✓' : 'Kopyala'"></span>
                                    </button>
                                </div>

                                <!-- 3. .env Şablonu & Cenuta SSH -->
                                <div class="grid grid-cols-2 gap-2 pt-1">
                                    <button type="button" 
                                            @click="navigator.clipboard.writeText('{{ addslashes($envSnippet) }}'); copiedCmd = 'env_{{ $code }}'; setTimeout(() => copiedCmd = '', 2000)"
                                            class="py-1.5 px-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-mono text-[10px] font-bold transition flex items-center justify-center gap-1">
                                        <i class="fas fa-cog text-cyan-600"></i>
                                        <span x-text="copiedCmd === 'env_{{ $code }}' ? '✓ .env Kopyalandı' : '.env Ayarını Al'"></span>
                                    </button>

                                    <button type="button" 
                                            @click="navigator.clipboard.writeText('{{ addslashes($cpanelCmd) }}'); copiedCmd = 'cp_{{ $code }}'; setTimeout(() => copiedCmd = '', 2000)"
                                            class="py-1.5 px-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-mono text-[10px] font-bold transition flex items-center justify-center gap-1">
                                        <i class="fas fa-server text-purple-600"></i>
                                        <span x-text="copiedCmd === 'cp_{{ $code }}' ? '✓ SSH Kopyalandı' : 'Cenuta SSH Kodu'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Bağımsız App: İTÜ Petek Terminal Paneli -->
            <div class="bg-gradient-to-r from-amber-500/10 via-slate-900 to-slate-950 text-white p-6 rounded-3xl border border-amber-500/30 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center text-lg font-black">
                            <i class="fas fa-hexagon-nodes"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-white text-base">İTÜ Petek (React Native / Expo Platformu)</h4>
                            <p class="text-xs text-amber-200">Klasör: <code>C:\laragon\www\petek</code> • Local: <code>http://petek.test</code></p>
                        </div>
                    </div>

                    <a href="http://petek.test" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs transition flex items-center gap-1">
                        <span>Yerel Web App'i Aç</span>
                        <i class="fas fa-external-link-alt text-[10px]"></i>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs font-mono">
                    <div class="bg-black/40 p-3 rounded-2xl border border-white/10 flex items-center justify-between">
                        <div class="truncate">
                            <div class="text-[9px] text-gray-400 uppercase font-bold">Expo / Web Başlat</div>
                            <div class="text-amber-300 mt-0.5">cd C:\laragon\www\petek && npm run dev</div>
                        </div>
                        <button type="button" @click="navigator.clipboard.writeText('cd C:\\laragon\\www\\petek && npm run dev'); copiedCmd = 'petek_dev'; setTimeout(() => copiedCmd = '', 2000)" class="px-2 py-1 bg-white/10 rounded text-[10px] shrink-0 font-sans font-bold">
                            <span x-text="copiedCmd === 'petek_dev' ? '✓' : 'Kopyala'"></span>
                        </button>
                    </div>

                    <div class="bg-black/40 p-3 rounded-2xl border border-white/10 flex items-center justify-between">
                        <div class="truncate">
                            <div class="text-[9px] text-gray-400 uppercase font-bold">Mobil Terminal (QR)</div>
                            <div class="text-emerald-300 mt-0.5">npx expo start</div>
                        </div>
                        <button type="button" @click="navigator.clipboard.writeText('cd C:\\laragon\\www\\petek && npx expo start'); copiedCmd = 'petek_expo'; setTimeout(() => copiedCmd = '', 2000)" class="px-2 py-1 bg-white/10 rounded text-[10px] shrink-0 font-sans font-bold">
                            <span x-text="copiedCmd === 'petek_expo' ? '✓' : 'Kopyala'"></span>
                        </button>
                    </div>

                    <div class="bg-black/40 p-3 rounded-2xl border border-white/10 flex items-center justify-between">
                        <div class="truncate">
                            <div class="text-[9px] text-gray-400 uppercase font-bold">TypeScript Denetimi</div>
                            <div class="text-cyan-300 mt-0.5">npx tsc --noEmit</div>
                        </div>
                        <button type="button" @click="navigator.clipboard.writeText('cd C:\\laragon\\www\\petek && npx tsc --noEmit'); copiedCmd = 'petek_tsc'; setTimeout(() => copiedCmd = '', 2000)" class="px-2 py-1 bg-white/10 rounded text-[10px] shrink-0 font-sans font-bold">
                            <span x-text="copiedCmd === 'petek_tsc' ? '✓' : 'Kopyala'"></span>
                        </button>
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
