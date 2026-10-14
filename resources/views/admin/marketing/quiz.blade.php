<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <i class="fas fa-magic text-cyan-600"></i> Akıllı Cihaz Bulucu Sihirbazı (Quiz) Ayarları
                </h2>
                <p class="text-xs text-gray-500 mt-1">Ziyaretçilerin test sonucunda eşleşeceği modelleri doğrudan ürünleriniz arasından seçin</p>
            </div>
            <a href="{{ route('home') }}#quiz-wizard" target="_blank" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold px-3.5 py-2 rounded-xl transition-colors flex items-center gap-1.5">
                <i class="fas fa-external-link-alt"></i> Sitede Test Et
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

            <form method="POST" action="{{ route('admin.marketing.store') }}">
                @csrf

                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm space-y-8">
                    
                    <div class="border-b border-gray-100 pb-4">
                        <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                            <i class="fas fa-bullseye text-blue-600"></i> 3 Müşteri Profili İçin Önerilecek Ürün Eşleştirmeleri
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Ziyaretçi aile büyüklüğü, su basıncı ve mineral tercihine göre test tamamladığında, seçtiğiniz ürünün güncel fiyatı, görseli ve detay linki otomatik olarak ekrana gelir.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <!-- 1. Çekirdek Aile & Pompasız / Kompakt -->
                        <div class="bg-blue-50/40 p-5 rounded-3xl border border-blue-100 space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-black text-sm shadow-md shadow-blue-500/20">1</div>
                                <div>
                                    <h4 class="text-xs font-black text-gray-900">Çekirdek Aile / Kompakt</h4>
                                    <span class="text-[10px] text-gray-500">1-3 Kişi &amp; Pompasız Tercih</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 mb-1.5">Önerilecek Ürün:</label>
                                <select name="quiz_product_small_id" class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3 text-gray-900 text-xs font-bold focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="">Varsayılan Model</option>
                                    @foreach($products ?? [] as $pr)
                                        <option value="{{ $pr->id }}" {{ ($settings['quiz_product_small_id'] ?? '') == $pr->id ? 'selected' : '' }}>
                                            {{ $pr->name }} ({{ number_format($pr->price, 0, ',', '.') }} TL)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 2. Standart Aile & Pompalı / Alkali (Önerilen) -->
                        <div class="bg-emerald-50/40 p-5 rounded-3xl border border-emerald-100 space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-black text-sm shadow-md shadow-emerald-500/20">2</div>
                                <div>
                                    <h4 class="text-xs font-black text-gray-900">Standart Aile / Pompalı Alkali</h4>
                                    <span class="text-[10px] text-emerald-600 font-extrabold">4-6 Kişi (En Çok Satan)</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 mb-1.5">Önerilecek Ürün:</label>
                                <select name="quiz_product_medium_id" class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3 text-gray-900 text-xs font-bold focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                                    <option value="">Varsayılan Model</option>
                                    @foreach($products ?? [] as $pr)
                                        <option value="{{ $pr->id }}" {{ ($settings['quiz_product_medium_id'] ?? '') == $pr->id ? 'selected' : '' }}>
                                            {{ $pr->name }} ({{ number_format($pr->price, 0, ',', '.') }} TL)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 3. İş Yeri / Yüksek Kapasite -->
                        <div class="bg-purple-50/40 p-5 rounded-3xl border border-purple-100 space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-purple-600 text-white flex items-center justify-center font-black text-sm shadow-md shadow-purple-500/20">3</div>
                                <div>
                                    <h4 class="text-xs font-black text-gray-900">İş Yeri / Yüksek Kapasite</h4>
                                    <span class="text-[10px] text-purple-600 font-extrabold">Ofis &amp; Kalabalık Tüketim</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 mb-1.5">Önerilecek Ürün:</label>
                                <select name="quiz_product_large_id" class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3 text-gray-900 text-xs font-bold focus:ring-purple-500 focus:border-purple-500 bg-white">
                                    <option value="">Varsayılan Model</option>
                                    @foreach($products ?? [] as $pr)
                                        <option value="{{ $pr->id }}" {{ ($settings['quiz_product_large_id'] ?? '') == $pr->id ? 'selected' : '' }}>
                                            {{ $pr->name }} ({{ number_format($pr->price, 0, ',', '.') }} TL)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black text-xs px-6 py-3 rounded-2xl shadow-lg transition-all transform active:scale-95 flex items-center gap-2">
                            <i class="fas fa-save"></i> Quiz Eşleştirmelerini Kaydet
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</x-app-layout>
