<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                Yeni Banner / Kampanya Ekle
            </h2>
            <a href="{{ route('admin.banners.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">
                &larr; Banner Listesine Dön
            </a>
        </div>
    </x-slot>

    @php
        $selectedPos = request('position', 'grid_1');
    @endphp

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-2xl border border-gray-200 p-6 sm:p-8">

                <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Pozisyon Seçimi & Görsel Yükleme -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-slate-50/70 rounded-2xl border border-slate-200">
                        <div>
                            <label class="block text-sm font-black text-gray-800 mb-1.5">Banner Pozisyonu *</label>
                            <select name="position" class="w-full border-gray-300 rounded-xl shadow-xs text-sm focus:border-rose-500 focus:ring-rose-500 font-semibold" required>
                                <option value="grid_1" {{ $selectedPos === 'grid_1' ? 'selected' : '' }}>1. Kutu (1. Sütun Üst) — Kare</option>
                                <option value="grid_2" {{ $selectedPos === 'grid_2' ? 'selected' : '' }}>2. Kutu (1. Sütun Alt) — Kare</option>
                                <option value="grid_3" {{ $selectedPos === 'grid_3' ? 'selected' : '' }}>3. Kutu (2. Sütun Üst) — Kare</option>
                                <option value="grid_4" {{ $selectedPos === 'grid_4' ? 'selected' : '' }}>4. Kutu (2. Sütun Alt) — Kare</option>
                                <option value="grid_5" {{ $selectedPos === 'grid_5' ? 'selected' : '' }}>5. Kutu (3. Sütun Üst) — Kare</option>
                                <option value="grid_6" {{ $selectedPos === 'grid_6' ? 'selected' : '' }}>6. Kutu (3. Sütun Alt) — Kare</option>
                                <option value="grid_7" {{ $selectedPos === 'grid_7' ? 'selected' : '' }}>7. Büyük Dikey Banner (4. Sütun Boydan Boya)</option>
                                <option value="slider" {{ $selectedPos === 'slider' ? 'selected' : '' }}>Genel / Diğer</option>
                            </select>
                            <p class="text-[11px] text-gray-500 mt-1">Ana sayfadaki 4 sütunlu şemadaki yerleşimi belirler.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-black text-gray-800 mb-1.5">Banner Görseli</label>
                            <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 border border-gray-300 rounded-xl p-1 bg-white">
                            <p class="text-[11px] text-gray-500 mt-1">Saydam (PNG) veya yüksek kaliteli ürün fotoğrafı önerilir.</p>
                        </div>
                    </div>

                    <!-- Metinler -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Ana Başlık</label>
                            <input type="text" name="title" value="{{ old('title') }}" placeholder="Örn: Gül Buketleri veya Orkideler" class="w-full border-gray-300 rounded-xl shadow-xs text-sm focus:border-rose-500 focus:ring-rose-500 font-medium">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Alt Başlık</label>
                            <input type="text" name="subtitle" value="{{ old('subtitle') }}" placeholder="Örn: Taptaze Kırmızı Güller veya Çift Dallı Kraliyet Serisi" class="w-full border-gray-300 rounded-xl shadow-xs text-sm focus:border-rose-500 focus:ring-rose-500 font-medium">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Rozet Metni</label>
                            <input type="text" name="badge" value="{{ old('badge') }}" placeholder="Örn: Çok Satan veya Aynı Gün Teslim" class="w-full border-gray-300 rounded-xl shadow-xs text-sm focus:border-rose-500 focus:ring-rose-500 font-medium">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Buton Metni</label>
                            <input type="text" name="button_text" value="{{ old('button_text', 'Hemen İncele') }}" placeholder="Örn: Hemen İncele veya Keşfet" class="w-full border-gray-300 rounded-xl shadow-xs text-sm focus:border-rose-500 focus:ring-rose-500 font-medium">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Sıra No</label>
                            <input type="number" name="order" value="{{ old('order', 1) }}" class="w-full border-gray-300 rounded-xl shadow-xs text-sm focus:border-rose-500 focus:ring-rose-500 font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Yönlendirme Linki (URL)</label>
                        <input type="text" name="link_url" value="{{ old('link_url') }}" placeholder="/kategori/gul-buketleri veya /urunler" class="w-full border-gray-300 rounded-xl shadow-xs text-sm focus:border-rose-500 focus:ring-rose-500 font-mono">
                        <p class="text-[11px] text-gray-500 mt-1">Tıklandığında açılacak sayfa adresi (Örn: /kategori/gul-buketleri veya https://...).</p>
                    </div>

                    <div class="pt-2">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded-md border-gray-300 text-rose-600 focus:ring-rose-500 h-4 w-4">
                            <span class="ml-2.5 text-sm text-gray-800 font-bold">Aktif (Sitede Gösterilsin)</span>
                        </label>
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t border-gray-200">
                        <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-sm transition-colors text-sm">
                            Bannerı Kaydet
                        </button>
                        <a href="{{ route('admin.banners.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-semibold">
                            Vazgeç
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
