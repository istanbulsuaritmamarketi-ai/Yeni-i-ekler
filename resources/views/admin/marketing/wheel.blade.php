<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <i class="fas fa-gift text-amber-500"></i> Şans Çarkı (Lucky Wheel) Ödül &amp; Kupon Ayarları
                </h2>
                <p class="text-xs text-gray-500 mt-1">Ziyaretçilerin çarkta kazanacağı ödül başlıklarını ve sepetinizde geçerli indirim kuponlarını yönetin</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.leads.index') }}" class="bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold px-3.5 py-2 rounded-xl transition-colors flex items-center gap-1.5 border border-red-200">
                    <i class="fas fa-bullseye"></i> Gelen Numaralar (Leads)
                </a>
                <a href="{{ route('admin.coupons.index') }}" class="bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold px-3.5 py-2 rounded-xl transition-colors flex items-center gap-1.5 border border-blue-200">
                    <i class="fas fa-ticket-alt"></i> Kupon Yönetimi
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 text-emerald-800 rounded-r-2xl shadow-sm text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.marketing.store') }}">
                @csrf

                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm space-y-8">
                    
                    <div class="border-b border-gray-100 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                                <i class="fas fa-dharmachakra text-pink-500"></i> Çarktaki 6 Dilimin Ödül Başlıkları &amp; Kupon Kodları
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">
                                Ziyaretçi numarasını girip çarkı çevirdiğinde kazandığı kod sisteminizdeki kuponla eşleşir ve sepette doğrudan indirim uygular.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @for($i = 1; $i <= 6; $i++)
                            @php
                                $defaultTitles = [
                                    1 => '500 TL Montaj İndirimi',
                                    2 => '1 Yıllık Yedek Filtre Hediye',
                                    3 => '%15 Sepet İndirimi',
                                    4 => 'Ücretsiz Randevulu Kurulum',
                                    5 => 'Alkali pH+ Filtre Hediye',
                                    6 => '300 TL Ekstra İndirim',
                                ];
                                $defaultCodes = [
                                    1 => 'ARITMA500',
                                    2 => 'FILTREHEDIYE',
                                    3 => 'INDIRIM15',
                                    4 => 'UCRETSIZMONTAJ',
                                    5 => 'ALKALIPH',
                                    6 => 'EKSTRA300',
                                ];
                                $defaultColors = [
                                    1 => '#2563eb',
                                    2 => '#10b981',
                                    3 => '#f59e0b',
                                    4 => '#8b5cf6',
                                    5 => '#06b6d4',
                                    6 => '#ef4444',
                                ];
                            @endphp
                            <div class="bg-gray-50/70 p-5 rounded-3xl border border-gray-200/80 space-y-3.5 hover:border-amber-400 transition-colors">
                                <div class="flex items-center justify-between pb-2 border-b border-gray-200/70">
                                    <span class="text-xs font-black text-gray-900 flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full text-white text-[11px] font-black flex items-center justify-center shadow" style="background-color: {{ $settings['wheel_slice_'.$i.'_color'] ?? $defaultColors[$i] }};">{{ $i }}</span>
                                        {{ $i }}. Dilim
                                    </span>
                                    <div class="flex items-center gap-1.5 text-[11px] text-gray-500 font-bold">
                                        <span>Renk:</span>
                                        <input type="color" name="wheel_slice_{{ $i }}_color" value="{{ $settings['wheel_slice_'.$i.'_color'] ?? $defaultColors[$i] }}" class="w-7 h-7 rounded-lg cursor-pointer border-0 p-0 shadow-sm">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Çarkta Görünecek Başlık:</label>
                                    <input type="text" name="wheel_slice_{{ $i }}_title" value="{{ $settings['wheel_slice_'.$i.'_title'] ?? $defaultTitles[$i] }}"
                                           class="shadow-sm border border-gray-300 rounded-2xl w-full py-2 px-3.5 text-gray-900 text-xs font-bold focus:ring-amber-500 focus:border-amber-500 bg-white">
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Eşleşen İndirim Kupon Kodu:</label>
                                    <input type="text" name="wheel_slice_{{ $i }}_code" value="{{ $settings['wheel_slice_'.$i.'_code'] ?? $defaultCodes[$i] }}"
                                           class="shadow-sm border border-gray-300 rounded-2xl w-full py-2 px-3.5 text-gray-900 text-xs font-mono font-black uppercase focus:ring-amber-500 focus:border-amber-500 bg-white" placeholder="{{ $defaultCodes[$i] }}">
                                </div>
                            </div>
                        @endfor
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-black text-xs px-6 py-3 rounded-2xl shadow-lg transition-all transform active:scale-95 flex items-center gap-2">
                            <i class="fas fa-save"></i> Çark &amp; Kupon Ayarlarını Kaydet
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</x-app-layout>
