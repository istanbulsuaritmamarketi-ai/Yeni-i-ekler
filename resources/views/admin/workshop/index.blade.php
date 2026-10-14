<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-lg shadow-sm shrink-0">
                    <i class="fas fa-camera"></i>
                </div>
                <div>
                    <h2 class="font-black text-xl sm:text-2xl text-slate-900 leading-tight flex items-center gap-2">
                        <span>Çiçekçi Atölye &amp; Mobil Kamera</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-700 uppercase tracking-wider">
                            Mobil Hızlı Mod
                        </span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Siparişleri hızlı hazırla, kamerasından foto çek, tek dokunuşla kuryeye veya onaya ilet</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="openQrScannerModal()" 
                        class="bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs px-3.5 py-2.5 rounded-xl shadow-xs transition flex items-center gap-2 active:scale-95">
                    <i class="fas fa-qrcode text-blue-400"></i>
                    <span>Fiş QR Oku</span>
                </button>

                <a href="{{ route('admin.orders.index') }}" 
                   class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs px-3.5 py-2.5 rounded-xl transition flex items-center gap-1.5">
                    <i class="fas fa-list"></i>
                    <span class="hidden sm:inline">Normal Liste</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6" x-data="workshopApp()">
        <div class="max-w-[1536px] mx-auto px-3 sm:px-6 lg:px-8 space-y-4">

            <!-- BİLDİRİMLER -->
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
                    <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- MOBİL ÜST BAR & HIZLI ARAMA & OTO-YENİLE -->
            <div class="bg-white rounded-3xl p-3 sm:p-4 border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
                <!-- Arama Formu -->
                <form action="{{ route('admin.workshop.index') }}" method="GET" class="flex-1 flex gap-2">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ $search ?? '' }}" 
                               placeholder="Sipariş No (#YC-..), Alıcı Adı, Telefon veya Semt..." 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-2xl transition shadow-xs active:scale-95">
                        Bul
                    </button>
                    @if(!empty($search))
                        <a href="{{ route('admin.workshop.index', ['tab' => $tab]) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold px-3 py-2.5 rounded-2xl transition flex items-center">
                            Sıfırla
                        </a>
                    @endif
                </form>

                <!-- Canlı Bilgi & Otomatik Yenile -->
                <div class="flex items-center justify-between md:justify-end gap-3 text-xs">
                    <div class="flex items-center gap-2 bg-slate-50 px-3 py-2 rounded-2xl border border-slate-200">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-slate-600 font-bold">Canlı Masa:</span>
                        <span class="font-mono font-black text-blue-600">{{ $counts['preparing'] }} Bekleyen</span>
                    </div>

                    <button type="button" @click="toggleAutoRefresh()" 
                            class="px-3 py-2 rounded-2xl border text-xs font-bold transition flex items-center gap-1.5"
                            :class="autoRefresh ? 'bg-emerald-50 border-emerald-300 text-emerald-700' : 'bg-slate-50 border-slate-200 text-slate-600'">
                        <i class="fas fa-sync-alt" :class="autoRefresh ? 'fa-spin text-emerald-600' : ''"></i>
                        <span x-text="autoRefresh ? 'Oto-Yenile (Açık)' : 'Oto-Yenile (Kapalı)'"></span>
                    </button>
                </div>
            </div>

            <!-- ATÖLYE HIZLI DURUM SEKMELERİ (DOKUNMATİK KAYDIRILABİLİR) -->
            <div class="bg-white rounded-3xl p-2 border border-slate-200 shadow-xs overflow-x-auto no-scrollbar">
                <nav class="flex items-center gap-1.5 text-xs font-bold whitespace-nowrap">
                    <!-- 1. Hazırlanacaklar (Varsayılan & En Önemli) -->
                    <a href="{{ route('admin.workshop.index', ['tab' => 'preparing', 'search' => $search]) }}" 
                       class="px-4 py-2.5 rounded-2xl transition flex items-center gap-2 {{ $tab === 'preparing' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                        <i class="fas fa-seedling"></i>
                        <span>1. Hazırlanacaklar</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'preparing' ? 'bg-white/25 text-white font-black' : 'bg-blue-100 text-blue-800 font-black' }}">
                            {{ $counts['preparing'] }}
                        </span>
                    </a>

                    <!-- 2. Fotoğraf Onayında -->
                    <a href="{{ route('admin.workshop.index', ['tab' => 'photo_review', 'search' => $search]) }}" 
                       class="px-4 py-2.5 rounded-2xl transition flex items-center gap-2 {{ $tab === 'photo_review' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                        <i class="fas fa-camera"></i>
                        <span>2. Foto Onayında</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'photo_review' ? 'bg-white/25 text-white font-black' : 'bg-purple-100 text-purple-800 font-black' }}">
                            {{ $counts['photo_review'] }}
                        </span>
                    </a>

                    <!-- 3. Kuryede / Dağıtımda -->
                    <a href="{{ route('admin.workshop.index', ['tab' => 'on_delivery', 'search' => $search]) }}" 
                       class="px-4 py-2.5 rounded-2xl transition flex items-center gap-2 {{ $tab === 'on_delivery' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                        <i class="fas fa-motorcycle"></i>
                        <span>3. Kuryede / Dağıtımda</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'on_delivery' ? 'bg-white/25 text-white font-black' : 'bg-indigo-100 text-indigo-800 font-black' }}">
                            {{ $counts['on_delivery'] }}
                        </span>
                    </a>

                    <!-- 4. Tamamlananlar -->
                    <a href="{{ route('admin.workshop.index', ['tab' => 'completed', 'search' => $search]) }}" 
                       class="px-4 py-2.5 rounded-2xl transition flex items-center gap-2 {{ $tab === 'completed' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                        <i class="fas fa-check-double"></i>
                        <span>Tamamlananlar</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'completed' ? 'bg-white/25 text-white font-black' : 'bg-emerald-100 text-emerald-800 font-black' }}">
                            {{ $counts['completed'] }}
                        </span>
                    </a>

                    <!-- 5. Tümü -->
                    <a href="{{ route('admin.workshop.index', ['tab' => 'all', 'search' => $search]) }}" 
                       class="px-4 py-2.5 rounded-2xl transition flex items-center gap-2 {{ $tab === 'all' ? 'bg-slate-800 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                        <span>Tümü</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'all' ? 'bg-white/25 text-white font-black' : 'bg-slate-100 text-slate-600' }}">
                            {{ $counts['all'] }}
                        </span>
                    </a>
                </nav>
            </div>

            <!-- CANLI GELEN YENİ SİPARİŞ UYARI ÇUBUĞU (ZİL ÇALDIĞINDA ANINDA BELİRİR) -->
            <div x-show="incomingOrdersNotice > 0" 
                 x-cloak
                 class="bg-blue-600 text-white rounded-3xl p-4 px-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xl ring-4 ring-blue-200 animate-pulse">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-white/20 text-white flex items-center justify-center text-lg shrink-0">
                        <i class="fas fa-bell"></i>
                    </span>
                    <div>
                        <div class="font-black text-sm">
                            <span x-text="incomingOrdersNotice"></span> Yeni Sipariş Masaya Düştü!
                        </div>
                        <p class="text-xs text-blue-100">Yeni hazırlık detaylarını ve çiçekleri görmek için listeyi yenileyin.</p>
                    </div>
                </div>
                <button type="button" onclick="window.location.reload()" 
                        class="bg-white text-blue-700 font-black text-xs px-5 py-2.5 rounded-2xl shadow-md hover:bg-blue-50 transition active:scale-95 shrink-0 flex items-center justify-center gap-2">
                    <i class="fas fa-sync-alt"></i>
                    <span>Listeyi Hemen Güncelle</span>
                </button>
            </div>

            <!-- SİPARİŞ KARTLARI LİSTESİ -->
            @if($orders->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($orders as $order)
                        @php
                            $firstItem = $order->items->first();
                            $product = $firstItem ? $firstItem->product : null;
                            $productImg = $product ? $product->image_url : asset('images/products/6.png');
                            $hasExtraItems = $order->items->count() > 1;
                            $isVip = $order->delivery_fee > 0;
                            $hasPhoto = !empty($order->preparation_photo);
                            $photoUrl = $hasPhoto ? asset('storage/' . $order->preparation_photo) : null;
                            $trackingUrl = route('order.track', ['order_number' => $order->order_number, 'phone' => $order->customer_phone]);
                            $whatsAppMsg = rawurlencode("Merhaba {$order->customer_name}, #{$order->order_number} numaralı çiçek siparişiniz atölyemizde özenle hazırlandı! Canlı sipariş ve teslimat takibiniz için linke tıklayabilirsiniz: {$trackingUrl}");
                        @endphp

                        <div class="bg-white rounded-3xl border {{ $isVip ? 'border-amber-300 ring-2 ring-amber-100 shadow-md' : 'border-slate-200 shadow-xs' }} overflow-hidden flex flex-col justify-between transition-all hover:border-blue-300"
                             id="order-card-{{ $order->id }}">
                            
                            <!-- Kart Üst Başlık: Sipariş No & Teslimat Zamanı & VIP -->
                            <div class="p-3.5 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-black text-xs text-slate-800 bg-white px-2 py-1 rounded-xl border border-slate-200">
                                        #{{ $order->order_number }}
                                    </span>

                                    @if($isVip)
                                        <span class="bg-gradient-to-r from-amber-500 to-orange-500 text-white text-[10px] font-black px-2 py-0.5 rounded-lg flex items-center gap-1 shadow-xs animate-pulse">
                                            <i class="fas fa-bolt"></i> VIP HIZLI KURYE
                                        </span>
                                    @endif
                                </div>

                                <!-- Durum Rozeti -->
                                <div>
                                    @if($order->status === 'Yeni Sipariş' || $order->status === 'Atölyede Hazırlanıyor' || $order->status === 'Hazırlanıyor')
                                        <span class="bg-blue-100 text-blue-800 text-[11px] font-black px-2.5 py-1 rounded-xl flex items-center gap-1">
                                            <i class="fas fa-hammer text-[10px]"></i> Hazırlanıyor
                                        </span>
                                    @elseif($order->status === 'Fotoğraf Onayında')
                                        <span class="bg-purple-100 text-purple-800 text-[11px] font-black px-2.5 py-1 rounded-xl flex items-center gap-1">
                                            <i class="fas fa-camera text-[10px]"></i> Foto Onayında
                                        </span>
                                    @elseif($order->status === 'Kuryede / Dağıtımda' || $order->status === 'Kuryede')
                                        <span class="bg-indigo-100 text-indigo-800 text-[11px] font-black px-2.5 py-1 rounded-xl flex items-center gap-1">
                                            <i class="fas fa-motorcycle text-[10px]"></i> Kuryede
                                        </span>
                                    @elseif($order->status === 'Teslim Edildi')
                                        <span class="bg-emerald-100 text-emerald-800 text-[11px] font-black px-2.5 py-1 rounded-xl flex items-center gap-1">
                                            <i class="fas fa-check text-[10px]"></i> Teslim Edildi
                                        </span>
                                    @else
                                        <span class="bg-slate-100 text-slate-700 text-[11px] font-black px-2.5 py-1 rounded-xl">
                                            {{ $order->status }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Kart Gövdesi: Çiçek Resmi, Ürün Bilgisi, Hediyeler -->
                            <div class="p-3.5 space-y-3 flex-1">
                                <!-- Teslimat Saati & Alıcı Semti -->
                                <div class="flex items-center justify-between text-xs bg-blue-50/70 p-2 rounded-2xl border border-blue-100">
                                    <div class="flex items-center gap-1.5 text-blue-950 font-black">
                                        <i class="fas fa-clock text-blue-500"></i>
                                        <span>{{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->translatedFormat('d M') : 'Bugün' }}</span>
                                        <span class="text-blue-300">|</span>
                                        <span>{{ $order->delivery_time_slot ?: 'Standart Aralık' }}</span>
                                    </div>
                                    <div class="font-bold text-blue-700 bg-white px-2 py-0.5 rounded-lg border border-blue-200 text-[11px]">
                                        <i class="fas fa-map-marker-alt text-blue-500 mr-0.5"></i>
                                        {{ $order->recipient_district ?: ($order->region->name ?? 'İstanbul') }}
                                    </div>
                                </div>

                                <!-- Ana Çiçek Görseli ve Bilgisi -->
                                <div class="flex gap-3 items-center">
                                    <div class="w-20 h-20 rounded-2xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0 shadow-xs relative group cursor-pointer"
                                         onclick="previewImageModal('{{ $productImg }}', '{{ addslashes($product->name ?? 'Çiçek') }}')">
                                        <img src="{{ $productImg }}" alt="Çiçek" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white transition text-xs">
                                            <i class="fas fa-search-plus"></i>
                                        </div>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-black text-slate-900 text-sm leading-snug line-clamp-2">
                                            {{ $product->name ?? 'Özel Çiçek Tasarımı' }}
                                        </h3>
                                        <div class="text-xs text-slate-500 mt-0.5 font-bold flex items-center gap-2">
                                            <span>Adet: <strong class="text-slate-800">{{ $firstItem->quantity ?? 1 }}</strong></span>
                                            <span>•</span>
                                            <span>Alıcı: <strong class="text-slate-800">{{ $order->recipient_name ?: $order->customer_name }}</strong></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- ÖZEL TASARIM BUKET REÇETESİ (ATÖLYE İÇİN) -->
                                @php
                                    $customBouquetItem = $order->items->first(fn($it) => ($it->item_type === 'custom_bouquet' || !empty($it->options['custom_bouquet'])));
                                @endphp
                                 @if($customBouquetItem && !empty($customBouquetItem->options))
                                    <div class="p-2.5 rounded-2xl bg-blue-50/90 border border-blue-200 text-xs space-y-1.5">
                                        <div class="text-[10px] font-black uppercase text-blue-800 tracking-wider flex items-center justify-between">
                                            <span class="flex items-center gap-1">
                                                <i class="fas fa-magic text-blue-600"></i>
                                                <span>Özel Tasarım Buket ({{ $customBouquetItem->options['total_stems'] ?? '' }} Dal)</span>
                                            </span>
                                            <span class="text-[9px] text-slate-500 font-bold">
                                                {{ $customBouquetItem->options['package_color'] ?? '' }} • {{ $customBouquetItem->options['ribbon_color'] ?? '' }}
                                            </span>
                                        </div>
                                        @if(!empty($customBouquetItem->options['stems']))
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @foreach($customBouquetItem->options['stems'] as $stemChoice)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-white border border-blue-200 text-blue-950 font-bold text-[11px] shadow-2xs">
                                                        <i class="fas fa-spa text-blue-500 text-[9px]"></i>
                                                        <span>{{ $stemChoice['name'] }}</span>
                                                        <span class="text-blue-600 font-black">({{ $stemChoice['quantity'] }}x)</span>
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if(!empty($customBouquetItem->options['instructions']))
                                            <div class="text-[10px] text-slate-600 italic bg-white/70 p-1.5 rounded-lg border border-blue-100">
                                                “{{ $customBouquetItem->options['instructions'] }}”
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- EK HEDİYELER (ÇİÇEKÇİ ASLA UNUTMASIN!) -->
                                @if($hasExtraItems)
                                    <div class="p-2.5 rounded-2xl bg-amber-50/90 border border-amber-200 text-xs space-y-1">
                                        <div class="text-[10px] font-black uppercase text-amber-800 tracking-wider flex items-center gap-1">
                                            <i class="fas fa-gift text-amber-600"></i>
                                            <span>Siparişe Dahil Ek Hediyeler:</span>
                                        </div>
                                        <div class="flex flex-wrap gap-1.5 mt-1">
                                            @foreach($order->items->slice(1) as $extraItem)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-white border border-amber-300 text-amber-950 font-bold text-[11px] shadow-2xs">
                                                    <i class="fas fa-gift text-amber-600 text-xs"></i>
                                                    <span>{{ $extraItem->product->name ?? 'Ek Ürün' }}</span>
                                                    <span class="text-amber-600 font-black">({{ $extraItem->quantity }}x)</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Kart Notu (Varsa Katlanabilir) -->
                                @if($order->card_note)
                                    <div x-data="{ openNote: false }" class="text-xs border border-slate-200 rounded-2xl p-2 bg-slate-50">
                                        <button type="button" @click="openNote = !openNote" class="w-full flex items-center justify-between text-slate-700 font-bold">
                                            <span class="flex items-center gap-1.5 text-blue-700">
                                                <i class="fas fa-envelope-open-text"></i>
                                                <span>Zarflı Kart Notu</span>
                                            </span>
                                            <i class="fas fa-chevron-down text-slate-400 transition" :class="openNote ? 'rotate-180' : ''"></i>
                                        </button>
                                        <div x-show="openNote" x-collapse class="mt-2 pt-2 border-t border-slate-200 text-slate-700 italic font-serif leading-relaxed">
                                            “{{ $order->card_note }}”
                                            @if($order->sender_name)
                                                <div class="mt-1 text-right not-italic font-sans text-[10px] font-bold text-slate-500">
                                                    — {{ $order->sender_name }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- MEVCUT ATÖLYE FOTOĞRAFI (YÜKLENDİYSE) -->
                                @if($hasPhoto)
                                    <div class="p-2.5 rounded-2xl bg-purple-50 border border-purple-200 flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-2">
                                            <img src="{{ $photoUrl }}" alt="Atölye Foto" 
                                                 onclick="previewImageModal('{{ $photoUrl }}', 'Atölye Hazırlık Fotoğrafı — #{{ $order->order_number }}')"
                                                 class="w-12 h-12 rounded-xl object-cover border border-purple-300 shadow-xs cursor-pointer hover:scale-105 transition">
                                            <div>
                                                <span class="text-[10px] font-black uppercase text-purple-700 block">Atölye Fotosu Hazır</span>
                                                <span class="text-xs font-bold text-purple-950">Fotoğraf Onayda</span>
                                            </div>
                                        </div>

                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}?text={{ $whatsAppMsg }}" 
                                           target="_blank"
                                           class="bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold px-2.5 py-1.5 rounded-xl transition flex items-center gap-1 shadow-xs shrink-0">
                                            <i class="fab fa-whatsapp"></i> WhatsApp
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Kart Altı: HIZLI KAMERA & İŞLEM BUTONLARI -->
                            <div class="p-3.5 bg-slate-50 border-t border-slate-100 space-y-2">
                                <!-- BÜYÜK DOKUNMATİK KAMERA BUTONU -->
                                <button type="button" 
                                        @click="openCaptureModal({{ $order->id }}, '{{ $order->order_number }}', '{{ addslashes($product->name ?? 'Çiçek Tasarımı') }}')"
                                        class="w-full py-3 px-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black text-sm shadow-sm hover:shadow transition flex items-center justify-center gap-2.5 active:scale-98">
                                    <i class="fas fa-camera text-base"></i>
                                    <span>{{ $hasPhoto ? 'Fotoğrafı Değiştir / Çek' : 'Fotoğraf Çek & Durum Güncelle' }}</span>
                                </button>

                                <!-- YAN YANA HIZLI BUTONLAR: DURUM DEĞİŞTİR / FİŞ YAZDIR / DETAY -->
                                <div class="grid grid-cols-3 gap-1.5 text-xs font-bold">
                                    <!-- Hızlı Durum Açılır Menüsü -->
                                    <div x-data="{ openStatus: false }" class="relative">
                                        <button type="button" @click="openStatus = !openStatus"
                                                class="w-full py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 rounded-xl transition flex items-center justify-center gap-1">
                                            <i class="fas fa-exchange-alt text-[10px]"></i>
                                            <span>Durum</span>
                                        </button>
                                        <div x-show="openStatus" @click.outside="openStatus = false" 
                                             class="absolute bottom-full left-0 mb-1 w-48 bg-white rounded-2xl shadow-xl border border-slate-200 p-1.5 z-20 space-y-1">
                                            <button type="button" @click="quickUpdateStatus({{ $order->id }}, 'Atölyede Hazırlanıyor'); openStatus = false;"
                                                    class="w-full text-left px-3 py-1.5 text-xs rounded-xl hover:bg-blue-50 hover:text-blue-700 font-bold transition flex items-center gap-2">
                                                <i class="fas fa-hammer text-blue-500"></i> Hazırlanıyor
                                            </button>
                                            <button type="button" @click="quickUpdateStatus({{ $order->id }}, 'Fotoğraf Onayında'); openStatus = false;"
                                                    class="w-full text-left px-3 py-1.5 text-xs rounded-xl hover:bg-purple-50 hover:text-purple-700 font-bold transition flex items-center gap-2">
                                                <i class="fas fa-camera text-purple-500"></i> Foto Onayında
                                            </button>
                                            <button type="button" @click="quickUpdateStatus({{ $order->id }}, 'Kuryede / Dağıtımda'); openStatus = false;"
                                                    class="w-full text-left px-3 py-1.5 text-xs rounded-xl hover:bg-indigo-50 hover:text-indigo-700 font-bold transition flex items-center gap-2">
                                                <i class="fas fa-motorcycle text-indigo-500"></i> Kuryeye Ver
                                            </button>
                                            <button type="button" @click="quickUpdateStatus({{ $order->id }}, 'Teslim Edildi'); openStatus = false;"
                                                    class="w-full text-left px-3 py-1.5 text-xs rounded-xl hover:bg-emerald-50 hover:text-emerald-700 font-bold transition flex items-center gap-2">
                                                <i class="fas fa-check-circle text-emerald-500"></i> Teslim Edildi
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Fiş Yazdır -->
                                    <a href="{{ route('admin.orders.print', $order) }}" target="_blank"
                                       class="py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 rounded-xl transition flex items-center justify-center gap-1">
                                        <i class="fas fa-print text-[10px]"></i>
                                        <span>Fiş Yazdır</span>
                                    </a>

                                    <!-- 4 Aşamalı Detay Kokpiti -->
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 rounded-xl transition flex items-center justify-center gap-1">
                                        <i class="fas fa-eye text-[10px]"></i>
                                        <span>Detay</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-xs space-y-3">
                    <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto text-2xl">
                        <i class="fas fa-spa"></i>
                    </div>
                    <h3 class="text-base font-black text-slate-800">Bu sekmede şu an işlem bekleyen sipariş bulunmuyor</h3>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                        Tebrikler! Atölyedeki çiçekler hazırlandı veya aradığınız kriterde sipariş yok.
                    </p>
                    <a href="{{ route('admin.workshop.index', ['tab' => 'all']) }}" 
                       class="inline-flex items-center gap-2 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 px-4 py-2.5 rounded-2xl transition">
                        <span>Tüm Siparişleri Göster</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            @endif

        </div>

        <!-- ============================================================= -->
        <!-- 📸 MOBİL KAMERA ÇEKİM & HIZLI YÜKLEME MODALI                    -->
        <!-- ============================================================= -->
        <div x-show="showCaptureModal" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4">
            
            <div @click.outside="closeCaptureModal()" 
                 class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[92vh]">
                
                <!-- Modal Başlığı -->
                <div class="p-4 bg-blue-600 text-white flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-sm">
                            <i class="fas fa-camera"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-sm" x-text="'Atölye Fotoğrafı: #' + activeOrderNumber"></h3>
                            <p class="text-[11px] text-blue-100 truncate max-w-[240px]" x-text="activeProductName"></p>
                        </div>
                    </div>
                    <button type="button" @click="closeCaptureModal()" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white text-xs transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Gövdesi: Kamera ve Önizleme -->
                <div class="p-4 sm:p-6 overflow-y-auto space-y-4 flex-1">
                    
                    <!-- Kamera Çekim veya Önizleme Kutusu -->
                    <div class="relative rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 flex flex-col items-center justify-center overflow-hidden min-h-[260px]">
                        
                        <!-- Henüz fotoğraf seçilmediyse -->
                        <template x-if="!capturedImage">
                            <div class="p-6 text-center space-y-4">
                                <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl mx-auto shadow-inner">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <div>
                                    <div class="font-black text-slate-800 text-sm">Çiçeğin Fotoğrafını Çekin</div>
                                    <p class="text-xs text-slate-500 mt-1 max-w-xs">
                                        Telefonunuzun arka kamerası açılacaktır. Hazırladığınız çiçeği net bir şekilde fotoğraflayın.
                                    </p>
                                </div>

                                <!-- HTML5 Kamera Açıcı (capture="environment" arka kamerayı doğrudan açar) -->
                                <div>
                                    <label class="cursor-pointer inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-black text-sm px-6 py-3 rounded-2xl shadow-md transition active:scale-95">
                                        <i class="fas fa-camera text-base"></i>
                                        <span>KAMERAYI AÇ</span>
                                        <input type="file" 
                                               accept="image/*" 
                                               capture="environment" 
                                               @change="handleFileSelected($event)" 
                                               class="hidden">
                                    </label>
                                </div>
                            </div>
                        </template>

                        <!-- Fotoğraf Çekildiğinde / Önizleme -->
                        <template x-if="capturedImage">
                            <div class="relative w-full h-full">
                                <img :src="capturedImage" alt="Önizleme" class="w-full h-72 sm:h-80 object-cover rounded-xl shadow-inner">
                                
                                <div class="absolute top-2 right-2">
                                    <label class="cursor-pointer bg-slate-900/80 hover:bg-slate-900 text-white text-xs font-bold px-3 py-1.5 rounded-xl shadow transition flex items-center gap-1.5">
                                        <i class="fas fa-redo"></i> Yeniden Çek
                                        <input type="file" accept="image/*" capture="environment" @change="handleFileSelected($event)" class="hidden">
                                    </label>
                                </div>

                                <div class="absolute bottom-2 left-2 bg-emerald-600/90 text-white text-[10px] font-black px-2.5 py-1 rounded-lg backdrop-blur-xs flex items-center gap-1">
                                    <i class="fas fa-compress-arrows-alt"></i> Otomatik Sıkıştırıldı (~250 KB)
                                </div>
                            </div>
                        </template>

                        <!-- Yükleme Göstergesi -->
                        <div x-show="isUploading" class="absolute inset-0 bg-white/90 backdrop-blur-xs flex flex-col items-center justify-center gap-3 z-30">
                            <div class="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                            <span class="text-xs font-black text-blue-600">Fotoğraf Yükleniyor &amp; İşleniyor...</span>
                        </div>
                    </div>

                    <!-- Çekilen Fotoğraf Sonrası Hızlı Eylem Seçenekleri -->
                    <template x-if="capturedImage">
                        <div class="space-y-2 pt-2">
                            <div class="text-[11px] font-black uppercase text-slate-400 tracking-wider">
                                Fotoğrafı Kaydet ve Durumu Güncelle:
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <!-- Seçenek 1: Fotoğraf Onayında -->
                                <button type="button" 
                                        @click="submitPhoto('Fotoğraf Onayında')"
                                        :disabled="isUploading"
                                        class="py-3 px-4 rounded-2xl bg-purple-600 hover:bg-purple-700 text-white font-black text-xs shadow-xs transition flex items-center justify-center gap-2 active:scale-95 disabled:opacity-50">
                                    <i class="fas fa-camera"></i>
                                    <span>Fotoğrafı Kaydet &amp; Onaya Gönder</span>
                                </button>

                                <!-- Seçenek 2: Kuryede / Dağıtımda -->
                                <button type="button" 
                                        @click="submitPhoto('Kuryede / Dağıtımda')"
                                        :disabled="isUploading"
                                        class="py-3 px-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-xs transition flex items-center justify-center gap-2 active:scale-95 disabled:opacity-50">
                                    <i class="fas fa-motorcycle"></i>
                                    <span>Fotoğrafı Kaydet &amp; Kuryeye Ver</span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Modal Alt Barı -->
                <div class="p-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-medium">Yeniçiçek Atölye Uygulaması</span>
                    <button type="button" @click="closeCaptureModal()" class="text-slate-600 hover:text-slate-900 font-bold px-3 py-1.5 rounded-xl">
                        Vazgeç
                    </button>
                </div>

            </div>
        </div>

        <!-- ============================================================= -->
        <!-- 📷 FİŞ QR KOD OKUYUCU MODALI                                   -->
        <!-- ============================================================= -->
        <div id="qr-modal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/85 backdrop-blur-sm hidden flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">
                <div class="p-4 bg-slate-900 text-white flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-qrcode text-blue-400"></i>
                        <h3 class="font-black text-sm">Sipariş Fişi QR Kodunu Okutun</h3>
                    </div>
                    <button type="button" onclick="closeQrScannerModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white text-xs">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-4 space-y-4">
                    <p class="text-xs text-slate-500 text-center">
                        Yazdırılan A4 sipariş fişindeki sağ alt QR kodu kameranıza yaklaştırın.
                    </p>

                    <!-- QR Okuyucu Kamera Alanı -->
                    <div id="reader" class="rounded-2xl overflow-hidden border-2 border-dashed border-blue-300 min-h-[250px] bg-black"></div>

                    <!-- Manuel Kod Arama Kutusu (Kamera desteği yoksa alternatif) -->
                    <div class="pt-2 border-t border-slate-200">
                        <div class="text-[11px] font-bold text-slate-500 mb-1">Veya Sipariş Numarasını Yazın:</div>
                        <div class="flex gap-2">
                            <input type="text" id="manual-qr-input" placeholder="Örn: YC-20261009-8472" 
                                   class="flex-1 px-3 py-2 border border-slate-200 rounded-xl text-xs uppercase font-mono">
                            <button type="button" onclick="handleManualQrSubmit()" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-xl">
                                Git
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================= -->
        <!-- 🔍 RESİM BÜYÜTME MODALI                                        -->
        <!-- ============================================================= -->
        <div id="image-preview-modal" class="fixed inset-0 z-50 bg-black/85 backdrop-blur-sm hidden flex items-center justify-center p-4" onclick="closeImagePreviewModal()">
            <div class="relative max-w-3xl max-h-[90vh] bg-transparent flex flex-col items-center" onclick="event.stopPropagation()">
                <button type="button" onclick="closeImagePreviewModal()" class="absolute -top-10 right-0 text-white text-xl hover:text-blue-400">
                    <i class="fas fa-times"></i>
                </button>
                <img id="preview-modal-img" src="" alt="Büyük Görsel" class="max-w-full max-h-[80vh] rounded-2xl shadow-2xl object-contain border border-white/20">
                <div id="preview-modal-title" class="text-white text-xs font-bold mt-3 text-center"></div>
            </div>
        </div>

    </div>

    <!-- HTML5 QR KOD OKUYUCU KÜTÜPHANESİ (CDN) -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        function workshopApp() {
            return {
                showCaptureModal: false,
                activeOrderId: null,
                activeOrderNumber: '',
                activeProductName: '',
                capturedImage: null,
                capturedFileBlob: null,
                isUploading: false,
                autoRefresh: false,
                refreshIntervalId: null,
                incomingOrdersNotice: 0,

                init() {
                    // Sayfa açıldığında URL'de active_order parametresi varsa o modalı doğrudan aç
                    const urlParams = new URLSearchParams(window.location.search);
                    const directOrderId = urlParams.get('capture_order');
                    if (directOrderId) {
                        const card = document.getElementById('order-card-' + directOrderId);
                        if (card) {
                            card.scrollIntoView({ behavior: 'smooth' });
                        }
                    }

                    // Yeni sipariş düştüğünde canlı olayı yakala
                    window.addEventListener('new-order-received', (event) => {
                        if (event.detail && event.detail.count) {
                            this.incomingOrdersNotice += event.detail.count;
                        }
                    });
                },

                toggleAutoRefresh() {
                    this.autoRefresh = !this.autoRefresh;
                    if (this.autoRefresh) {
                        this.refreshIntervalId = setInterval(() => {
                            window.location.reload();
                        }, 30000); // Her 30 saniyede bir yenile
                    } else {
                        if (this.refreshIntervalId) clearInterval(this.refreshIntervalId);
                    }
                },

                openCaptureModal(orderId, orderNumber, productName) {
                    this.activeOrderId = orderId;
                    this.activeOrderNumber = orderNumber;
                    this.activeProductName = productName;
                    this.capturedImage = null;
                    this.capturedFileBlob = null;
                    this.showCaptureModal = true;
                },

                closeCaptureModal() {
                    this.showCaptureModal = false;
                    this.capturedImage = null;
                    this.capturedFileBlob = null;
                    this.activeOrderId = null;
                },

                // Telefon kamerasından gelen yüksek çözünürlüklü fotoğrafı Canvas ile 250KB'a sıkıştır
                handleFileSelected(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            // Canvas Sıkıştırma: Max Genişlik 1600px
                            const maxWidth = 1600;
                            let width = img.width;
                            let height = img.height;

                            if (width > maxWidth) {
                                height = Math.round((height * maxWidth) / width);
                                width = maxWidth;
                            }

                            const canvas = document.createElement('canvas');
                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);

                            canvas.toBlob((blob) => {
                                this.capturedFileBlob = blob;
                                this.capturedImage = URL.createObjectURL(blob);
                            }, 'image/jpeg', 0.85);
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                // Fotoğrafı Sunucuya Gönder ve Durumu Güncelle
                async submitPhoto(targetStatus) {
                    if (!this.capturedFileBlob || !this.activeOrderId) return;

                    this.isUploading = true;

                    const formData = new FormData();
                    formData.append('preparation_photo', this.capturedFileBlob, 'atolye_' + this.activeOrderNumber + '.jpg');
                    formData.append('status', targetStatus);
                    formData.append('_token', '{{ csrf_token() }}');

                    try {
                        const response = await fetch(`/admin/atolye/${this.activeOrderId}/foto`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.closeCaptureModal();
                            window.location.reload();
                        } else {
                            alert(result.message || 'Fotoğraf yüklenirken bir hata oluştu.');
                        }
                    } catch (error) {
                        console.error('Yükleme hatası:', error);
                        // Fallback: Standart form post ile tekrar dene
                        alert('Fotoğraf yüklenemedi. Lütfen internet bağlantınızı kontrol edin.');
                    } finally {
                        this.isUploading = false;
                    }
                },

                // Tek Dokunuşla Durum Güncelle
                async quickUpdateStatus(orderId, newStatus) {
                    try {
                        const response = await fetch(`/admin/atolye/${orderId}/durum`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ status: newStatus })
                        });

                        const result = await response.json();
                        if (result.success) {
                            window.location.reload();
                        }
                    } catch (e) {
                        console.error(e);
                        window.location.reload();
                    }
                }
            };
        }

        // ================= QR SCANNER KONTROLLERİ =================
        let html5QrScanner = null;

        function openQrScannerModal() {
            document.getElementById('qr-modal').classList.remove('hidden');

            if (typeof Html5QrcodeScanner !== 'undefined') {
                html5QrScanner = new Html5QrcodeScanner("reader", { 
                    fps: 10, 
                    qrbox: { width: 220, height: 220 } 
                }, false);

                html5QrScanner.render(onScanSuccess, onScanFailure);
            }
        }

        function closeQrScannerModal() {
            document.getElementById('qr-modal').classList.add('hidden');
            if (html5QrScanner) {
                try {
                    html5QrScanner.clear();
                } catch(e) {}
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            // QR kod okundu: decodedText ya doğrudan order_number ya da tracking URL içerir
            closeQrScannerModal();

            let orderNo = decodedText;
            if (decodedText.includes('order_number=')) {
                const url = new URL(decodedText);
                orderNo = url.searchParams.get('order_number') || decodedText;
            } else if (decodedText.includes('/')) {
                const parts = decodedText.split('/');
                orderNo = parts[parts.length - 1];
            }

            // Arama yap veya sayfayı o siparişe odakla
            window.location.href = `/admin/atolye?search=${encodeURIComponent(orderNo)}`;
        }

        function onScanFailure(error) {
            // Sürekli hata basmaması için sessizce devam eder
        }

        function handleManualQrSubmit() {
            const val = document.getElementById('manual-qr-input').value.trim();
            if (val) {
                closeQrScannerModal();
                window.location.href = `/admin/atolye?search=${encodeURIComponent(val)}`;
            }
        }

        // ================= RESİM ÖNİZLEME MODALI =================
        function previewImageModal(src, title) {
            document.getElementById('preview-modal-img').src = src;
            document.getElementById('preview-modal-title').innerText = title || '';
            document.getElementById('image-preview-modal').classList.remove('hidden');
        }

        function closeImagePreviewModal() {
            document.getElementById('image-preview-modal').classList.add('hidden');
        }
    </script>
</x-app-layout>
