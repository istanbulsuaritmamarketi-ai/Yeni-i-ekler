<!DOCTYPE html>
<html lang="tr" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#ffffff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Usta Ekranı">
    <title>Sipariş Hazırlama Masası — {{ config('app.name', 'Yeniçiçek') }}</title>

    <!-- TailwindCSS & FontAwesome & Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        [x-cloak] { display: none !important; }
        
        /* Çiçekçi Atölye Dokunmatik Kaydırma */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-800 font-sans antialiased"
      x-data="craftsmanApp()" 
      x-init="initApp()">

    <!-- CANLI ALARM FLASI (Yeni sipariş düştüğünde ekran hafifçe parlar) -->
    <div x-show="flashingAlert" x-cloak class="fixed inset-0 pointer-events-none z-[9998] ring-8 ring-blue-500/30 transition-opacity"></div>

    <div class="min-h-full flex flex-col">
        
        <!-- ============================================================== -->
        <!-- 1. ÜST KOKPİT BARI (SADE VE NET)                                -->
        <!-- ============================================================== -->
        <header class="sticky top-0 z-40 bg-white border-b border-slate-200 px-4 sm:px-6 py-3 shadow-2xs">
            <div class="max-w-[1720px] mx-auto flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                
                <!-- Sol: Logo & Başlık -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.workshop.index') }}" 
                       class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center text-sm shadow-xs shrink-0 hover:bg-blue-700 transition" title="Mobil Atölye">
                        <i class="fas fa-hammer"></i>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="font-bold text-sm sm:text-base text-slate-900 tracking-tight flex items-center gap-1.5">
                                <span>Sipariş Hazırlama Masası</span>
                                <span class="text-[10px] bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded-full border border-emerald-200 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Canlı</span>
                                </span>
                            </h1>
                        </div>
                        <p class="text-[11px] text-slate-400">
                            {{ config('app.name', 'Yeniçiçek') }} • USTA HAZIRLIK KOKPİTİ
                        </p>
                    </div>
                </div>

                <!-- Orta: Dijital Saat & Canlı Sayaçlar -->
                <div class="flex items-center gap-2 sm:gap-3 overflow-x-auto no-scrollbar py-0.5">
                    <!-- Saat -->
                    <div class="bg-slate-100 px-3 py-1.5 rounded-xl text-center shrink-0">
                        <span class="font-mono font-bold text-xs text-slate-600" x-text="currentTime">--:--:--</span>
                    </div>

                    <!-- Bekleyen / Hazırlanacak Sayaç -->
                    <div class="bg-blue-50 border border-blue-200/80 px-3 py-1.5 rounded-xl flex items-center gap-1.5 shrink-0">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span class="text-xs font-bold text-blue-700">
                            <span x-text="counts.preparing">0</span> Hazırlanacak
                        </span>
                    </div>

                    <!-- Foto Onayında Sayaç -->
                    <div class="bg-purple-50 border border-purple-200/80 px-3 py-1.5 rounded-xl flex items-center gap-1.5 shrink-0">
                        <i class="fas fa-camera text-purple-600 text-xs"></i>
                        <span class="text-xs font-bold text-purple-700">
                            <span x-text="counts.photo_review">0</span> Onayda
                        </span>
                    </div>

                    <!-- Kuryede Sayaç -->
                    <div class="bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-xl flex items-center gap-1.5 shrink-0">
                        <i class="fas fa-motorcycle text-slate-500 text-xs"></i>
                        <span class="text-xs font-bold text-slate-700">
                            <span x-text="counts.on_delivery">0</span> Kuryede
                        </span>
                    </div>
                </div>

                <!-- Sağ: Kontroller (Ses, Ekran Kilidi, Tam Ekran, Çıkış) -->
                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                    
                    <!-- Ekran Uyanık Tut (Wake Lock) -->
                    <button type="button" 
                            @click="toggleWakeLock()" 
                            :title="wakeLockActive ? 'Ekran kapanmaz (Uyanık)' : 'Ekran kapanabilir (Tıkla ve açık tut)'"
                            class="px-2.5 py-1.5 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 border cursor-pointer"
                            :class="wakeLockActive ? 'bg-amber-50 text-amber-800 border-amber-200 shadow-2xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'">
                        <i class="fas" :class="wakeLockActive ? 'fa-sun text-amber-500' : 'fa-moon text-slate-400'"></i>
                        <span class="hidden md:inline text-[11px]" x-text="wakeLockActive ? 'Ekran Açık' : 'Uyku Kilidi'"></span>
                    </button>

                    <!-- Sesli Bildirim Zil Butonu -->
                    <button type="button" 
                            @click="toggleSound()" 
                            :title="soundEnabled ? 'Sipariş Zili Açık (Kapatmak için tıkla)' : 'Sipariş Zili Kapalı'"
                            class="px-2.5 py-1.5 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 border cursor-pointer"
                            :class="soundEnabled ? 'bg-blue-50 text-blue-700 border-blue-200 shadow-2xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'">
                        <i class="fas" :class="soundEnabled ? 'fa-bell text-blue-600' : 'fa-bell-slash text-slate-400'"></i>
                        <span class="hidden md:inline text-[11px]" x-text="soundEnabled ? 'Zil Açık' : 'Zil Kapalı'"></span>
                    </button>

                    <!-- Ses Testi -->
                    <button type="button" 
                            @click="playChime()" 
                            title="Çiçekçi zilini test et"
                            class="p-1.5 px-2.5 rounded-xl text-xs font-semibold bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 transition flex items-center gap-1 cursor-pointer">
                        <i class="fas fa-volume-up text-blue-600"></i>
                        <span class="hidden sm:inline text-[11px]">Test</span>
                    </button>

                    <!-- Tam Ekran Kiosk Butonu -->
                    <button type="button" 
                            @click="toggleFullscreen()" 
                            title="Tam ekran yap"
                            class="p-1.5 px-2.5 rounded-xl text-xs font-semibold bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 transition flex items-center gap-1 cursor-pointer">
                        <i class="fas" :class="fullscreenActive ? 'fa-compress text-slate-600' : 'fa-expand text-slate-600'"></i>
                        <span class="hidden sm:inline text-[11px]" x-text="fullscreenActive ? 'Küçült' : 'Tam Ekran'"></span>
                    </button>

                    <!-- Admin Paneline Dönüş -->
                    <a href="{{ route('admin.workshop.index') }}" 
                       title="Normal Atölye Listesine Dön"
                       class="py-1.5 px-3 rounded-xl text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white transition flex items-center gap-1.5 shadow-xs">
                        <i class="fas fa-arrow-left text-[11px]"></i>
                        <span>Panel</span>
                    </a>
                </div>

            </div>
        </header>

        <!-- ============================================================== -->
        <!-- 2. HIZLI FİLTRELER & ARAMA ÇUBUĞU                               -->
        <!-- ============================================================== -->
        <div class="px-4 sm:px-6 py-2.5 bg-white border-b border-slate-200">
            <div class="max-w-[1720px] mx-auto flex flex-col md:flex-row md:items-center justify-between gap-3">
                
                <!-- Durum Butonları (Sekmeler) -->
                <nav class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5">
                    <a href="{{ route('admin.workshop.craftsman', ['tab' => 'preparing']) }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shrink-0 {{ $tab === 'preparing' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <i class="fas fa-hammer text-[11px]"></i>
                        <span>1. Hazırlanacak</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'preparing' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700 font-mono' }}">{{ $counts['preparing'] }}</span>
                    </a>

                    <a href="{{ route('admin.workshop.craftsman', ['tab' => 'photo_review']) }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shrink-0 {{ $tab === 'photo_review' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <i class="fas fa-camera text-[11px]"></i>
                        <span>2. Fotoğraf Onayında</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'photo_review' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700 font-mono' }}">{{ $counts['photo_review'] }}</span>
                    </a>

                    <a href="{{ route('admin.workshop.craftsman', ['tab' => 'on_delivery']) }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shrink-0 {{ $tab === 'on_delivery' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <i class="fas fa-motorcycle text-[11px]"></i>
                        <span>3. Kuryede / Yolda</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'on_delivery' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700 font-mono' }}">{{ $counts['on_delivery'] }}</span>
                    </a>

                    <a href="{{ route('admin.workshop.craftsman', ['tab' => 'today']) }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shrink-0 {{ $tab === 'today' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <i class="fas fa-calendar-day text-[11px]"></i>
                        <span>Bugünkü Tüm Siparişler</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'today' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700 font-mono' }}">{{ $counts['today'] }}</span>
                    </a>
                </nav>

                <!-- Canlı Arama -->
                <div class="relative w-full md:w-64 shrink-0">
                    <input type="text" 
                           x-model="searchFilter" 
                           placeholder="Sipariş no, alıcı ara..." 
                           class="w-full pl-8 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500">
                    <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <template x-if="searchFilter">
                        <button type="button" @click="searchFilter = ''" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                            <i class="fas fa-times"></i>
                        </button>
                    </template>
                </div>

            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 3. SİPARİŞ KARTLARI IZGARASI                                   -->
        <!-- ============================================================== -->
        <main class="flex-1 p-4 sm:p-6 max-w-[1720px] mx-auto w-full">
            
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4 sm:gap-5 items-start">
                
                @forelse($orders as $order)
                    @php
                        $firstItem = $order->items->first();
                        $isVip = $order->delivery_fee > 0;
                        $isToday = $order->delivery_date && \Carbon\Carbon::parse($order->delivery_date)->isToday();
                        $hasCustomBouquet = $order->items->contains(fn($item) => $item->item_type === 'custom_bouquet' || (isset($item->options['custom_bouquet']) && $item->options['custom_bouquet']));
                        $customItem = $order->items->firstWhere('item_type', 'custom_bouquet');
                        $options = $customItem ? ($customItem->options ?? []) : [];
                    @endphp

                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col justify-between relative group"
                         x-show="matchesFilter('{{ strtolower($order->order_number . ' ' . $order->recipient_name . ' ' . $order->customer_name . ' ' . $order->recipient_district . ' ' . ($firstItem->product->name ?? '')) }}')"
                         id="craftsman-order-{{ $order->id }}">
                        
                        <!-- Üst Bar: Sipariş No & Teslimat Zamanı & VIP -->
                        <div class="p-3.5 pb-3 border-b border-slate-100 bg-slate-50/60 flex items-start justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-black text-sm text-slate-900">
                                        #{{ $order->order_number }}
                                    </span>
                                    @if($isVip)
                                        <span class="bg-amber-100 text-amber-800 text-[10px] font-black px-2 py-0.5 rounded-md flex items-center gap-1">
                                            <i class="fas fa-bolt text-[9px]"></i> VIP 2 SAATTE
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="text-[11px] font-medium text-slate-500 mt-1 flex items-center gap-1.5">
                                    <i class="fas fa-clock text-blue-600 text-[10px]"></i>
                                    <span>{{ $isToday ? 'Bugün' : ($order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d.m.Y') : 'Tarih Belirtilmedi') }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-slate-800 font-bold">{{ $order->delivery_time_slot ?: 'Standart Aralık' }}</span>
                                </div>
                            </div>

                            <!-- Aşama Rozeti -->
                            <div class="text-right shrink-0">
                                @php
                                    $statusBadge = match($order->status) {
                                        'Yeni Sipariş', 'pending' => 'bg-amber-50 text-amber-800 border-amber-200',
                                        'Atölyede Hazırlanıyor', 'Hazırlanıyor', 'processing' => 'bg-blue-50 text-blue-800 border-blue-200',
                                        'Fotoğraf Onayında' => 'bg-purple-50 text-purple-800 border-purple-200',
                                        'Kuryede / Dağıtımda', 'Yolda', 'on_delivery', 'Kuryede' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                                        'Teslim Edildi', 'Tamamlandı' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200'
                                    };
                                @endphp
                                <span class="inline-block px-2.5 py-1 rounded-lg text-[10px] font-bold border {{ $statusBadge }}">
                                    {{ $order->status }}
                                </span>
                            </div>
                        </div>

                        <!-- Orta Gövde: Çiçekler, Reçete ve Kart Notu -->
                        <div class="p-3.5 space-y-3">
                            
                            <!-- 1. Çiçek & Kalemler Bilgisi -->
                            <div class="space-y-2">
                                @foreach($order->items as $item)
                                    <div class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-50 border border-slate-100">
                                        <div class="w-12 h-12 rounded-lg bg-white overflow-hidden shrink-0 border border-slate-200 flex items-center justify-center">
                                            @if($item->product && $item->product->image_url)
                                                <img src="{{ $item->product->image_url }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <div class="text-blue-500 text-sm"><i class="fas fa-spa"></i></div>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h3 class="font-bold text-xs text-slate-900 truncate" title="{{ $item->product->name ?? 'Çiçek' }}">
                                                {{ $item->product->name ?? 'Çiçek' }}
                                            </h3>
                                            <div class="text-[11px] text-slate-500 mt-0.5">
                                                Adet: <span class="text-slate-900 font-mono font-bold">{{ $item->quantity }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- 2. Varsa Özel Buket Reçetesi -->
                            @if($hasCustomBouquet && isset($options['stems']))
                                <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-2.5 space-y-1.5 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-blue-900 flex items-center gap-1.5 text-[11px]">
                                            <i class="fas fa-receipt text-blue-600"></i>
                                            <span>Özel Buket Reçetesi</span>
                                        </span>
                                        <span class="bg-white border border-blue-200 text-blue-800 text-[10px] font-mono font-bold px-2 py-0.5 rounded-md">
                                            Toplam {{ $options['total_stems'] ?? count($options['stems']) }} Dal
                                        </span>
                                    </div>

                                    <!-- Çiçek Dalları Dökümü -->
                                    <div class="flex flex-wrap gap-1 pt-0.5">
                                        @foreach($options['stems'] as $stem)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-white border border-blue-200 text-[11px] font-medium text-slate-800">
                                                <i class="fas fa-spa text-blue-500 text-[9px]"></i>
                                                <span class="font-mono text-blue-700 font-bold">{{ $stem['quantity'] }}x</span>
                                                <span>{{ $stem['name'] }}</span>
                                            </span>
                                        @endforeach
                                    </div>

                                    <!-- Ambalaj & Kurdele -->
                                    <div class="grid grid-cols-2 gap-2 text-[10px] pt-1.5 border-t border-blue-100 text-slate-600">
                                        <div>
                                            <span class="text-slate-400">Ambalaj:</span>
                                            <span class="font-bold text-slate-800">{{ $options['package_color'] ?? 'Standart' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400">Kurdele:</span>
                                            <span class="font-bold text-slate-800">{{ $options['ribbon_color'] ?? 'Standart' }}</span>
                                        </div>
                                    </div>

                                    @if(!empty($options['instructions']))
                                        <div class="text-[11px] bg-white p-2 rounded-lg border border-blue-100 text-slate-700 mt-1">
                                            <span class="text-[9px] text-slate-400 font-bold block uppercase">Müşteri Notu:</span>
                                            "{{ $options['instructions'] }}"
                                        </div>
                                    @endif
                                </div>
                            @elseif($order->notes && str_contains($order->notes, 'ÖZEL BUKET REÇETESİ'))
                                <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-2.5 text-xs text-slate-700">
                                    <span class="font-bold text-blue-900 flex items-center gap-1.5 mb-1"><i class="fas fa-receipt text-blue-600"></i><span>Buket Reçetesi:</span></span>
                                    <p class="font-medium text-[11px]">{{ $order->notes }}</p>
                                </div>
                            @endif

                            <!-- 3. Müşteri Kart Notu -->
                            @if($order->card_note)
                                <div class="bg-amber-50/50 p-2.5 rounded-xl border border-amber-200/70 space-y-1">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-amber-900 flex items-center gap-1 text-[11px]">
                                            <i class="fas fa-envelope-open-text text-amber-600"></i>
                                            <span>Kart Notu</span>
                                        </span>
                                        @if($order->is_anonymous)
                                            <span class="bg-purple-50 text-purple-700 text-[9px] font-bold px-1.5 py-0.5 rounded border border-purple-200">
                                                Gizli Gönderici
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-xs font-serif italic text-slate-800 leading-relaxed bg-white/70 p-2 rounded-lg border border-amber-100">
                                        "{{ $order->card_note }}"
                                    </p>
                                    <div class="text-[10px] text-slate-500 flex justify-between pt-0.5">
                                        <span>İmza:</span>
                                        <span class="font-bold text-slate-700">
                                            {{ $order->is_anonymous ? 'İsimsiz / Gizli' : ($order->sender_name ?: $order->customer_name) }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <!-- 4. Alıcı Bilgileri & Teslimat Bölgesi -->
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-1">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500">Alıcı:</span>
                                    <span class="font-bold text-slate-900">{{ $order->recipient_name ?: $order->customer_name }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500">Teslimat İlçesi:</span>
                                    <span class="font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-200/60">{{ $order->recipient_district ?: ($order->region->name ?? 'İstanbul') }}</span>
                                </div>
                                <div class="pt-1 border-t border-slate-200/60 text-slate-500 text-[11px] truncate">
                                    <span class="text-slate-400 font-medium">Adres:</span> {{ $order->address }}
                                </div>
                            </div>

                            <!-- 5. Fotoğraf Durumu Önizlemesi -->
                            @if($order->preparation_photo)
                                <div class="p-2 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ asset('storage/' . $order->preparation_photo) }}" alt="Hazırlık Fotoğrafı" class="w-10 h-10 rounded-lg object-cover border border-emerald-300">
                                        <div>
                                            <span class="text-xs font-bold text-emerald-800 block">✓ Fotoğraf Çekildi</span>
                                            <span class="text-[10px] text-emerald-600">Müşteri onayına hazır</span>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $order->preparation_photo) }}" target="_blank" class="p-1.5 text-emerald-700 hover:text-emerald-900 text-xs font-bold">
                                        <i class="fas fa-expand"></i>
                                    </a>
                                </div>
                            @endif

                        </div>

                        <!-- Alt Aksiyon Çubuğu -->
                        <div class="p-3 border-t border-slate-100 bg-slate-50/70 space-y-2">
                            
                            <!-- Fotoğraf Çek & Durum Butonları -->
                            <div class="grid grid-cols-2 gap-2">
                                
                                <!-- Fotoğraf Yükle Butonu -->
                                <button type="button" 
                                        @click="triggerCameraUpload({{ $order->id }})"
                                        class="py-2.5 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-2xs transition active:scale-95 cursor-pointer">
                                    <i class="fas fa-camera text-xs"></i>
                                    <span>{{ $order->preparation_photo ? 'Yeniden Çek' : 'Fotoğraf Çek' }}</span>
                                </button>

                                <!-- Kuryeye Teslim Et Butonu -->
                                <button type="button" 
                                        @click="quickUpdateStatus({{ $order->id }}, 'Kuryede / Dağıtımda')"
                                        class="py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-2xs transition active:scale-95 cursor-pointer">
                                    <i class="fas fa-motorcycle text-xs"></i>
                                    <span>Kuryeye Ver</span>
                                </button>

                            </div>

                            <!-- Fiş Yazdır & Detay Butonları -->
                            <div class="grid grid-cols-2 gap-2">
                                
                                <!-- Fiş Bas Butonu -->
                                <a href="{{ route('admin.orders.print', $order) }}" target="_blank"
                                   class="py-2 px-3 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center gap-1.5 transition border border-slate-200">
                                    <i class="fas fa-print text-xs text-slate-400"></i>
                                    <span>Fiş (A4)</span>
                                </a>

                                <!-- Hazırlanıyor Durumuna Al Butonu -->
                                @if($order->status !== 'Atölyede Hazırlanıyor')
                                    <button type="button" 
                                            @click="quickUpdateStatus({{ $order->id }}, 'Atölyede Hazırlanıyor')"
                                            class="py-2 px-3 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-xs flex items-center justify-center gap-1 transition cursor-pointer">
                                        <i class="fas fa-hammer text-xs text-slate-500"></i>
                                        <span>Hazırlanıyor Yap</span>
                                    </button>
                                @else
                                    <button type="button" 
                                            @click="quickUpdateStatus({{ $order->id }}, 'Fotoğraf Onayında')"
                                            class="py-2 px-3 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 font-bold text-xs flex items-center justify-center gap-1 transition cursor-pointer">
                                        <i class="fas fa-check text-xs"></i>
                                        <span>Onaya Gönder</span>
                                    </button>
                                @endif

                            </div>

                        </div>

                    </div>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <div class="bg-white rounded-3xl border border-slate-200 p-12 max-w-md mx-auto shadow-xs space-y-3">
                            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl mx-auto">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h3 class="font-bold text-slate-900 text-base">Bu sekmede bekleyen sipariş bulunmuyor</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Atölyedeki çiçekler hazırlandı. Yeni sipariş düştüğünde zil sesiyle burada otomatik listelenecektir.
                            </p>
                        </div>
                    </div>
                @endforelse

            </div>

        </main>

        <!-- ============================================================== -->
        <!-- 4. GİZLİ KAMERA INPUT                                          -->
        <!-- ============================================================== -->
        <input type="file" 
               id="craftsmanCameraInput" 
               accept="image/*" 
               capture="environment" 
               class="hidden" 
               @change="onFileSelected($event)">

        <!-- ============================================================== -->
        <!-- 5. YENİ SİPARİŞ DÜŞTÜĞÜNDE SADE ALARM MODALI                   -->
        <!-- ============================================================== -->
        <div x-show="alarmModalOpen" x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            
            <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-5 text-center transform transition-all">
                
                <!-- Çan Simgesi -->
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mx-auto shadow-2xs">
                    <i class="fas fa-bell"></i>
                </div>

                <div>
                    <span class="inline-block px-3 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider mb-2">
                        Yeni Sipariş Geldi!
                    </span>
                    <h2 class="text-2xl font-black text-slate-900 font-mono" x-text="'#' + (latestAlarmOrder ? latestAlarmOrder.order_number : '')"></h2>
                </div>

                <!-- Detay Kartı -->
                <template x-if="latestAlarmOrder">
                    <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200 text-left space-y-2">
                        <div class="flex items-center gap-3">
                            <img :src="latestAlarmOrder.product_image" alt="" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0">
                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-xs text-slate-900 truncate" x-text="latestAlarmOrder.product_name"></h4>
                                <div class="text-[11px] text-blue-600 font-bold mt-0.5" x-text="latestAlarmOrder.recipient_district + ' • ' + latestAlarmOrder.recipient_name"></div>
                                <div class="text-[11px] text-slate-500 font-semibold mt-0.5" x-text="latestAlarmOrder.delivery_time_slot"></div>
                            </div>
                        </div>

                        <template x-if="latestAlarmOrder.card_note">
                            <div class="pt-2 border-t border-slate-200 text-xs text-slate-700 italic font-serif">
                                "<span x-text="latestAlarmOrder.card_note"></span>"
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Aksiyon Butonları -->
                <div class="flex flex-col sm:flex-row items-center gap-2 pt-2">
                    <button type="button" 
                            @click="dismissAlarm()"
                            class="w-full py-3.5 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition active:scale-95 cursor-pointer flex items-center justify-center gap-2">
                        <i class="fas fa-hammer"></i>
                        <span>Sesi Kapat &amp; Çiçeği Hazırla</span>
                    </button>

                    <button type="button" 
                            @click="dismissAlarm(); if(latestAlarmOrder) triggerCameraUpload(latestAlarmOrder.id);"
                            class="w-full sm:w-auto py-3.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-200 cursor-pointer flex items-center justify-center gap-1.5">
                        <i class="fas fa-camera text-blue-600"></i>
                        <span>Kamera</span>
                    </button>
                </div>

            </div>

        </div>

    </div>

    <!-- ============================================================== -->
    <!-- 6. ALPINE.JS MOTORU (SES, WAKE LOCK, CANLI POLING)             -->
    <!-- ============================================================== -->
    <script>
        function craftsmanApp() {
            return {
                currentTime: '',
                wakeLockActive: false,
                wakeLockSentinel: null,
                soundEnabled: true,
                speechEnabled: true,
                fullscreenActive: false,
                searchFilter: '',
                lastKnownId: {{ $latestOrderId }},
                alarmModalOpen: false,
                latestAlarmOrder: null,
                flashingAlert: false,
                targetUploadOrderId: null,
                audioCtx: null,
                counts: {
                    preparing: {{ $counts['preparing'] }},
                    photo_review: {{ $counts['photo_review'] }},
                    on_delivery: {{ $counts['on_delivery'] }},
                    today: {{ $counts['today'] }},
                },

                initApp() {
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);

                    // Yerel ses ayarlarını yükle
                    if (localStorage.getItem('craftsman_sound') !== null) {
                        this.soundEnabled = localStorage.getItem('craftsman_sound') === 'true';
                    }

                    // Otomatik Wake Lock dene
                    this.requestWakeLock();

                    // 4 saniyede bir yeni siparişleri kontrol et
                    setInterval(() => {
                        this.pollNewOrders();
                    }, 4000);

                    // Bildirim İzni İste (Web Push)
                    if ('Notification' in window && Notification.permission === 'default') {
                        document.addEventListener('click', () => {
                            Notification.requestPermission();
                        }, { once: true });
                    }
                },

                updateClock() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                },

                // ==========================================
                // ☀️ EKRAN UYANIK TUTMA (WAKE LOCK API)
                // ==========================================
                async requestWakeLock() {
                    if ('wakeLock' in navigator) {
                        try {
                            this.wakeLockSentinel = await navigator.wakeLock.request('screen');
                            this.wakeLockActive = true;
                            this.wakeLockSentinel.addEventListener('release', () => {
                                this.wakeLockActive = false;
                            });
                        } catch (err) {
                            console.log('Wake Lock Error:', err);
                        }
                    }
                },

                async toggleWakeLock() {
                    if (this.wakeLockActive && this.wakeLockSentinel) {
                        await this.wakeLockSentinel.release();
                        this.wakeLockSentinel = null;
                        this.wakeLockActive = false;
                    } else {
                        await this.requestWakeLock();
                    }
                },

                // ==========================================
                // ⛶ TAM EKRAN KİOSK MODU
                // ==========================================
                toggleFullscreen() {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen().then(() => {
                            this.fullscreenActive = true;
                        }).catch(e => console.log(e));
                    } else {
                        if (document.exitFullscreen) {
                            document.exitFullscreen().then(() => {
                                this.fullscreenActive = false;
                            });
                        }
                    }
                },

                // ==========================================
                // 🔔 ÇİÇEKÇİ ZİL SESİ SENTEZİ (WEB AUDIO API)
                // ==========================================
                getAudioContext() {
                    if (!this.audioCtx) {
                        const AudioContext = window.AudioContext || window.webkitAudioContext;
                        if (AudioContext) {
                            this.audioCtx = new AudioContext();
                        }
                    }
                    if (this.audioCtx && this.audioCtx.state === 'suspended') {
                        this.audioCtx.resume();
                    }
                    return this.audioCtx;
                },

                playChime() {
                    try {
                        const ctx = this.getAudioContext();
                        if (!ctx) return;

                        // 4 Tonlu Parlak Çan (C5, E5, G5, C6)
                        const notes = [523.25, 659.25, 783.99, 1046.50];
                        const now = ctx.currentTime;

                        notes.forEach((freq, index) => {
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();

                            osc.type = 'triangle';
                            osc.frequency.setValueAtTime(freq, now + index * 0.12);

                            gain.gain.setValueAtTime(0, now + index * 0.12);
                            gain.gain.linearRampToValueAtTime(0.5, now + index * 0.12 + 0.02);
                            gain.gain.exponentialRampToValueAtTime(0.001, now + index * 0.12 + 0.7);

                            osc.connect(gain);
                            gain.connect(ctx.destination);

                            osc.start(now + index * 0.12);
                            osc.stop(now + index * 0.12 + 0.7);
                        });
                    } catch (e) {
                        console.error('Audio error:', e);
                    }
                },

                toggleSound() {
                    this.soundEnabled = !this.soundEnabled;
                    localStorage.setItem('craftsman_sound', this.soundEnabled);
                    if (this.soundEnabled) {
                        this.playChime();
                    }
                },

                speakSpeech(text) {
                    if ('speechSynthesis' in window && this.speechEnabled) {
                        window.speechSynthesis.cancel();
                        const utterance = new SpeechSynthesisUtterance(text);
                        utterance.lang = 'tr-TR';
                        utterance.rate = 1.0;
                        utterance.pitch = 1.1;
                        window.speechSynthesis.speak(utterance);
                    }
                },

                // ==========================================
                // 📡 CANLI SİPARİŞ KONTROLÜ & ALARM TETİKLEME
                // ==========================================
                pollNewOrders() {
                    fetch('{{ route("admin.workshop.craftsman.check") }}?last_id=' + this.lastKnownId, {
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data && data.has_new && data.orders && data.orders.length > 0) {
                            this.lastKnownId = data.latest_id;
                            this.counts = data.counts;
                            const newest = data.orders[0];
                            this.latestAlarmOrder = newest;
                            this.alarmModalOpen = true;
                            this.flashingAlert = true;

                            // 1. Ses Çal
                            if (this.soundEnabled) {
                                this.playChime();
                                setTimeout(() => this.playChime(), 1200);
                            }

                            // 2. Sesli Anons
                            if (newest.speech_text) {
                                setTimeout(() => this.speakSpeech(newest.speech_text), 800);
                            }

                            // 3. Mobil Titreşim
                            if ('vibrate' in navigator) {
                                navigator.vibrate([400, 200, 400, 200, 800]);
                            }

                            // 4. Web Push Bildirimi
                            if ('Notification' in window && Notification.permission === 'granted') {
                                new Notification('YENİ ÇİÇEK SİPARİŞİ GELDİ!', {
                                    body: '#' + newest.order_number + ' — ' + newest.recipient_district + ' • ' + newest.product_name,
                                    icon: newest.product_image || '/images/logo-badge.png',
                                    badge: '/images/logo-badge.png'
                                });
                            }
                        }
                    })
                    .catch(err => console.log('Poll error:', err));
                },

                dismissAlarm() {
                    this.alarmModalOpen = false;
                    this.flashingAlert = false;
                    window.location.reload();
                },

                matchesFilter(text) {
                    if (!this.searchFilter) return true;
                    return text.includes(this.searchFilter.toLowerCase());
                },

                // ==========================================
                // 📸 KAMERA / FOTOĞRAF YÜKLEME
                // ==========================================
                triggerCameraUpload(orderId) {
                    this.targetUploadOrderId = orderId;
                    const input = document.getElementById('craftsmanCameraInput');
                    if (input) {
                        input.value = '';
                        input.click();
                    }
                },

                onFileSelected(event) {
                    const file = event.target.files[0];
                    if (!file || !this.targetUploadOrderId) return;

                    const orderId = this.targetUploadOrderId;
                    const formData = new FormData();
                    formData.append('preparation_photo', file);
                    formData.append('status', 'Fotoğraf Onayında');
                    formData.append('_token', '{{ csrf_token() }}');

                    const targetCard = document.getElementById('craftsman-order-' + orderId);
                    if (targetCard) {
                        targetCard.classList.add('opacity-50');
                    }

                    fetch('/admin/atolye/' + orderId + '/foto', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (targetCard) targetCard.classList.remove('opacity-50');
                        if (res.success) {
                            window.location.reload();
                        } else {
                            alert(res.message || 'Fotoğraf yüklenemedi.');
                        }
                    })
                    .catch(e => {
                        if (targetCard) targetCard.classList.remove('opacity-50');
                        alert('Fotoğraf yüklenirken bir sorun oluştu.');
                    });
                },

                // ==========================================
                // 🛵 HIZLI DURUM GÜNCELLEME
                // ==========================================
                quickUpdateStatus(orderId, newStatus) {
                    if (!confirm('#' + orderId + ' siparişinin durumunu "' + newStatus + '" olarak güncellemek istiyor musunuz?')) {
                        return;
                    }

                    const formData = new FormData();
                    formData.append('status', newStatus);
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch('/admin/atolye/' + orderId + '/durum', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            window.location.reload();
                        } else {
                            alert(res.message || 'Durum güncellenemedi.');
                        }
                    })
                    .catch(e => alert('Hata oluştu.'));
                }
            };
        }
    </script>
</body>
</html>
