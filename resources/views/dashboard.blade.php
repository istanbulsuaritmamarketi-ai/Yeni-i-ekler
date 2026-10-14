<x-app-layout>
    @if(($siteMode ?? 'ecommerce') === 'ecommerce')
        <!-- ======================================================== -->
        <!-- FLORİST KOMUTA PANELİ -->
        <!-- ======================================================== -->
        <x-slot name="header">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-gradient-to-tr from-blue-700 to-sky-500 text-white flex items-center justify-center text-sm shadow-xs shrink-0">
                    <i class="fas fa-spa"></i>
                </span>
                <div class="min-w-0">
                    <h2 class="font-black text-sm text-slate-900 leading-none truncate">
                        {{ __('Taze Çiçekler Atölye & Sipariş Komuta Paneli') }}
                    </h2>
                    <p class="text-[11px] text-slate-400 mt-0.5 truncate">Bugünün teslimatları, atölye hazırlık fotoğrafları ve ilçe kurye ağı</p>
                </div>
            </div>
        </x-slot>

        <div class="py-6 space-y-6">
            <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- 1. FLORIST BAŞLIK & HIZLI İŞLEMLER (Ultra Sade & Minimalist) -->
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h1 class="text-base sm:text-lg font-black tracking-tight text-slate-900 leading-none">
                            Çiçek Atölyesi Yönetimi
                        </h1>
                        <div class="flex items-center gap-2 text-[11px] text-slate-400 font-medium mt-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Atölye ve Kurye Ağı Aktif</span>
                            <span>•</span>
                            <span>{{ now()->translatedFormat('d F Y, l') }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-start sm:self-auto">
                        <a href="{{ route('admin.orders.index', ['tab' => 'today']) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition">
                            <i class="fas fa-calendar-day text-[11px]"></i>
                            <span>Bugünkü Siparişler ({{ $flowerStats['today_orders'] ?? 0 }})</span>
                        </a>

                        <a href="{{ route('admin.products.create') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white shadow-xs transition active:scale-95">
                            <i class="fas fa-plus text-[10px]"></i>
                            <span>Yeni Çiçek</span>
                        </a>
                    </div>
                </div>

                <!-- 2. 4 TEMEL FLORIST KPI KARTI -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    
                    <!-- KPI 1: Bugünkü Teslimatlar -->
                    <a href="{{ route('admin.orders.index', ['tab' => 'today']) }}" class="bg-white hover:bg-rose-50/40 p-6 rounded-3xl border border-slate-200/80 shadow-xs transition block group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Bugünkü Teslimat</span>
                            <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-3xl font-black text-slate-900">{{ $flowerStats['today_orders'] ?? 0 }} Sipariş</div>
                        <div class="mt-1 text-xs text-rose-600 font-bold flex items-center gap-1">
                            <span>Teslimat slotlarına göre sırala</span>
                            <i class="fas fa-chevron-right text-[10px]"></i>
                        </div>
                    </a>

                    <!-- KPI 2: Atölyede Hazırlanan & Fotoğraf Bekleyen -->
                    <a href="{{ route('admin.orders.index', ['tab' => 'preparing']) }}" class="bg-white hover:bg-blue-50/40 p-6 rounded-3xl border border-slate-200/80 shadow-xs transition block group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Atölye &amp; Fotoğraf</span>
                            <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                                <i class="fas fa-camera"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-3xl font-black text-slate-900">{{ ($flowerStats['preparing_orders'] ?? 0) + ($flowerStats['photo_review'] ?? 0) }} Buket</div>
                        <div class="mt-1 text-xs text-blue-600 font-bold flex items-center gap-1">
                            <span>{{ $flowerStats['photo_review'] ?? 0 }} tanesi fotoğraf onayında</span>
                        </div>
                    </a>

                    <!-- KPI 3: Kuryede / Yolda -->
                    <a href="{{ route('admin.orders.index', ['tab' => 'on_delivery']) }}" class="bg-white hover:bg-indigo-50/40 p-6 rounded-3xl border border-slate-200/80 shadow-xs transition block group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kuryede / Dağıtımda</span>
                            <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                                <i class="fas fa-motorcycle"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-3xl font-black text-slate-900">{{ $flowerStats['on_delivery'] ?? 0 }} Dağıtım</div>
                        <div class="mt-1 text-xs text-indigo-600 font-bold flex items-center gap-1">
                            <span>İstanbul ilçelerinde yolda</span>
                        </div>
                    </a>

                    <!-- KPI 4: Satış Cirosu -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs block">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Toplam Ciro</span>
                            <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-base">
                                <i class="fas fa-lira-sign"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-3xl font-black text-emerald-600">₺{{ number_format($flowerStats['total_revenue'] ?? 0, 0, ',', '.') }}</div>
                        <div class="mt-1 text-xs text-slate-500 font-bold flex items-center gap-1">
                            <span>Bugün: ₺{{ number_format($flowerStats['today_revenue'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>

                </div>

                <!-- 3. İKİLİ ÇALIŞMA ALANI: CANLI SİPARİŞLER (SOL) + HIZLI YÖNETİM (SAĞ) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- SOL: SON GELEN ÇİÇEK SİPARİŞLERİ TABLOSU (8 KOLON) -->
                    <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h3 class="font-black text-base text-slate-900 flex items-center gap-2">
                                    <i class="fas fa-bell text-rose-500"></i>
                                    <span>Son Gelen Siparişler</span>
                                </h3>
                                <p class="text-xs text-slate-400 mt-0.5">Atölye ve teslimat durumunu canlı takip edin</p>
                            </div>
                            <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-rose-600 hover:underline flex items-center gap-1">
                                Tümünü Gör <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 text-left">
                                <thead class="bg-slate-50/80 text-[11px] font-black text-slate-500 uppercase tracking-wider">
                                    <tr>
                                        <th class="px-5 py-3.5">Sipariş No</th>
                                        <th class="px-5 py-3.5">Alıcı &amp; İlçe</th>
                                        <th class="px-5 py-3.5">Teslimat</th>
                                        <th class="px-5 py-3.5">Fotoğraf</th>
                                        <th class="px-5 py-3.5">Tutar</th>
                                        <th class="px-5 py-3.5">Durum</th>
                                        <th class="px-5 py-3.5 text-right">İşlem</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs">
                                    @forelse($recentOrders as $order)
                                        <tr class="hover:bg-rose-50/30 transition-colors">
                                            <td class="px-5 py-3.5 whitespace-nowrap font-mono font-bold text-rose-600">
                                                <a href="{{ route('admin.orders.show', $order) }}">#{{ $order->order_number }}</a>
                                                <span class="block text-[10px] text-slate-400 font-sans">{{ $order->created_at->format('H:i') }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <div class="font-bold text-slate-800">{{ $order->recipient_name ?: $order->customer_name }}</div>
                                                <div class="text-[10px] text-slate-400">{{ $order->recipient_district ?: ($order->region->name ?? 'İstanbul') }}</div>
                                            </td>
                                            <td class="px-5 py-3.5 whitespace-nowrap">
                                                <div class="font-bold text-slate-700">
                                                    {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d.m.Y') : 'Bugün' }}
                                                </div>
                                                <div class="text-[10px] text-slate-400">{{ $order->delivery_time_slot ?: 'Standart' }}</div>
                                            </td>
                                            <td class="px-5 py-3.5 whitespace-nowrap">
                                                @if($order->preparation_photo)
                                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                                                        <i class="fas fa-check"></i> Fotoğraflı
                                                    </span>
                                                @else
                                                    <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">
                                                        <i class="fas fa-camera"></i> Bekliyor
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-5 py-3.5 whitespace-nowrap font-black text-slate-900">
                                                ₺{{ number_format($order->total_amount, 2, ',', '.') }}
                                            </td>
                                            <td class="px-5 py-3.5 whitespace-nowrap">
                                                @php
                                                    $statusStyle = match($order->status) {
                                                        'Yeni Sipariş', 'pending' => 'bg-amber-100 text-amber-800',
                                                        'Atölyede Hazırlanıyor', 'Hazırlanıyor', 'processing' => 'bg-blue-100 text-blue-800',
                                                        'Fotoğraf Onayında' => 'bg-purple-100 text-purple-800',
                                                        'Kuryede / Dağıtımda', 'Yolda', 'on_delivery', 'Kuryede' => 'bg-indigo-100 text-indigo-800',
                                                        'Teslim Edildi', 'Tamamlandı' => 'bg-emerald-100 text-emerald-800',
                                                        default => 'bg-slate-100 text-slate-800'
                                                    };
                                                @endphp
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $statusStyle }}">
                                                    {{ $order->status }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                                <a href="{{ route('admin.orders.show', $order) }}" class="text-xs font-bold text-slate-700 hover:text-rose-600 bg-slate-100 hover:bg-rose-50 px-2.5 py-1 rounded-lg transition">
                                                    Yönet
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                                Henüz kayıtlı sipariş bulunmuyor.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- SAĞ: HIZLI MAĞAZA MODÜLLERİ (4 KOLON) -->
                    <div class="lg:col-span-4 space-y-4">
                        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs">
                            <h3 class="font-black text-sm text-slate-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-th-large text-rose-500"></i>
                                <span>Hızlı Yönetim Panelleri</span>
                            </h3>

                            <div class="space-y-3 text-xs">
                                <a href="{{ route('admin.products.index') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 hover:bg-rose-50/60 border border-slate-100 transition group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-sm font-bold">
                                            <i class="fas fa-spa"></i>
                                        </div>
                                        <div>
                                            <div class="font-black text-slate-800 group-hover:text-rose-600 transition-colors">Çiçek Kataloğu</div>
                                            <div class="text-[11px] text-slate-400">{{ $flowerStats['total_flowers'] ?? 0 }} taze tasarım</div>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-slate-300 text-[10px]"></i>
                                </a>



                                <a href="{{ route('admin.banners.index') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 hover:bg-rose-50/60 border border-slate-100 transition group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-sm font-bold">
                                            <i class="fas fa-images"></i>
                                        </div>
                                        <div>
                                            <div class="font-black text-slate-800 group-hover:text-rose-600 transition-colors">Vitrin &amp; Bannerlar</div>
                                            <div class="text-[11px] text-slate-400">{{ $flowerStats['active_banners'] ?? 0 }} aktif vitrin bannerı</div>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-slate-300 text-[10px]"></i>
                                </a>

                                <a href="{{ route('admin.regions.index') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 hover:bg-rose-50/60 border border-slate-100 transition group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm font-bold">
                                            <i class="fas fa-map-marked-alt"></i>
                                        </div>
                                        <div>
                                            <div class="font-black text-slate-800 group-hover:text-rose-600 transition-colors">39 İlçe Kurye Ağı</div>
                                            <div class="text-[11px] text-slate-400">İstanbul semt ve teslimat ücretleri</div>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-slate-300 text-[10px]"></i>
                                </a>

                                <a href="{{ route('admin.coupons.index') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 hover:bg-rose-50/60 border border-slate-100 transition group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold">
                                            <i class="fas fa-ticket-alt"></i>
                                        </div>
                                        <div>
                                            <div class="font-black text-slate-800 group-hover:text-rose-600 transition-colors">İndirim Kuponları</div>
                                            <div class="text-[11px] text-slate-400">Sepet ve kampanya kodları</div>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-slate-300 text-[10px]"></i>
                                </a>

                                <a href="{{ route('admin.marketing.payment') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 hover:bg-rose-50/60 border border-slate-100 transition group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold">
                                            <i class="fas fa-credit-card"></i>
                                        </div>
                                        <div>
                                            <div class="font-black text-slate-800 group-hover:text-rose-600 transition-colors">PayTR Ödeme Ayarları</div>
                                            <div class="text-[11px] text-slate-400">Sanal POS ve taksit parametreleri</div>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-slate-300 text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    @else
        <!-- ========================================== -->
        <!-- YİĞİT CORE MASTER ENGINE KONSOLU        -->
        <!-- ========================================== -->
        <x-slot name="header">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="font-black text-xl text-gray-900 leading-tight flex items-center gap-2">
                        <i class="fas fa-microchip text-indigo-600"></i>
                        <span>{{ __('Yiğit Core — Master Komuta & Altyapı Konsolu') }}</span>
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">Çoklu web sitesi mimarisi, 14 nötr sistem modülü ve otonom denetim merkezi</p>
                </div>
                
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('admin.siteAudit.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black py-2 px-3.5 rounded-xl text-xs flex items-center gap-1.5 shadow-sm transition">
                        <i class="fas fa-stethoscope"></i> Otonom Kalite Botu
                    </a>
                    <a href="{{ route('admin.siteMatrix.index') }}" class="bg-slate-900 hover:bg-slate-800 text-white font-black py-2 px-3.5 rounded-xl text-xs flex items-center gap-1.5 shadow-sm transition">
                        <i class="fas fa-cubes text-amber-400"></i> Çoklu Site Matrisi
                    </a>
                </div>
            </div>
        </x-slot>

        <div class="space-y-8" x-data="{ copiedCmd: '', activeModal: null }">
            <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-800 relative overflow-hidden">
                <div class="absolute -right-16 -bottom-16 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-2">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-white text-xs font-bold backdrop-blur-xs border border-white/10">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>YİĞİT CORE v2.5 • Master Blueprint • 9/9 Denetim Kusursuz</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                            Sistem Komuta Merkezi
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                            Burası nötr çekirdek depodur. 14 bağımsız modülün ve otonom test botlarının ana kütüphanesidir.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2.5 shrink-0">
                        <a href="{{ route('admin.siteMatrix.index') }}#sync_codes" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs px-4 py-2.5 rounded-xl shadow-md transition flex items-center gap-1.5">
                            <i class="fas fa-terminal"></i> Terminal &amp; Sync Kodları
                        </a>
                        <a href="{{ route('admin.pages.index') }}" class="bg-white/10 hover:bg-white/20 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition flex items-center gap-1.5 border border-white/10">
                            <i class="fas fa-balance-scale text-amber-400"></i> KVKK &amp; Sözleşmeler
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <a href="{{ route('admin.siteMatrix.index') }}" class="bg-white hover:bg-indigo-50/50 p-6 rounded-3xl border border-gray-200/80 shadow-xs transition block group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase">Ekosistem Siteleri</span>
                        <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-base">
                            <i class="fas fa-globe"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-3xl font-black text-gray-900">{{ $stats['total_sites'] ?? 0 }} Proje</div>
                    <div class="mt-1 text-xs text-indigo-600 font-bold flex items-center gap-1">
                        <span>Matriste ve terminal kodlarında incele</span>
                        <i class="fas fa-chevron-right text-[10px]"></i>
                    </div>
                </a>

                <a href="{{ route('admin.siteMatrix.index') }}" class="bg-white hover:bg-emerald-50/50 p-6 rounded-3xl border border-gray-200/80 shadow-xs transition block group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase">Master Modüller</span>
                        <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-base">
                            <i class="fas fa-cubes"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-3xl font-black text-gray-900">{{ $stats['total_modules'] ?? 0 }} Modül</div>
                    <div class="mt-1 text-xs text-emerald-600 font-bold flex items-center gap-1">
                        <span>14 modül kütüphanesi hazır</span>
                    </div>
                </a>

                <a href="{{ route('admin.siteAudit.index') }}" class="bg-white hover:bg-emerald-50/50 p-6 rounded-3xl border border-gray-200/80 shadow-xs transition block group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase">Sistem Sağlığı</span>
                        <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-base">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-3xl font-black text-emerald-600">%{{ $stats['health_score'] ?? 100 }}</div>
                    <div class="mt-1 text-xs text-emerald-700 font-bold flex items-center gap-1">
                        <span>Tam Yeşil • Kusursuz</span>
                    </div>
                </a>

                <a href="{{ route('admin.pages.index') }}" class="bg-white hover:bg-blue-50/50 p-6 rounded-3xl border border-gray-200/80 shadow-xs transition block group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase">Hukuk &amp; Sayfalar</span>
                        <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-base">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-3xl font-black text-gray-900">{{ $stats['total_pages'] ?? 0 }} Sayfa</div>
                    <div class="mt-1 text-xs text-blue-600 font-bold flex items-center gap-1">
                        <span>Sözleşmeler ve KVKK metinleri</span>
                    </div>
                </a>
            </div>
        </div>
    @endif
</x-app-layout>
