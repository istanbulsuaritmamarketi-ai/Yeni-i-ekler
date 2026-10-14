<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <i class="fas fa-cubes text-purple-600"></i> Hediye &amp; Oyuncak Promosyonu Yönetimi
                </h2>
                <p class="text-xs text-gray-500 mt-1">Ürün sayfalarında ve sepette müşterilere sunulacak ücretsiz hediye görselleri ve başlıklarını yönetin</p>
            </div>
            <a href="{{ route('cart.index') }}" target="_blank" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold px-3.5 py-2 rounded-xl transition-colors flex items-center gap-1.5">
                <i class="fas fa-shopping-cart"></i> Sepette Gör
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 text-emerald-800 rounded-r-2xl shadow-sm text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Dinamik Hediye Yönetimi Hızlı Geçiş Kartı -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-700 text-white rounded-3xl p-6 shadow-md flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <span class="bg-white/20 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider mb-2 inline-block">YENİ ÖZELLİK</span>
                    <h3 class="font-black text-lg text-white">Dinamik Hediye &amp; Oyuncak Yönetimi</h3>
                    <p class="text-xs text-purple-100 mt-1 max-w-xl">Artık dilediğiniz kadar yeni hediye seçeneği ekleyebilir, silebilir ve her ürüne özel hediye paketleri atayabilirsiniz.</p>
                </div>
                <a href="{{ route('admin.gifts.index') }}" class="bg-white text-purple-900 hover:bg-purple-50 font-black text-xs px-5 py-3 rounded-2xl shadow-lg transition-transform active:scale-95 flex items-center gap-2 whitespace-nowrap">
                    <i class="fas fa-cubes text-purple-600"></i> Hediye Listesine Git
                </a>
            </div>

            <form method="POST" action="{{ route('admin.marketing.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm space-y-8"
                     x-data="{ promoActive: {{ ($settings['gift_promo_active'] ?? '1') == '1' ? 'true' : 'false' }} }">
                    
                    <!-- Aktif / Pasif Toggle -->
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 pb-6">
                        <div>
                            <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                                <i class="fas fa-gift text-purple-600 text-lg"></i> Siparişte Ücretsiz Hediye Seçim Modülü
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">Müşteriler ürün sayfasında hediyeleri inceler ve sepet/ödeme adımında 0 TL olarak istedikleri hediyeyi seçerler.</p>
                        </div>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="hidden" name="gift_promo_active" :value="promoActive ? '1' : '0'">
                            <input type="checkbox" class="sr-only peer" :checked="promoActive" @change="promoActive = !promoActive">
                            <div class="relative w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[3px] after:start-[3px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-7 after:transition-all peer-checked:bg-purple-600"></div>
                            <span class="ms-3 text-xs font-black text-gray-900" x-text="promoActive ? 'Kampanya Aktif' : 'Kampanya Kapalı'"></span>
                        </label>
                    </div>

                    <!-- Ana Başlık ve Açıklama -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Kutu Başlığı</label>
                            <input type="text" name="gift_promo_title"
                                   value="{{ $settings['gift_promo_title'] ?? 'Bu Siparişinize Özel 1 Adet Ücretsiz Sürpriz Hediyenizi Seçin!' }}"
                                   class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-bold focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Açıklama / Not</label>
                            <input type="text" name="gift_promo_subtitle"
                                   value="{{ $settings['gift_promo_subtitle'] ?? 'Tüm su arıtma ve filtre siparişlerinizde ücretsiz paketlenip montaj ekibimiz tarafından teslim edilir.' }}"
                                   class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-medium focus:ring-purple-500 focus:border-purple-500">
                        </div>
                    </div>

                    <!-- 4 Hediye Seçeneği Kartları -->
                    <div class="space-y-4 pt-4 border-t border-gray-100">
                        <h4 class="text-sm font-black text-gray-900 flex items-center gap-2">
                            <i class="fas fa-list text-purple-600"></i> 4 Adet Hediye Seçeneği (Fotoğraf &amp; Detaylar)
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            
                            <!-- 1. Hediye: Çocuk Oyuncağı -->
                            <div class="p-5 bg-purple-50/40 rounded-3xl border border-purple-100 space-y-3">
                                <div class="flex items-center justify-between pb-2 border-b border-purple-100">
                                    <span class="text-xs font-black text-purple-900 flex items-center gap-2">
                                        <i class="fas fa-robot text-purple-600 text-sm"></i> 1. Hediye (Oyuncak)
                                    </span>
                                    <input type="text" name="gift_1_badge" value="{{ $settings['gift_1_badge'] ?? 'En Çok Tercih Edilen' }}" class="text-[10px] font-bold text-purple-700 bg-white border border-purple-200 rounded-lg px-2 py-0.5" placeholder="Rozet">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Hediye Adı:</label>
                                    <input type="text" name="gift_1_title" value="{{ $settings['gift_1_title'] ?? 'Işıklı & Dans Eden Sevimli Peluş Figür' }}" class="shadow-sm border border-gray-300 rounded-xl w-full py-2 px-3 text-xs font-bold bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Kısa Açıklama:</label>
                                    <input type="text" name="gift_1_desc" value="{{ $settings['gift_1_desc'] ?? 'Çocukların çok seveceği neşeli ışıklı figür oyuncak (0 TL Hediye)' }}" class="shadow-sm border border-gray-300 rounded-xl w-full py-1.5 px-3 text-xs text-gray-600 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Hediye Fotoğrafı (Opsiyonel):</label>
                                    @if(!empty($settings['gift_1_image']))
                                        <div class="mb-2 p-1.5 bg-white rounded-xl inline-flex items-center gap-2 border">
                                            <img src="{{ asset('storage/' . $settings['gift_1_image']) }}" class="w-10 h-10 object-contain rounded-lg">
                                            <span class="text-[10px] text-gray-500 font-bold">Yüklü Fotoğraf</span>
                                        </div>
                                    @endif
                                    <input type="file" name="gift_1_image" accept="image/*" class="block w-full text-[11px] text-gray-900 border border-gray-300 rounded-xl cursor-pointer bg-white p-1.5">
                                </div>
                            </div>

                            <!-- 2. Hediye: Çocuk Su Matarası -->
                            <div class="p-5 bg-blue-50/40 rounded-3xl border border-blue-100 space-y-3">
                                <div class="flex items-center justify-between pb-2 border-b border-blue-100">
                                    <span class="text-xs font-black text-blue-900 flex items-center gap-2">
                                        <i class="fas fa-flask text-blue-600 text-sm"></i> 2. Hediye (Su Matarası)
                                    </span>
                                    <input type="text" name="gift_2_badge" value="{{ $settings['gift_2_badge'] ?? 'Çocuklar İçin Özel' }}" class="text-[10px] font-bold text-blue-700 bg-white border border-blue-200 rounded-lg px-2 py-0.5" placeholder="Rozet">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Hediye Adı:</label>
                                    <input type="text" name="gift_2_title" value="{{ $settings['gift_2_title'] ?? 'Okul İçin BPA İçermeyen Çocuk Su Matarası' }}" class="shadow-sm border border-gray-300 rounded-xl w-full py-2 px-3 text-xs font-bold bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Kısa Açıklama:</label>
                                    <input type="text" name="gift_2_desc" value="{{ $settings['gift_2_desc'] ?? '500ml sağlıklı kilitli sızdırmaz çocuk matarası (0 TL Hediye)' }}" class="shadow-sm border border-gray-300 rounded-xl w-full py-1.5 px-3 text-xs text-gray-600 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Hediye Fotoğrafı (Opsiyonel):</label>
                                    @if(!empty($settings['gift_2_image']))
                                        <div class="mb-2 p-1.5 bg-white rounded-xl inline-flex items-center gap-2 border">
                                            <img src="{{ asset('storage/' . $settings['gift_2_image']) }}" class="w-10 h-10 object-contain rounded-lg">
                                            <span class="text-[10px] text-gray-500 font-bold">Yüklü Fotoğraf</span>
                                        </div>
                                    @endif
                                    <input type="file" name="gift_2_image" accept="image/*" class="block w-full text-[11px] text-gray-900 border border-gray-300 rounded-xl cursor-pointer bg-white p-1.5">
                                </div>
                            </div>

                            <!-- 3. Hediye: Çelik Termos Bardak -->
                            <div class="p-5 bg-amber-50/40 rounded-3xl border border-amber-100 space-y-3">
                                <div class="flex items-center justify-between pb-2 border-b border-amber-100">
                                    <span class="text-xs font-black text-amber-900 flex items-center gap-2">
                                        <i class="fas fa-mug-hot text-amber-600 text-sm"></i> 3. Hediye (Çelik Termos)
                                    </span>
                                    <input type="text" name="gift_3_badge" value="{{ $settings['gift_3_badge'] ?? 'Ev & Araç İçin' }}" class="text-[10px] font-bold text-amber-700 bg-white border border-amber-200 rounded-lg px-2 py-0.5" placeholder="Rozet">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Hediye Adı:</label>
                                    <input type="text" name="gift_3_title" value="{{ $settings['gift_3_title'] ?? 'Çift Katmanlı Paslanmaz Çelik Termos Bardak' }}" class="shadow-sm border border-gray-300 rounded-xl w-full py-2 px-3 text-xs font-bold bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Kısa Açıklama:</label>
                                    <input type="text" name="gift_3_desc" value="{{ $settings['gift_3_desc'] ?? 'Sıcak ve soğuğu 6 saat muhafaza eden şık termos (0 TL Hediye)' }}" class="shadow-sm border border-gray-300 rounded-xl w-full py-1.5 px-3 text-xs text-gray-600 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Hediye Fotoğrafı (Opsiyonel):</label>
                                    @if(!empty($settings['gift_3_image']))
                                        <div class="mb-2 p-1.5 bg-white rounded-xl inline-flex items-center gap-2 border">
                                            <img src="{{ asset('storage/' . $settings['gift_3_image']) }}" class="w-10 h-10 object-contain rounded-lg">
                                            <span class="text-[10px] text-gray-500 font-bold">Yüklü Fotoğraf</span>
                                        </div>
                                    @endif
                                    <input type="file" name="gift_3_image" accept="image/*" class="block w-full text-[11px] text-gray-900 border border-gray-300 rounded-xl cursor-pointer bg-white p-1.5">
                                </div>
                            </div>

                            <!-- 4. Hediye: Dijital TDS Su Test Kalemi -->
                            <div class="p-5 bg-emerald-50/40 rounded-3xl border border-emerald-100 space-y-3">
                                <div class="flex items-center justify-between pb-2 border-b border-emerald-100">
                                    <span class="text-xs font-black text-emerald-900 flex items-center gap-2">
                                        <i class="fas fa-vial text-emerald-600 text-sm"></i> 4. Hediye (TDS Test Cihazı)
                                    </span>
                                    <input type="text" name="gift_4_badge" value="{{ $settings['gift_4_badge'] ?? 'Su Kalitesi Ölçer' }}" class="text-[10px] font-bold text-emerald-700 bg-white border border-emerald-200 rounded-lg px-2 py-0.5" placeholder="Rozet">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Hediye Adı:</label>
                                    <input type="text" name="gift_4_title" value="{{ $settings['gift_4_title'] ?? 'Dijital TDS Su Saflık & Kalite Test Kalemi' }}" class="shadow-sm border border-gray-300 rounded-xl w-full py-2 px-3 text-xs font-bold bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Kısa Açıklama:</label>
                                    <input type="text" name="gift_4_desc" value="{{ $settings['gift_4_desc'] ?? 'Musluk suyunuzun saflığını ve arıtma kalitesini test edin (0 TL Hediye)' }}" class="shadow-sm border border-gray-300 rounded-xl w-full py-1.5 px-3 text-xs text-gray-600 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Hediye Fotoğrafı (Opsiyonel):</label>
                                    @if(!empty($settings['gift_4_image']))
                                        <div class="mb-2 p-1.5 bg-white rounded-xl inline-flex items-center gap-2 border">
                                            <img src="{{ asset('storage/' . $settings['gift_4_image']) }}" class="w-10 h-10 object-contain rounded-lg">
                                            <span class="text-[10px] text-gray-500 font-bold">Yüklü Fotoğraf</span>
                                        </div>
                                    @endif
                                    <input type="file" name="gift_4_image" accept="image/*" class="block w-full text-[11px] text-gray-900 border border-gray-300 rounded-xl cursor-pointer bg-white p-1.5">
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-black text-xs px-6 py-3 rounded-2xl shadow-lg transition-all transform active:scale-95 flex items-center gap-2">
                            <i class="fas fa-save"></i> Hediye Promosyonlarını Kaydet
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</x-app-layout>
