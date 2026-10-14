<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight">
                    {{ __('Kategori Yönetimi') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Ürün kategorilerini ve kategori kapak görsellerini yönetin</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="bg-theme-primary hover:bg-theme-primary-hover text-white font-black py-2.5 px-5 rounded-2xl text-xs flex items-center gap-2 shadow-xs transition-all">
                <i class="fas fa-plus"></i> Yeni Kategori Ekle
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm">
                
                @if(session('success'))
                    <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 text-emerald-800 rounded-r-2xl shadow-sm text-sm font-bold flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="text-left text-xs font-black text-gray-400 uppercase tracking-wider">
                                <th class="py-3 px-4">Görsel</th>
                                <th class="py-3 px-4">Kategori Adı</th>
                                <th class="py-3 px-4">URL (Slug)</th>
                                <th class="py-3 px-4">Durum</th>
                                <th class="py-3 px-4">Sıra</th>
                                <th class="py-3 px-4 text-right">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($categories as $category)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-3 px-4">
                                        <div class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center overflow-hidden">
                                            @if($category->image_path)
                                                <img src="{{ asset('storage/' . $category->image_path) }}" alt="{{ $category->name }}" class="w-full h-full object-contain p-1">
                                            @else
                                                <i class="fas fa-folder text-gray-300 text-lg"></i>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 font-bold text-sm text-gray-900">{{ $category->name }}</td>
                                    <td class="py-3 px-4 font-mono text-xs text-gray-500">{{ $category->slug }}</td>
                                    <td class="py-3 px-4">
                                        @if($category->is_active)
                                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800">Aktif</span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-600">Pasif</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-xs font-bold text-gray-600">{{ $category->sort_order }}</td>
                                    <td class="py-3 px-4 text-right space-x-2">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-theme-primary hover:text-theme-primary-hover bg-theme-light px-3 py-1.5 rounded-xl text-xs font-bold transition-colors">
                                            <i class="fas fa-edit"></i> Düzenle
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-xl text-xs font-bold transition-colors">
                                                <i class="fas fa-trash-alt"></i> Sil
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-400 text-xs font-medium">Henüz kategori eklenmemiş.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
