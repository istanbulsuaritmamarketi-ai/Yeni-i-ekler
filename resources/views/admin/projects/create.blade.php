<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <i class="fas fa-plus text-amber-500"></i> Yeni Proje Ekle
                </h2>
                <p class="text-xs text-gray-500 mt-1">İnşaat / ajans portföyüne yeni bir çalışma ekleyin</p>
            </div>
            <a href="{{ route('admin.projects.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-900 bg-white border border-gray-200 px-4 py-2.5 rounded-xl shadow-xs transition">
                &larr; Projelere Dön
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto py-4">
        <form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-200/80 shadow-xs space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Proje Başlığı *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Örn: Vadi Modern Konutları & Rezidans"
                           class="w-full text-xs font-bold py-2.5 px-3.5 border border-gray-300 rounded-xl focus:ring-theme-primary focus:border-theme-primary">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Kategori</label>
                    <input type="text" name="category" value="{{ old('category') }}" placeholder="Örn: Konut Projesi, Web Tasarım, Villa"
                           class="w-full text-xs font-medium py-2.5 px-3.5 border border-gray-300 rounded-xl focus:ring-theme-primary focus:border-theme-primary">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Konum / Lokasyon</label>
                    <input type="text" name="location" value="{{ old('location') }}" placeholder="Örn: Başakşehir / İstanbul"
                           class="w-full text-xs font-medium py-2.5 px-3.5 border border-gray-300 rounded-xl focus:ring-theme-primary focus:border-theme-primary">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Proje Durumu *</label>
                    <select name="status" required class="w-full text-xs font-bold py-2.5 px-3.5 border border-gray-300 rounded-xl focus:ring-theme-primary focus:border-theme-primary">
                        <option value="Devam Ediyor">Devam Ediyor</option>
                        <option value="Tamamlandı" selected>Tamamlandı</option>
                        <option value="Planlama Aşamasında">Planlama Aşamasında</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Müşteri / İşveren</label>
                    <input type="text" name="client" value="{{ old('client') }}" placeholder="Örn: Reform İnşaat A.Ş."
                           class="w-full text-xs font-medium py-2.5 px-3.5 border border-gray-300 rounded-xl focus:ring-theme-primary focus:border-theme-primary">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Kısa Özet</label>
                    <textarea name="summary" rows="2" placeholder="Portföy kartlarında görünecek kısa tanıtım yazısı..."
                              class="w-full text-xs font-medium py-2.5 px-3.5 border border-gray-300 rounded-xl focus:ring-theme-primary focus:border-theme-primary">{{ old('summary') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Detaylı Açıklama</label>
                    <textarea name="description" rows="5" placeholder="Projenin mimari detayları, teknik özellikleri, kullanılan teknolojiler..."
                              class="w-full text-xs font-medium py-2.5 px-3.5 border border-gray-300 rounded-xl focus:ring-theme-primary focus:border-theme-primary">{{ old('description') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Ana Proje Görseli</label>
                    <input type="file" name="featured_image" accept="image/*"
                           class="w-full text-xs font-medium py-2 px-3 border border-gray-300 rounded-xl file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                </div>

                <div class="flex items-center gap-6 md:col-span-2 pt-2 border-t border-gray-100">
                    <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold text-gray-700">
                        <input type="checkbox" name="is_featured" value="1" class="rounded text-amber-600 focus:ring-amber-500">
                        <span>Ana Sayfada Öne Çıkar</span>
                    </label>

                    <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold text-gray-700">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span>Aktif / Yayında</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.projects.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-50">
                    İptal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-theme-primary hover:bg-theme-primary-hover text-white text-xs font-black shadow-sm transition">
                    Projeyi Kaydet
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
