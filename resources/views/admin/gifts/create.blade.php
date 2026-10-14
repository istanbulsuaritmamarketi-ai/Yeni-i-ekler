<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <i class="fas fa-plus-circle text-purple-600"></i> Yeni Hediye Ekle
                </h2>
                <p class="text-xs text-gray-500 mt-1">Siparişlerde müşterilerinize sunulacak yeni bir ücretsiz promosyon ürünü tanımlayın</p>
            </div>
            <a href="{{ route('admin.gifts.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold px-3.5 py-2.5 rounded-xl transition-colors flex items-center gap-1.5">
                <i class="fas fa-arrow-left"></i> Listeye Dön
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm">
                
                <form action="{{ route('admin.gifts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">
                            Hediye Adı <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="Örn: Butik Çikolata veya Peluş Ayıcık"
                               class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm font-semibold py-2.5 px-3.5">
                        @error('name') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                Rozet / Etiket (Opsiyonel)
                            </label>
                            <input type="text" name="badge" value="{{ old('badge') }}"
                                   placeholder="Örn: En Çok Tercih Edilen, Çocuklara Özel"
                                   class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-xs py-2.5 px-3.5">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                Tema Rengi
                            </label>
                            <select name="color" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-xs py-2.5 px-3.5">
                                <option value="purple" {{ old('color') == 'purple' ? 'selected' : '' }}>Mor (Purple)</option>
                                <option value="blue" {{ old('color') == 'blue' ? 'selected' : '' }}>Mavi (Blue)</option>
                                <option value="emerald" {{ old('color') == 'emerald' ? 'selected' : '' }}>Yeşil (Emerald)</option>
                                <option value="amber" {{ old('color') == 'amber' ? 'selected' : '' }}>Turuncu/Amber</option>
                                <option value="pink" {{ old('color') == 'pink' ? 'selected' : '' }}>Pembe (Pink)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">
                            Kısa Açıklama (Opsiyonel)
                        </label>
                        <textarea name="description" rows="2"
                                  placeholder="Örn: Siparişinizle birlikte 0 TL olarak özel kutusunda veya saksısında teslim edilir."
                                  class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-xs py-2 px-3.5">{{ old('description') }}</textarea>
                    </div>

                    <!-- Dış Bağlantı & Piyasa Değeri (ÇiçekSepeti, Amcanızın Çiçekçisi, Trendyol vb.) -->
                    <div class="p-4 bg-purple-50/50 rounded-2xl border border-purple-100 space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-purple-950">
                            <i class="fas fa-external-link-alt text-purple-600"></i> Dış Ürün Linki &amp; Piyasa Değeri (Opsiyonel)
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">
                                    Dış Bağlantı / Ürün Sayfası Linki
                                </label>
                                <input type="url" name="external_url" value="{{ old('external_url') }}"
                                       placeholder="https://cicekci.com/orkide veya https://ciceksepeti.com/..."
                                       class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-xs py-2 px-3.5 bg-white">
                                <p class="text-[10px] text-gray-500 mt-1">Müşteriye "Hediyeyi / Canlı Çiçeği İncele" butonu çıkarır.</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">
                                    Piyasa / Liste Değeri (TL)
                                </label>
                                <input type="number" step="0.01" name="market_price" value="{{ old('market_price') }}"
                                       placeholder="Örn: 750 (Normalde 750 TL yerine 0 TL Hediye)"
                                       class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-xs py-2 px-3.5 bg-white">
                                <p class="text-[10px] text-gray-500 mt-1">Müşteride yüksek hediye değeri ve güven algısı oluşturur.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                Hediye Fotoğrafı (Önerilen: 600x600 px PNG/WebP)
                            </label>
                            <input type="file" name="image" accept="image/*"
                                   class="w-full text-xs text-gray-700 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 border border-gray-300 rounded-2xl p-1.5">
                            @error('image') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                FontAwesome İkon Kodu (Fotoğraf yoksa gösterilir)
                            </label>
                            <input type="text" name="icon" value="{{ old('icon', 'fas fa-gift') }}"
                                   placeholder="Örn: fas fa-robot, fas fa-flask, fas fa-gift"
                                   class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-xs py-2.5 px-3.5">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100 items-center">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                Sıralama Numarası
                            </label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                                   class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-xs py-2.5 px-3.5">
                        </div>

                        <div class="flex items-center pt-5">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="rounded border-gray-300 text-purple-600 shadow-sm focus:ring-purple-500 w-4 h-4">
                                <span class="ms-2.5 text-xs font-bold text-gray-800">Yayında / Aktif Olarak Göster</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.gifts.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-colors">
                            İptal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold shadow-sm transition-colors flex items-center gap-1.5">
                            <i class="fas fa-save"></i> Hediyeyi Kaydet
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</x-app-layout>
