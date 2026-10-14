<x-frontend-layout title="Hesabım & Kuponlarım - İstanbul Su Arıtma Marketi">
    <div class="bg-gray-50/60 min-h-screen py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Üst Karşılama Başlığı (Tek Renk & Sade) -->
            <div class="bg-rose-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
                    <i class="fas fa-ticket-alt text-[180px]"></i>
                </div>
                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1.5">
                        <h1 class="text-2xl sm:text-3xl font-black">
                            Hoş Geldiniz, {{ $user->name }}
                        </h1>
                        <p class="text-blue-100 text-xs sm:text-sm max-w-xl">
                            Size özel tanımlanan indirim kuponlarını, şans çarkı hediyelerinizi ve geçmiş siparişlerinizi buradan kolayca takip edebilirsiniz.
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('products.index') }}" class="bg-white text-blue-800 hover:bg-blue-50 font-black text-xs px-5 py-3 rounded-2xl shadow-lg transition-all transform active:scale-95 flex items-center gap-2 whitespace-nowrap">
                            <i class="fas fa-shopping-bag"></i> Alışverişe Başla
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bildirimler -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
                    <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
                    <i class="fas fa-exclamation-circle text-rose-600 text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                <!-- Sol Kolon: Kuponlarım & Siparişlerim -->
                <div class="lg:col-span-2 space-y-8">
                    
                    <!-- KUPONLARIM VE PROMOSYONLARIM KARTI -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm space-y-6">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <div>
                                <h2 class="text-lg font-black text-gray-900 flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-sm">
                                        <i class="fas fa-gift"></i>
                                    </span>
                                    Tanımlı İndirim Kuponlarım ({{ $userCoupons->count() }})
                                </h2>
                                <p class="text-xs text-gray-500 mt-0.5">Alışverişlerinizde sepet aşamasında kullanabileceğiniz aktif indirimleriniz</p>
                            </div>
                        </div>

                        @if($userCoupons->isEmpty())
                            <div class="text-center py-10 space-y-3 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                                <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center mx-auto text-lg">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                                <p class="text-xs font-bold text-gray-600">Henüz hesabınıza tanımlı bir kupon bulunmuyor.</p>
                                <p class="text-[11px] text-gray-400">Ürün sayfalarındaki Şans Çarkını çevirerek anında sürpriz indirim kuponları kazanabilirsiniz!</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($userCoupons as $uc)
                                    @php
                                        $cp = $uc->coupon;
                                        $isExpired = $cp->expires_at && $cp->expires_at->isPast();
                                        $sourceText = match($uc->source) {
                                            'welcome' => 'Hoş Geldin Kuponu',
                                            'spin_wheel' => 'Şans Çarkı Hediyesi',
                                            default => 'Özel Promosyon'
                                        };
                                    @endphp
                                    <div class="relative overflow-hidden rounded-2xl border {{ $uc->is_used || $isExpired ? 'border-gray-200 bg-gray-50 opacity-70' : 'border-purple-200 bg-gradient-to-br from-purple-50/60 via-white to-indigo-50/40' }} p-5 flex flex-col justify-between shadow-2xs space-y-4">
                                        
                                        <!-- Üst Bilgi -->
                                        <div>
                                            <div class="flex items-center justify-between gap-2 mb-2">
                                                <span class="text-[10px] font-black uppercase tracking-wider {{ $uc->is_used ? 'bg-gray-200 text-gray-700' : 'bg-purple-100 text-purple-800' }} px-2.5 py-0.5 rounded-full">
                                                    {{ $sourceText }}
                                                </span>
                                                @if($uc->is_used)
                                                    <span class="text-[10px] font-bold text-gray-500">Kullanıldı</span>
                                                @elseif($isExpired)
                                                    <span class="text-[10px] font-bold text-red-600">Süresi Doldu</span>
                                                @else
                                                    <span class="text-[10px] font-black text-emerald-600 flex items-center gap-1">
                                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Aktif
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="text-xl font-black text-gray-900 mt-1">
                                                @if($cp->type === 'percent')
                                                    %{{ number_format($cp->value, 0) }} İndirim
                                                @else
                                                    {{ number_format($cp->value, 0, ',', '.') }} TL İndirim
                                                @endif
                                            </div>

                                            <div class="text-[11px] text-gray-600 mt-1 space-y-0.5">
                                                @if($cp->min_order_amount > 0)
                                                    <div>• Min. Sepet: <strong>{{ number_format($cp->min_order_amount, 0, ',', '.') }} TL</strong></div>
                                                @else
                                                    <div>• Alt sepet limiti yok</div>
                                                @endif
                                                @if($cp->expires_at)
                                                    <div>• Son Kullanım: {{ $cp->expires_at->format('d.m.Y') }}</div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Kupon Kodu & Aksiyonlar -->
                                        <div class="pt-3 border-t border-purple-100/60 flex items-center justify-between gap-2"
                                             x-data="{ copied: false }">
                                            <div class="bg-white px-3 py-1.5 rounded-xl border border-dashed border-purple-300 font-mono font-black text-xs text-purple-900 tracking-wider">
                                                {{ $cp->code }}
                                            </div>

                                            <div class="flex items-center gap-1.5">
                                                <button type="button" 
                                                        @click="navigator.clipboard.writeText('{{ $cp->code }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                        class="px-2.5 py-1.5 text-xs font-bold bg-white hover:bg-gray-100 border border-gray-200 rounded-xl text-gray-700 transition-colors cursor-pointer"
                                                        title="Kodu Kopyala">
                                                    <i class="fas" :class="copied ? 'fa-check text-emerald-600' : 'fa-copy'"></i>
                                                    <span x-text="copied ? 'Kopyalandı' : 'Kopyala'"></span>
                                                </button>

                                                @if(!$uc->is_used && !$isExpired)
                                                    <a href="{{ route('customer.coupons.apply', $cp->code) }}" 
                                                       class="px-3 py-1.5 text-xs font-black bg-purple-600 hover:bg-purple-700 text-white rounded-xl shadow-xs transition-colors whitespace-nowrap">
                                                        Sepette Kullan &rarr;
                                                    </a>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- SİPARİŞLERİM KARTI -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm space-y-6">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <div>
                                <h2 class="text-lg font-black text-gray-900 flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-sm">
                                        <i class="fas fa-box"></i>
                                    </span>
                                    Siparişlerim ({{ $orders->count() }})
                                </h2>
                                <p class="text-xs text-gray-500 mt-0.5">Satın aldığınız ürünlerin kargo ve teslimat durumu</p>
                            </div>
                        </div>

                        @if($orders->isEmpty())
                            <div class="text-center py-10 space-y-3 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto text-lg">
                                    <i class="fas fa-shopping-basket"></i>
                                </div>
                                <p class="text-xs font-bold text-gray-600">Henüz kayıtlı bir siparişiniz bulunmuyor.</p>
                                <a href="{{ route('products.index') }}" class="inline-block text-xs text-blue-600 font-bold hover:underline">
                                    Su arıtma cihazlarını inceleyin &rarr;
                                </a>
                            </div>
                        @else
                            <div class="divide-y divide-gray-100">
                                @foreach($orders as $order)
                                    <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <div>
                                            <div class="font-black text-sm text-gray-900 flex items-center gap-2">
                                                <span>{{ $order->order_number }}</span>
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">
                                                    {{ $order->status }}
                                                </span>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $order->created_at->format('d.m.Y H:i') }} • {{ $order->items->count() ?? 1 }} Ürün
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-base font-black text-gray-900">
                                                {{ number_format($order->total_amount, 0, ',', '.') }} TL
                                            </div>
                                            <div class="text-[11px] text-emerald-600 font-bold">
                                                {{ $order->payment_method === 'credit_card' ? 'Online Ödendi' : 'Kapıda Ödeme' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>

                <!-- Sağ Kolon: Profil & Şifre Güncelleme -->
                <div class="space-y-6">
                    <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm space-y-6">
                        <div class="border-b border-gray-100 pb-3">
                            <h3 class="font-black text-sm text-gray-900 flex items-center gap-2">
                                <i class="fas fa-id-card text-blue-600"></i> Hesap Bilgilerim
                            </h3>
                        </div>

                        <form action="{{ route('customer.profile.update') }}" method="POST" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Adınız Soyadınız</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full text-xs font-bold py-2.5 px-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Telefon Numaranız</label>
                                <input type="text" value="{{ $user->phone ?? 'Belirtilmemiş' }}" disabled
                                       class="w-full text-xs font-bold py-2.5 px-3 border border-gray-200 bg-gray-50 text-gray-500 rounded-xl cursor-not-allowed">
                                <p class="text-[10px] text-gray-400 mt-1">Telefon numarası hesap güvenliği için sabittir.</p>
                            </div>

                            <div class="pt-3 border-t border-gray-100">
                                <h4 class="text-xs font-black text-gray-800 mb-3 flex items-center gap-1.5">
                                    <i class="fas fa-lock text-gray-400"></i> Şifre Değiştir (İsteğe Bağlı)
                                </h4>

                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Mevcut Şifre</label>
                                        <input type="password" name="current_password"
                                               class="w-full text-xs py-2 px-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Yeni Şifre</label>
                                        <input type="password" name="new_password"
                                               class="w-full text-xs py-2 px-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-black py-3 rounded-xl shadow-xs transition-colors flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-save"></i> Bilgilerimi Güncelle
                            </button>
                        </form>

                        <div class="pt-3 border-t border-gray-100">
                            <a href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('customer-logout-form').submit();"
                               class="w-full bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold py-2.5 rounded-xl border border-rose-200 transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                                <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                            </a>
                            <form id="customer-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>

                    <!-- Müşteri Hizmetleri İletişim Kutusu -->
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50/60 rounded-3xl p-5 border border-blue-100 space-y-3">
                        <div class="flex items-center gap-2 text-blue-950 font-black text-xs">
                            <i class="fas fa-headset text-blue-600 text-sm"></i> Yardıma mı İhtiyacınız Var?
                        </div>
                        <p class="text-[11px] text-blue-800 leading-relaxed">
                            Cihaz montajı, filtre değişimi veya kupon kullanımıyla ilgili sorularınız için müşteri hizmetlerimize 7/24 WhatsApp üzerinden ulaşabilirsiniz.
                        </p>
                        <a href="https://wa.me/905442502758?text=Merhaba,%20İstanbul%20Su%20Arıtma%20Marketi%20hesabımla%20ilgili%20destek%20almak%20istiyorum." 
                           target="_blank" 
                           class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black px-4 py-2 rounded-xl shadow-xs transition-colors">
                            <i class="fab fa-whatsapp"></i> WhatsApp Destek Hattı
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-frontend-layout>
