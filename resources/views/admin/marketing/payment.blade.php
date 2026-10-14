<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <i class="fas fa-credit-card text-emerald-600"></i> PayTR &amp; Online Kredi Kartı Ödeme Entegrasyonu
                </h2>
                <p class="text-xs text-gray-500 mt-1">PayTR Hosted Payment (Direkt Yönlendirme) ile %100 güvenli, BDDK ve PCI-DSS uyumlu online ödeme</p>
            </div>
            <div>
                @if(($settings['paytr_active'] ?? '0') == '1')
                    <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-800 text-xs font-black px-3.5 py-1.5 rounded-full border border-emerald-200 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> PayTR Aktif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-600 text-xs font-black px-3.5 py-1.5 rounded-full border border-gray-200">
                        <i class="fas fa-pause"></i> PayTR Devre Dışı
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-2xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                        <p class="text-xs font-bold text-emerald-900">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- 1. GÜVENLİK VE ÇALIŞMA PRENSİBİ BİLGİ KUTUSU -->
            <div class="bg-gradient-to-r from-emerald-900 via-teal-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-md relative overflow-hidden">
                <div class="relative z-10 max-w-3xl space-y-2">
                    <span class="bg-emerald-500/20 text-emerald-300 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider border border-emerald-400/30 flex items-center gap-1.5 w-fit">
                        <i class="fas fa-shield-alt"></i> SIFIR GÜVENLİK RİSKİ (HOSTED PAYMENT)
                    </span>
                    <h3 class="text-lg sm:text-xl font-black">Kart Bilgileri Doğrudan PayTR Resmi Sitesinde Girilir</h3>
                    <p class="text-xs text-emerald-100/80 leading-relaxed">
                        Müşteri sepetinde "Kredi Kartı" seçtiğinde otomatik olarak PayTR'ın 256-bit SSL korumalı resmi sayfasına yönlendirilir. 3D Secure SMS doğrulamasını PayTR'da tamamlar ve ödeme bitince başarıyla sitemize döner. Sunucunuza tek bir kart bilgisi bile değmez.
                    </p>
                </div>
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            <!-- 2. PAYTR BİLDİRİM URL (WEBHOOK / CALLBACK) KUTUSU -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm space-y-4"
                 x-data="{ copiedCallback: false, callbackUrl: '{{ url('/paytr/callback') }}' }">
                
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-base shadow-sm">
                        <i class="fas fa-link"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">PayTR Bildirim URL'si (Callback / Webhook)</h3>
                        <p class="text-xs text-gray-400">PayTR Mağaza Panelinize girmeniz gereken zorunlu bildirim linki</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Sitenizin PayTR Bildirim URL'si:</label>
                    <div class="flex gap-2">
                        <input type="text" readonly :value="callbackUrl"
                               class="shadow-sm border border-gray-300 rounded-2xl flex-1 py-2.5 px-3.5 text-xs font-mono font-bold bg-gray-50 text-gray-900 select-all">
                        <button type="button" @click="navigator.clipboard.writeText(callbackUrl); copiedCallback = true; setTimeout(() => copiedCallback = false, 2500);"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-black text-xs px-5 py-2.5 rounded-2xl shadow-sm transition-all flex items-center gap-1.5 flex-shrink-0">
                            <i class="fas fa-copy" x-show="!copiedCallback"></i>
                            <i class="fas fa-check" x-show="copiedCallback"></i>
                            <span x-text="copiedCallback ? 'Kopyalandı!' : 'Linki Kopyala'"></span>
                        </button>
                    </div>
                </div>

                <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100 text-xs text-blue-900 space-y-1">
                    <span class="font-black block flex items-center gap-1.5"><i class="fas fa-info-circle text-blue-600"></i> PayTR Paneline Nasıl Tanımlanır?</span>
                    <p class="text-[11px] leading-relaxed text-blue-800">
                        <a href="https://bilgi.paytr.com" target="_blank" class="underline font-bold">bilgi.paytr.com</a> adresinden PayTR Mağaza Panelinize girin &gt; <strong>Ayarlar &gt; Bildirim URL Ayarları (Webhook)</strong> bölümüne yukarıdaki linki yapıştırıp kaydedin.
                    </p>
                </div>

            </div>

            <!-- 3. PAYTR ENTEGRASYON FORMU -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm">
                
                <form action="{{ route('admin.marketing.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="border-b border-gray-100 pb-4">
                        <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                            <i class="fas fa-key text-emerald-600"></i> PayTR API &amp; Mağaza Bilgileri
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">PayTR paneli &gt; Bilgi menüsünde yer alan API anahtarlarınızı buraya girin.</p>
                    </div>

                    <!-- Aktiflik & Test Modu Switchleri -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-200/70">
                        
                        <label class="flex items-center justify-between cursor-pointer p-2">
                            <div>
                                <span class="text-xs font-black text-gray-900 block">Kredi Kartı ile Ödemeyi Aktifleştir</span>
                                <span class="text-[11px] text-gray-500">Sepet ekranında PayTR seçeneği müşteriye gösterilir</span>
                            </div>
                            <input type="hidden" name="paytr_active" value="0">
                            <input type="checkbox" name="paytr_active" value="1" 
                                   class="w-5 h-5 text-emerald-600 rounded-lg border-gray-300 focus:ring-0" 
                                   {{ ($settings['paytr_active'] ?? '0') == '1' ? 'checked' : '' }}>
                        </label>

                        <label class="flex items-center justify-between cursor-pointer p-2 sm:border-l sm:border-gray-200 sm:pl-6">
                            <div>
                                <span class="text-xs font-black text-amber-900 block">Test Modu (Geliştirici Modu)</span>
                                <span class="text-[11px] text-gray-500">Açıkken sahte test kartlarıyla deneme yapılabilir</span>
                            </div>
                            <input type="hidden" name="paytr_test_mode" value="0">
                            <input type="checkbox" name="paytr_test_mode" value="1" 
                                   class="w-5 h-5 text-amber-600 rounded-lg border-gray-300 focus:ring-0" 
                                   {{ ($settings['paytr_test_mode'] ?? '1') == '1' ? 'checked' : '' }}>
                        </label>

                    </div>

                    <!-- API Anahtarları -->
                    <div class="space-y-4">
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Mağaza Numarası (Merchant ID) <span class="text-red-500">*</span></label>
                            <input type="text" name="paytr_merchant_id" value="{{ $settings['paytr_merchant_id'] ?? '' }}" 
                                   placeholder="Örn: 123456" 
                                   class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-xs font-mono font-bold">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Mağaza Parolası (Merchant Key) <span class="text-red-500">*</span></label>
                                <input type="password" name="paytr_merchant_key" value="{{ $settings['paytr_merchant_key'] ?? '' }}" 
                                       placeholder="PayTR Mağaza Parolası" 
                                       class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-xs font-mono font-bold">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Mağaza Gizli Anahtarı (Merchant Salt) <span class="text-red-500">*</span></label>
                                <input type="password" name="paytr_merchant_salt" value="{{ $settings['paytr_merchant_salt'] ?? '' }}" 
                                       placeholder="PayTR Mağaza Gizli Anahtarı" 
                                       class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-xs font-mono font-bold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Maksimum İzin Verilen Taksit Sayısı</label>
                            <select name="paytr_max_installment" class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-xs font-bold bg-white">
                                <option value="0" {{ ($settings['paytr_max_installment'] ?? '12') == '0' ? 'selected' : '' }}>Sadece Tek Çekim (Taksitsiz)</option>
                                <option value="3" {{ ($settings['paytr_max_installment'] ?? '12') == '3' ? 'selected' : '' }}>Maksimum 3 Taksit</option>
                                <option value="6" {{ ($settings['paytr_max_installment'] ?? '12') == '6' ? 'selected' : '' }}>Maksimum 6 Taksit</option>
                                <option value="9" {{ ($settings['paytr_max_installment'] ?? '12') == '9' ? 'selected' : '' }}>Maksimum 9 Taksit</option>
                                <option value="12" {{ ($settings['paytr_max_installment'] ?? '12') == '12' ? 'selected' : '' }}>Maksimum 12 Taksit (Tüm Taksitler Açık)</option>
                            </select>
                        </div>

                    </div>

                    <!-- Kaydet Butonu -->
                    <div class="flex justify-end pt-4 border-t border-gray-100">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs py-3 px-8 rounded-2xl shadow-lg transition-all transform active:scale-95 flex items-center gap-2">
                            <i class="fas fa-save"></i> PayTR Ayarlarını Kaydet
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</x-app-layout>
