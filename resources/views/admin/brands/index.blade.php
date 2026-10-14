<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight">
                    {{ __('Filtre Markaları Yönetimi') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Müşterilerin filtre bulucuda arayacağı su arıtma markalarını (İhlas, LG, BMS, Conax vb.) yönetin</p>
            </div>
            <a href="{{ route('admin.brands.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-2.5 px-5 rounded-2xl text-xs flex items-center gap-2 shadow-lg transition-all transform active:scale-95">
                <i class="fas fa-plus"></i> Yeni Marka Ekle
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
                                <th class="py-3 px-4">Görsel / İkon</th>
                                <th class="py-3 px-4">Marka Adı</th>
                                <th class="py-3 px-4">Arama Kelimesi</th>
                                <th class="py-3 px-4">Açıklama</th>
                                <th class="py-3 px-4 text-center">Bağlı Ürün</th>
                                <th class="py-3 px-4">Durum</th>
                                <th class="py-3 px-4">Sıra</th>
                                <th class="py-3 px-4 text-right">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($brands as $brand)
                                <tr class="hover:bg-gray-50/50 transition-colors {{ $brand->parent_id ? 'bg-purple-50/20' : '' }}">
                                    <td class="py-3 px-4">
                                        <div class="w-11 h-11 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center overflow-hidden">
                                            @if($brand->image_path)
                                                <img src="{{ asset('storage/' . $brand->image_path) }}" alt="{{ $brand->name }}" class="w-full h-full object-contain p-1">
                                            @else
                                                <i class="fas {{ $brand->parent_id ? 'fa-code-branch text-purple-400' : 'fa-shield-alt text-blue-500' }} text-base"></i>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($brand->parent)
                                            <div class="flex items-center gap-1.5 text-[11px] text-gray-400 font-bold mb-0.5">
                                                <i class="fas fa-level-up-alt rotate-90 text-purple-500"></i> {{ $brand->parent->name }}
                                            </div>
                                            <span class="font-black text-sm text-purple-950 pl-3 border-l-2 border-purple-300">
                                                {{ $brand->name }}
                                            </span>
                                            <span class="ml-1 text-[9px] font-black uppercase bg-purple-100 text-purple-800 px-1.5 py-0.5 rounded">Alt Model</span>
                                        @else
                                            <div class="font-black text-sm text-gray-900 flex items-center gap-1.5">
                                                <span>{{ $brand->name }}</span>
                                                @if($brand->children->count() > 0)
                                                    <span class="text-[10px] font-bold text-gray-400">({{ $brand->children->count() }} Alt Model)</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 font-mono text-xs text-blue-600 bg-blue-50/50 px-2 py-1 rounded inline-block">{{ $brand->search_query }}</td>
                                    <td class="py-3 px-4 text-xs text-gray-500 max-w-xs truncate">{{ $brand->description }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <a href="{{ route('admin.brands.products', $brand->id) }}" class="inline-flex items-center gap-1 text-xs font-black px-3 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200/60 shadow-2xs transition-colors" title="Bu markaya ait ürünleri seç ve yönet">
                                            <i class="fas fa-tasks text-[10px]"></i> {{ $brand->products_count }} Ürün Yönet
                                        </a>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($brand->is_active)
                                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800">Aktif</span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-600">Pasif</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-xs font-bold text-gray-600">{{ $brand->sort_order }}</td>
                                    <td class="py-3 px-4 text-right space-x-1.5">
                                        <a href="{{ route('admin.brands.create', ['parent_id' => $brand->parent_id ? $brand->parent_id : $brand->id]) }}" class="text-indigo-700 hover:text-indigo-900 text-xs font-black inline-flex items-center gap-1 bg-indigo-50 px-2 py-1 rounded-lg" title="Bu markanın altına alt model ekle">
                                            <i class="fas fa-code-branch text-[10px]"></i> + Alt Model
                                        </a>
                                        <a href="{{ route('admin.brands.edit', $brand->id) }}" class="text-blue-600 hover:text-blue-900 text-xs font-black inline-flex items-center gap-1 bg-blue-50 px-2.5 py-1 rounded-lg">
                                            <i class="fas fa-edit"></i> Düzenle
                                        </a>
                                        <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" class="inline" onsubmit="return confirm('Bu markayı silmek istediğinize emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-900 text-xs font-black inline-flex items-center gap-1 bg-rose-50 px-2.5 py-1 rounded-lg">
                                                <i class="fas fa-trash"></i> Sil
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-gray-400 text-sm">
                                        Henüz marka eklenmemiş.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
