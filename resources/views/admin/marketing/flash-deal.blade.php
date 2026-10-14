<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <i class="fas fa-bolt text-amber-500"></i> Günün Fırsatı &amp; Geri Sayım Kampanyası
                </h2>
                <p class="text-xs text-gray-500 mt-1">Sitede saniye saniye geri sayan ve aciliyet yaratan kampanya bandını yönetin</p>
            </div>
            <a href="{{ route('home') }}" target="_blank" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold px-3.5 py-2 rounded-xl transition-colors flex items-center gap-1.5">
                <i class="fas fa-external-link-alt"></i> Sitede Gör
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 text-emerald-800 rounded-r-2xl shadow-sm text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.marketing.store') }}">
                @csrf

                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm" x-data="{ flashActive: {{ ($settings['flash_deal_active'] ?? '1') == '1' ? 'true' : 'false' }} }">
                    
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-8 pb-6 border-b border-gray-100">
                        <div>
                            <h3 class="text-lg font-black text-gray-900 flex items-center gap-2">
                                <i class="fas fa-toggle-on text-amber-500 text-xl"></i> Kampanya Durumu
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">Kampanya bandını ana sayfada ve ürün sayfalarında anında açıp kapatabilirsiniz.</p>
                        </div>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="hidden" name="flash_deal_active" :value="flashActive ? '1' : '0'">
                            <input type="checkbox" class="sr-only peer" :checked="flashActive" @change="flashActive = !flashActive">
                            <div class="relative w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[3px] after:start-[3px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-7 after:transition-all peer-checked:bg-amber-500"></div>
                            <span class="ms-3 text-xs font-black text-gray-900" x-text="flashActive ? 'Kampanya Aktif' : 'Kampanya Kapalı'"></span>
                        </label>
                    </div>

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2">Rozet / Etiket Metni</label>
                                <input type="text" name="flash_deal_badge"
                                       value="{{ $settings['flash_deal_badge'] ?? 'GÜNÜN FIRSATI' }}"
                                       class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-bold focus:ring-amber-500 focus:border-amber-500" placeholder="GÜNÜN FIRSATI">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-700 mb-2">Kampanya Başlığı / Duyurusu</label>
                                <input type="text" name="flash_deal_title"
                                       value="{{ $settings['flash_deal_title'] ?? 'İstanbul İçi Dilediğiniz Zaman Randevulu Ücretsiz Montaj & Yedek Filtre Kampanyası' }}"
                                       class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-bold focus:ring-amber-500 focus:border-amber-500" placeholder="Kampanya başlığını yazınız">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2">Geri Sayım Bitiş Tarihi &amp; Saati</label>
                                <input type="datetime-local" name="flash_deal_end_time"
                                       value="{{ $settings['flash_deal_end_time'] ?? now()->addDays(2)->format('Y-m-d\TH:i') }}"
                                       class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-bold focus:ring-amber-500 focus:border-amber-500">
                                <p class="text-[11px] text-gray-400 mt-1.5">Sayaç bu tarihe ulaştığında otomatik olarak durur.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2">Öne Çıkarılacak Kampanyalı Ürün (Opsiyonel)</label>
                                <select name="flash_deal_product_id" class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-bold focus:ring-amber-500 focus:border-amber-500 bg-white">
                                    <option value="">Tüm Ürünler / Genel Kampanya</option>
                                    @foreach($products ?? [] as $pr)
                                        <option value="{{ $pr->id }}" {{ ($settings['flash_deal_product_id'] ?? '') == $pr->id ? 'selected' : '' }}>
                                            {{ $pr->name }} ({{ number_format($pr->price, 0, ',', '.') }} TL)
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-gray-400 mt-1.5">Ziyaretçiler butona tıkladığında doğrudan bu ürüne veya katalog sayfasına yönlendirilir.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-black text-xs px-6 py-3 rounded-2xl shadow-lg transition-all transform active:scale-95 flex items-center gap-2">
                            <i class="fas fa-save"></i> Fırsat Ayarlarını Kaydet
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</x-app-layout>
