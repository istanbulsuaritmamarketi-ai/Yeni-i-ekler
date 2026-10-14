<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                Banner Düzenle: <span class="text-rose-600">{{ $banner->title ?: $banner->position }}</span>
            </h2>
            <a href="{{ route('admin.banners.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">
                &larr; Banner Listesine Dön
            </a>
        </div>
    </x-slot>

    @php
        $imgUrl = null;
        if ($banner->image_path) {
            $p = $banner->image_path;
            if (str_starts_with($p, 'http://') || str_starts_with($p, 'https://')) {
                $imgUrl = $p;
            } elseif (file_exists(public_path('storage/' . $p))) {
                $imgUrl = asset('storage/' . $p);
            } elseif (file_exists(public_path('images/' . $p))) {
                $imgUrl = asset('images/' . $p);
            } elseif (file_exists(public_path($p))) {
                $imgUrl = asset($p);
            } else {
                $imgUrl = asset('images/' . $p);
            }
        }
    @endphp

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-2xl border border-gray-200 p-6 sm:p-8">
                
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-bold">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Pozisyon Seçimi & Görsel Önizleme -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-slate-50/70 rounded-2xl border border-slate-200">
                        <div>
                            <label class="block text-sm font-black text-gray-800 mb-1.5">Banner Pozisyonu *</label>
                            <select name="position" class="w-full border-gray-300 rounded-xl shadow-xs text-sm focus:border-rose-500 focus:ring-rose-500 font-semibold" required>
                                <option value="grid_1" {{ $banner->position === 'grid_1' ? 'selected' : '' }}>1. Kutu (1. Sütun Üst) — Kare</option>
                                <option value="grid_2" {{ $banner->position === 'grid_2' ? 'selected' : '' }}>2. Kutu (1. Sütun Alt) — Kare</option>
                                <option value="grid_3" {{ $banner->position === 'grid_3' ? 'selected' : '' }}>3. Kutu (2. Sütun Üst) — Kare</option>
                                <option value="grid_4" {{ $banner->position === 'grid_4' ? 'selected' : '' }}>4. Kutu (2. Sütun Alt) — Kare</option>
                                <option value="grid_5" {{ $banner->position === 'grid_5' ? 'selected' : '' }}>5. Kutu (3. Sütun Üst) — Kare</option>
                                <option value="grid_6" {{ $banner->position === 'grid_6' ? 'selected' : '' }}>6. Kutu (3. Sütun Alt) — Kare</option>
                                <option value="grid_7" {{ $banner->position === 'grid_7' ? 'selected' : '' }}>7. Büyük Dikey Banner (4. Sütun Boydan Boya)</option>
                                <option value="slider" {{ $banner->position === 'slider' ? 'selected' : '' }}>Genel / Diğer</option>
                            </select>
                            <p class="text-[11px] text-gray-500 mt-1">Ana sayfadaki 4 sütunlu şemadaki yerleşimi belirler.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-black text-gray-800 mb-1.5">Görsel Seç / Değiştir</label>
                            <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 border border-gray-300 rounded-xl p-1 bg-white">
                            <p class="text-[11px] text-gray-500 mt-1">Saydam (PNG) veya yüksek kaliteli ürün fotoğrafı önerilir.</p>
                        </div>

                        @if($imgUrl)
                            <div class="md:col-span-2 flex items-center gap-4 pt-2 border-t border-slate-200">
                                <span class="text-xs font-bold text-gray-600">Mevcut Görsel:</span>
                                <div class="h-20 w-24 rounded-xl bg-white border border-gray-200 p-1 flex items-center justify-center">
                                    <img src="{{ $imgUrl }}" class="max-h-full max-w-full object-contain">
                                </div>
                                <span class="text-xs text-gray-400 font-mono">{{ $banner->image_path }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Metinler -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Ana Başlık</label>
                            <input type="text" name="title" value="{{ old('title', $banner->title) }}" placeholder="Örn: Gül Buketleri veya Orkideler" class="w-full border-gray-300 rounded-xl shadow-xs text-sm focus:border-rose-500 focus:ring-rose-500 font-medium">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Alt Başlık</label>
                            <input type="text" name="subtitle" value="{{ old('subtitle', $banner->subtitle) }}" placeholder="Örn: Taptaze Kırmızı Güller veya Çift Dallı Kraliyet Serisi" class="w-full border-gray-300 rounded-xl shadow-xs text-sm focus:border-rose-500 focus:ring-rose-500 font-medium">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Rozet Metni</label>
                            <input type="text" name="badge" value="{{ old('badge', $banner->badge) }}" placeholder="Örn: Çok Satan veya Aynı Gün Teslim" class="w-full border-gray-300 rounded-xl shadow-xs text-sm focus:border-rose-500 focus:ring-rose-500 font-medium">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Buton Metni</label>
                            <input type="text" name="button_text" value="{{ old('button_text', $banner->button_text ?? 'Hemen İncele') }}" placeholder="Örn: Hemen İncele veya Keşfet" class="w-full border-gray-300 rounded-xl shadow-xs text-sm focus:border-rose-500 focus:ring-rose-500 font-medium">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Sıra No</label>
                            <input type="number" name="order" value="{{ old('order', $banner->order ?? 1) }}" class="w-full border-gray-300 rounded-xl shadow-xs text-sm focus:border-rose-500 focus:ring-rose-500 font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Yönlendirme Linki (URL)</label>
                        <input type="text" name="link_url" value="{{ old('link_url', $banner->link_url) }}" placeholder="/kategori/gul-buketleri veya /urunler" class="w-full border-gray-300 rounded-xl shadow-xs text-sm focus:border-rose-500 focus:ring-rose-500 font-mono">
                        <p class="text-[11px] text-gray-500 mt-1">Tıklandığında açılacak sayfa adresi (Örn: /kategori/gul-buketleri veya https://...).</p>
                    </div>

                    <div class="pt-2">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ $banner->is_active ? 'checked' : '' }} class="rounded-md border-gray-300 text-rose-600 focus:ring-rose-500 h-4 w-4">
                            <span class="ml-2.5 text-sm text-gray-800 font-bold">Aktif (Sitede Gösterilsin)</span>
                        </label>
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t border-gray-200">
                        <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-sm transition-colors text-sm">
                            Değişiklikleri Kaydet
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
