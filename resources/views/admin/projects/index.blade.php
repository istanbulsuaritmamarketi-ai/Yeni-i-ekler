<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <i class="fas fa-building text-amber-500"></i> Proje & Portföy Yönetimi
                </h2>
                <p class="text-xs text-gray-500 mt-1">İnşaat, mimarlık ve kurumsal portföy projelerinizi yönetin</p>
            </div>
            
            <a href="{{ route('admin.projects.create') }}" 
               class="bg-theme-primary hover:bg-theme-primary-hover text-white font-black text-xs py-2.5 px-4 rounded-xl shadow-xs transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i> Yeni Proje Ekle
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50/80 border-b border-gray-100 text-gray-400 uppercase tracking-wider font-bold">
                        <tr>
                            <th class="py-4 px-6">Görsel</th>
                            <th class="py-4 px-6">Proje Başlığı</th>
                            <th class="py-4 px-6">Kategori & Konum</th>
                            <th class="py-4 px-6">Durum</th>
                            <th class="py-4 px-6">Öne Çıkan</th>
                            <th class="py-4 px-6 text-right">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($projects as $p)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-4 px-6">
                                    <div class="w-16 h-12 rounded-xl bg-gray-100 overflow-hidden border border-gray-200 shrink-0">
                                        @if($p->featured_image)
                                            <img src="{{ asset('storage/' . $p->featured_image) }}" alt="{{ $p->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <i class="fas fa-building text-base"></i>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-black text-gray-900 text-sm">{{ $p->title }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">Sıra: {{ $p->sort_order }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="font-bold text-gray-700 block">{{ $p->category ?? 'Genel' }}</span>
                                    <span class="text-[11px] text-gray-400"><i class="fas fa-map-marker-alt text-amber-500 mr-1"></i>{{ $p->location ?? 'Belirtilmedi' }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 text-[11px] font-black rounded-lg border {{ $p->status === 'Tamamlandı' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                        {{ $p->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    @if($p->is_featured)
                                        <span class="text-amber-500 font-black flex items-center gap-1"><i class="fas fa-star"></i> Evet</span>
                                    @else
                                        <span class="text-gray-400">Hayır</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right space-x-2">
                                    <a href="{{ route('admin.projects.edit', $p) }}" class="p-2 rounded-xl bg-gray-100 hover:bg-theme-light text-gray-600 hover:text-theme-primary transition inline-flex items-center">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.projects.destroy', $p) }}" method="POST" class="inline-block" onsubmit="return confirm('Bu projeyi silmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 transition inline-flex items-center">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-400">
                                    <i class="fas fa-folder-open text-4xl mb-3 block text-gray-300"></i>
                                    Henüz portföyde proje kaydı bulunmuyor.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($projects->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
