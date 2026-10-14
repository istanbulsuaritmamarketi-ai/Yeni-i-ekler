@php
    $totalCount = count($districts);
    $anadoluDistricts = array_filter($districts, fn($d) => ($d['side'] ?? '') === 'Anadolu');
    $avrupaDistricts = array_filter($districts, fn($d) => ($d['side'] ?? '') === 'Avrupa');
    $appName = config('app.name', 'Tazecicekler.com');
@endphp
<x-frontend-layout :title="'İstanbul Çiçek Teslimat Bölgelerimiz (' . $totalCount . ' İlçe) — ' . $appName" description="İstanbul genelinde 39 ilçeye aynı gün 2 saatte teslimat, fotoğraflı onay ve özel kart notuyla çiçek gönderin.">
    <!-- Breadcrumb & Başlık (Tek Renk & Sade) -->
    <div class="bg-rose-700 text-white py-8 sm:py-12 px-4 sm:px-6 lg:px-8 border-b border-rose-800">
        <div class="max-w-[1536px] mx-auto text-center">
            <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight leading-tight mb-2">
                İstanbul'un {{ $totalCount }} İlçesinde Aynı Gün Hizmetinizdeyiz
            </h1>
            <p class="text-xs sm:text-sm text-rose-100 max-w-2xl mx-auto leading-relaxed">
                Çiçek göndermek istediğiniz ilçeyi seçerek özel teslimat saatlerini ve popüler tasarımları keşfedin.
            </p>
        </div>
    </div>

    <!-- İlçe Listeleri -->
    <div class="bg-slate-50 py-10 sm:py-14 min-h-screen">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-12 space-y-12">
            
            <!-- Anadolu Yakası -->
            @if(count($anadoluDistricts) > 0)
            <div>
                <div class="flex items-center gap-3 mb-5 border-b border-rose-100 pb-3">
                    <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-sm font-black">
                        <i class="fas fa-compass"></i>
                    </span>
                    <h2 class="text-base sm:text-lg font-black text-slate-900">
                        Anadolu Yakası ({{ count($anadoluDistricts) }} İlçe)
                    </h2>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-3">
                    @foreach($anadoluDistricts as $dSlug => $dData)
                        <a href="{{ route('district.show', $dSlug) }}" 
                           class="p-3.5 rounded-2xl bg-white border border-rose-100 hover:border-rose-500 hover:bg-rose-50/50 transition-all text-center group shadow-2xs transform hover:-translate-y-0.5 active:scale-95 flex flex-col items-center justify-center">
                            <i class="fas fa-spa text-rose-500 group-hover:scale-110 transition-transform mb-1 text-base"></i>
                            <span class="text-xs font-black text-slate-800 group-hover:text-rose-600">{{ $dData['name'] }}</span>
                            <span class="text-[10px] text-emerald-600 font-bold mt-0.5">2 Saatte Teslim</span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Avrupa Yakası -->
            @if(count($avrupaDistricts) > 0)
            <div>
                <div class="flex items-center gap-3 mb-5 border-b border-rose-100 pb-3">
                    <span class="w-8 h-8 rounded-xl bg-pink-100 text-pink-600 flex items-center justify-center text-sm font-black">
                        <i class="fas fa-compass"></i>
                    </span>
                    <h2 class="text-base sm:text-lg font-black text-slate-900">
                        Avrupa Yakası ({{ count($avrupaDistricts) }} İlçe)
                    </h2>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-3">
                    @foreach($avrupaDistricts as $dSlug => $dData)
                        <a href="{{ route('district.show', $dSlug) }}" 
                           class="p-3.5 rounded-2xl bg-white border border-rose-100 hover:border-rose-500 hover:bg-rose-50/50 transition-all text-center group shadow-2xs transform hover:-translate-y-0.5 active:scale-95 flex flex-col items-center justify-center">
                            <i class="fas fa-spa text-pink-500 group-hover:scale-110 transition-transform mb-1 text-base"></i>
                            <span class="text-xs font-black text-slate-800 group-hover:text-rose-600">{{ $dData['name'] }}</span>
                            <span class="text-[10px] text-emerald-600 font-bold mt-0.5">2 Saatte Teslim</span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Alt Bilgi / Çağrı Bandı -->
            <div class="p-6 sm:p-8 rounded-3xl bg-slate-900 text-white flex flex-col sm:flex-row items-center justify-between gap-6 shadow-xl border border-slate-800">
                <div>
                    <h3 class="text-base sm:text-lg font-black text-white">Özel Bir Adres veya Toplu Sipariş mi Planlıyorsunuz?</h3>
                    <p class="text-xs text-slate-300 mt-1">Düğün, nişan, açılış çelenkleri ve kurumsal etkinlikler için çiçek danışmanımızla doğrudan iletişime geçebilirsiniz.</p>
                </div>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSettings['whatsapp'] ?? '905013798236') }}?text={{ urlencode('Merhaba, özel çiçek siparişi ve etkinlik teslimatı hakkında bilgi almak istiyorum.') }}" target="_blank" class="bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs sm:text-sm px-6 py-3.5 rounded-2xl shadow transition-all transform active:scale-95 flex items-center gap-2 shrink-0">
                    <i class="fab fa-whatsapp text-lg"></i>
                    <span>Çiçek Danışmanına Yazın</span>
                </a>
            </div>

        </div>
    </div>
</x-frontend-layout>
