<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight">
            {{ __('Yeni Kategori Ekle') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm">
                
                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1" for="name">
                                Kategori Adı <span class="text-red-500">*</span>
                            </label>
                            <input class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-xs font-bold text-gray-900 focus:ring-theme-primary focus:border-theme-primary" 
                                   id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Örn: Buket Çiçekler veya Lüks Villalar" required>
                            @error('name') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1" for="slug">
                                Kategori URL (Slug) <span class="text-red-500">*</span>
                            </label>
                            <input class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-xs font-mono font-bold text-gray-900 focus:ring-theme-primary focus:border-theme-primary" 
                                   id="slug" name="slug" type="text" value="{{ old('slug') }}" placeholder="ornek-kategori-adi" required>
                            @error('slug') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Kategori Görseli Yükleme -->
                    <div class="p-5 bg-slate-50 rounded-2xl border border-gray-200 space-y-3">
                        <label class="block text-xs font-black text-gray-900" for="image">
                            <i class="fas fa-image text-theme-primary mr-1"></i> Kategori Görseli / İkonu (Opsiyonel)
                        </label>
                        <p class="text-[11px] text-gray-500">Ana sayfada ve kategori listelerinde kutucuk içinde görünecek şık kategori görseli (PNG, JPG, WEBP).</p>
                        <input type="file" id="image" name="image" accept="image/*"
                               class="block w-full text-xs text-gray-900 border border-gray-300 rounded-xl cursor-pointer bg-white p-2">
                        @error('image') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1" for="description">
                            Kategori Açıklaması
                        </label>
                        <textarea class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-xs text-gray-900 focus:ring-blue-500 focus:border-blue-500" 
                                  id="description" name="description" rows="3" placeholder="Kategori hakkında kısa tanıtım yazısı...">{{ old('description') }}</textarea>
                        @error('description') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="w-1/3">
                        <label class="block text-xs font-bold text-gray-700 mb-1" for="sort_order">
                            Sıralama (Öncelik)
                        </label>
                        <input class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-xs text-gray-900 font-bold focus:ring-blue-500 focus:border-blue-500" 
                               id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', 0) }}">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a class="text-xs font-bold text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-4 py-2.5 rounded-xl transition-colors" href="{{ route('admin.categories.index') }}">İptal</a>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-black text-xs py-2.5 px-6 rounded-xl shadow-lg transition-all transform active:scale-95" type="submit">
                            <i class="fas fa-save mr-1"></i> Kategoriyi Kaydet
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
