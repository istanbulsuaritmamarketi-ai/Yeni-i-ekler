<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    İl & İlçe Teslimat Yönetimi (81 İl)
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Türkiye genelindeki 81 il ve 974 ilçenin sipariş ve çiçek teslimat durumunu buradan açıp kapatabilirsiniz.
                </p>
            </div>
            
            <!-- Yeni İlçe Ekle Butonu -->
            <button type="button" 
                    onclick="document.getElementById('addDistrictModal').classList.remove('hidden')" 
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-xs transition-colors text-sm self-start sm:self-auto">
                <i class="fas fa-plus"></i> Yeni İlçe Ekle
            </button>
        </div>
    </x-slot>

    <div class="py-8" x-data="regionManager()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 text-xs">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            <!-- 1. İSTATİSTİK KARTLARI -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-2xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-map"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 block uppercase tracking-wider">Toplam İl</span>
                        <span class="text-xl sm:text-2xl font-black text-slate-800">{{ $totalCities }} İl</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-2xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-city"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 block uppercase tracking-wider">Toplam İlçe</span>
                        <span class="text-xl sm:text-2xl font-black text-slate-800">{{ $totalDistricts }} İlçe</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-2xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 block uppercase tracking-wider">Teslimat Açık</span>
                        <span class="text-xl sm:text-2xl font-black text-emerald-600">{{ $activeDistricts }} İlçe</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-2xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 block uppercase tracking-wider">Teslimat Kapalı</span>
                        <span class="text-xl sm:text-2xl font-black text-red-600">{{ $inactiveDistricts }} İlçe</span>
                    </div>
                </div>
            </div>

            <!-- 2. TÜM TÜRKİYE GENELİ HIZLI TOPLU İŞLEM ARAÇ ÇUBUĞU -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-2xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-bolt text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-xs sm:text-sm font-black text-slate-800">Tüm Türkiye Hızlı Ayarları</h3>
                        <p class="text-[11px] text-slate-500">Tek tıkla tüm illeri veya büyükşehirleri açıp kapatabilirsiniz.</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <!-- Tüm Türkiye'yi Aç -->
                    <form action="{{ route('admin.regions.toggleAll') }}" method="POST" onsubmit="return confirm('Tüm Türkiye genelindeki 81 il ve 974 ilçeyi teslimata AÇMAK istediğinize emin misiniz?')">
                        @csrf
                        <input type="hidden" name="action" value="all_active">
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-2xs transition-colors">
                            <i class="fas fa-check-double text-[11px]"></i> Tüm Türkiye'yi Aç
                        </button>
                    </form>

                    <!-- Sadece 30 Büyükşehri Aç -->
                    <form action="{{ route('admin.regions.toggleAll') }}" method="POST" onsubmit="return confirm('30 Büyükşehri AÇIP, diğer 51 ili KAPATMAK istediğinize emin misiniz?')">
                        @csrf
                        <input type="hidden" name="action" value="metropolitan">
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-2xs transition-colors">
                            <i class="fas fa-landmark text-[11px]"></i> 30 Büyükşehri Aç
                        </button>
                    </form>

                    <!-- Tümünü Kapat -->
                    <form action="{{ route('admin.regions.toggleAll') }}" method="POST" onsubmit="return confirm('DİKKAT: Tüm Türkiye genelindeki ilçeleri teslimata KAPATMAK istediğinize emin misiniz?')">
                        @csrf
                        <input type="hidden" name="action" value="all_inactive">
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-red-50 text-red-700 hover:text-red-800 border border-slate-200 text-xs font-bold rounded-xl transition-colors">
                            <i class="fas fa-power-off text-[11px]"></i> Tümünü Kapat
                        </button>
                    </form>
                </div>
            </div>

            <!-- 2.1 HIZLI KURYE / VIP TESLİMAT ÜCRETİ VE AYARLARI -->
            <div class="bg-gradient-to-r from-amber-500/10 via-blue-500/5 to-transparent p-5 sm:p-6 rounded-3xl border border-amber-200/80 shadow-xs bg-white">
                <form action="{{ route('admin.regions.saveExpressSettings') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-amber-200/60">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-lg shadow-sm">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-black text-slate-900 flex items-center gap-2">
                                    Hızlı Kurye &amp; VIP Teslimat Ayarları
                                    <span class="text-[10px] bg-amber-100 text-amber-800 font-extrabold px-2 py-0.5 rounded-full uppercase">Sipariş Ek Ücreti</span>
                                </h3>
                                <p class="text-xs text-slate-500">Müşterinin çiçeğin daha hızlı ulaşmasını istediğinde seçebileceği öncelikli VIP kurye seçeneği ve ek ücreti.</p>
                            </div>
                        </div>

                        <label class="relative inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" name="express_courier_active" value="1" {{ \App\Models\Setting::get('express_courier_active', '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                            <span class="ml-2.5 text-xs font-black text-slate-800">Hızlı Kurye Aktif</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-black text-slate-700 mb-1">Hızlı Kurye Ek Ücreti (₺) *</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-2.5 text-slate-400 font-black text-sm">₺</span>
                                <input type="number" step="1" min="0" name="express_courier_fee" required
                                       value="{{ \App\Models\Setting::get('express_courier_fee', '150') }}"
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-8 pr-3.5 py-2 text-sm font-black text-slate-800 focus:bg-white focus:outline-none focus:border-amber-500">
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">Standart teslimat ücretsizken, hızlı kuryeyi seçenlerden alınacak ek bedel.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-700 mb-1">Kurye Hizmet Başlığı *</label>
                            <input type="text" name="express_courier_title" required
                                   value="{{ \App\Models\Setting::get('express_courier_title', 'VIP Hızlı Kurye (60-90 Dk)') }}"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-amber-500">
                            <p class="text-[10px] text-slate-400 mt-1">Müşteri sipariş ekranında görünen başlık.</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-black text-slate-700 mb-1">Açıklama / Alt Metin</label>
                                <input type="text" name="express_courier_subtitle"
                                       value="{{ \App\Models\Setting::get('express_courier_subtitle', 'Öncelikli atölye hazırlığı & ekspres motorlu kurye') }}"
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-amber-500">
                            </div>
                            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-black px-4 py-2.5 rounded-xl text-xs transition-colors shadow-xs shrink-0 self-end">
                                Kaydet
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- 3. İL SEÇİCİ, ARAMA VE FİLTRELEME ÇUBUĞU -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-2xs space-y-4">
                <form action="{{ route('admin.regions.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    
                    <!-- İl Seçimi (81 İl Dropdown) -->
                    <div class="md:col-span-5">
                        <label class="block text-xs font-black text-slate-700 mb-1.5">
                            İl Seçin (81 İl Listesi)
                        </label>
                        <select name="city" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                            <option value="all" {{ $selectedCity === 'all' ? 'selected' : '' }}>-- Tüm İller (Toplu Görünüm) --</option>
                            @foreach($cityStats as $cityName => $stat)
                                @php
                                    $isFull = $stat->active_count == $stat->total_count;
                                    $isNone = $stat->active_count == 0;
                                    $statusIcon = $isFull ? '🟢' : ($isNone ? '🔴' : '🟡');
                                @endphp
                                <option value="{{ $cityName }}" {{ $selectedCity === $cityName ? 'selected' : '' }}>
                                    {{ $statusIcon }} {{ $cityName }} ({{ $stat->active_count }}/{{ $stat->total_count }} Açık)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- İlçe / İl Arama -->
                    <div class="md:col-span-4">
                        <label class="block text-xs font-black text-slate-700 mb-1.5">
                            İlçe Adı ile Hızlı Ara
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   name="q" 
                                   value="{{ $search }}" 
                                   placeholder="Örn: Kadıköy, Çankaya, Bodrum..." 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                            <i class="fas fa-search absolute left-3 top-3.5 text-slate-400 text-xs"></i>
                        </div>
                    </div>

                    <!-- Durum Filtresi -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-slate-700 mb-1.5">Durum</label>
                        <select name="status" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500">
                            <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Tümü</option>
                            <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Sadece Açık</option>
                            <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Sadece Kapalı</option>
                        </select>
                    </div>

                    <!-- Filtre Butonu -->
                    <div class="md:col-span-1 flex gap-1">
                        <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-2.5 px-3 rounded-xl text-xs transition-colors">
                            Filtrele
                        </button>
                    </div>
                </form>

                <!-- Seçili İl Başlığı ve O İle Özel Toplu Aç/Kapat Butonları -->
                @if($selectedCity && $selectedCity !== 'all' && !$search)
                    @php
                        $currentStat = $cityStats->get($selectedCity);
                        $curActive = $currentStat ? $currentStat->active_count : 0;
                        $curTotal = $currentStat ? $currentStat->total_count : $regions->count();
                    @endphp
                    <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <span class="text-base font-black text-slate-900">{{ $selectedCity }}</span>
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-bold {{ $curActive > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                {{ $curActive }} / {{ $curTotal }} İlçe Açık
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- Bu İlin Tüm İlçelerini Aç -->
                            <form action="{{ route('admin.regions.toggleCity') }}" method="POST">
                                @csrf
                                <input type="hidden" name="city" value="{{ $selectedCity }}">
                                <input type="hidden" name="status" value="1">
                                <button type="submit" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-bold transition-colors">
                                    <i class="fas fa-check text-[10px]"></i> Tüm {{ $selectedCity }} İlçelerini Aç
                                </button>
                            </form>

                            <!-- Bu İlin Tüm İlçelerini Kapat -->
                            <form action="{{ route('admin.regions.toggleCity') }}" method="POST">
                                @csrf
                                <input type="hidden" name="city" value="{{ $selectedCity }}">
                                <input type="hidden" name="status" value="0">
                                <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-lg text-xs font-bold transition-colors">
                                    <i class="fas fa-times text-[10px]"></i> Tümünü Kapat
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <!-- 4. İLÇE LİSTESİ VE CANLI AÇ/KAPAT SWITCH'LERİ -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <h3 class="font-black text-slate-800 text-sm">İlçeler &amp; Teslimat Durumu</h3>
                        <span class="text-xs bg-slate-200 text-slate-700 font-bold px-2 py-0.5 rounded-full">
                            {{ $regions->count() }} Kayıt
                        </span>
                    </div>
                    <span class="text-[11px] text-slate-500 font-medium hidden sm:inline">
                        <i class="fas fa-info-circle text-blue-500"></i> Switch'e tıkladığınızda durum anında kaydedilir.
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-xs font-black text-slate-500 uppercase tracking-wider">İl</th>
                                <th class="px-6 py-3.5 text-left text-xs font-black text-slate-500 uppercase tracking-wider">İlçe Adı</th>
                                <th class="px-6 py-3.5 text-center text-xs font-black text-slate-500 uppercase tracking-wider">Teslimat Durumu</th>
                                <th class="px-6 py-3.5 text-center text-xs font-black text-slate-500 uppercase tracking-wider">Aç / Kapat (Switch)</th>
                                <th class="px-6 py-3.5 text-right text-xs font-black text-slate-500 uppercase tracking-wider">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($regions as $region)
                                <tr id="region-row-{{ $region->id }}" class="hover:bg-slate-50/70 transition-colors">
                                    <td class="px-6 py-3.5 whitespace-nowrap">
                                        <span class="font-bold text-slate-700 text-xs bg-slate-100 px-2.5 py-1 rounded-md">
                                            {{ $region->city ?: 'İstanbul' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 whitespace-nowrap">
                                        <span class="font-black text-slate-900 text-sm">
                                            {{ $region->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 whitespace-nowrap text-center">
                                        <span id="badge-{{ $region->id }}" 
                                              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold {{ $region->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                            <span class="w-2 h-2 rounded-full {{ $region->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            <span class="status-text">{{ $region->is_active ? 'Teslimata Açık' : 'Teslimata Kapalı' }}</span>
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 whitespace-nowrap text-center">
                                        <!-- Canlı AJAX Toggle Switch -->
                                        <label class="relative inline-flex items-center cursor-pointer select-none">
                                            <input type="checkbox" 
                                                   id="toggle-{{ $region->id }}"
                                                   {{ $region->is_active ? 'checked' : '' }} 
                                                   @change="toggleRegion({{ $region->id }})"
                                                   class="sr-only peer">
                                            <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                                        </label>
                                    </td>
                                    <td class="px-6 py-3.5 whitespace-nowrap text-right">
                                        <form action="{{ route('admin.regions.destroy', $region) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ $region->name }} ilçesini tamamen silmek istediğinize emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 p-1.5 font-bold">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                        <i class="fas fa-map-marker-alt text-3xl mb-2 block text-slate-300"></i>
                                        Aradığınız kriterlere uygun ilçe bulunamadı.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- 5. YENİ İLÇE EKLE MODALI -->
        <div id="addDistrictModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-black text-slate-900">Yeni İlçe / Semt Ekle</h3>
                    <button type="button" onclick="document.getElementById('addDistrictModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ route('admin.regions.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">İl Adı *</label>
                        <select name="city" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
                            @foreach($cityStats->keys() as $cName)
                                <option value="{{ $cName }}" {{ $selectedCity === $cName ? 'selected' : '' }}>{{ $cName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">İlçe / Semt Adı *</label>
                        <input type="text" name="name" required placeholder="Örn: Maslak veya Yeni İlçe" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" onclick="document.getElementById('addDistrictModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">
                            Vazgeç
                        </button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-xs">
                            Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bildirim Tostu (Toast) -->
        <div id="toastNotification" class="fixed bottom-6 right-6 z-50 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
            <div class="bg-slate-900 text-white px-4 py-2.5 rounded-xl shadow-xl text-xs font-bold flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-400"></i>
                <span id="toastMessage">Durum güncellendi.</span>
            </div>
        </div>
    </div>

    <script>
        function regionManager() {
            return {
                async toggleRegion(id) {
                    const checkbox = document.getElementById('toggle-' + id);
                    const badge = document.getElementById('badge-' + id);
                    const isChecked = checkbox.checked;

                    try {
                        const response = await fetch(`/admin/regions/${id}/toggle`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ status: isChecked })
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Badge güncelle
                            if (data.is_active) {
                                badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800';
                                badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500"></span><span class="status-text">Teslimata Açık</span>';
                            } else {
                                badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-slate-100 text-slate-600';
                                badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-slate-400"></span><span class="status-text">Teslimata Kapalı</span>';
                            }

                            // Tost bildirimi göster
                            showToast(data.message);
                        } else {
                            checkbox.checked = !isChecked;
                            showToast('Güncelleme yapılamadı!', true);
                        }
                    } catch (error) {
                        checkbox.checked = !isChecked;
                        showToast('Bir bağlantı hatası oluştu!', true);
                    }
                }
            }
        }

        function showToast(msg, isError = false) {
            const toast = document.getElementById('toastNotification');
            const toastMsg = document.getElementById('toastMessage');
            toastMsg.innerText = msg;
            
            const icon = toast.querySelector('i');
            if (isError) {
                icon.className = 'fas fa-exclamation-circle text-red-400';
            } else {
                icon.className = 'fas fa-check-circle text-emerald-400';
            }

            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');

            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 2500);
        }
    </script>
</x-app-layout>
