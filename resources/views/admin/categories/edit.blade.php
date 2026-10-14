<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight">
            {{ __('Kategori Düzenle: ') }} {{ $category->name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm">
                
                <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1" for="name">
                                Kategori Adı <span class="text-red-500">*</span>
                            </label>
                            <input class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-xs font-bold text-gray-900 focus:ring-blue-500 focus:border-blue-500" 
                                   id="name" name="name" type="text" value="{{ old('name', $category->name) }}" required>
                            @error('name') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1" for="slug">
                                Kategori URL (Slug) <span class="text-red-500">*</span>
                            </label>
                            <input class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-xs font-mono font-bold text-gray-900 focus:ring-blue-500 focus:border-blue-500" 
                                   id="slug" name="slug" type="text" value="{{ old('slug', $category->slug) }}" required>
                            @error('slug') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Kategori Görseli Yükleme & Önizleme -->
                    <div class="p-5 bg-blue-50/50 rounded-2xl border border-blue-100 space-y-3">
                        <label class="block text-xs font-black text-gray-900" for="image">
                            <i class="fas fa-image text-blue-600 mr-1"></i> Kategori Görseli / İkonu
                        </label>
                        
                        @if($category->image_path)
                            <div class="flex items-center justify-between gap-4 p-3.5 bg-white rounded-2xl border border-blue-200 shadow-2xs">
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset('storage/' . $category->image_path) }}" alt="{{ $category->name }}" class="w-14 h-14 object-contain rounded-xl border border-gray-100 bg-gray-50 p-1">
                                    <div>
                                        <span class="text-xs font-bold text-gray-900 block">Mevcut Kategori Görseli</span>
                                        <span class="text-[11px] text-gray-400">Değiştirmek için aşağıdan yeni dosya seçin</span>
                                    </div>
                                </div>
                                <button type="button" 
                                        onclick="if(confirm('Kategori görselini tamamen silmek istediğinize emin misiniz?')) document.getElementById('delete-category-image-form').submit();" 
                                        class="text-xs font-bold text-red-600 hover:text-white hover:bg-red-600 bg-red-50 border border-red-200 px-3 py-2 rounded-xl transition-all flex items-center gap-1.5 cursor-pointer">
                                    <i class="fas fa-trash-alt"></i> Görseli Kaldır
                                </button>
                            </div>
                        @endif

                        <input type="file" id="image" name="image" accept="image/*"
                               class="block w-full text-xs text-gray-900 border border-gray-300 rounded-xl cursor-pointer bg-white p-2">
                        @error('image') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1" for="description">
                            Kategori Açıklaması
                        </label>
                        <textarea class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-xs text-gray-900 focus:ring-blue-500 focus:border-blue-500" 
                                  id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                        @error('description') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1" for="is_active">
                                Kategori Durumu
                            </label>
                            <select class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-xs font-bold text-gray-900 focus:ring-blue-500 focus:border-blue-500 bg-white" id="is_active" name="is_active">
                                <option value="1" {{ old('is_active', $category->is_active) ? 'selected' : '' }}>Aktif (Sitede Görünür)</option>
                                <option value="0" {{ !old('is_active', $category->is_active) ? 'selected' : '' }}>Pasif (Gizli)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1" for="sort_order">
                                Sıralama (Öncelik)
                            </label>
                            <input class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-xs text-gray-900 font-bold focus:ring-blue-500 focus:border-blue-500" 
                                   id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', $category->sort_order) }}">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a class="text-xs font-bold text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-4 py-2.5 rounded-xl transition-colors" href="{{ route('admin.categories.index') }}">İptal</a>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-black text-xs py-2.5 px-6 rounded-xl shadow-lg transition-all transform active:scale-95" type="submit">
                            <i class="fas fa-save mr-1"></i> Değişiklikleri Güncelle
                        </button>
                    </div>
                </form>

                @if($category->image_path)
                    <form id="delete-category-image-form" action="{{ route('admin.categories.image.destroy', $category) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
