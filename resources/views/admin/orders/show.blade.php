<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-black text-2xl text-slate-900 leading-tight">
                        Sipariş #{{ $order->order_number }}
                    </h2>
                    @php
                        $statusBadge = match($order->status) {
                            'Yeni Sipariş', 'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                            'Atölyede Hazırlanıyor', 'Hazırlanıyor', 'processing' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'Fotoğraf Onayında' => 'bg-purple-100 text-purple-800 border-purple-200',
                            'Kuryede / Dağıtımda', 'Yolda', 'on_delivery', 'Kuryede' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                            'Teslim Edildi', 'Tamamlandı' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'İptal Edildi', 'İptal edildi', 'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                            default => 'bg-slate-100 text-slate-800 border-slate-200'
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-black border {{ $statusBadge }}">
                        {{ $order->status }}
                    </span>
                    @if($order->delivery_fee > 0)
                        <span class="px-3 py-1 rounded-full text-xs font-black bg-blue-600 text-white flex items-center gap-1.5 shadow-xs">
                            <i class="fas fa-bolt text-[10px]"></i> VIP Hızlı Kurye (+₺{{ number_format($order->delivery_fee, 0, ',', '.') }})
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Sipariş Tarihi: {{ $order->created_at->translatedFormat('d F Y, H:i') }} ({{ $order->created_at->diffForHumans() }})
                </p>
            </div>

            @php
                $cleanPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '90' . substr($cleanPhone, 1);
                } elseif (!str_starts_with($cleanPhone, '90')) {
                    $cleanPhone = '90' . $cleanPhone;
                }
                
                $itemsSummary = $order->items->map(fn($i) => ($i->product->name ?? 'Çiçek') . ' (' . $i->quantity . ' Adet)')->implode(', ');
                $deliveryDateStr = $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->translatedFormat('d F Y') : 'Bugün';

                $waMsg = "*TAZECİÇEKLER.COM — SİPARİŞ BİLGİLENDİRMESİ*\n\n"
                       . "Sayın *" . $order->customer_name . "*,\n\n"
                       . "Sipariş Numaranız: *#" . $order->order_number . "*\n"
                       . "Durum: *" . $order->status . "*\n\n"
                       . "*Çiçek & Ürünler:*\n" . $itemsSummary . "\n\n"
                       . "*Alıcı:* " . ($order->recipient_name ?? $order->customer_name) . "\n"
                       . "*Teslimat Bölgesi:* " . ($order->recipient_district ?? ($order->region->name ?? 'İstanbul')) . "\n"
                       . "*Teslimat Zamanı:* " . $deliveryDateStr . " / " . ($order->delivery_time_slot ?? 'Standart') . "\n"
                       . ($order->delivery_fee > 0 ? "*Kurye Hizmeti:* VIP Hızlı Kurye (+₺" . number_format($order->delivery_fee, 0, ',', '.') . ")\n" : "")
                       . ($order->card_note ? "*Kart Notunuz:* \"" . $order->card_note . "\"\n" : "")
                       . "\n*Toplam Tutar:* " . number_format($order->total_amount, 2, ',', '.') . " TL\n\n"
                       . "*Canlı Sipariş & Fotoğraf Takibi:*\n" . url('/siparis-takip?order_number=' . $order->order_number . '&phone=' . $order->customer_phone) . "\n\n"
                       . "Tazecicekler.com'u tercih ettiğiniz için teşekkür ederiz!";

                $whatsappUrl = "https://wa.me/" . $cleanPhone . "?text=" . urlencode($waMsg);
            @endphp

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.workshop.index', ['search' => $order->order_number]) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-3.5 py-2.5 rounded-xl text-xs flex items-center gap-1.5 shadow-xs transition">
                    <i class="fas fa-camera"></i> Atölye &amp; Kamera
                </a>
                <a href="{{ $whatsappUrl }}" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs flex items-center gap-2 shadow-xs transition">
                    <i class="fab fa-whatsapp text-sm"></i> WhatsApp Bildirimi
                </a>
                <a href="{{ route('admin.orders.print', $order) }}" target="_blank" class="bg-slate-900 hover:bg-black text-white font-bold px-4 py-2.5 rounded-xl text-xs flex items-center gap-1.5 shadow-xs transition">
                    <i class="fas fa-print"></i> Fişi Yazdır (A4)
                </a>
                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('#{{ $order->order_number }} numaralı çiçek siparişini silmek istediğinize emin misiniz?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold px-3.5 py-2.5 rounded-xl text-xs flex items-center gap-1.5 shadow-xs transition cursor-pointer">
                        <i class="fas fa-trash-alt"></i> Sil
                    </button>
                </form>
                <a href="{{ route('admin.orders.index') }}" class="bg-white hover:bg-slate-100 text-slate-700 font-bold px-3.5 py-2.5 rounded-xl text-xs border border-slate-200 transition">
                    Siparişler
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="{ 
        step: {{ request()->has('step') ? (int) request('step') : (in_array($order->status, ['Kuryede / Dağıtımda', 'Yolda', 'on_delivery', 'Teslim Edildi', 'Tamamlandı']) ? 4 : ($order->preparation_photo || $order->status === 'Fotoğraf Onayında' ? 3 : ($order->status === 'Atölyede Hazırlanıyor' ? 2 : 1))) }},
        slipPrinted: false
    }">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl text-xs font-bold flex items-center gap-3">
                    <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- ============================================================== -->
            <!-- AŞAMA AŞAMA SİPARİŞ YÖNETİM BARİ (STEPPER WIZARD TABS) -->
            <!-- ============================================================== -->
            <div class="bg-white rounded-3xl p-3 border border-slate-200 shadow-xs">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    
                    <!-- ADIM 1 -->
                    <button type="button" @click="step = 1" 
                            class="flex items-center gap-3 p-3.5 rounded-2xl text-left transition-all cursor-pointer"
                            :class="step === 1 ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25 ring-2 ring-blue-600/20' : 'bg-slate-50 hover:bg-slate-100 text-slate-700'">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shrink-0 transition"
                             :class="step === 1 ? 'bg-white text-blue-600 shadow-xs' : 'bg-white text-slate-600 border border-slate-200'">
                            1
                        </div>
                        <div class="overflow-hidden">
                            <div class="font-black text-xs truncate">1. İncele &amp; Fiş Yazdır</div>
                            <div class="text-[10px] truncate" :class="step === 1 ? 'text-white/80' : 'text-slate-400'">Sipariş detayları &amp; A4 fiş</div>
                        </div>
                    </button>

                    <!-- ADIM 2 -->
                    <button type="button" @click="step = 2" 
                            class="flex items-center gap-3 p-3.5 rounded-2xl text-left transition-all cursor-pointer"
                            :class="step === 2 ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25 ring-2 ring-blue-600/20' : 'bg-slate-50 hover:bg-slate-100 text-slate-700'">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shrink-0 transition"
                             :class="step === 2 ? 'bg-white text-blue-600 shadow-xs' : 'bg-white text-slate-600 border border-slate-200'">
                            2
                        </div>
                        <div class="overflow-hidden">
                            <div class="font-black text-xs truncate">2. Çiçek &amp; Hediyeleri Hazırla</div>
                            <div class="text-[10px] truncate" :class="step === 2 ? 'text-white/80' : 'text-slate-400'">Buket &amp; hediye eşlikçileri</div>
                        </div>
                    </button>

                    <!-- ADIM 3 -->
                    <button type="button" @click="step = 3" 
                            class="flex items-center gap-3 p-3.5 rounded-2xl text-left transition-all cursor-pointer"
                            :class="step === 3 ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25 ring-2 ring-blue-600/20' : 'bg-slate-50 hover:bg-slate-100 text-slate-700'">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shrink-0 transition"
                             :class="step === 3 ? 'bg-white text-blue-600 shadow-xs' : 'bg-white text-slate-600 border border-slate-200'">
                            3
                        </div>
                        <div class="overflow-hidden">
                            <div class="font-black text-xs truncate">3. Atölye Hazırlık Fotoğrafı</div>
                            <div class="text-[10px] truncate" :class="step === 3 ? 'text-white/80' : 'text-slate-400'">
                                {{ $order->preparation_photo ? 'Fotoğraf Yüklendi' : 'Fotoğraf Bekleniyor' }}
                            </div>
                        </div>
                    </button>

                    <!-- ADIM 4 -->
                    <button type="button" @click="step = 4" 
                            class="flex items-center gap-3 p-3.5 rounded-2xl text-left transition-all cursor-pointer"
                            :class="step === 4 ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25 ring-2 ring-blue-600/20' : 'bg-slate-50 hover:bg-slate-100 text-slate-700'">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shrink-0 transition"
                             :class="step === 4 ? 'bg-white text-blue-600 shadow-xs' : 'bg-white text-slate-600 border border-slate-200'">
                            4
                        </div>
                        <div class="overflow-hidden">
                            <div class="font-black text-xs truncate">4. Kurye &amp; Teslimat</div>
                            <div class="text-[10px] truncate" :class="step === 4 ? 'text-white/80' : 'text-slate-400'">Adres &amp; teslim durumu</div>
                        </div>
                    </button>

                </div>
            </div>


            <!-- ============================================================== -->
            <!-- EKRAN 1: SİPARİŞİ İNCELE & FİŞİ YAZDIR -->
            <!-- ============================================================== -->
            <div x-show="step === 1" class="space-y-6">
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- Sol Taraf (7 Kolon): Özellikler, Teslimat Zamanı & Kart Notu -->
                    <div class="lg:col-span-7 space-y-6">
                        
                        <!-- Teslimat & Zaman Bilgisi -->
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-black">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <h3 class="font-black text-sm text-slate-900">Teslimat Zamanı &amp; Kurye Türü</h3>
                                </div>
                                @if($order->delivery_fee > 0)
                                    <span class="bg-blue-600 text-white text-[11px] font-black px-3 py-1 rounded-full flex items-center gap-1">
                                        <i class="fas fa-bolt"></i> VIP Hızlı Kurye (+₺{{ number_format($order->delivery_fee, 0, ',', '.') }})
                                    </span>
                                @else
                                    <span class="bg-emerald-100 text-emerald-800 text-[11px] font-black px-3 py-1 rounded-full">
                                        Standart Teslimat (Ücretsiz)
                                    </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                                    <span class="text-[10px] uppercase font-black text-slate-400 block mb-1">Teslimat Günü</span>
                                    <span class="text-sm font-black text-slate-900">{{ $deliveryDateStr }}</span>
                                </div>
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                                    <span class="text-[10px] uppercase font-black text-slate-400 block mb-1">Teslimat Saat Dilimi</span>
                                    <span class="text-sm font-black text-blue-600">{{ $order->delivery_time_slot ?: 'Standart Aralık' }}</span>
                                </div>
                            </div>

                            <!-- Alıcı Özeti -->
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500 font-bold">Alıcı:</span>
                                    <span class="text-slate-900 font-black text-sm">{{ $order->recipient_name ?: $order->customer_name }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500 font-bold">Telefon:</span>
                                    <span class="text-blue-600 font-bold font-mono">{{ $order->recipient_phone ?: $order->customer_phone }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500 font-bold">İlçe / Bölge:</span>
                                    <span class="text-slate-900 font-black">{{ $order->recipient_district ?: ($order->region->name ?? 'İstanbul') }}</span>
                                </div>
                                <div class="pt-2 border-t border-slate-200">
                                    <span class="text-slate-500 font-bold block mb-0.5">Adres:</span>
                                    <span class="text-slate-800">{{ $order->address }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Mühürlü Kart Notu -->
                        <div class="bg-blue-50/70 rounded-3xl p-6 sm:p-7 border border-blue-200 space-y-3">
                            <div class="flex items-center justify-between pb-3 border-b border-blue-200/80">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center text-sm font-black">
                                        <i class="fas fa-envelope-open-text"></i>
                                    </div>
                                    <h3 class="font-black text-sm text-blue-950">Müşterinin Kart Notu (Zarfa Konulacak)</h3>
                                </div>
                                @if($order->is_anonymous)
                                    <span class="bg-purple-100 text-purple-800 text-[10px] font-black px-2.5 py-1 rounded-full border border-purple-200">
                                        <i class="fas fa-user-secret"></i> Gizli Gönderici
                                    </span>
                                @endif
                            </div>

                            <div class="bg-white p-5 rounded-2xl border border-blue-100 shadow-2xs">
                                @if($order->card_note)
                                    <p class="text-sm sm:text-base font-serif italic text-slate-800 leading-relaxed whitespace-pre-line">
                                        "{{ $order->card_note }}"
                                    </p>
                                    <div class="mt-4 pt-3 border-t border-blue-100 flex items-center justify-between text-xs">
                                        <span class="text-slate-400 font-medium">İmza / Gönderici:</span>
                                        <span class="font-black text-blue-600 font-serif">
                                            {{ $order->is_anonymous ? 'Gizli Bir Hayran' : ($order->sender_name ?: $order->customer_name) }}
                                        </span>
                                    </div>
                                @else
                                    <p class="text-xs text-slate-400 italic">Müşteri bu siparişe kart notu eklemedi.</p>
                                @endif
                            </div>
                        </div>

                    </div>

                    <!-- Sağ Taraf (5 Kolon): Fiş Yazdırma Kutusu & İlerle Butonu -->
                    <div class="lg:col-span-5 space-y-6">
                        
                        <!-- Fiş Yazdır Aksiyon Kartı -->
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-5 text-center">
                            <div class="w-16 h-16 rounded-3xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mx-auto shadow-inner">
                                <i class="fas fa-print"></i>
                            </div>

                            <div>
                                <h3 class="font-black text-base text-slate-900">Sipariş Fişini &amp; Kart Notunu Yazdır</h3>
                                <p class="text-xs text-slate-500 mt-1">
                                    Çiçekçilik standart çift bölmeli A4 çıktısı hazırlanır. Sol taraf zarf içine konur, sağ taraf kuryeye verilir.
                                </p>
                            </div>

                            <!-- Yazdır Butonu -->
                            <a href="{{ route('admin.orders.print', $order) }}" target="_blank" @click="slipPrinted = true"
                               class="w-full py-4 bg-slate-900 hover:bg-black text-white rounded-2xl font-black text-sm flex items-center justify-center gap-2.5 shadow-lg shadow-slate-900/20 transition-all transform hover:-translate-y-0.5">
                                <i class="fas fa-print text-base text-blue-400"></i>
                                <span>A4 Fişi &amp; Kartı Yazdır (Çıktı Al)</span>
                            </a>

                            <!-- Yazdırıldı Onay Rozeti -->
                            <div class="p-3 rounded-2xl border text-xs font-bold transition flex items-center justify-center gap-2"
                                 :class="slipPrinted ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-500'">
                                <i class="fas" :class="slipPrinted ? 'fa-check-circle text-emerald-600' : 'fa-circle text-slate-300'"></i>
                                <span x-text="slipPrinted ? 'Fiş Yazdırıldı! Çiçek hazırlığına geçebilirsiniz.' : 'Henüz fiş yazdırılmadı.'"></span>
                            </div>

                            <!-- Finansal Özet -->
                            <div class="pt-4 border-t border-slate-100 text-xs space-y-2 text-left">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Sipariş Tutarı:</span>
                                    <span class="font-black text-slate-900">₺{{ number_format($order->total_amount, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500">Ödeme Durumu:</span>
                                    @if($order->payment_method === 'cash' || $order->payment_method === 'cod')
                                        <span class="text-amber-700 bg-amber-50 px-2 py-0.5 rounded font-black border border-amber-200">
                                            Kapıda Tahsilat
                                        </span>
                                    @else
                                        <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded font-black border border-emerald-200">
                                            Ödendi (Kredi Kartı)
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- ADIM 1 DEVAM ET BUTONU -->
                        <div class="bg-blue-600 rounded-3xl p-6 text-white text-center space-y-3 shadow-lg shadow-blue-600/20">
                            <span class="text-[11px] font-black uppercase tracking-wider text-blue-100 block">Sıradaki Aşama</span>
                            <h4 class="font-black text-base text-white">Çiçek Hazırlığı ve Atölye</h4>
                            <p class="text-xs text-blue-100">Fişi yazdırdıysanız buket malzemeleri ve ek hediyeleri kontrol etmek için devam ediniz.</p>
                            
                            <button type="button" @click="step = 2" 
                                    class="w-full py-3.5 bg-white hover:bg-blue-50 text-blue-600 rounded-2xl font-black text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <span>Devam Et: Çiçek Hazırlığına Geç</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>

                    </div>

                </div>

            </div>


            <!-- ============================================================== -->
            <!-- EKRAN 2: ÇİÇEK & HEDİYELERİ HAZIRLA (ATÖLYE AŞAMASI) -->
            <!-- ============================================================== -->
            <div x-show="step === 2" x-cloak class="space-y-6">
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- Sol Taraf (7 Kolon): Hazırlanacak Çiçekler & Ek Ürünler Büyük Liste -->
                    <div class="lg:col-span-7 space-y-4">
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-black">
                                        <i class="fas fa-spa"></i>
                                    </div>
                                    <h3 class="font-black text-sm text-slate-900">Hazırlanacak Çiçek Tasarımları &amp; Ürünler</h3>
                                </div>
                                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                                    {{ $order->items->count() }} Kalem Ürün
                                </span>
                            </div>

                            <div class="space-y-4">
                                @foreach($order->items as $item)
                                    @php
                                        $prodImg = $item->product ? $item->product->image_url : asset('images/products/6.png');
                                        $isUpsell = (isset($item->item_type) && $item->item_type === 'upsell');
                                        $isCustomBouquet = ($item->item_type === 'custom_bouquet' || !empty($item->options['custom_bouquet']));
                                    @endphp
                                    <div class="p-4 rounded-2xl border transition-all flex flex-col sm:flex-row items-center sm:items-start gap-4 {{ $isCustomBouquet ? 'bg-blue-50/40 border-blue-200' : ($isUpsell ? 'bg-amber-50/50 border-amber-200' : 'bg-slate-50 border-slate-200') }}">
                                        <!-- Büyük Ürün Görseli -->
                                        <div class="w-24 h-24 rounded-2xl bg-white border border-slate-200 overflow-hidden shrink-0 shadow-xs">
                                            <img src="{{ $prodImg }}" alt="{{ $item->product->name ?? 'Çiçek' }}" class="w-full h-full object-cover">
                                        </div>

                                        <!-- Ürün Bilgisi -->
                                        <div class="flex-1 text-center sm:text-left space-y-1.5 min-w-0">
                                            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                                                <h4 class="font-black text-sm text-slate-900">
                                                    {{ $item->product->name ?? 'Çiçek Tasarımı' }}
                                                </h4>
                                                @if($isCustomBouquet)
                                                    <span class="text-[10px] font-black bg-blue-200 text-blue-900 px-2 py-0.5 rounded-full flex items-center gap-1">
                                                        <i class="fas fa-magic text-[9px]"></i>
                                                        <span>Kişiye Özel Tasarım Buket</span>
                                                    </span>
                                                @elseif($isUpsell)
                                                    <span class="text-[10px] font-black bg-amber-200 text-amber-900 px-2 py-0.5 rounded-full">
                                                        <i class="fas fa-gift mr-1"></i> Ek Hediye Eşlikçisi
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="text-xs text-slate-500">
                                                Adet: <strong class="text-slate-900 font-mono text-sm">{{ $item->quantity }} Adet</strong> &nbsp;|&nbsp; 
                                                Birim Fiyat: <strong>₺{{ number_format($item->price, 2, ',', '.') }}</strong>
                                            </div>

                                            @if($isCustomBouquet && !empty($item->options))
                                                <!-- Özel Buket Reçetesi (Atölye Ustası Kontrolü) -->
                                                <div class="mt-2 p-3 bg-white rounded-xl border border-blue-200 text-xs space-y-2">
                                                    <div class="flex flex-wrap items-center justify-between gap-1 text-[11px] font-bold text-slate-700">
                                                        <span class="text-blue-800 font-black flex items-center gap-1">
                                                            <i class="fas fa-spa text-[10px]"></i>
                                                            <span>Dal Toplamı: {{ $item->options['total_stems'] ?? '?' }} Dal</span>
                                                        </span>
                                                        <span class="text-slate-500 text-[10px]">
                                                            Ambalaj: <strong class="text-slate-800">{{ $item->options['package_color'] ?? '-' }}</strong> | 
                                                            Kurdele: <strong class="text-slate-800">{{ $item->options['ribbon_color'] ?? '-' }}</strong>
                                                        </span>
                                                    </div>
                                                    @if(!empty($item->options['stems']))
                                                        <div class="flex flex-wrap gap-1.5">
                                                            @foreach($item->options['stems'] as $stemChoice)
                                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 border border-blue-100 text-blue-950 font-bold text-[11px]">
                                                                    <i class="fas fa-spa text-blue-500 text-[9px]"></i>
                                                                    <span>{{ $stemChoice['name'] }}</span>
                                                                    <span class="text-blue-600 font-black">({{ $stemChoice['quantity'] }} Dal)</span>
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    @if(!empty($item->options['instructions']))
                                                        <div class="pt-1.5 border-t border-blue-100 text-[11px] text-slate-700">
                                                            <strong class="text-blue-700">Müşteri Notu:</strong> {{ $item->options['instructions'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif($item->product && $item->product->short_description)
                                                <p class="text-[11px] text-slate-600 leading-snug">
                                                    {{ $item->product->short_description }}
                                                </p>
                                            @endif
                                        </div>

                                        <!-- Toplam Tutar -->
                                        <div class="text-right shrink-0">
                                            <span class="text-xs text-slate-400 block">Tutar:</span>
                                            <span class="text-base font-black text-slate-900">
                                                ₺{{ number_format($item->price * $item->quantity, 2, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Sağ Taraf (5 Kolon): Atölye Kontrol Listesi & Aşama Güncelleme -->
                    <div class="lg:col-span-5 space-y-6">
                        
                        <!-- Kontrol Listesi Kartı -->
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-4">
                            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                                <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-black">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <h3 class="font-black text-sm text-slate-900">Atölye Hazırlık Kontrol Listesi</h3>
                            </div>

                            <div class="space-y-3 text-xs">
                                <label class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-100 cursor-pointer hover:bg-slate-100 transition">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                    <span class="font-bold text-slate-800">Taze çiçekler buketlendi ve sapları bağlandı</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-100 cursor-pointer hover:bg-slate-100 transition">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                    <span class="font-bold text-slate-800">Zarflı kart notu çiçeğe iliştirildi</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-100 cursor-pointer hover:bg-slate-100 transition">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                    <span class="font-bold text-slate-800">Varsa çikolata / ek hediyeler pakete dahil edildi</span>
                                </label>
                            </div>

                            <!-- Durumu 'Atölyede Hazırlanıyor' yap formu -->
                            <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="pt-2">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="Atölyede Hazırlanıyor">
                                <input type="hidden" name="step" value="2">
                                <button type="submit" class="w-full py-3 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-2xl font-bold text-xs flex items-center justify-center gap-2 transition cursor-pointer">
                                    <i class="fas fa-paint-brush"></i> Durumu "Atölyede Hazırlanıyor" Olarak İşaretle
                                </button>
                            </form>
                        </div>

                        <!-- Alt Butonlar: Geri & Devam Et -->
                        <div class="flex items-center justify-between gap-3">
                            <button type="button" @click="step = 1" 
                                    class="py-3.5 px-6 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-bold text-xs flex items-center gap-2 transition cursor-pointer">
                                <i class="fas fa-arrow-left"></i> Geri: Sipariş Özeti
                            </button>

                            <button type="button" @click="step = 3" 
                                    class="py-3.5 px-6 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-xs flex items-center gap-2 shadow-md shadow-blue-600/20 transition cursor-pointer">
                                <span>Devam Et: Fotoğraf Aşamasına Geç</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>

                    </div>

                </div>

            </div>


            <!-- ============================================================== -->
            <!-- EKRAN 3: ATÖLYE FOTOĞRAFI & ONAY AŞAMASI -->
            <!-- ============================================================== -->
            <div x-show="step === 3" x-cloak class="space-y-6">
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- Sol Taraf (6 Kolon): Fotoğraf Yükleme Formu -->
                    <div class="lg:col-span-6 space-y-4">
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-sm font-black">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                    <h3 class="font-black text-sm text-slate-900">Atölye Hazırlık Fotoğrafı</h3>
                                </div>
                                @if($order->preparation_photo)
                                    <span class="bg-emerald-100 text-emerald-800 text-[10px] font-black px-2.5 py-1 rounded-full">
                                        <i class="fas fa-check"></i> Fotoğraf Yayında
                                    </span>
                                @endif
                            </div>

                            <p class="text-xs text-slate-500">
                                Çiçek hazırlandıktan sonra cep telefonu veya kamerayla net bir fotoğrafını çekip sisteme yükleyin. Fotoğraf doğrudan müşterinin canlı takip sayfasına yansır.
                            </p>

                            <!-- Fotoğraf Yükleme Formu -->
                            <form action="{{ route('admin.orders.uploadPhoto', $order) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                        {{ $order->preparation_photo ? 'Fotoğrafı Değiştir / Güncelle' : 'Çiçek Fotoğrafı Seç' }}
                                    </label>
                                    <input type="file" name="preparation_photo" accept="image/*" required
                                           class="w-full text-xs text-slate-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 border border-slate-200 rounded-xl p-1.5 bg-slate-50 cursor-pointer">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Aşama Durumunu Güncelle:</label>
                                    <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-purple-500">
                                        <option value="Fotoğraf Onayında" selected>Fotoğraf Onayında (Müşteriye SMS/Mail Gider)</option>
                                        <option value="Kuryede / Dağıtımda">Doğrudan Kuryeye Sevk Et (Kuryede / Dağıtımda)</option>
                                    </select>
                                </div>

                                <button type="submit" class="w-full py-3.5 bg-purple-600 hover:bg-purple-700 text-white rounded-2xl font-black text-xs shadow-md shadow-purple-600/20 transition flex items-center justify-center gap-2 cursor-pointer">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Fotoğrafı Kaydet &amp; Müşteri Takibine Yansıt</span>
                                </button>

                                <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                                    <span class="text-[11px] text-slate-500">Telefondan mı bağlanıyorsunuz?</span>
                                    <a href="{{ route('admin.workshop.index', ['search' => $order->order_number]) }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                                        <i class="fas fa-camera"></i> Mobil Hızlı Kamera Modunu Aç &rarr;
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Sağ Taraf (6 Kolon): Fotoğraf Önizleme & WhatsApp Paylaşımı -->
                    <div class="lg:col-span-6 space-y-4">
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-4">
                            <h3 class="font-black text-sm text-slate-900 pb-3 border-b border-slate-100">
                                Fotoğraf Önizleme (Müşterinin Gördüğü Ekran)
                            </h3>

                            @if($order->preparation_photo)
                                <div class="relative rounded-2xl overflow-hidden border border-purple-200 shadow-md group">
                                    <img src="{{ asset('storage/' . $order->preparation_photo) }}" alt="Hazırlanan Çiçek" class="w-full h-72 object-cover">
                                    <a href="{{ asset('storage/' . $order->preparation_photo) }}" target="_blank"
                                       class="absolute bottom-3 right-3 bg-slate-900/80 hover:bg-black text-white text-xs font-bold px-3 py-1.5 rounded-xl backdrop-blur-xs flex items-center gap-1.5">
                                        <i class="fas fa-expand"></i> Tam Boyut Gör
                                    </a>
                                </div>

                                <!-- WhatsApp İle Fotoğraflı Takip Linki Gönder -->
                                <a href="{{ $whatsappUrl }}" target="_blank" 
                                   class="w-full py-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-2xl font-bold text-xs flex items-center justify-center gap-2 transition">
                                    <i class="fab fa-whatsapp text-emerald-600"></i> Müşteriye WhatsApp'tan Fotoğraf Onay Linki Gönder
                                </a>
                            @else
                                <div class="w-full h-72 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center p-6 text-center">
                                    <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mb-3">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                    <span class="text-sm font-bold text-slate-700">Henüz Fotoğraf Yüklenmedi</span>
                                    <p class="text-xs text-slate-400 mt-1 max-w-xs">
                                        Çiçek atölyede hazırlandıktan sonra sol taraftaki formu kullanarak fotoğraf yükleyebilirsiniz.
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Alt Butonlar: Geri & Devam Et -->
                        <div class="flex items-center justify-between gap-3 pt-2">
                            <button type="button" @click="step = 2" 
                                    class="py-3.5 px-6 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-bold text-xs flex items-center gap-2 transition cursor-pointer">
                                <i class="fas fa-arrow-left"></i> Geri: Çiçek Hazırlığı
                            </button>

                            <button type="button" @click="step = 4" 
                                    class="py-3.5 px-6 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-xs flex items-center gap-2 shadow-md shadow-blue-600/20 transition cursor-pointer">
                                <span>Devam Et: Kurye &amp; Teslimat Aşamasına Geç</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                </div>

            </div>


            <!-- ============================================================== -->
            <!-- EKRAN 4: KURYE BİLGİLERİ & DAĞITIM / TESLİMAT AŞAMASI -->
            <!-- ============================================================== -->
            <div x-show="step === 4" x-cloak class="space-y-6">
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- Sol Taraf (7 Kolon): Kurye Dağıtım Bilgileri & Navigasyon -->
                    <div class="lg:col-span-7 space-y-4">
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-black">
                                        <i class="fas fa-motorcycle"></i>
                                    </div>
                                    <h3 class="font-black text-sm text-slate-900">Kurye Teslimat &amp; Adres Bilgisi</h3>
                                </div>
                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($order->address . ' ' . ($order->recipient_district ?? '')) }}" 
                                   target="_blank" 
                                   class="text-xs font-bold text-blue-600 hover:underline inline-flex items-center gap-1.5 bg-blue-50 px-3 py-1.5 rounded-xl border border-blue-100">
                                    <i class="fas fa-external-link-alt text-[10px]"></i> Google Haritalar'da Aç
                                </a>
                            </div>

                            <div class="space-y-3 text-xs">
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-500 font-bold">Alıcı Adı:</span>
                                        <span class="font-black text-slate-900 text-sm">{{ $order->recipient_name ?: $order->customer_name }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-500 font-bold">Alıcı Telefonu:</span>
                                        <a href="tel:{{ $order->recipient_phone ?: $order->customer_phone }}" class="font-black text-blue-600 hover:underline font-mono">
                                            {{ $order->recipient_phone ?: $order->customer_phone }} (Hemen Ara)
                                        </a>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-500 font-bold">Teslimat İlçesi:</span>
                                        <span class="font-black text-slate-900">{{ $order->recipient_district ?: ($order->region->name ?? 'İstanbul') }}</span>
                                    </div>
                                    <div class="pt-2 border-t border-slate-200">
                                        <span class="text-slate-500 font-bold block mb-1">Tam Açık Adres:</span>
                                        <div class="bg-white p-3 rounded-xl border border-slate-200 text-slate-800 leading-relaxed font-medium">
                                            {{ $order->address }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Kapıda Tahsilat Uyarısı -->
                                @if($order->payment_method === 'cash' || $order->payment_method === 'cod')
                                    <div class="p-4 rounded-2xl bg-amber-50 border-2 border-amber-300 text-amber-950 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-hand-holding-dollar text-xl text-amber-600"></i>
                                            <div>
                                                <div class="font-black text-xs text-amber-900">KAPIDA TAHSİLAT YAPILACAK!</div>
                                                <div class="text-[11px] text-amber-700">Teslim anında alıcıdan nakit veya POS ile tahsil ediniz.</div>
                                            </div>
                                        </div>
                                        <span class="text-base font-black text-amber-950 font-mono">
                                            ₺{{ number_format($order->total_amount, 2, ',', '.') }}
                                        </span>
                                    </div>
                                @else
                                    <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-950 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-check-circle text-emerald-600"></i>
                                            <span class="font-bold text-xs text-emerald-800">ÖDEME ALINDI (Online POS) — Tahsilat Yapılmayacak</span>
                                        </div>
                                        <span class="font-black text-emerald-900 text-xs">₺{{ number_format($order->total_amount, 2, ',', '.') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sağ Taraf (5 Kolon): Durum Güncelleme & Siparişi Tamamlama -->
                    <div class="lg:col-span-5 space-y-4">
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-4">
                            <h3 class="font-black text-sm text-slate-900 pb-3 border-b border-slate-100">
                                Kurye Aşaması &amp; Siparişi Sonuçlandır
                            </h3>

                            <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="space-y-4">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="step" value="4">

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Durumu Değiştir:</label>
                                    <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-indigo-500">
                                        <option value="Kuryede / Dağıtımda" {{ in_array($order->status, ['Kuryede / Dağıtımda', 'Yolda', 'on_delivery', 'Kuryede']) ? 'selected' : '' }}>
                                            Kuryede / Dağıtımda (Yola Çıktı)
                                        </option>
                                        <option value="Teslim Edildi" {{ in_array($order->status, ['Teslim Edildi', 'Tamamlandı']) ? 'selected' : '' }}>
                                            Teslim Edildi (Tamamlandı)
                                        </option>
                                        <option value="Atölyede Hazırlanıyor" {{ in_array($order->status, ['Atölyede Hazırlanıyor', 'Hazırlanıyor', 'processing']) ? 'selected' : '' }}>
                                            Atölyede Hazırlanıyor
                                        </option>
                                        <option value="İptal Edildi" {{ in_array($order->status, ['İptal Edildi', 'İptal edildi', 'cancelled']) ? 'selected' : '' }}>
                                            İptal Edildi
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Kurye / Operasyon Notu:</label>
                                    <textarea name="notes" rows="2" placeholder="Örn: Güvenliğe teslim edildi, alıcı bizzat aldı..."
                                              class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:border-indigo-500">{{ $order->notes }}</textarea>
                                </div>

                                <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black text-xs shadow-md shadow-indigo-600/20 transition flex items-center justify-center gap-2 cursor-pointer">
                                    <i class="fas fa-check"></i> Durumu ve Kurye Notunu Kaydet
                                </button>
                            </form>
                        </div>

                        <!-- Alt Butonlar -->
                        <div class="flex items-center justify-between gap-3 pt-2">
                            <button type="button" @click="step = 3" 
                                    class="py-3.5 px-6 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-bold text-xs flex items-center gap-2 transition cursor-pointer">
                                <i class="fas fa-arrow-left"></i> Geri: Fotoğraf
                            </button>

                            <a href="{{ route('admin.orders.index') }}" 
                               class="py-3.5 px-6 bg-slate-900 hover:bg-black text-white rounded-2xl font-black text-xs flex items-center gap-2 shadow-md transition">
                                <i class="fas fa-list"></i> Siparişler Listesine Dön
                            </a>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
