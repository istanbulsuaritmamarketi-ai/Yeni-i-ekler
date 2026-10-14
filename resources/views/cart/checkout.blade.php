<x-frontend-layout title="Siparişi Tamamla — Tazecicekler.com">
    <div class="bg-slate-50 py-10 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Başlık ve İlerleme Adımı -->
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-check-circle text-rose-600"></i> Teslimat &amp; Ödeme Adımı
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">Lütfen çiçeğin teslim edileceği kişi ve adres bilgilerini giriniz</p>
                </div>

                <div class="flex items-center gap-2 text-xs font-extrabold text-slate-400">
                    <span class="text-rose-600">1. Sepet</span>
                    <i class="fas fa-chevron-right text-[10px]"></i>
                    <span class="text-rose-600 bg-rose-100 px-3 py-1 rounded-full text-rose-800">2. Teslimat &amp; Ödeme</span>
                    <i class="fas fa-chevron-right text-[10px]"></i>
                    <span>3. Onay</span>
                </div>
            </div>

            <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form"
                  x-data="{
                      sameAsSender: true,
                      paymentMethod: 'paytr',
                      deliveryType: 'standard',
                      baseTotal: {{ (float) $total }},
                      expressFee: {{ $expressCourierActive ? (float) $expressCourierFee : 0 }},
                      senderName: '{{ old('customer_name', $flowerDelivery['sender_name'] ?? '') }}',
                      recipientName: '{{ old('recipient_name', '') }}',
                      isAnon: {{ old('is_anonymous', ($flowerDelivery['is_anonymous'] ?? false) ? 'true' : 'false') }},
                      deliverySlot: '{{ old('delivery_time_slot', $flowerDelivery['delivery_time_slot'] ?? '12:00 - 15:00') }}',
                      get currentTotal() {
                          return this.deliveryType === 'express' ? (this.baseTotal + this.expressFee) : this.baseTotal;
                      },
                      get currentDeliveryFeeText() {
                          return this.deliveryType === 'express' ? '+₺' + Number(this.expressFee).toLocaleString('tr-TR') : 'ÜCRETSİZ';
                      }
                  }">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- SOL ALAN: FORM ALANLARI -->
                    <div class="lg:col-span-8 space-y-6">

                        <!-- 1. GÖNDEREN BİLGİLERİ (SİZ) -->
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-rose-100 shadow-xs space-y-4">
                            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                                <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-sm font-black">1</span>
                                <div>
                                    <h2 class="text-sm sm:text-base font-black text-slate-900">Gönderen Bilgileri (Siz)</h2>
                                    <p class="text-[11px] text-slate-500">Sipariş takibi ve fatura bilgilendirmesi için gereklidir</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Adınız Soyadınız *</label>
                                    <input type="text" name="customer_name" x-model="senderName" required
                                           value="{{ old('customer_name', $flowerDelivery['sender_name'] ?? '') }}"
                                           placeholder="Ad Soyad"
                                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Cep Telefonunuz *</label>
                                    <input type="tel" name="customer_phone" required
                                           value="{{ old('customer_phone') }}"
                                           placeholder="05XX XXX XX XX"
                                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold text-slate-700 mb-1">E-Posta Adresiniz (İsteğe Bağlı)</label>
                                    <input type="email" name="customer_email"
                                           value="{{ old('customer_email') }}"
                                           placeholder="ornek@email.com"
                                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500">
                                </div>
                            </div>
                        </div>

                        <!-- 2. ALICI & TESLİMAT BİLGİLERİ (ÇİÇEĞİN GİDECEĞİ KİŞİ) -->
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-rose-100 shadow-xs space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-sm font-black">2</span>
                                    <div>
                                        <h2 class="text-sm sm:text-base font-black text-slate-900">Alıcı ve Teslimat Adresi</h2>
                                        <p class="text-[11px] text-slate-500">Çiçeğin teslim edileceği kişi ve adres bilgileri</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Alıcı Adı Soyadı *</label>
                                    <input type="text" name="recipient_name" required
                                           value="{{ old('recipient_name') }}"
                                           placeholder="Çiçeği teslim alacak kişi"
                                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Alıcı Telefon Numarası *</label>
                                    <input type="tel" name="recipient_phone" required
                                           value="{{ old('recipient_phone') }}"
                                           placeholder="Kuryenin arayabileceği numara"
                                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500">
                                </div>

                                <div class="col-span-1 sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4"
                                     x-data="{
                                         regionsByCity: @js($regions->groupBy('city')),
                                         selectedCity: '{{ old('delivery_city', '') }}',
                                         selectedDistrictId: '{{ old('region_id', '') }}',
                                         init() {
                                             const cities = Object.keys(this.regionsByCity);
                                             if (!this.selectedCity || !this.regionsByCity[this.selectedCity]) {
                                                 this.selectedCity = cities.includes('İstanbul') ? 'İstanbul' : (cities[0] || '');
                                             }
                                         },
                                         get availableDistricts() {
                                             return this.regionsByCity[this.selectedCity] || [];
                                         }
                                     }">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Teslimat İli *</label>
                                        <select name="delivery_city" x-model="selectedCity" @change="selectedDistrictId = ''"
                                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500">
                                            <template x-for="c in Object.keys(regionsByCity)" :key="c">
                                                <option :value="c" x-text="c" :selected="c === selectedCity"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Teslimat İlçesi *</label>
                                        <select name="region_id" x-model="selectedDistrictId" required
                                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500">
                                            <option value="">-- İlçe Seçin --</option>
                                            <template x-for="reg in availableDistricts" :key="reg.id">
                                                <option :value="reg.id" x-text="reg.name" :selected="reg.id == selectedDistrictId"></option>
                                            </template>
                                        </select>
                                        <p class="text-[11px] text-rose-500 font-bold mt-1" x-show="availableDistricts.length === 0 && Object.keys(regionsByCity).length > 0">
                                            Bu il için şu anda teslimat hizmeti verilmemektedir.
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Teslimat Günü &amp; Saati</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="date" name="delivery_date" 
                                               value="{{ old('delivery_date', $flowerDelivery['delivery_date'] ?? now()->format('Y-m-d')) }}"
                                               min="{{ now()->format('Y-m-d') }}"
                                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-2 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500">
                                        
                                        <select name="delivery_time_slot" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2 py-2 text-[11px] font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500">
                                            @foreach($timeSlots as $slot)
                                                <option value="{{ $slot }}" {{ old('delivery_time_slot', $flowerDelivery['delivery_time_slot'] ?? '') == $slot ? 'selected' : '' }}>
                                                    {{ $slot }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Tam Teslimat Adresi *</label>
                                    <textarea name="address" rows="2" required
                                              placeholder="Mahalle, Cadde/Sokak, Bina No, Kat, Daire, Kurum/İşyeri Adı..."
                                              class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500">{{ old('address') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- 3. TESLİMAT HIZI & KURYE SEÇENEĞİ -->
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-rose-100 shadow-xs space-y-4">
                            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                                <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-sm font-black">3</span>
                                <div>
                                    <h2 class="text-sm sm:text-base font-black text-slate-900">Teslimat Hızı &amp; Kurye Seçeneği</h2>
                                    <p class="text-[11px] text-slate-500">Çiçeğinizin teslimat hızını ve kurye önceliğini belirleyin</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Standart Teslimat -->
                                <label class="p-4 sm:p-5 rounded-2xl border-2 cursor-pointer transition-all flex flex-col justify-between"
                                       :class="deliveryType === 'standard' ? 'border-rose-600 bg-rose-50/60 ring-2 ring-rose-500/20 shadow-xs' : 'border-slate-200 hover:bg-slate-50'">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center gap-2.5">
                                            <input type="radio" name="delivery_type" value="standard" x-model="deliveryType" class="text-rose-600">
                                            <span class="text-sm font-black text-slate-900">Standart Teslimat</span>
                                        </div>
                                        <span class="text-xs font-black text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full">Ücretsiz</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-2">
                                        Seçtiğiniz tarih ve saat diliminde kuryemiz tarafından özenle teslim edilir.
                                    </p>
                                </label>

                                @if($expressCourierActive)
                                    <!-- VIP Hızlı Kurye -->
                                    <label class="p-4 sm:p-5 rounded-2xl border-2 cursor-pointer transition-all flex flex-col justify-between"
                                           :class="deliveryType === 'express' ? 'border-rose-600 bg-rose-50/60 ring-2 ring-rose-500/20 shadow-xs' : 'border-slate-200 hover:bg-slate-50'">
                                        <div class="flex items-start justify-between">
                                            <div class="flex items-center gap-2.5">
                                                <input type="radio" name="delivery_type" value="express" x-model="deliveryType" class="text-rose-600">
                                                <span class="text-sm font-black text-slate-900">{{ $expressCourierTitle }}</span>
                                            </div>
                                            <span class="text-xs font-black text-rose-600 bg-rose-100 px-2.5 py-0.5 rounded-full">+₺{{ number_format($expressCourierFee, 0, ',', '.') }}</span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 mt-2">
                                            {{ $expressCourierSubtitle }}
                                        </p>
                                    </label>
                                @endif
                            </div>
                        </div>

                        <!-- 4. ZARFLI KART NOTU & GİZLİLİK -->
                        <div class="bg-rose-50/70 rounded-3xl p-6 sm:p-8 border border-rose-200/80 shadow-xs space-y-4">
                            <div class="flex items-center gap-3 pb-3 border-rose-200 border-b">
                                <span class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center text-sm font-black">4</span>
                                <div>
                                    <h2 class="text-sm sm:text-base font-black text-rose-950">Mühürlü Çiçek Kart Notu</h2>
                                    <p class="text-[11px] text-rose-700">Sevdiklerinize hissettirmek istediğiniz duygusal mesaj</p>
                                </div>
                            </div>

                            <div>
                                <textarea name="card_note" rows="3" maxlength="250"
                                          placeholder="Kartın içine yazılmasını istediğiniz not (Örn: Hayatıma kattığın tüm güzellikler için teşekkür ederim...)"
                                          class="w-full bg-white border border-rose-200 rounded-2xl p-3.5 text-xs font-medium text-slate-800 focus:outline-none focus:border-rose-500">{{ old('card_note', $flowerDelivery['card_note'] ?? '') }}</textarea>
                                
                                <div class="mt-3 flex items-center justify-between">
                                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                                        <input type="checkbox" name="is_anonymous" value="1" x-model="isAnon"
                                               class="rounded text-rose-600 focus:ring-rose-500">
                                        <span>İsmim Alıcıya Gizlensin (Gizli Hayran / Anonim Gönderim)</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- 5. ÖDEME YÖNTEMİ SEÇİMİ -->
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-rose-100 shadow-xs space-y-4">
                            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                                <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-sm font-black">5</span>
                                <div>
                                    <h2 class="text-sm sm:text-base font-black text-slate-900">Ödeme Yöntemi</h2>
                                    <p class="text-[11px] text-slate-500">Size en uygun güvenli ödeme seçeneğini belirleyin</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Online POS (Kredi / Banka Kartı) -->
                                <label class="p-4 sm:p-5 rounded-2xl border-2 cursor-pointer transition-all flex flex-col justify-between"
                                       :class="paymentMethod === 'paytr' ? 'border-rose-600 bg-rose-50/60 ring-2 ring-rose-500/20 shadow-xs' : 'border-slate-200 hover:bg-slate-50'">
                                    <div class="flex items-center justify-between">
                                        <input type="radio" name="payment_method" value="paytr" x-model="paymentMethod" class="text-rose-600">
                                        <i class="fas fa-credit-card text-rose-600 text-xl"></i>
                                    </div>
                                    <div class="mt-3">
                                        <span class="text-sm font-black text-slate-900 block">Kredi / Banka Kartı (POS)</span>
                                        <span class="text-xs text-slate-500 block mt-0.5">PayTR 3D Secure ile Güvenli Ödeme</span>
                                    </div>
                                </label>

                                <!-- Kapıda Ödeme (Nakit / Mobil POS) -->
                                <label class="p-4 sm:p-5 rounded-2xl border-2 cursor-pointer transition-all flex flex-col justify-between"
                                       :class="paymentMethod === 'cash' ? 'border-rose-600 bg-rose-50/60 ring-2 ring-rose-500/20 shadow-xs' : 'border-slate-200 hover:bg-slate-50'">
                                    <div class="flex items-center justify-between">
                                        <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="text-rose-600">
                                        <i class="fas fa-hand-holding-dollar text-emerald-600 text-xl"></i>
                                    </div>
                                    <div class="mt-3">
                                        <span class="text-sm font-black text-slate-900 block">Kapıda Ödeme</span>
                                        <span class="text-xs text-slate-500 block mt-0.5">Nakit veya Kuryede POS Cihazı ile</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- 5. HUKUKİ ONAYLAR (KVKK & MESAFELİ SATIŞ) -->
                        <div class="p-4 rounded-2xl bg-white border border-slate-200 text-xs text-slate-600 space-y-2">
                            <label class="flex items-start gap-2.5 cursor-pointer">
                                <input type="checkbox" required checked class="rounded text-rose-600 focus:ring-rose-500 mt-0.5">
                                <span>
                                    <a href="{{ route('page.show', 'mesafeli-satis-sozlesmesi') }}" target="_blank" class="text-rose-600 underline font-bold">Mesafeli Satış Sözleşmesi</a> ve 
                                    <a href="{{ route('page.show', 'on-bilgilendirme-formu') }}" target="_blank" class="text-rose-600 underline font-bold">Ön Bilgilendirme Formu</a>'nu okudum, onaylıyorum.
                                </span>
                            </label>
                            <label class="flex items-start gap-2.5 cursor-pointer">
                                <input type="checkbox" required checked class="rounded text-rose-600 focus:ring-rose-500 mt-0.5">
                                <span>
                                    <a href="{{ route('page.show', 'kvkk-aydinlatma-metni') }}" target="_blank" class="text-rose-600 underline font-bold">6698 KVKK Aydınlatma Metni</a> kapsamında kişisel verilerimin işlenmesine açık rıza veriyorum.
                                </span>
                            </label>
                        </div>

                    </div>

                    <!-- SAĞ ALAN: SİPARİŞ ÖZETİ & TAMAMLAMA BUTONU -->
                    <div class="lg:col-span-4 space-y-6">
                        <div class="bg-white rounded-3xl p-6 border border-rose-100 shadow-xl sticky top-28 space-y-4">
                            <h3 class="text-base font-black text-slate-900 pb-3 border-b border-slate-100">Sipariş Özeti</h3>

                            <!-- Ürün Listesi Küçük -->
                            <div class="space-y-3 max-h-56 overflow-y-auto pr-1">
                                @foreach($cart as $c)
                                    @php
                                        $cImg = !empty($c['image']) ? (file_exists(public_path('images/' . $c['image'])) ? asset('images/' . $c['image']) : asset('storage/' . $c['image'])) : asset('images/products/6.png');
                                    @endphp
                                    <div class="flex items-center justify-between gap-3 text-xs">
                                        <div class="flex items-center gap-2">
                                            <div class="w-10 h-10 rounded-xl overflow-hidden bg-slate-50 shrink-0">
                                                <img src="{{ $cImg }}" alt="{{ $c['name'] }}" class="w-full h-full object-cover">
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="font-extrabold text-slate-900 truncate">{{ $c['name'] }}</div>
                                                <div class="text-[10px] text-slate-400">{{ $c['quantity'] }} adet</div>
                                            </div>
                                        </div>
                                        <div class="font-black text-slate-800 shrink-0">
                                            ₺{{ number_format($c['price'] * $c['quantity'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="pt-3 border-t border-slate-100 space-y-2 text-xs text-slate-600">
                                <div class="flex justify-between">
                                    <span>Ara Toplam:</span>
                                    <span class="font-bold text-slate-800">₺{{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span>Kurye Bedeli:</span>
                                    <span class="font-bold" 
                                          :class="deliveryType === 'express' ? 'text-rose-600 font-black' : 'text-emerald-600'" 
                                          x-text="currentDeliveryFeeText">
                                        ÜCRETSİZ
                                    </span>
                                </div>
                                @if($discount > 0)
                                    <div class="flex justify-between text-emerald-600 font-bold">
                                        <span>Kupon İndirimi:</span>
                                        <span>-₺{{ number_format($discount, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="pt-3 border-t border-slate-100 flex justify-between items-baseline">
                                    <span class="text-sm font-black text-slate-900">Toplam Ödenecek:</span>
                                    <span class="text-2xl font-black text-rose-600">
                                        ₺<span x-text="Number(currentTotal).toLocaleString('tr-TR')">{{ number_format($total, 0, ',', '.') }}</span>
                                    </span>
                                </div>
                                <div x-show="deliveryType === 'express'" x-cloak class="pt-2">
                                    <div class="p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-[11px] font-bold flex items-center gap-2">
                                        <i class="fas fa-bolt text-rose-600"></i>
                                        <span>Öncelikli Hazırlık &amp; VIP Hızlı Kurye</span>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" 
                                    class="w-full py-4 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl font-black text-sm shadow-xl shadow-rose-600/30 transition-all transform hover:-translate-y-0.5 active:scale-98 flex items-center justify-center gap-2">
                                <i class="fas fa-lock text-xs"></i>
                                <span>Siparişi Onayla &amp; Tamamla</span>
                            </button>

                            <div class="text-[11px] text-slate-400 text-center space-y-1 pt-1">
                                <div class="flex items-center justify-center gap-1.5 text-emerald-600 font-bold">
                                    <i class="fas fa-shield-alt"></i>
                                    <span>256-Bit SSL Şifreli Güvenli Ödeme</span>
                                </div>
                                <div>Tüm verileriniz kanuni güvence altındadır.</div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>
</x-frontend-layout>
