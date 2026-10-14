<x-frontend-layout title="Canlı Çiçek Sipariş & Kurye Takibi — Tazecicekler.com">
    <div class="bg-slate-50 py-12 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-xl mx-auto mb-8">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-100 text-rose-700 text-xs font-black uppercase tracking-wider mb-2">
                    <i class="fas fa-truck-fast"></i> Canlı Kurye &amp; Teslimat Takibi
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Siparişiniz Nerede?</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Sipariş numaranızı veya telefon numaranızı girerek çiçeğinizin anlık durumunu öğrenin.</p>
            </div>

            <!-- Sorgulama Formu -->
            <div class="bg-white shadow-sm rounded-3xl border border-rose-100 p-6 mb-8">
                <form action="{{ route('order.track') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                    <div class="sm:col-span-5">
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1">Sipariş Numarası</label>
                        <input type="text" name="order_number" value="{{ request('order_number') }}"
                               placeholder="ORD-..."
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500 uppercase">
                    </div>
                    <div class="sm:col-span-5">
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1">VEYA Telefon Numarası</label>
                        <input type="tel" name="phone" value="{{ request('phone') }}"
                               placeholder="05XX XXX XX XX"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500">
                    </div>
                    <div class="sm:col-span-2 sm:pt-5">
                        <button type="submit"
                                class="w-full py-2.5 sm:py-3 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center justify-center gap-1.5">
                            <i class="fas fa-search"></i>
                            <span>Sorgula</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Hata Mesajı -->
            @if($error)
                <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl mb-8 text-center text-xs font-bold shadow-xs">
                    <i class="fas fa-exclamation-triangle text-rose-600 mr-1.5"></i>
                    {{ $error }}
                </div>
            @endif

            <!-- Sipariş Sonucu -->
            @if($order)
                <div class="bg-white shadow-xl rounded-3xl overflow-hidden border border-rose-100 space-y-6 p-6 sm:p-8">
                    
                    <!-- Başlık & Temel Bilgiler -->
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 pb-6 border-b border-slate-100">
                        <div>
                            <span class="text-[10px] font-bold text-rose-500 uppercase tracking-widest block">Sipariş Bilgisi</span>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5">{{ $order->order_number }}</h2>
                            <p class="text-xs text-slate-400 mt-1">Sipariş Tarihi: {{ $order->created_at->locale('tr')->isoFormat('D MMMM YYYY, HH:mm') }}</p>
                        </div>
                        <div class="sm:text-right">
                            <span class="text-[10px] text-slate-400 block font-bold uppercase">Mevcut Durum:</span>
                            <span class="inline-block px-3.5 py-1.5 rounded-full text-xs font-black bg-rose-100 text-rose-800 mt-1">
                                {{ $order->status }}
                            </span>
                        </div>
                    </div>

                    <!-- 5 ADIMLI CANLI SÜREÇ ÇUBUĞU -->
                    @php
                        $stepNumber = match($order->status) {
                            'Sipariş alındı' => 1,
                            'Hazırlanıyor' => 2,
                            'Onaylandı' => 2,
                            'Fotoğraf Onaylandı' => 3,
                            'Yolda' => 4,
                            'Dağıtımda' => 4,
                            'Tamamlandı' => 5,
                            'Teslim Edildi' => 5,
                            default => 1,
                        };
                    @endphp

                    <div class="py-4">
                        <div class="grid grid-cols-5 gap-2 text-center">
                            <!-- 1. Alındı -->
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-sm font-black transition {{ $stepNumber >= 1 ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30' : 'bg-slate-100 text-slate-400' }}">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <span class="text-[10px] font-extrabold text-slate-800 mt-2 block">1. Alındı</span>
                            </div>

                            <!-- 2. Hazırlanıyor -->
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-sm font-black transition {{ $stepNumber >= 2 ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30' : 'bg-slate-100 text-slate-400' }}">
                                    <i class="fas fa-scissors"></i>
                                </div>
                                <span class="text-[10px] font-extrabold text-slate-800 mt-2 block">2. Atölyede</span>
                            </div>

                            <!-- 3. Fotoğraf Onayı -->
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-sm font-black transition {{ $stepNumber >= 3 ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30' : 'bg-slate-100 text-slate-400' }}">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <span class="text-[10px] font-extrabold text-slate-800 mt-2 block">3. Foto Onay</span>
                            </div>

                            <!-- 4. Kurye Yolda -->
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-sm font-black transition {{ $stepNumber >= 4 ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30' : 'bg-slate-100 text-slate-400' }}">
                                    <i class="fas fa-motorcycle"></i>
                                </div>
                                <span class="text-[10px] font-extrabold text-slate-800 mt-2 block">4. Kuryede</span>
                            </div>

                            <!-- 5. Teslim Edildi -->
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-sm font-black transition {{ $stepNumber >= 5 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'bg-slate-100 text-slate-400' }}">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span class="text-[10px] font-extrabold text-slate-800 mt-2 block">5. Teslim</span>
                            </div>
                        </div>
                    </div>

                    <!-- ATÖLYE FOTOĞRAFI (VARSA MÜŞTERİYE GÖSTERİLİR) -->
                    @if($order->preparation_photo)
                        <div class="bg-rose-50/70 rounded-3xl p-5 border border-rose-200">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-7 h-7 rounded-xl bg-rose-600 text-white flex items-center justify-center text-xs">
                                    <i class="fas fa-camera"></i>
                                </span>
                                <h3 class="text-xs sm:text-sm font-black text-rose-950">Atölyede Hazırlanan Çiçeğinizin Fotoğrafı</h3>
                            </div>
                            <div class="max-w-sm mx-auto rounded-2xl overflow-hidden shadow-lg border border-rose-200">
                                <img src="{{ asset('storage/' . $order->preparation_photo) }}" alt="Hazırlanan Çiçek" class="w-full h-auto object-cover">
                            </div>
                        </div>
                    @endif

                    <!-- Teslimat & Alıcı Detayları -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs pt-4 border-t border-slate-100">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="text-[10px] font-extrabold uppercase text-slate-400 block">Alıcı ve Teslimat Bilgisi</span>
                            <div><strong>Alıcı:</strong> {{ $order->recipient_name ?: $order->customer_name }}</div>
                            <div><strong>Bölge:</strong> {{ $order->recipient_district ?? ($order->region->name ?? 'İstanbul') }}</div>
                            <div><strong>Planlanan Tarih:</strong> {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->locale('tr')->isoFormat('D MMMM YYYY') : 'Aynı Gün' }}</div>
                            <div><strong>Saat Aralığı:</strong> {{ $order->delivery_time_slot ?? '12:00 - 15:00' }}</div>
                            @if($order->delivery_fee > 0)
                                <div class="inline-flex items-center gap-1 text-[10px] font-black bg-rose-600 text-white px-2 py-0.5 rounded-full mt-1">
                                    <i class="fas fa-bolt text-[8px]"></i> VIP Hızlı Kurye Öncelikli Teslimat
                                </div>
                            @endif
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="text-[10px] font-extrabold uppercase text-slate-400 block">Sipariş &amp; Kart Detayı</span>
                            <div><strong>Toplam Tutar:</strong> ₺{{ number_format($order->total_amount, 0, ',', '.') }}</div>
                            <div><strong>Ödeme Şekli:</strong> {{ strtoupper($order->payment_method ?? 'PAYTR') }}</div>
                            @if($order->card_note)
                                <div class="italic text-rose-800">
                                    <strong>Kart Notu:</strong> "{{ $order->card_note }}"
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Çiçekler Listesi -->
                    <div class="pt-4 border-t border-slate-100">
                        <h4 class="text-xs font-black uppercase text-slate-400 mb-3">Siparişteki Çiçekler &amp; Hediyeler</h4>
                        <div class="space-y-2.5">
                            @foreach($order->items as $it)
                                @php
                                    $isCustom = ($it->item_type === 'custom_bouquet' || !empty($it->options['custom_bouquet']));
                                @endphp
                                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs space-y-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-[10px] shrink-0">
                                                {{ $it->quantity }}x
                                            </span>
                                            <span class="font-bold text-slate-800">{{ $it->product->name ?? 'Çiçek Tasarımı' }}</span>
                                            @if($isCustom)
                                                <span class="text-[10px] font-black bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full border border-blue-200 flex items-center gap-1">
                                                    <i class="fas fa-magic text-[9px]"></i>
                                                    <span>Özel Tasarım</span>
                                                </span>
                                            @endif
                                        </div>
                                        <span class="font-black text-rose-600">₺{{ number_format($it->unit_price * $it->quantity, 0, ',', '.') }}</span>
                                    </div>
                                    @if($isCustom && !empty($it->options))
                                        <div class="pl-8 space-y-1.5 pt-1.5 border-t border-slate-200/70">
                                            <div class="text-[11px] text-slate-600 font-medium">
                                                Ambalaj: <strong class="text-slate-800">{{ $it->options['package_color'] ?? '-' }}</strong> &nbsp;|&nbsp; 
                                                Kurdele: <strong class="text-slate-800">{{ $it->options['ribbon_color'] ?? '-' }}</strong>
                                            </div>
                                            @if(!empty($it->options['stems']))
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($it->options['stems'] as $st)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-white border border-rose-100 text-rose-950 font-bold text-[10px] shadow-2xs">
                                                            <i class="fas fa-spa text-blue-500 text-[9px]"></i>
                                                            <span>{{ $st['name'] }}</span>
                                                            <span class="text-rose-600 font-black">({{ $st['quantity'] }} Dal)</span>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            @endif

        </div>
    </div>
</x-frontend-layout>
