<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Çiçekçi 4'lü A4 Sipariş Fişi &amp; Kartı</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            body { 
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .no-print { display: none !important; }
            .a4-sheet { 
                box-shadow: none !important; 
                margin: 0 !important;
                padding: 0 !important;
                width: 210mm !important;
                height: 297mm !important;
                max-height: 297mm !important;
                page-break-after: always !important;
                page-break-inside: avoid !important;
                overflow: hidden !important;
            }
            @page {
                size: A4 portrait;
                margin: 0;
            }
        }

        /* Standart A4 & 4'lü Perforaj Ölçüleri */
        .a4-sheet {
            width: 210mm;
            height: 297mm;
            max-height: 297mm;
            overflow: hidden;
            box-sizing: border-box;
            background: #ffffff;
            margin: 0 auto;
            position: relative;
        }

        .slip-slot {
            height: 74.25mm;
            max-height: 74.25mm;
            box-sizing: border-box;
            overflow: hidden;
            display: flex;
        }

        .slip-left {
            width: 85mm;
            max-width: 85mm;
            height: 100%;
            box-sizing: border-box;
            overflow: hidden;
        }

        .slip-right {
            width: 125mm;
            max-width: 125mm;
            height: 100%;
            box-sizing: border-box;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-slate-100 p-2 sm:p-6 text-slate-800 antialiased font-sans"
      x-data="{
          slotChoice: '1',
          showGuides: true,
          isBulk: {{ $isBulk ? 'true' : 'false' }}
      }">

    @php
        $siteTitle = $settings['site_title'] ?? config('app.name', 'Yeniçiçek');
        $sitePhone = $settings['contact_phone'] ?? '0850 307 45 45';
        $siteUrl = request()->getHost();
    @endphp

    <!-- ============================================================== -->
    <!-- ÜST KONTROL ÇUBUĞU (YALNIZCA EKRANDA GÖRÜNÜR, BASKIDA GİZLENİR) -->
    <!-- ============================================================== -->
    <div class="max-w-[210mm] mx-auto mb-4 no-print bg-white p-4 rounded-3xl shadow-sm border border-slate-200 space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                @if(!$isBulk && $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3.5 py-2 rounded-xl transition flex items-center gap-1.5">
                        <i class="fas fa-arrow-left"></i> Siparişe Dön
                    </a>
                    <span class="text-xs font-black text-blue-600 bg-blue-50 px-2.5 py-1 rounded-xl border border-blue-200 font-mono">
                        #{{ $order->order_number }}
                    </span>
                @else
                    <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3.5 py-2 rounded-xl transition flex items-center gap-1.5">
                        <i class="fas fa-arrow-left"></i> Sipariş Listesine Dön
                    </a>
                    <span class="text-xs font-black text-purple-600 bg-purple-50 px-2.5 py-1 rounded-xl border border-purple-200">
                        {{ $orders->count() }} Sipariş Toplu Yazdırılıyor
                    </span>
                @endif
            </div>

            <!-- Yazdır Butonu -->
            <div class="flex items-center gap-2">
                <button type="button" @click="showGuides = !showGuides"
                        class="text-xs font-bold px-3 py-2 rounded-xl border transition flex items-center gap-1.5 cursor-pointer"
                        :class="showGuides ? 'bg-slate-100 text-slate-700 border-slate-300' : 'bg-white text-slate-400 border-slate-200'">
                    <i class="fas" :class="showGuides ? 'fa-border-all text-slate-600' : 'fa-square'"></i>
                    <span x-text="showGuides ? 'Kesim Çizgileri Açık' : 'Kesim Çizgileri Gizli (Delikli Kağıt)'"></span>
                </button>

                <button type="button" onclick="window.print()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-black px-6 py-2.5 rounded-2xl text-xs flex items-center gap-2 shadow-md shadow-blue-600/20 transition cursor-pointer">
                    <i class="fas fa-print"></i>
                    <span>Yazdır (A4 Çıktı Al)</span>
                </button>
            </div>
        </div>

        @if(!$isBulk)
            <!-- TEK SİPARİŞ İÇİN: 4'LÜ KAĞITTA GÖZ / SLOT KONUMU SEÇİCİSİ -->
            <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2">
                    <span class="font-black text-slate-700">Kağıtta Hangi Göze Basılsın?</span>
                    <span class="text-[11px] text-slate-400">(Önceden yırtılmış kağıtlarınızı israf etmeden kullanabilirsiniz)</span>
                </div>

                <div class="flex flex-wrap items-center gap-1.5 font-bold">
                    <button type="button" @click="slotChoice = '1'" 
                            class="px-3 py-1.5 rounded-xl border transition text-xs"
                            :class="slotChoice === '1' ? 'bg-blue-600 text-white border-blue-600 font-black shadow-xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'">
                        1. Göz (Üst)
                    </button>
                    <button type="button" @click="slotChoice = '2'" 
                            class="px-3 py-1.5 rounded-xl border transition text-xs"
                            :class="slotChoice === '2' ? 'bg-blue-600 text-white border-blue-600 font-black shadow-xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'">
                        2. Göz
                    </button>
                    <button type="button" @click="slotChoice = '3'" 
                            class="px-3 py-1.5 rounded-xl border transition text-xs"
                            :class="slotChoice === '3' ? 'bg-blue-600 text-white border-blue-600 font-black shadow-xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'">
                        3. Göz
                    </button>
                    <button type="button" @click="slotChoice = '4'" 
                            class="px-3 py-1.5 rounded-xl border transition text-xs"
                            :class="slotChoice === '4' ? 'bg-blue-600 text-white border-blue-600 font-black shadow-xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'">
                        4. Göz (Alt)
                    </button>
                    <button type="button" @click="slotChoice = 'all'" 
                            class="px-3 py-1.5 rounded-xl border transition text-xs"
                            :class="slotChoice === 'all' ? 'bg-slate-900 text-white border-slate-900 font-black shadow-xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'">
                        4 Gözü Doldur (4 Kopya)
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- ============================================================== -->
    <!-- A4 YAZDIRMA SAYFALARI (HER SAYFADA 4 SIRA / 4 BÖLME YER ALIR)   -->
    <!-- ============================================================== -->
    @php
        $pages = $isBulk ? $orders->chunk(4) : collect([collect([$order])]);
    @endphp

    @foreach($pages as $pageIndex => $pageOrders)
        <div class="a4-sheet shadow-lg rounded-2xl mb-8 border border-slate-300"
             :class="showGuides ? 'divide-y divide-dashed divide-slate-300' : ''">

            @for($slotIndex = 0; $slotIndex < 4; $slotIndex++)
                @php
                    $currentOrder = null;
                    if ($isBulk) {
                        $currentOrder = $pageOrders->values()->get($slotIndex);
                    } else {
                        // Tekli sipariş modunda slot seçimine göre yerleştir
                        $currentOrder = $order;
                    }

                    if ($currentOrder) {
                        $firstItem = $currentOrder->items->first();
                        $productImg = $firstItem && $firstItem->product ? $firstItem->product->image_url : asset('images/products/6.png');
                        $deliveryDateStr = $currentOrder->delivery_date ? \Carbon\Carbon::parse($currentOrder->delivery_date)->translatedFormat('d M') : 'Bugün';
                        $trackingUrl = route('order.track', ['order_number' => $currentOrder->order_number, 'phone' => $currentOrder->customer_phone]);
                        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=' . urlencode($trackingUrl);
                        $customItem = $currentOrder->items->first(fn($it) => ($it->item_type === 'custom_bouquet' || !empty($it->options['custom_bouquet'])));
                    }
                @endphp

                <!-- SIRA / GÖZ: 74.25mm YÜKSEKLİK -->
                <div class="slip-slot"
                     @if(!$isBulk)
                         x-show="slotChoice === 'all' || slotChoice === '{{ $slotIndex + 1 }}'"
                     @endif
                     :class="showGuides ? 'border-b border-dashed border-slate-300' : ''">

                    @if($currentOrder)
                        <!-- ====================================================== -->
                        <!-- SOL BÖLME: MÜŞTERİ KART NOTU (85mm x 74.25mm)          -->
                        <!-- ====================================================== -->
                        <div class="slip-left relative flex flex-col justify-between p-3.5"
                             :class="showGuides ? 'border-r border-dashed border-slate-300' : ''">
                            
                            <!-- Kart Üst Başlık & Logo -->
                            <div class="flex items-center justify-between pb-1 border-b border-blue-100">
                                <div class="flex items-center gap-1.5">
                                    <i class="fas fa-spa text-blue-600 text-xs"></i>
                                    <span class="font-black text-[11px] uppercase tracking-tight text-slate-900 font-sans">
                                        {{ $siteTitle }}
                                    </span>
                                </div>
                                <span class="text-[8px] font-mono text-slate-400">#{{ $currentOrder->order_number }}</span>
                            </div>

                            <!-- Müşteri Kart Notu (Zarf İçine Konulacak Yazı) -->
                            <div class="my-auto py-1 px-1">
                                @if($currentOrder->card_note)
                                    <p class="font-serif italic text-slate-800 text-[10.5px] leading-snug line-clamp-3">
                                        “{{ $currentOrder->card_note }}”
                                    </p>
                                @else
                                    <p class="font-serif italic text-slate-400 text-[9px] text-center">
                                        (Müşteri kart notu eklemedi)
                                    </p>
                                @endif
                            </div>

                            <!-- İmza / Gönderici & Zarf İbaresi -->
                            <div class="pt-1 border-t border-blue-100 flex items-end justify-between text-[8.5px]">
                                <span class="text-slate-400 font-sans uppercase tracking-wider text-[7.5px]">Zarf İçi Kart</span>
                                <span class="font-serif font-black text-blue-700 text-[9.5px]">
                                    {{ $currentOrder->is_anonymous ? 'Gizli Bir Hayran' : ($currentOrder->sender_name ?: $currentOrder->customer_name) }}
                                </span>
                            </div>

                            <!-- Alt Çiçek Yaprağı Pastel Filigranı (Fotoğraftaki gibi) -->
                            <div class="absolute bottom-0 left-0 right-0 h-1.5 opacity-30 pointer-events-none bg-gradient-to-r from-blue-300 via-sky-200 to-indigo-200"></div>
                        </div>

                        <!-- ====================================================== -->
                        <!-- SAĞ BÖLME: KURYE & ATÖLYE FİŞİ (125mm x 74.25mm)       -->
                        <!-- ====================================================== -->
                        <div class="slip-right relative flex flex-col justify-between p-3 bg-white">
                            
                            <!-- Üst Satır: Sipariş No, Tarih/Saat, VIP Etiketi, Thumbnail ve QR -->
                            <div class="flex items-start justify-between gap-1.5 pb-1 border-b border-slate-100">
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-mono font-black text-[11px] text-slate-900">#{{ $currentOrder->order_number }}</span>
                                        @if($currentOrder->delivery_fee > 0)
                                            <span class="bg-blue-600 text-white text-[7.5px] font-black px-1.5 py-0.2 rounded font-sans uppercase">
                                                VIP HIZLI
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[9px] font-black text-slate-700 mt-0.5 flex items-center gap-1">
                                        <i class="fas fa-clock text-blue-600 text-[8px]"></i>
                                        <span>{{ $deliveryDateStr }}</span>
                                        <span class="text-slate-300">|</span>
                                        <span>{{ $currentOrder->delivery_time_slot ?: 'Standart Aralık' }}</span>
                                    </div>
                                </div>

                                <!-- Sağ Köşe: Küçük Görsel ve QR Kod -->
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <div class="w-9 h-9 rounded-lg border border-slate-200 overflow-hidden bg-slate-50 shrink-0">
                                        <img src="{{ $productImg }}" alt="Çiçek" class="w-full h-full object-cover">
                                    </div>
                                    <img src="{{ $qrCodeUrl }}" alt="QR" class="w-9 h-9 border border-slate-200 rounded-lg p-0.5 bg-white shrink-0">
                                </div>
                            </div>

                            <!-- Orta Satır: Alıcı Bilgileri & Açık Adres -->
                            <div class="py-1 space-y-0.5 text-[9px] leading-tight">
                                <div class="flex items-center justify-between">
                                    <div class="font-black text-slate-900 text-[10px] truncate">
                                        {{ $currentOrder->recipient_name ?: $currentOrder->customer_name }}
                                        <span class="font-mono font-bold text-slate-500 ml-1 text-[9px]">
                                            {{ $currentOrder->recipient_phone ?: $currentOrder->customer_phone }}
                                        </span>
                                    </div>
                                    <span class="font-black text-[8.5px] text-blue-700 bg-blue-50 px-1.5 py-0.2 rounded border border-blue-200 shrink-0">
                                        {{ $currentOrder->recipient_district ?: ($currentOrder->region->name ?? 'İstanbul') }}
                                    </span>
                                </div>
                                <div class="text-slate-600 truncate font-medium text-[8.5px]">
                                    <strong class="text-slate-800">Adres:</strong> {{ \Illuminate\Support\Str::limit($currentOrder->address, 65) }}
                                </div>
                            </div>

                            <!-- Alt Satır: Kalemler / Özel Buket Reçetesi & Tahsilat -->
                            <div class="pt-1 border-t border-slate-100 flex items-center justify-between text-[8px]">
                                <!-- Kalemler Özeti -->
                                <div class="text-slate-600 truncate max-w-[65%] font-medium">
                                    @if($customItem && !empty($customItem->options['summary']))
                                        <span class="text-blue-700 font-bold truncate flex items-center gap-1"><i class="fas fa-spa text-[9px]"></i><span>{{ \Illuminate\Support\Str::limit($customItem->options['summary'], 42) }}</span></span>
                                    @else
                                        <span class="truncate">• {{ \Illuminate\Support\Str::limit($firstItem->product->name ?? 'Çiçek Tasarımı', 32) }}</span>
                                        @if($currentOrder->items->count() > 1)
                                            <span class="text-blue-600 font-black">(+{{ $currentOrder->items->count() - 1 }} hediye)</span>
                                        @endif
                                    @endif
                                </div>

                                <!-- Tahsilat Durumu -->
                                <div>
                                    @if($currentOrder->payment_method === 'cash' || $currentOrder->payment_method === 'cod')
                                        <span class="font-black text-amber-900 bg-amber-100 px-1.5 py-0.5 rounded border border-amber-300">
                                            KAPIDA: ₺{{ number_format($currentOrder->total_amount, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="font-black text-emerald-800 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                            ✓ ÖDENDİ (POS)
                                        </span>
                                    @endif
                                </div>
                            </div>

                        </div>
                    @else
                        <!-- BOŞ / YIRTILMIŞ GÖZ (KAĞIT İSRAF ETMEZ) -->
                        <div class="w-full h-full flex items-center justify-center text-slate-300 text-[10px] font-mono italic">
                            (Boş Slot / Yazdırılmadı)
                        </div>
                    @endif

                </div>

                @if(!$isBulk)
                    <!-- Eğer kullanıcı tek bir göz seçtiyse diğer gözleri boş bırak -->
                    <div class="slip-slot"
                         x-show="slotChoice !== 'all' && slotChoice !== '{{ $slotIndex + 1 }}'"
                         :class="showGuides ? 'border-b border-dashed border-slate-200' : ''">
                        <div class="w-full h-full flex items-center justify-center text-slate-200 text-[10px] font-mono select-none">
                            <span class="no-print">[{{ $slotIndex + 1 }}. Göz Boş Bırakıldı]</span>
                        </div>
                    </div>
                @endif

            @endfor

        </div>
    @endforeach

</body>
</html>
