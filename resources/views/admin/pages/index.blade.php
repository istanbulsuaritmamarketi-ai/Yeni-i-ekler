<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <i class="fas fa-balance-scale text-theme-primary"></i> Hukuki Metinler &amp; Kurumsal Sayfalar
                </h2>
                <p class="text-xs text-gray-500 mt-1">6698 KVKK, Çerez Politikası, Mesafeli Satış ve Kurumsal Sözleşmelerinizi düzenleyin</p>
            </div>
            <a href="{{ route('admin.pages.create') }}" class="bg-theme-primary hover:bg-theme-primary-hover text-white font-black text-xs py-2.5 px-4 rounded-xl shadow-xs transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Yeni Sayfa / Metin Ekle
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        
        <!-- Bilgilendirme Kutusu -->
        <div class="bg-indigo-50/70 border border-indigo-200/80 rounded-3xl p-5 flex items-start gap-4">
            <div class="w-10 h-10 rounded-2xl bg-indigo-500 text-white flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-gavel"></i>
            </div>
            <div>
                <h4 class="font-black text-indigo-950 text-sm">Hukuki ve Yasal Uyum Bilgisi</h4>
                <p class="text-xs text-indigo-800 mt-0.5 leading-relaxed">
                    Burada kayıtlı sayfalar web sitenizin en alt (footer) kısmındaki <strong>"Kurumsal &amp; Hukuki"</strong> menüsünde, sipariş onay adımlarındaki sözleşme pencerelerinde ve KVKK çerez bildiriminde otomatik olarak kullanılır. Dilediğiniz sayfanın üzerine tıklayarak metinleri şirketinizin ünvanına göre düzenleyebilirsiniz.
                </p>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50/80 border-b border-gray-100 text-gray-400 uppercase tracking-wider font-bold">
                        <tr>
                            <th class="py-4 px-6">Sayfa Başlığı</th>
                            <th class="py-4 px-6">Canlı URL</th>
                            <th class="py-4 px-6">Tür / Kategori</th>
                            <th class="py-4 px-6">Yayın Durumu</th>
                            <th class="py-4 px-6 text-right">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pages as $item)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-4 px-6">
                                    <div class="font-black text-gray-900 text-sm flex items-center gap-2">
                                        @if(str_contains($item->slug, 'kvkk') || str_contains($item->slug, 'cerez'))
                                            <i class="fas fa-shield-alt text-emerald-500"></i>
                                        @elseif(str_contains($item->slug, 'sozlesme') || str_contains($item->slug, 'iade'))
                                            <i class="fas fa-file-contract text-blue-500"></i>
                                        @else
                                            <i class="fas fa-file-alt text-gray-400"></i>
                                        @endif
                                        <span>{{ $item->title }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <a href="{{ route('page.show', $item->slug) }}" target="_blank" class="font-mono text-[11px] text-theme-primary hover:underline inline-flex items-center gap-1">
                                        <span>/sayfa/{{ $item->slug }}</span>
                                        <i class="fas fa-external-link-alt text-[9px]"></i>
                                    </a>
                                </td>
                                <td class="py-4 px-6">
                                    @if(str_contains($item->slug, 'kvkk') || str_contains($item->slug, 'cerez') || str_contains($item->slug, 'sozlesme') || str_contains($item->slug, 'iade'))
                                        <span class="px-2.5 py-1 text-[10px] font-black rounded-lg bg-amber-50 text-amber-800 border border-amber-200 uppercase inline-flex items-center">
                                            <i class="fas fa-balance-scale mr-1 text-amber-600"></i> Yasal / KVKK
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] font-black rounded-lg bg-slate-100 text-slate-700 border border-slate-200 uppercase inline-flex items-center">
                                            <i class="fas fa-building mr-1 text-slate-600"></i> Kurumsal
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 text-[11px] font-black rounded-lg border {{ $item->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                        {{ $item->is_active ? 'Yayında (Aktif)' : 'Taslak (Gizli)' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right space-x-2">
                                    <a href="{{ route('admin.pages.edit', $item) }}" class="p-2 rounded-xl bg-gray-100 hover:bg-theme-light text-gray-600 hover:text-theme-primary transition inline-flex items-center" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.pages.destroy', $item) }}" method="POST" class="inline-block" onsubmit="return confirm('Bu sayfayı silmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 transition inline-flex items-center" title="Sil">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-400">
                                    <i class="fas fa-file-contract text-4xl mb-3 block text-gray-300"></i>
                                    Henüz sayfa kaydı bulunmuyor.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>