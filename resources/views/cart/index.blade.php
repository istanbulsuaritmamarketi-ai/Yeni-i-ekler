<x-frontend-layout title="Alışveriş Sepeti — Tazecicekler.com">
    <div class="bg-slate-50 py-10 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 flex items-center gap-3">
                        <i class="fas fa-shopping-bag text-rose-600"></i> Çiçek Sepetim
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">Özenle hazırlanan çiçekleriniz siparişe hazır</p>
                </div>
                @if(!empty($cart))
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Sepetinizdeki tüm çiçekleri temizlemek istediğinize emin misiniz?');">
                        @csrf
                        <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-extrabold bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-xl transition flex items-center gap-1.5">
                            <i class="fas fa-trash-alt"></i> Sepeti Temizle
                        </button>
                    </form>
                @endif
            </div>

            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 text-emerald-800 rounded-r-2xl shadow-xs flex items-center justify-between">
                    <div class="flex items-center gap-2 font-bold text-xs sm:text-sm">
                        <i class="fas fa-check-circle text-emerald-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-rose-50 border-l-4 border-rose-500 p-4 text-rose-800 rounded-r-2xl shadow-xs flex items-center justify-between">
                    <div class="flex items-center gap-2 font-bold text-xs sm:text-sm">
                        <i class="fas fa-exclamation-circle text-rose-600"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if(empty($cart))
                <div class="bg-white p-12 text-center rounded-3xl shadow-sm border border-rose-100 max-w-lg mx-auto my-8">
                    <div class="w-20 h-20 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                        <i class="fas fa-spa"></i>
                    </div>
                    <h2 class="text-xl font-black text-slate-900 mb-2">Sepetiniz Boş</h2>
                    <p class="text-slate-500 text-xs sm:text-sm mb-6">Sevdiklerinizi mutlu edecek en taze çiçek ve hediyelerimizi inceleyerek hemen sepetinize ekleyebilirsiniz.</p>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 bg-rose-600 text-white font-extrabold text-xs sm:text-sm py-3 px-6 rounded-2xl hover:bg-rose-700 transition shadow-lg shadow-rose-600/30">
                        <i class="fas fa-arrow-left"></i> Çiçekleri İncele
                    </a>
                </div>
            @else
                <!-- Güven ve Teslimat Bilgi Bandı (Tek Renk & Sade) -->
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl shadow-xs flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center text-lg shrink-0">
                            <i class="fas fa-camera"></i>
                        </div>
                        <div>
                            <span class="text-xs sm:text-sm font-black text-rose-950 block">Fotoğraflı Onay &amp; Özel Çiçek Kuryesi</span>
                            <span class="text-[11px] text-slate-600">Buketiniz kuryeye verilmeden önce hazırlanıp onaylanır.</span>
                        </div>
                    </div>
                    <span class="hidden sm:inline-block bg-emerald-600 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase">
                        Ücretsiz Teslimat
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- Sol Sütun: Ürün Listesi -->
                    <div class="lg:col-span-8 space-y-6">
                        <div class="bg-white rounded-3xl shadow-xs border border-rose-100 overflow-hidden divide-y divide-slate-100">
                            @foreach($cart as $id => $item)
                                @php
                                    $itemImg = !empty($item['image']) ? (file_exists(public_path('images/' . $item['image'])) ? asset('images/' . $item['image']) : asset('storage/' . $item['image'])) : asset('images/products/6.png');
                                @endphp
                                <div class="p-4 sm:p-6 flex flex-col sm:flex-row items-center gap-4 sm:gap-6 justify-between">
                                    <div class="flex items-center gap-4 w-full sm:w-auto">
                                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl overflow-hidden bg-rose-50 shrink-0 border border-slate-100">
                                            <img src="{{ $itemImg }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            @if(isset($item['is_custom_bouquet']) && $item['is_custom_bouquet'])
                                                <span class="text-[9px] font-black text-purple-700 uppercase bg-purple-100 px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                                    <i class="fas fa-magic"></i> Kişiye Özel Tasarım Buket
                                                </span>
                                            @elseif(isset($item['is_upsell']) && $item['is_upsell'])
                                                <span class="text-[9px] font-black text-pink-600 uppercase bg-pink-50 px-2 py-0.5 rounded-md">Ek Sürpriz Hediye</span>
                                            @else
                                                <span class="text-[9px] font-black text-rose-600 uppercase bg-rose-50 px-2 py-0.5 rounded-md">Taze Çiçek</span>
                                            @endif
                                            <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 mt-1">
                                                {{ $item['name'] }}
                                            </h3>

                                            @if(isset($item['is_custom_bouquet']) && $item['is_custom_bouquet'] && !empty($item['options']['summary']))
                                                <p class="text-[11px] text-slate-500 font-medium mt-1 max-w-md line-clamp-2">
                                                    {{ $item['options']['summary'] }}
                                                </p>
                                            @endif

                                            <div class="text-xs font-black text-rose-600 mt-1">
                                                ₺{{ number_format($item['price'], 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Adet Güncelleme ve Silme -->
                                    <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-0 border-slate-100">
                                        <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden bg-slate-50">
                                            <form action="{{ route('cart.update', $id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="action" value="decrease">
                                                <button type="submit" class="px-2.5 py-1 text-slate-600 hover:bg-slate-200 transition font-bold text-xs">-</button>
                                            </form>
                                            <span class="px-3 py-1 font-black text-xs text-slate-800">{{ $item['quantity'] }}</span>
                                            <form action="{{ route('cart.update', $id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="action" value="increase">
                                                <button type="submit" class="px-2.5 py-1 text-slate-600 hover:bg-slate-200 transition font-bold text-xs">+</button>
                                            </form>
                                        </div>

                                        <div class="text-right">
                                            <div class="text-sm font-black text-slate-900">
                                                ₺{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                            </div>
                                            <form action="{{ route('cart.remove', $id) }}" method="POST" class="mt-0.5">
                                                @csrf
                                                <button type="submit" class="text-[11px] text-slate-400 hover:text-rose-600 transition font-bold">
                                                    Kaldır
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Kart Notu & Teslimat Özeti (Varsa) -->
                        @if(!empty($flowerDelivery['card_note']) || !empty($flowerDelivery['delivery_date']))
                            <div class="bg-rose-50/80 rounded-3xl p-5 border border-rose-200 text-xs space-y-2">
                                <div class="font-extrabold text-rose-950 flex items-center gap-1.5">
                                    <i class="fas fa-envelope-open-text text-rose-600"></i> Teslimat Tercihleri:
                                </div>
                                @if(!empty($flowerDelivery['delivery_date']))
                                    <div class="text-slate-700">
                                        <strong>Teslim Tarihi &amp; Saati:</strong> {{ $flowerDelivery['delivery_date'] }} ({{ $flowerDelivery['delivery_time_slot'] ?? '12:00 - 15:00' }})
                                    </div>
                                @endif
                                @if(!empty($flowerDelivery['card_note']))
                                    <div class="text-slate-700 italic">
                                        <strong>Kart Notunuz:</strong> "{{ $flowerDelivery['card_note'] }}"
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Çapraz Satış Önerileri (Sepete Ekstra Ekle) -->
                        @if(isset($upsellProducts) && $upsellProducts->count() > 0)
                            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-rose-100 shadow-xs">
                                <h3 class="text-xs sm:text-sm font-black text-slate-900 mb-3 flex items-center gap-2">
                                    <i class="fas fa-gift text-rose-600"></i> Çiçeğinizin Yanına Ekleyebileceğiniz Hediyeler
                                </h3>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @foreach($upsellProducts as $up)
                                        <div class="border border-slate-100 rounded-2xl p-3 text-center flex flex-col justify-between group hover:border-rose-200 hover:bg-rose-50/30 transition">
                                            <div class="aspect-square rounded-xl overflow-hidden bg-slate-50 mb-2">
                                                <img src="{{ $up->image_url }}" alt="{{ $up->name }}" class="w-full h-full object-cover">
                                            </div>
                                            <div>
                                                <h4 class="text-[11px] font-bold text-slate-800 line-clamp-1">{{ $up->name }}</h4>
                                                <div class="text-xs font-black text-rose-600 mt-1">₺{{ number_format($up->effective_price, 0, ',', '.') }}</div>
                                            </div>
                                            <form action="{{ route('cart.add', $up->id) }}" method="POST" class="mt-2">
                                                @csrf
                                                <button type="submit" class="w-full py-1.5 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white rounded-xl text-[10px] font-extrabold transition">
                                                    + Ekle
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Sağ Sütun: Kupon ve Özet -->
                    <div class="lg:col-span-4 space-y-6">
                        
                        <!-- Kupon Kodu Formu -->
                        <div class="bg-white rounded-3xl p-5 border border-rose-100 shadow-xs">
                            <h3 class="text-xs font-black uppercase text-slate-700 tracking-wider mb-2">İndirim Kuponu</h3>
                            @if($couponData)
                                <div class="bg-emerald-50 border border-emerald-200 p-3 rounded-2xl flex items-center justify-between text-xs">
                                    <div>
                                        <span class="font-extrabold text-emerald-800">{{ $couponData['code'] }}</span>
                                        <span class="text-emerald-600 block text-[10px]">-{{ $couponData['discount'] }} TL İndirim Uygulandı</span>
                                    </div>
                                    <form action="{{ route('cart.coupon.remove') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-rose-500 hover:text-rose-700 font-bold text-xs">Kaldır</button>
                                    </form>
                                </div>
                            @else
                                <form action="{{ route('cart.coupon.apply') }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="text" name="code" placeholder="Kupon Kodu (Örn: YENI10)"
                                           class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500 uppercase">
                                    <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-black px-4 py-2 rounded-xl transition">
                                        Uygula
                                    </button>
                                </form>
                            @endif
                        </div>

                        <!-- Sepet Özeti -->
                        <div class="bg-white rounded-3xl p-6 border border-rose-100 shadow-xs space-y-4">
                            <h3 class="text-sm font-black text-slate-900 pb-3 border-b border-slate-100">Sipariş Özeti</h3>

                            <div class="space-y-2.5 text-xs text-slate-600">
                                <div class="flex justify-between">
                                    <span>Ara Toplam:</span>
                                    <span class="font-bold text-slate-800">₺{{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Özel Kurye Teslimatı:</span>
                                    <span class="font-bold text-emerald-600">ÜCRETSİZ</span>
                                </div>
                                @if($discount > 0)
                                    <div class="flex justify-between text-emerald-600 font-bold">
                                        <span>Kupon İndirimi:</span>
                                        <span>-₺{{ number_format($discount, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="pt-3 border-t border-slate-100 flex justify-between items-baseline">
                                    <span class="text-sm font-black text-slate-900">Toplam Tutar:</span>
                                    <span class="text-2xl font-black text-rose-600">₺{{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <a href="{{ route('checkout') }}" 
                               class="w-full py-4 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl font-black text-sm shadow-xl shadow-rose-600/30 transition-all transform hover:-translate-y-0.5 active:scale-98 flex items-center justify-center gap-2">
                                <span>Ödemeye Geç (Siparişi Tamamla)</span>
                                <i class="fas fa-arrow-right text-xs"></i>
                            </a>

                            <div class="text-[11px] text-slate-400 text-center space-y-1 pt-2">
                                <div class="flex items-center justify-center gap-2">
                                    <i class="fas fa-shield-alt text-emerald-500"></i>
                                    <span>256-Bit SSL &amp; PayTR Korumalı Güvenli Ödeme</span>
                                </div>
                                <div>Kayıt olmadan anında sipariş verebilirsiniz.</div>
                            </div>
                        </div>

                    </div>
                </div>
            @endif

        </div>
    </div>
</x-frontend-layout>
