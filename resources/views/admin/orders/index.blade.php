<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-900 leading-tight flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-lg">
                        <i class="fas fa-shopping-bag"></i>
                    </span>
                    <span>{{ __('Sipariş & Atölye Yönetimi') }}</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">Canlı çiçek siparişleri, atölye hazırlık fotoğrafları ve ilçe kurye dağıtımı</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.workshop.index') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-black text-xs px-4 py-2.5 rounded-xl shadow-xs transition-all flex items-center gap-2 active:scale-95">
                    <i class="fas fa-camera"></i> <span>Mobil Atölye &amp; Kamera</span>
                </a>
                <a href="{{ route('admin.orders.export') }}" 
                   class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs transition-all flex items-center gap-2">
                    <i class="fas fa-file-excel"></i> Excel'e Aktar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="{
        selectedOrders: [],
        allOrderIds: {{ json_encode($orders->pluck('id')->values()->all()) }},
        toggleAll(checked) {
            this.selectedOrders = checked ? [...this.allOrderIds] : [];
        },
        isSelected(id) {
            return this.selectedOrders.includes(id);
        }
    }">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl text-xs font-bold flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl text-xs font-bold flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- TOPLU İŞLEM & 4'LÜ PERFORAJLI FİŞ YAZDIRMA BARI -->
            <div x-show="selectedOrders.length > 0" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="sticky top-4 z-30 bg-slate-900 text-white rounded-3xl p-4 sm:p-5 shadow-2xl border border-blue-500/40 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-black text-sm shadow-md">
                        <span x-text="selectedOrders.length"></span>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-white flex items-center gap-2">
                            <span>Sipariş Seçildi</span>
                            <span class="text-[10px] bg-blue-500/30 text-blue-300 px-2 py-0.5 rounded-full font-bold">4'lü A4 Perforajlı Fiş Modu</span>
                        </h4>
                        <p class="text-xs text-slate-300 mt-0.5">Seçtiğiniz siparişler 4'lü delikli/perforajlı A4 kurye ve kart notu sayfasına basılır.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <form action="{{ route('admin.orders.bulkPrint') }}" method="POST" target="_blank" class="inline">
                        @csrf
                        <template x-for="id in selectedOrders" :key="id">
                            <input type="hidden" name="order_ids[]" :value="id">
                        </template>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-black text-xs px-5 py-3 rounded-2xl shadow-lg transition flex items-center gap-2 active:scale-95 cursor-pointer">
                            <i class="fas fa-print text-sm"></i>
                            <span>Seçilenleri 4'lü Fişe Yazdır (<span x-text="selectedOrders.length"></span>)</span>
                        </button>
                    </form>
                    <button type="button" @click="selectedOrders = []" 
                            class="bg-white/10 hover:bg-white/20 text-white text-xs font-bold px-4 py-3 rounded-2xl transition cursor-pointer">
                        Seçimi Kaldır
                    </button>
                </div>
            </div>

            <!-- ARAMA & FİLTRELEME ÇUBUĞU -->
            <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                <form action="{{ route('admin.orders.index') }}" method="GET" class="flex-1 flex gap-2">
                    @if(request('tab'))
                        <input type="hidden" name="tab" value="{{ request('tab') }}">
                    @endif
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ $search ?? '' }}" 
                               placeholder="Sipariş no, alıcı adı, telefon veya ilçe ara..." 
                               class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                        Ara
                    </button>
                    @if(!empty($search))
                        <a href="{{ route('admin.orders.index', ['tab' => $tab]) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold px-3 py-2 rounded-xl transition flex items-center">
                            Temizle
                        </a>
                    @endif
                </form>

                <div class="text-xs text-slate-400 font-bold whitespace-nowrap">
                    Toplam <span class="text-slate-800 font-black">{{ $orders->total() }}</span> sipariş
                </div>
            </div>

            <!-- ÇİÇEKÇİLİK OPERASYON SEKMELERİ (TABS) -->
            <div class="bg-white rounded-3xl p-2 border border-slate-200 shadow-xs overflow-x-auto no-scrollbar">
                <nav class="flex items-center gap-1.5 text-xs font-bold whitespace-nowrap">
                    <a href="{{ route('admin.orders.index', ['tab' => 'all', 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-xl transition flex items-center gap-2 {{ ($tab === 'all' || !$tab) ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                        <span>Tümü</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ ($tab === 'all' || !$tab) ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $counts['all'] }}</span>
                    </a>

                    <a href="{{ route('admin.orders.index', ['tab' => 'today', 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-xl transition flex items-center gap-2 {{ $tab === 'today' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                        <i class="fas fa-calendar-day"></i>
                        <span>Bugünkü Teslimatlar</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'today' ? 'bg-white/25 text-white' : 'bg-amber-100 text-amber-800 font-black' }}">{{ $counts['today'] }}</span>
                    </a>

                    <a href="{{ route('admin.orders.index', ['tab' => 'preparing', 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-xl transition flex items-center gap-2 {{ $tab === 'preparing' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                        <i class="fas fa-paint-brush"></i>
                        <span>Atölyede Hazırlananlar</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'preparing' ? 'bg-white/25 text-white' : 'bg-blue-100 text-blue-800 font-black' }}">{{ $counts['preparing'] }}</span>
                    </a>

                    <a href="{{ route('admin.orders.index', ['tab' => 'photo_review', 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-xl transition flex items-center gap-2 {{ $tab === 'photo_review' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                        <i class="fas fa-camera"></i>
                        <span>Fotoğraf Onayında</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'photo_review' ? 'bg-white/25 text-white' : 'bg-purple-100 text-purple-800 font-black' }}">{{ $counts['photo_review'] }}</span>
                    </a>

                    <a href="{{ route('admin.orders.index', ['tab' => 'on_delivery', 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-xl transition flex items-center gap-2 {{ $tab === 'on_delivery' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                        <i class="fas fa-motorcycle"></i>
                        <span>Kuryede / Dağıtımda</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'on_delivery' ? 'bg-white/25 text-white' : 'bg-indigo-100 text-indigo-800 font-black' }}">{{ $counts['on_delivery'] }}</span>
                    </a>

                    <a href="{{ route('admin.orders.index', ['tab' => 'completed', 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-xl transition flex items-center gap-2 {{ $tab === 'completed' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                        <i class="fas fa-check-circle"></i>
                        <span>Teslim Edildi</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'completed' ? 'bg-white/25 text-white' : 'bg-emerald-100 text-emerald-800 font-black' }}">{{ $counts['completed'] }}</span>
                    </a>

                    <a href="{{ route('admin.orders.index', ['tab' => 'cancelled', 'search' => $search]) }}" 
                       class="px-4 py-2 rounded-xl transition flex items-center gap-2 {{ $tab === 'cancelled' ? 'bg-red-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                        <span>İptal</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'cancelled' ? 'bg-white/25 text-white' : 'bg-red-100 text-red-800' }}">{{ $counts['cancelled'] }}</span>
                    </a>
                </nav>
            </div>

            <!-- SİPARİŞ LİSTESİ TABLOSU -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left">
                        <thead class="bg-slate-50/80 text-[11px] font-black text-slate-500 uppercase tracking-wider">
                            <tr>
                                <th class="w-10 px-4 py-4 text-center">
                                    <input type="checkbox" 
                                           @change="toggleAll($event.target.checked)" 
                                           :checked="selectedOrders.length > 0 && selectedOrders.length === allOrderIds.length"
                                           class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300 cursor-pointer">
                                </th>
                                <th class="px-6 py-4">Sipariş No</th>
                                <th class="px-6 py-4">Alıcı &amp; İlçe</th>
                                <th class="px-6 py-4">Teslimat Zamanı</th>
                                <th class="px-6 py-4">Çiçek Tasarımı</th>
                                <th class="px-6 py-4">Fotoğraf</th>
                                <th class="px-6 py-4">Kart Notu</th>
                                <th class="px-6 py-4">Tutar &amp; Ödeme</th>
                                <th class="px-6 py-4">Durum</th>
                                <th class="px-6 py-4 text-right">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse($orders as $order)
                                <tr class="transition-colors" :class="isSelected({{ $order->id }}) ? 'bg-blue-50/70' : 'hover:bg-blue-50/30'">
                                    <!-- Seçim Kutusu -->
                                    <td class="w-10 px-4 py-4 text-center">
                                        <input type="checkbox" 
                                               :value="{{ $order->id }}" 
                                               x-model="selectedOrders"
                                               class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300 cursor-pointer">
                                    </td>

                                    <!-- Sipariş No & Tarih -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="font-mono font-black text-blue-600 hover:underline">
                                            #{{ $order->order_number }}
                                        </a>
                                        <span class="block text-[10px] text-slate-400 mt-0.5">
                                            {{ $order->created_at->format('d.m.Y H:i') }}
                                        </span>
                                    </td>

                                    <!-- Alıcı & İlçe -->
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900">
                                            {{ $order->recipient_name ?: $order->customer_name }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                            <i class="fas fa-map-marker-alt text-blue-500 text-[10px]"></i>
                                            <span>{{ $order->recipient_district ?: ($order->region->name ?? 'İstanbul') }}</span>
                                        </div>
                                        @if($order->is_anonymous)
                                            <span class="inline-block mt-1 text-[9px] bg-purple-50 text-purple-700 font-bold px-1.5 py-0.5 rounded">
                                                <i class="fas fa-user-secret mr-1"></i> Gizli Gönderici
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Teslimat Zamanı -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $isToday = $order->delivery_date && \Carbon\Carbon::parse($order->delivery_date)->isToday();
                                        @endphp
                                        <div class="font-extrabold {{ $isToday ? 'text-blue-600' : 'text-slate-800' }}">
                                            @if($isToday)
                                                <span class="inline-block w-2 h-2 rounded-full bg-blue-500 mr-1 animate-pulse"></span>
                                                Bugün
                                            @elseif($order->delivery_date)
                                                {{ \Carbon\Carbon::parse($order->delivery_date)->format('d.m.Y') }}
                                            @else
                                                Belirtilmedi
                                            @endif
                                        </div>
                                        <span class="block text-[10px] text-slate-400 mt-0.5">
                                            {{ $order->delivery_time_slot ?: 'Standart' }}
                                        </span>
                                        @if($order->delivery_fee > 0)
                                            <span class="inline-flex items-center gap-1 mt-1 text-[9px] font-black bg-blue-600 text-white px-2 py-0.5 rounded-full shadow-xs">
                                                <i class="fas fa-bolt text-[8px]"></i> VIP Hızlı Kurye (+₺{{ number_format($order->delivery_fee, 0, ',', '.') }})
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Çiçek & Kalemler -->
                                    <td class="px-6 py-4">
                                        @if($order->items->first())
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-lg bg-blue-50 overflow-hidden flex-shrink-0 border border-blue-100">
                                                    @if($order->items->first()->product && $order->items->first()->product->image_url)
                                                        <img src="{{ $order->items->first()->product->image_url }}" alt="" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-blue-400 text-xs">
                                                            <i class="fas fa-spa"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="max-w-[160px] truncate font-medium text-slate-800" title="{{ $order->items->first()->product->name ?? 'Çiçek' }}">
                                                    {{ $order->items->first()->product->name ?? 'Çiçek' }}
                                                    @if($order->items->count() > 1)
                                                        <span class="text-[10px] text-slate-400 block">+{{ $order->items->count() - 1 }} ek ürün</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic">Ürün yok</span>
                                        @endif
                                    </td>

                                    <!-- Fotoğraf Durumu -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($order->preparation_photo)
                                            <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black bg-purple-100 text-purple-800 hover:bg-purple-200 transition">
                                                <i class="fas fa-check-circle"></i>
                                                <span>Fotoğraf Var</span>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 hover:bg-amber-100 transition border border-amber-200">
                                                <i class="fas fa-camera text-[9px]"></i>
                                                <span>Yükle</span>
                                            </a>
                                        @endif
                                    </td>

                                    <!-- Kart Notu -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($order->card_note)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md" title="{{ $order->card_note }}">
                                                <i class="fas fa-envelope text-[10px]"></i> Not Var
                                            </span>
                                        @else
                                            <span class="text-slate-300 text-[11px]">-</span>
                                        @endif
                                    </td>

                                    <!-- Tutar & Ödeme -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-black text-slate-900">
                                            ₺{{ number_format($order->total_amount, 2, ',', '.') }}
                                        </div>
                                        @if($order->payment_status === 'paid')
                                            <span class="text-[10px] text-emerald-600 font-bold flex items-center gap-0.5 mt-0.5">
                                                <i class="fas fa-check text-[8px]"></i> Ödendi
                                            </span>
                                        @else
                                            <span class="text-[10px] text-amber-600 font-bold flex items-center gap-0.5 mt-0.5">
                                                <i class="fas fa-clock text-[8px]"></i> Bekliyor
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Aşama Rozeti -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusStyle = match($order->status) {
                                                'Yeni Sipariş', 'pending' => 'bg-amber-100 text-amber-800',
                                                'Atölyede Hazırlanıyor', 'Hazırlanıyor', 'processing' => 'bg-blue-100 text-blue-800',
                                                'Fotoğraf Onayında' => 'bg-purple-100 text-purple-800',
                                                'Kuryede / Dağıtımda', 'Yolda', 'on_delivery', 'Kuryede' => 'bg-indigo-100 text-indigo-800',
                                                'Teslim Edildi', 'Tamamlandı' => 'bg-emerald-100 text-emerald-800',
                                                'İptal Edildi', 'İptal edildi', 'cancelled' => 'bg-red-100 text-red-800',
                                                default => 'bg-slate-100 text-slate-800'
                                            };
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black {{ $statusStyle }}">
                                            {{ $order->status }}
                                        </span>
                                    </td>

                                    <!-- İşlemler -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.orders.show', $order) }}" 
                                               class="bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1"
                                               title="Detay & Fotoğraf">
                                                <i class="fas fa-eye text-[10px]"></i>
                                                <span>Detay</span>
                                            </a>
                                            <a href="{{ route('admin.orders.print', $order) }}" target="_blank"
                                               class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-2 py-1.5 rounded-lg text-xs transition"
                                               title="Yazdır">
                                                <i class="fas fa-print text-[10px]"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-12 text-center text-slate-400">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-300 flex items-center justify-center text-xl mx-auto mb-2">
                                            <i class="fas fa-inbox"></i>
                                        </div>
                                        <p class="font-bold text-slate-600">Bu sekmede gösterilecek sipariş bulunamadı.</p>
                                        <p class="text-xs text-slate-400 mt-1">Farklı bir sekmeyi seçebilir veya yeni sipariş oluşturabilirsiniz.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Sayfalama -->
                @if($orders->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
