<x-frontend-layout title="Siparişiniz Alındı — Tazecicekler.com">
    <div class="bg-slate-50 py-12 sm:py-16 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-rose-100">
                
                <!-- Başarı Başlığı (Tek Renk & Sade) -->
                <div class="bg-rose-600 py-10 px-6 text-center text-white relative">
                    <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-4 text-4xl shadow-lg shadow-black/10">
                        <i class="fas fa-heart text-white"></i>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black">Siparişiniz Sevgiyle Alındı!</h1>
                    <p class="mt-2 text-rose-100 text-xs sm:text-sm font-medium">Çiçek tasarımcılarımız çiçeğinizi en taze haliyle hazırlamaya başladı.</p>
                </div>
                
                <div class="p-6 sm:p-10 space-y-8">
                    <!-- Sipariş Numarası -->
                    <div class="text-center p-5 bg-rose-50/60 rounded-2xl border border-rose-100">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-widest block">Sipariş Takip Numaranız</span>
                        <span class="text-3xl sm:text-4xl font-black text-rose-600 tracking-wider block mt-1">{{ $order->order_number }}</span>
                        <p class="text-[11px] text-slate-500 mt-1">Bu numara ile siparişinizin durumunu anlık takip edebilirsiniz.</p>
                    </div>

                    <!-- Teslimat Detayları Özeti -->
                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 text-xs space-y-3">
                        <h3 class="font-extrabold text-slate-900 flex items-center gap-2 pb-2 border-b border-slate-200">
                            <i class="fas fa-truck text-rose-600"></i> Teslimat Özeti
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-slate-700">
                            <div>
                                <span class="text-slate-400 block text-[10px]">Alıcı:</span>
                                <strong class="text-slate-900">{{ $order->recipient_name ?: $order->customer_name }}</strong>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">Teslimat Yeri:</span>
                                <strong class="text-slate-900">{{ $order->recipient_district ?? 'İstanbul' }}</strong>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">Planlanan Tarih:</span>
                                <strong class="text-slate-900">{{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->locale('tr')->isoFormat('D MMMM YYYY') : 'Aynı Gün' }}</strong>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">Teslimat Saati:</span>
                                <strong class="text-slate-900">{{ $order->delivery_time_slot ?? 'Günün İlk Saatleri' }}</strong>
                            </div>
                        </div>

                        @if($order->card_note)
                            <div class="pt-2 border-t border-slate-200">
                                <span class="text-rose-600 block text-[10px] font-bold uppercase">Mühürlü Kart Notunuz:</span>
                                <p class="italic text-slate-800 font-medium mt-0.5">"{{ $order->card_note }}"</p>
                            </div>
                        @endif
                    </div>

                    <!-- 3 Adımlı Hazırlık Süreci -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-black uppercase text-slate-700 tracking-wider">Sırada Ne Var?</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div class="p-3.5 rounded-2xl bg-white border border-rose-100 shadow-2xs">
                                <span class="w-6 h-6 rounded-full bg-rose-600 text-white text-[11px] font-black flex items-center justify-center mb-2">1</span>
                                <strong class="block text-slate-900">Özenli Hazırlık</strong>
                                <span class="text-slate-500 text-[11px] mt-0.5 block">En taze goncalar tek tek seçilerek buketiniz bağlanır.</span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-white border border-rose-100 shadow-2xs">
                                <span class="w-6 h-6 rounded-full bg-rose-600 text-white text-[11px] font-black flex items-center justify-center mb-2">2</span>
                                <strong class="block text-slate-900"><i class="fas fa-camera text-rose-600 mr-1"></i> Fotoğraflı Onay</strong>
                                <span class="text-slate-500 text-[11px] mt-0.5 block">Hazırlanan çiçek kuryeye çıkmadan önce fotoğraflanır.</span>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-white border border-rose-100 shadow-2xs">
                                <span class="w-6 h-6 rounded-full bg-rose-600 text-white text-[11px] font-black flex items-center justify-center mb-2">3</span>
                                <strong class="block text-slate-900">Özel Çiçek Kuryesi</strong>
                                <span class="text-slate-500 text-[11px] mt-0.5 block">Seçilen saat diliminde alıcısına güleryüzle ulaştırılır.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Aksiyon Butonları -->
                    <div class="flex flex-col sm:flex-row justify-center gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" 
                           class="bg-rose-600 hover:bg-rose-700 text-white font-black text-xs py-3.5 px-8 rounded-2xl shadow-lg shadow-rose-600/30 transition flex items-center justify-center gap-2">
                            <i class="fas fa-truck-fast"></i>
                            <span>Siparişimi Canlı Takip Et</span>
                        </a>
                        <a href="{{ route('home') }}" 
                           class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs py-3.5 px-8 rounded-2xl transition flex items-center justify-center">
                            Vitrine Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-frontend-layout>
