<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <i class="fas fa-user-shield text-emerald-600"></i> KVKK &amp; Çerez Açık Rıza Kayıtları (İspat Kütüğü)
                </h2>
                <p class="text-xs text-gray-500 mt-1">6698 sayılı KVKK kapsamında ziyaretçilerden toplanan yasal çerez izinleri ve zaman damgalı loglar</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.marketing.cookieConsents.export') }}" 
                   class="bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs px-4 py-2.5 rounded-2xl shadow-sm transition-all flex items-center gap-2">
                    <i class="fas fa-file-excel"></i> Rıza Kayıtlarını Excel'e Aktar
                </a>
                <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-800 text-xs font-black px-3.5 py-2.5 rounded-2xl border border-emerald-200 shadow-sm">
                    <i class="fas fa-check-double"></i> KVKK İspat Kütüğü
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- 1. HUKUKİ DAYANAK & MEVZUAT BİLGİLENDİRMESİ -->
            <div class="bg-gradient-to-r from-blue-900 to-indigo-950 text-white rounded-3xl p-6 shadow-md relative overflow-hidden">
                <div class="relative z-10 max-w-3xl">
                    <span class="bg-emerald-500/30 text-emerald-300 text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider border border-emerald-400/30">
                        ⚖️ HUKUKİ GEÇERLİLİK &amp; KANITLAMA
                    </span>
                    <h3 class="text-lg font-black mt-2">6698 Sayılı KVKK Uyarınca Açık Rızanın İspat Yükümlülüğü</h3>
                    <p class="text-xs text-blue-100/80 mt-1 leading-relaxed">
                        KVKK Kurumu'nun Çerez Rehberi gereğince zorunlu olmayan (analitik ve pazarlama) çerezler için açık rıza alınması ve bu rızanın <strong>veri sorumlusu tarafından ispatlanabilir</strong> şekilde kayıt altına alınması şarttır. Aşağıdaki kayıtlar resmi denetimlerde zaman damgası, IP ve onay türü ile yasal delil niteliği taşır.
                    </p>
                </div>
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-cyan-500/10 rounded-full blur-2xl pointer-events-none"></div>
            </div>

            <!-- 2. İSTATİSTİK KARTLARI -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500">Toplam Onay Kaydı</span>
                        <div class="w-9 h-9 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-sm">
                            <i class="fas fa-database"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-gray-900 mt-2">{{ number_format($totalConsents) }}</div>
                    <span class="text-[11px] text-gray-400 mt-0.5 block">Kayıt altına alınan rızalar</span>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500">Tümünü Kabul Edenler</span>
                        <div class="w-9 h-9 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm shadow-sm">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-emerald-600 mt-2">{{ number_format($acceptedCount) }}</div>
                    <span class="text-[11px] text-emerald-600 font-bold mt-0.5 block">
                        %{{ $totalConsents > 0 ? round(($acceptedCount / $totalConsents) * 100, 1) : 0 }} Kabul Oranı
                    </span>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500">Özel Tercih Yapanlar</span>
                        <div class="w-9 h-9 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm shadow-sm">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-amber-600 mt-2">{{ number_format($customCount) }}</div>
                    <span class="text-[11px] text-gray-400 mt-0.5 block">Kategori seçerek onaylayan</span>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500">Yalnızca Zorunlu (Reddeden)</span>
                        <div class="w-9 h-9 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm shadow-sm">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-rose-600 mt-2">{{ number_format($rejectedCount) }}</div>
                    <span class="text-[11px] text-gray-400 mt-0.5 block">Pazarlamayı reddedenler</span>
                </div>
            </div>

            <!-- 3. DETAYLI KANIT LOGLARI TABLOSU -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm">
                
                <div class="flex flex-wrap items-center justify-between gap-3 pb-4 mb-4 border-b border-gray-100">
                    <div>
                        <h3 class="text-base font-black text-gray-900">Zaman Damgalı Rıza Kayıt Listesi</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Her kayıt tarayıcı, onay kategorisi ve zaman damgasıyla eşlenmiştir.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-left">
                        <thead>
                            <tr class="text-[11px] font-black text-gray-400 uppercase tracking-wider">
                                <th class="py-3 px-3">Rıza Token</th>
                                <th class="py-3 px-3">IP Adresi</th>
                                <th class="py-3 px-3">Onay Türü</th>
                                <th class="py-3 px-3">Onaylanan Çerezler</th>
                                <th class="py-3 px-3">Cihaz / Tarayıcı</th>
                                <th class="py-3 px-3 text-right">Zaman Damgası (Tarih &amp; Saat)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs">
                            @forelse($consents as $consent)
                                <tr class="hover:bg-gray-50/60 transition-colors">
                                    <td class="py-3 px-3 font-mono font-bold text-gray-700">
                                        <span class="bg-gray-100 px-2 py-0.5 rounded-md text-[11px]">{{ Str::limit($consent->consent_token, 12) }}</span>
                                    </td>
                                    <td class="py-3 px-3 font-mono font-medium text-gray-600">
                                        {{ $consent->ip_address }}
                                    </td>
                                    <td class="py-3 px-3">
                                        @if($consent->consent_type === 'all')
                                            <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-800 font-bold px-2.5 py-0.5 rounded-full text-[10px]">
                                                <i class="fas fa-check"></i> Tümünü Kabul Etti
                                            </span>
                                        @elseif($consent->consent_type === 'custom')
                                            <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-800 font-bold px-2.5 py-0.5 rounded-full text-[10px]">
                                                <i class="fas fa-sliders-h"></i> Özel Tercih
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 bg-rose-100 text-rose-800 font-bold px-2.5 py-0.5 rounded-full text-[10px]">
                                                <i class="fas fa-ban"></i> Yalnızca Zorunlu
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-gray-100 text-gray-700" title="Zorunlu">Zorunlu</span>
                                            <span class="px-1.5 py-0.2 rounded text-[10px] font-bold {{ $consent->analytics ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}" title="Analitik (Google)">
                                                {{ $consent->analytics ? '✓ Analitik' : '✕ Analitik' }}
                                            </span>
                                            <span class="px-1.5 py-0.2 rounded text-[10px] font-bold {{ $consent->marketing ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}" title="Pazarlama (Meta Pixel)">
                                                {{ $consent->marketing ? '✓ Pazarlama' : '✕ Pazarlama' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3 text-gray-500 max-w-xs truncate" title="{{ $consent->user_agent }}">
                                        {{ Str::limit($consent->user_agent, 40) }}
                                    </td>
                                    <td class="py-3 px-3 text-right text-gray-600 font-mono font-bold whitespace-nowrap">
                                        {{ $consent->created_at->format('d.m.Y H:i:s') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-400 text-xs font-medium">
                                        Henüz kaydedilmiş çerez onay logu bulunmuyor.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $consents->links() }}
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
