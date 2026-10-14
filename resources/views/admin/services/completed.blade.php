<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('✅ Yapılan İşler (Tamamlanan Servis & Montajlar)') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Başarıyla tamamlanan tüm filtre değişimi, montaj ve teknik servis operasyonları</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.services.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-3.5 rounded-xl text-xs flex items-center gap-1.5 shadow-sm transition-colors">
                    <i class="fas fa-plus"></i> Yeni Servis Kaydı Aç
                </a>
                <a href="{{ route('admin.services.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-3.5 rounded-xl text-xs flex items-center gap-1.5 transition-colors">
                    <i class="fas fa-list"></i> Tüm Servisler
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- İSTATİSTİK KARTLARI -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase">Toplam Yapılan İş</div>
                        <div class="text-2xl font-black text-gray-900 mt-1">{{ $totalCompletedCount }}</div>
                        <p class="text-[11px] text-gray-500 mt-0.5">Tüm zamanlar tamamlanan</p>
                    </div>
                    <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-lg">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase">Toplam Servis Cirosu</div>
                        <div class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($totalCompletedRevenue, 0, ',', '.') }} TL</div>
                        <p class="text-[11px] text-emerald-700 mt-0.5">Tahsil edilen toplam tutar</p>
                    </div>
                    <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-lg">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase">Bu Ay Yapılan İş</div>
                        <div class="text-2xl font-black text-indigo-600 mt-1">{{ $thisMonthJobs }}</div>
                        <p class="text-[11px] text-gray-500 mt-0.5">{{ now()->translatedFormat('F Y') }} dönemi</p>
                    </div>
                    <div class="w-11 h-11 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-lg">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase">Bu Ay Servis Cirosu</div>
                        <div class="text-2xl font-black text-green-600 mt-1">{{ number_format($thisMonthRevenue, 0, ',', '.') }} TL</div>
                        <p class="text-[11px] text-green-700 mt-0.5">Bu ayki servis geliri</p>
                    </div>
                    <div class="w-11 h-11 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-lg">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
            </div>

            <!-- FİLTRELEME & ARAMA FORMU -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                <form action="{{ route('admin.services.completed') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 mb-1">Müşteri / Cihaz Ara</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ad, tel, cihaz..." 
                            class="w-full text-xs rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 mb-1">Teknisyen / Usta</label>
                        <input type="text" name="staff" value="{{ request('staff') }}" placeholder="Usta adı..." 
                            class="w-full text-xs rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 mb-1">İlçe / Bölge</label>
                        <select name="region_id" class="w-full text-xs rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Tüm İlçeler</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 mb-1">İşlem Türü</label>
                        <select name="service_type" class="w-full text-xs rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Tüm İşlem Türleri</option>
                            <option value="Filtre Değişimi" {{ request('service_type') == 'Filtre Değişimi' ? 'selected' : '' }}>Filtre Değişimi</option>
                            <option value="Yeni Kurulum / Montaj" {{ request('service_type') == 'Yeni Kurulum / Montaj' ? 'selected' : '' }}>Yeni Montaj</option>
                            <option value="Arıza Tespiti & Onarım" {{ request('service_type') == 'Arıza Tespiti & Onarım' ? 'selected' : '' }}>Arıza Onarım</option>
                            <option value="Tank / Musluk Değişimi" {{ request('service_type') == 'Tank / Musluk Değişimi' ? 'selected' : '' }}>Tank / Musluk</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-3 rounded-xl text-xs flex items-center justify-center gap-1.5 transition-colors">
                            <i class="fas fa-filter"></i> Filtrele
                        </button>
                        @if(request()->hasAny(['search', 'staff', 'region_id', 'service_type']))
                            <a href="{{ route('admin.services.completed') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold p-2 rounded-xl text-xs" title="Filtreyi Temizle">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- YAPILAN İŞLER TABLOSU -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-extrabold text-gray-900 text-sm flex items-center gap-2">
                        <i class="fas fa-clipboard-check text-emerald-600"></i> Tamamlanan İş Listesi
                    </h3>
                    <span class="text-xs text-gray-500 font-medium">Toplam <strong>{{ $completedJobs->total() }}</strong> tamamlanmış iş</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-xs">
                        <thead class="bg-gray-50/80 text-gray-600 font-bold uppercase">
                            <tr>
                                <th class="px-5 py-3 text-left">Tamamlanma Tarihi</th>
                                <th class="px-5 py-3 text-left">Müşteri</th>
                                <th class="px-5 py-3 text-left">İşlem &amp; Cihaz</th>
                                <th class="px-5 py-3 text-left">Teknisyen / Usta</th>
                                <th class="px-5 py-3 text-left">İlçe</th>
                                <th class="px-5 py-3 text-left">Alınan Ücret</th>
                                <th class="px-5 py-3 text-left">Sonraki Bakım</th>
                                <th class="px-5 py-3 text-right">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($completedJobs as $job)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($job->preferred_date)->format('d.m.Y') }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $job->preferred_time }}</div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900">{{ $job->customer_name }}</div>
                                        <a href="tel:{{ $job->customer_phone }}" class="text-blue-600 hover:underline">{{ $job->customer_phone }}</a>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-block px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 font-bold border border-emerald-100 mb-1">
                                            {{ $job->service_type }}
                                        </span>
                                        <div class="text-[11px] text-gray-500 truncate max-w-xs">{{ $job->device_info ?: 'Cihaz belirtilmemiş' }}</div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-800 flex items-center gap-1.5">
                                            <i class="fas fa-user-cog text-gray-400"></i>
                                            <span>{{ $job->assigned_staff ?: 'Yetkili Ekip' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap font-medium text-gray-700">
                                        {{ $job->region->name ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if($job->service_price && $job->service_price > 0)
                                            <span class="font-black text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                                                {{ number_format($job->service_price, 0, ',', '.') }} TL
                                            </span>
                                        @else
                                            <span class="text-gray-400 font-medium">Girilmedi</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if($job->next_maintenance_date)
                                            <span class="font-bold text-blue-900">
                                                {{ \Carbon\Carbon::parse($job->next_maintenance_date)->format('d.m.Y') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-right space-x-1.5 font-bold">
                                        <!-- Fiş Yazdır -->
                                        <a href="{{ route('admin.services.print', $job) }}" target="_blank" class="p-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors inline-block" title="Servis Fişini Yazdır (A4)">
                                            <i class="fas fa-print"></i>
                                        </a>

                                        <!-- WhatsApp Teşekkür -->
                                        @php
                                            $cleanPhone = preg_replace('/[^0-9]/', '', $job->customer_phone);
                                            if (str_starts_with($cleanPhone, '0')) $cleanPhone = '9' . $cleanPhone;
                                            elseif (!str_starts_with($cleanPhone, '90') && strlen($cleanPhone) == 10) $cleanPhone = '90' . $cleanPhone;
                                            
                                            $thankYouMsg = "Merhaba Sn. " . $job->customer_name . ",\n\nİstanbul Su Arıtma Marketi olarak gerçekleştirdiğimiz " . $job->service_type . " işleminin tamamlandığını bildirmek isteriz. Bizi tercih ettiğiniz için teşekkür eder, sağlıklı ve temiz suyla dolu güzel günler dileriz! 💧\n\nMemnuniyetiniz bizim için çok değerli.";
                                        @endphp
                                        <a href="https://wa.me/{{ $cleanPhone }}?text={{ urlencode($thankYouMsg) }}" target="_blank" 
                                            class="p-1.5 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg transition-colors inline-block" title="Müşteriye WhatsApp Teşekkür Mesajı Gönder">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>

                                        <!-- Detay & Yönet -->
                                        <a href="{{ route('admin.services.show', $job) }}" class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors inline-block">
                                            Detay &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-400 text-sm">
                                        <i class="fas fa-clipboard-check text-4xl text-gray-200 mb-3 block"></i>
                                        <p class="font-medium">Kriterlere uygun tamamlanmış iş kaydı bulunamadı.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-100">
                    {{ $completedJobs->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
