<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Ana Sayfa Banner Yönetimi</h2>
                <p class="text-xs text-gray-500 mt-1">Ana sayfa 4 sütunlu (7 bannerlı) vitrin kutularını buradan yönetebilirsiniz.</p>
            </div>
            <a href="{{ route('admin.banners.create') }}" class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-xs transition-colors text-sm self-start sm:self-auto">
                <i class="fas fa-plus"></i> Yeni Banner Ekle
            </a>
        </div>
    </x-slot>

    @php
        $getImgUrl = function($banner) {
            if (!$banner || !$banner->image_path) return null;
            $path = $banner->image_path;
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
            if (file_exists(public_path('storage/' . $path))) return asset('storage/' . $path);
            if (file_exists(public_path('images/' . $path))) return asset('images/' . $path);
            if (file_exists(public_path($path))) return asset($path);
            return asset('images/' . $path);
        };

        $posMap = [
            'grid_1' => ['title' => '1. Kutu (1. Sütun Üst)', 'col' => 1, 'row' => 'top'],
            'grid_2' => ['title' => '2. Kutu (1. Sütun Alt)', 'col' => 1, 'row' => 'bottom'],
            'grid_3' => ['title' => '3. Kutu (2. Sütun Üst)', 'col' => 2, 'row' => 'top'],
            'grid_4' => ['title' => '4. Kutu (2. Sütun Alt)', 'col' => 2, 'row' => 'bottom'],
            'grid_5' => ['title' => '5. Kutu (3. Sütun Üst)', 'col' => 3, 'row' => 'top'],
            'grid_6' => ['title' => '6. Kutu (3. Sütun Alt)', 'col' => 3, 'row' => 'bottom'],
            'grid_7' => ['title' => '7. Dikey Büyük Banner (4. Sütun)', 'col' => 4, 'row' => 'full'],
        ];

        $bannerByPos = $banners->keyBy('position');
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- 1. GÖRSEL DİZİLİM ŞEMASI (KULLANICI ÇİZİMİ İLE BİREBİR) -->
            <div class="bg-white rounded-2xl shadow-xs border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-extrabold text-base text-gray-800 flex items-center gap-2">
                            <i class="fas fa-th-large text-rose-600"></i>
                            Ana Sayfa Banner Dizilim Planı (7 Kutu)
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">Aşağıdaki kutulardan herhangi birine tıklayarak doğrudan o pozisyonu düzenleyebilirsiniz.</p>
                    </div>
                    <span class="text-xs bg-rose-50 text-rose-700 font-bold px-3 py-1 rounded-full border border-rose-100">
                        4 Sütunlu Sistem
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-stretch">
                    <!-- SÜTUN 1: Kutu 1 & 2 -->
                    <div class="flex flex-col gap-4 justify-between">
                        <!-- KUTU 1 -->
                        @php $b1 = $bannerByPos->get('grid_1'); @endphp
                        <div class="border-2 {{ $b1 ? 'border-rose-200 bg-rose-50/30' : 'border-dashed border-gray-300 bg-gray-50' }} rounded-xl p-3 flex flex-col justify-between h-[150px] relative group hover:border-rose-400 transition-all">
                            <div class="flex items-start justify-between gap-1">
                                <div>
                                    <span class="text-[10px] font-black uppercase text-rose-600 bg-rose-100 px-1.5 py-0.5 rounded">Banner 1</span>
                                    <h4 class="text-xs font-bold text-gray-800 mt-1 line-clamp-1">{{ $b1->title ?? '1. Kutu (Gül Buketleri)' }}</h4>
                                </div>
                                @if($b1 && $getImgUrl($b1))
                                    <img src="{{ $getImgUrl($b1) }}" class="h-10 w-10 object-contain rounded">
                                @endif
                            </div>
                            <div class="flex items-center justify-between text-[11px] pt-2 border-t border-gray-200/70">
                                <span class="text-gray-500">1. Sütun Üst</span>
                                @if($b1)
                                    <a href="{{ route('admin.banners.edit', $b1) }}" class="font-bold text-rose-600 hover:text-rose-800">Düzenle &rarr;</a>
                                @else
                                    <a href="{{ route('admin.banners.create', ['position' => 'grid_1']) }}" class="font-bold text-blue-600 hover:text-blue-800">+ Ekle</a>
                                @endif
                            </div>
                        </div>

                        <!-- KUTU 2 -->
                        @php $b2 = $bannerByPos->get('grid_2'); @endphp
                        <div class="border-2 {{ $b2 ? 'border-rose-200 bg-rose-50/30' : 'border-dashed border-gray-300 bg-gray-50' }} rounded-xl p-3 flex flex-col justify-between h-[150px] relative group hover:border-rose-400 transition-all">
                            <div class="flex items-start justify-between gap-1">
                                <div>
                                    <span class="text-[10px] font-black uppercase text-rose-600 bg-rose-100 px-1.5 py-0.5 rounded">Banner 2</span>
                                    <h4 class="text-xs font-bold text-gray-800 mt-1 line-clamp-1">{{ $b2->title ?? '2. Kutu (Kutuda Çiçekler)' }}</h4>
                                </div>
                                @if($b2 && $getImgUrl($b2))
                                    <img src="{{ $getImgUrl($b2) }}" class="h-10 w-10 object-contain rounded">
                                @endif
                            </div>
                            <div class="flex items-center justify-between text-[11px] pt-2 border-t border-gray-200/70">
                                <span class="text-gray-500">1. Sütun Alt</span>
                                @if($b2)
                                    <a href="{{ route('admin.banners.edit', $b2) }}" class="font-bold text-rose-600 hover:text-rose-800">Düzenle &rarr;</a>
                                @else
                                    <a href="{{ route('admin.banners.create', ['position' => 'grid_2']) }}" class="font-bold text-blue-600 hover:text-blue-800">+ Ekle</a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- SÜTUN 2: Kutu 3 & 4 -->
                    <div class="flex flex-col gap-4 justify-between">
                        <!-- KUTU 3 -->
                        @php $b3 = $bannerByPos->get('grid_3'); @endphp
                        <div class="border-2 {{ $b3 ? 'border-rose-200 bg-rose-50/30' : 'border-dashed border-gray-300 bg-gray-50' }} rounded-xl p-3 flex flex-col justify-between h-[150px] relative group hover:border-rose-400 transition-all">
                            <div class="flex items-start justify-between gap-1">
                                <div>
                                    <span class="text-[10px] font-black uppercase text-rose-600 bg-rose-100 px-1.5 py-0.5 rounded">Banner 3</span>
                                    <h4 class="text-xs font-bold text-gray-800 mt-1 line-clamp-1">{{ $b3->title ?? '3. Kutu (Orkideler)' }}</h4>
                                </div>
                                @if($b3 && $getImgUrl($b3))
                                    <img src="{{ $getImgUrl($b3) }}" class="h-10 w-10 object-contain rounded">
                                @endif
                            </div>
                            <div class="flex items-center justify-between text-[11px] pt-2 border-t border-gray-200/70">
                                <span class="text-gray-500">2. Sütun Üst</span>
                                @if($b3)
                                    <a href="{{ route('admin.banners.edit', $b3) }}" class="font-bold text-rose-600 hover:text-rose-800">Düzenle &rarr;</a>
                                @else
                                    <a href="{{ route('admin.banners.create', ['position' => 'grid_3']) }}" class="font-bold text-blue-600 hover:text-blue-800">+ Ekle</a>
                                @endif
                            </div>
                        </div>

                        <!-- KUTU 4 -->
                        @php $b4 = $bannerByPos->get('grid_4'); @endphp
                        <div class="border-2 {{ $b4 ? 'border-rose-200 bg-rose-50/30' : 'border-dashed border-gray-300 bg-gray-50' }} rounded-xl p-3 flex flex-col justify-between h-[150px] relative group hover:border-rose-400 transition-all">
                            <div class="flex items-start justify-between gap-1">
                                <div>
                                    <span class="text-[10px] font-black uppercase text-rose-600 bg-rose-100 px-1.5 py-0.5 rounded">Banner 4</span>
                                    <h4 class="text-xs font-bold text-gray-800 mt-1 line-clamp-1">{{ $b4->title ?? '4. Kutu (Mevsim Çiçekleri)' }}</h4>
                                </div>
                                @if($b4 && $getImgUrl($b4))
                                    <img src="{{ $getImgUrl($b4) }}" class="h-10 w-10 object-contain rounded">
                                @endif
                            </div>
                            <div class="flex items-center justify-between text-[11px] pt-2 border-t border-gray-200/70">
                                <span class="text-gray-500">2. Sütun Alt</span>
                                @if($b4)
                                    <a href="{{ route('admin.banners.edit', $b4) }}" class="font-bold text-rose-600 hover:text-rose-800">Düzenle &rarr;</a>
                                @else
                                    <a href="{{ route('admin.banners.create', ['position' => 'grid_4']) }}" class="font-bold text-blue-600 hover:text-blue-800">+ Ekle</a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- SÜTUN 3: Kutu 5 & 6 -->
                    <div class="flex flex-col gap-4 justify-between">
                        <!-- KUTU 5 -->
                        @php $b5 = $bannerByPos->get('grid_5'); @endphp
                        <div class="border-2 {{ $b5 ? 'border-rose-200 bg-rose-50/30' : 'border-dashed border-gray-300 bg-gray-50' }} rounded-xl p-3 flex flex-col justify-between h-[150px] relative group hover:border-rose-400 transition-all">
                            <div class="flex items-start justify-between gap-1">
                                <div>
                                    <span class="text-[10px] font-black uppercase text-rose-600 bg-rose-100 px-1.5 py-0.5 rounded">Banner 5</span>
                                    <h4 class="text-xs font-bold text-gray-800 mt-1 line-clamp-1">{{ $b5->title ?? '5. Kutu (Doğum Günü)' }}</h4>
                                </div>
                                @if($b5 && $getImgUrl($b5))
                                    <img src="{{ $getImgUrl($b5) }}" class="h-10 w-10 object-contain rounded">
                                @endif
                            </div>
                            <div class="flex items-center justify-between text-[11px] pt-2 border-t border-gray-200/70">
                                <span class="text-gray-500">3. Sütun Üst</span>
                                @if($b5)
                                    <a href="{{ route('admin.banners.edit', $b5) }}" class="font-bold text-rose-600 hover:text-rose-800">Düzenle &rarr;</a>
                                @else
                                    <a href="{{ route('admin.banners.create', ['position' => 'grid_5']) }}" class="font-bold text-blue-600 hover:text-blue-800">+ Ekle</a>
                                @endif
                            </div>
                        </div>

                        <!-- KUTU 6 -->
                        @php $b6 = $bannerByPos->get('grid_6'); @endphp
                        <div class="border-2 {{ $b6 ? 'border-rose-200 bg-rose-50/30' : 'border-dashed border-gray-300 bg-gray-50' }} rounded-xl p-3 flex flex-col justify-between h-[150px] relative group hover:border-rose-400 transition-all">
                            <div class="flex items-start justify-between gap-1">
                                <div>
                                    <span class="text-[10px] font-black uppercase text-rose-600 bg-rose-100 px-1.5 py-0.5 rounded">Banner 6</span>
                                    <h4 class="text-xs font-bold text-gray-800 mt-1 line-clamp-1">{{ $b6->title ?? '6. Kutu (Romantik Seri)' }}</h4>
                                </div>
                                @if($b6 && $getImgUrl($b6))
                                    <img src="{{ $getImgUrl($b6) }}" class="h-10 w-10 object-contain rounded">
                                @endif
                            </div>
                            <div class="flex items-center justify-between text-[11px] pt-2 border-t border-gray-200/70">
                                <span class="text-gray-500">3. Sütun Alt</span>
                                @if($b6)
                                    <a href="{{ route('admin.banners.edit', $b6) }}" class="font-bold text-rose-600 hover:text-rose-800">Düzenle &rarr;</a>
                                @else
                                    <a href="{{ route('admin.banners.create', ['position' => 'grid_6']) }}" class="font-bold text-blue-600 hover:text-blue-800">+ Ekle</a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- SÜTUN 4: Kutu 7 (Dikey Boydan Boya) -->
                    <div class="flex flex-col h-full">
                        @php $b7 = $bannerByPos->get('grid_7'); @endphp
                        <div class="border-2 {{ $b7 ? 'border-rose-600 bg-rose-50/60' : 'border-dashed border-gray-300 bg-gray-50' }} rounded-xl p-4 flex flex-col justify-between h-full min-h-[316px] relative group hover:border-rose-700 transition-all">
                            <div>
                                <div class="flex items-start justify-between gap-1">
                                    <span class="text-[10px] font-black uppercase text-white bg-rose-600 px-2 py-0.5 rounded">Banner 7 (Dikey)</span>
                                    <span class="text-[10px] font-extrabold text-gray-500">4. Sütun Boydan Boya</span>
                                </div>
                                <h4 class="text-sm font-black text-gray-900 mt-2">{{ $b7->title ?? '7. Dikey Büyük Banner' }}</h4>
                                <p class="text-[11px] text-gray-500 mt-1 line-clamp-2">{{ $b7->subtitle ?? 'Haftanın Özel Çiçek Fırsatları' }}</p>
                            </div>

                            @if($b7 && $getImgUrl($b7))
                                <div class="my-3 text-center">
                                    <img src="{{ $getImgUrl($b7) }}" class="h-28 w-auto max-w-full object-contain mx-auto filter drop-shadow">
                                </div>
                            @endif

                            <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-200">
                                <span class="font-bold text-rose-700">Tam Boy Vitrin</span>
                                @if($b7)
                                    <a href="{{ route('admin.banners.edit', $b7) }}" class="font-black text-rose-700 hover:text-rose-900">Düzenle &rarr;</a>
                                @else
                                    <a href="{{ route('admin.banners.create', ['position' => 'grid_7']) }}" class="font-bold text-blue-600 hover:text-blue-800">+ Ekle</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. TÜM BANNERLAR TABLOSU -->
            <div class="bg-white rounded-2xl shadow-xs border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 text-sm">Banner Listesi (Detaylı Görünüm)</h3>
                    <span class="text-xs text-gray-500">Toplam: {{ $banners->count() }} Banner</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pozisyon</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Görsel</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Başlık & Alt Başlık</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Rozet / Buton</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Yönlendirme Linki</th>
                                <th class="px-6 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Durum</th>
                                <th class="px-6 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($banners as $banner)
                                @php
                                    $posInfo = $posMap[$banner->position] ?? ['title' => $banner->position];
                                    $imgUrl = $getImgUrl($banner);
                                @endphp
                                <tr class="hover:bg-gray-50/70 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-black {{ str_starts_with($banner->position, 'grid_') ? 'bg-rose-100 text-rose-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $posInfo['title'] ?? $banner->position }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($imgUrl)
                                            <div class="w-14 h-14 rounded-xl bg-slate-50 border border-slate-200 p-1 flex items-center justify-center">
                                                <img src="{{ $imgUrl }}" class="max-h-full max-w-full object-contain">
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Görsel Yok</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $banner->title ?: '—' }}</div>
                                        @if($banner->subtitle)
                                            <div class="text-xs text-gray-500">{{ $banner->subtitle }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($banner->badge)
                                            <span class="inline-block bg-amber-100 text-amber-800 text-[11px] font-bold px-2 py-0.5 rounded">
                                                {{ $banner->badge }}
                                            </span>
                                        @endif
                                        @if($banner->button_text)
                                            <div class="text-xs text-gray-400 mt-1">Buton: <span class="text-gray-700 font-semibold">{{ $banner->button_text }}</span></div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-blue-600 font-mono">
                                        {{ $banner->link_url ?: '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2.5 py-1 inline-flex text-xs font-bold rounded-full {{ $banner->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $banner->is_active ? 'Aktif' : 'Pasif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                        <a href="{{ route('admin.banners.edit', $banner) }}" class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-lg transition-colors">
                                            <i class="fas fa-edit"></i> Düzenle
                                        </a>
                                        <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700 p-1.5" onclick="return confirm('Bu bannerı silmek istediğinize emin misiniz?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">Kayıtlı banner bulunamadı.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
