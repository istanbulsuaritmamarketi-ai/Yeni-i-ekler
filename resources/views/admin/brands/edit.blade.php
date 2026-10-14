<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight">
                    {{ __('Markayı Düzenle: ' . $brand->name) }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Filtre markası bilgilerini ve arama kriterini güncelleyin</p>
            </div>
            <a href="{{ route('admin.brands.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-700 flex items-center gap-1 bg-white border border-gray-200 px-4 py-2 rounded-xl shadow-xs">
                <i class="fas fa-arrow-left"></i> Geri Dön
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm">
                
                <form action="{{ route('admin.brands.update', $brand->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-gray-700 mb-1">Marka Adı *</label>
                            <input type="text" name="name" value="{{ old('name', $brand->name) }}" required 
                                   class="w-full border-gray-300 rounded-2xl shadow-xs text-xs font-bold focus:border-blue-500 focus:ring-blue-500">
                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-700 mb-1">Üst / Ana Marka (Varsa)</label>
                            <select name="parent_id" class="w-full border-gray-300 rounded-2xl shadow-xs text-xs font-bold focus:border-blue-500 focus:ring-blue-500 bg-white">
                                <option value="">-- Yok (Bu Bir Ana Markadır) --</option>
                                @foreach($parentBrands as $pb)
                                    <option value="{{ $pb->id }}" {{ (old('parent_id', $brand->parent_id) == $pb->id) ? 'selected' : '' }}>
                                        {{ $pb->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-gray-400 mt-1">Örn: İhlas'ın altına "Aura Cebilon" veya "Cebilon Silver" eklerken İhlas'ı seçin.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-gray-700 mb-1">Arama Kelimesi (Filtreleri Eşleştirmek İçin)</label>
                            <input type="text" name="search_query" value="{{ old('search_query', $brand->search_query) }}" 
                                   class="w-full border-gray-300 rounded-2xl shadow-xs text-xs font-bold focus:border-blue-500 focus:ring-blue-500">
                            <p class="text-[10px] text-gray-400 mt-1">Müşteri tıkladığında ürünlerde bu kelime aranır (Örn: İhlas, LG, BMS).</p>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-700 mb-1">Sıralama</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', $brand->sort_order) }}" 
                                   class="w-full border-gray-300 rounded-2xl shadow-xs text-xs font-bold focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-700 mb-1">Kısa Açıklama / Uyumlu Modeller</label>
                        <textarea name="description" rows="3" 
                                  class="w-full border-gray-300 rounded-2xl shadow-xs text-xs focus:border-blue-500 focus:ring-blue-500">{{ old('description', $brand->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-700 mb-1">Marka Logosu / Görseli</label>
                        @if($brand->image_path)
                            <div class="mb-2 w-16 h-16 rounded-xl bg-gray-50 border border-gray-200 p-1 flex items-center justify-center">
                                <img src="{{ asset('storage/' . $brand->image_path) }}" alt="{{ $brand->name }}" class="max-h-full max-w-full object-contain">
                            </div>
                        @endif
                        <input type="file" name="image" accept="image/*" 
                               class="w-full border border-gray-300 rounded-2xl p-2 text-xs text-gray-500">
                    </div>

                    <div class="flex items-center">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $brand->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-xs font-bold text-gray-700">Aktif (Filtre bulucu sayfasında göster)</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black text-xs px-6 py-3 rounded-2xl shadow-md transition-all transform active:scale-95 flex items-center gap-2">
                            <i class="fas fa-check"></i> Değişiklikleri Kaydet
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
