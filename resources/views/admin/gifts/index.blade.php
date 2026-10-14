<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <i class="fas fa-gift text-purple-600"></i> Hediye &amp; Promosyon Yönetimi
                </h2>
                <p class="text-xs text-gray-500 mt-1">Ürün sayfalarında ve sepette müşterilere sunulacak ücretsiz hediye seçeneklerini ekleyin ve yönetin</p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.marketing.gifts') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold px-3.5 py-2.5 rounded-xl transition-colors flex items-center gap-1.5">
                    <i class="fas fa-cog text-purple-600"></i> Genel Kampanya Ayarları
                </a>
                <a href="{{ route('admin.gifts.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-colors flex items-center gap-1.5">
                    <i class="fas fa-plus"></i> Yeni Hediye Ekle
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 text-emerald-800 rounded-r-2xl shadow-sm text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                    {{ session('success') }}
                </div>
            @endif

            <!-- İstatistik Kartları -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Toplam Hediye</span>
                        <span class="text-2xl font-black text-gray-900">{{ $totalGifts }} Adet</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                        <i class="fas fa-cubes"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Yayında / Aktif Hediye</span>
                        <span class="text-2xl font-black text-emerald-600">{{ $activeGifts }} Adet</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <!-- Hediye Listesi Tablosu / Kartları -->
            <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-base text-gray-900 flex items-center gap-2">
                        <i class="fas fa-list text-purple-600"></i> Tanımlı Hediye Seçenekleri
                    </h3>
                    <span class="text-xs text-gray-500">Müşteri siparişte bu aktif seçeneklerden 1 adet ücretsiz hediye seçebilir</span>
                </div>

                @if($gifts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-[11px] border-b border-gray-200">
                                <tr>
                                    <th class="py-3.5 px-6">Görsel / İkon</th>
                                    <th class="py-3.5 px-6">Hediye Adı</th>
                                    <th class="py-3.5 px-6">Rozet</th>
                                    <th class="py-3.5 px-6">Açıklama</th>
                                    <th class="py-3.5 px-6 text-center">Sıra</th>
                                    <th class="py-3.5 px-6 text-center">Durum</th>
                                    <th class="py-3.5 px-6 text-right">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($gifts as $gift)
                                    <tr class="hover:bg-gray-50/70 transition-colors">
                                        <td class="py-4 px-6">
                                            @if($gift->image_path)
                                                <div class="w-12 h-12 rounded-xl bg-white border border-gray-200 p-1 flex items-center justify-center overflow-hidden shadow-2xs">
                                                    <img src="{{ asset('storage/' . $gift->image_path) }}" alt="{{ $gift->name }}" class="max-h-full max-w-full object-contain">
                                                </div>
                                            @else
                                                <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-lg shadow-2xs">
                                                    <i class="{{ $gift->icon ?: 'fas fa-gift' }}"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="font-bold text-gray-900 text-sm">{{ $gift->name }}</div>
                                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                                <span class="text-[10px] text-gray-400">ID: #{{ $gift->id }}</span>
                                                @if($gift->market_price)
                                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-extrabold px-2 py-0.2 rounded-full">
                                                        Değer: {{ number_format($gift->market_price, 0, ',', '.') }} TL
                                                    </span>
                                                @endif
                                                @if($gift->external_url)
                                                    <a href="{{ $gift->external_url }}" target="_blank" class="text-[10px] text-blue-600 font-bold hover:underline inline-flex items-center gap-1">
                                                        <i class="fas fa-external-link-alt text-[9px]"></i> Dış Ürün Linki
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            @if($gift->badge)
                                                <span class="inline-flex items-center gap-1 bg-purple-50 text-purple-700 border border-purple-200 text-[11px] font-bold px-2.5 py-0.5 rounded-full">
                                                    <i class="fas fa-tag text-[9px]"></i> {{ $gift->badge }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-gray-600 max-w-xs truncate">
                                            {{ $gift->description ?: '—' }}
                                        </td>
                                        <td class="py-4 px-6 text-center font-bold text-gray-700">
                                            {{ $gift->sort_order }}
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <form action="{{ route('admin.gifts.toggle', $gift) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition-colors {{ $gift->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                                    <span class="w-2 h-2 rounded-full {{ $gift->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                                    {{ $gift->is_active ? 'Aktif' : 'Pasif' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('admin.gifts.edit', $gift) }}" class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg font-bold text-xs transition-colors" title="Düzenle">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.gifts.destroy', $gift) }}" method="POST" onsubmit="return confirm('Bu hediyeyi silmek istediğinize emin misiniz?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg font-bold text-xs transition-colors" title="Sil">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 border-t border-gray-100">
                        {{ $gifts->links() }}
                    </div>
                @else
                    <div class="p-12 text-center text-gray-500">
                        <i class="fas fa-gift text-4xl text-gray-300 mb-3"></i>
                        <p class="font-bold text-sm">Henüz kayıtlı hediye bulunmuyor.</p>
                        <p class="text-xs text-gray-400 mt-1">Yeni bir hediye seçeneği eklemek için sağ üstteki butonu kullanabilirsiniz.</p>
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>
