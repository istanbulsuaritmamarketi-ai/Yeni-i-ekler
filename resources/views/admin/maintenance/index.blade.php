<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('💧 Filtre Değişim & Bakım Takip Sistemi') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Müşterilerinizin periyodik 6 aylık filtre değişim süreleri ve WhatsApp hatırlatma paneli</p>
            </div>
            <div class="flex items-center gap-2.5">
                <button type="button" @click="$dispatch('open-bulk-modal')" 
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-xl text-xs sm:text-sm flex items-center gap-2 shadow-sm transition-colors">
                    <i class="fab fa-whatsapp text-base"></i> Toplu WhatsApp Hatırlatıcısı
                </button>
                <a href="{{ route('admin.services.create') }}" class="bg-theme-primary hover:bg-theme-primary-hover text-white font-bold py-2 px-4 rounded-xl text-xs sm:text-sm flex items-center gap-2 shadow-sm transition-colors">
                    <i class="fas fa-plus"></i> Yeni Servis / Talep Kaydı
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        showBulkModal: false,
        template: 'Merhaba Sn. {musteri_adi},\n\n{{ config('app.name') }} teknik servisimizden aldığınız hizmetin periyodik bakım ve kontrol zamanı gelmiştir ({tarih}).\n\nUygun bir gün ve saatte yerinde kontrol ve bakım randevusu oluşturmak ister misiniz?',
        getWhatsAppUrl(phone, name, date) {
            let clean = phone.replace(/[^0-9]/g, '');
            if (clean.startsWith('0')) clean = '9' + clean;
            else if (!clean.startsWith('90') && clean.length === 10) clean = '90' + clean;
            
            let msg = this.template.replace('{musteri_adi}', name).replace('{tarih}', date);
            return 'https://wa.me/' + clean + '?text=' + encodeURIComponent(msg);
        }
    }" @open-bulk-modal.window="showBulkModal = true">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 text-green-700 rounded-r shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- İstatistik Kartları -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase">Bu Ay Değişecekler</span>
                        <div class="text-3xl font-extrabold text-blue-600 mt-1">{{ $thisMonthCount }}</div>
                        <p class="text-[11px] text-gray-500">Mevcut ay içinde vadesi gelenler</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase">Gecikenler / Süresi Dolan</span>
                        <div class="text-3xl font-extrabold text-red-600 mt-1">{{ $overdueCount }}</div>
                        <p class="text-[11px] text-gray-500">Vakti geçmiş, aranması gerekenler</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center text-xl">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase">Gelecek Aylar</span>
                        <div class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $upcomingCount }}</div>
                        <p class="text-[11px] text-gray-500">İlerleyen dönemde değişecekler</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>

            <!-- Sekmeler & Arama Çubuğu -->
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                    <a href="{{ route('admin.maintenance.index', ['tab' => 'due_this_month']) }}" class="px-4 py-2 text-xs font-bold rounded-xl transition-all {{ $tab === 'due_this_month' ? 'bg-blue-600 text-white shadow' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                        Bu Ay Değişecekler ({{ $thisMonthCount }})
                    </a>
                    <a href="{{ route('admin.maintenance.index', ['tab' => 'overdue']) }}" class="px-4 py-2 text-xs font-bold rounded-xl transition-all {{ $tab === 'overdue' ? 'bg-red-600 text-white shadow' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                        Gecikenler ({{ $overdueCount }})
                    </a>
                    <a href="{{ route('admin.maintenance.index', ['tab' => 'upcoming']) }}" class="px-4 py-2 text-xs font-bold rounded-xl transition-all {{ $tab === 'upcoming' ? 'bg-emerald-600 text-white shadow' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                        Gelecek Aylar ({{ $upcomingCount }})
                    </a>
                </div>

                <!-- Arama -->
                <form action="{{ route('admin.maintenance.index') }}" method="GET" class="w-full sm:w-72">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Müşteri, telefon veya cihaz..."
                            class="w-full pl-3 pr-9 py-2 text-xs rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                        <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600">
                            <i class="fas fa-search text-xs"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Liste Tablosu -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Müşteri</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">İlçe / Bölge</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Cihaz / İşlem</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Değişim Vadesi</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($records as $service)
                                @php
                                    $dueDate = \Carbon\Carbon::parse($service->next_maintenance_date);
                                    $isOverdue = $dueDate->isPast();
                                    
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $service->customer_phone);
                                    if (str_starts_with($cleanPhone, '0')) {
                                        $cleanPhone = '9' . $cleanPhone;
                                    } elseif (!str_starts_with($cleanPhone, '90') && strlen($cleanPhone) == 10) {
                                        $cleanPhone = '90' . $cleanPhone;
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-sm text-gray-900">{{ $service->customer_name }}</div>
                                        <a href="tel:{{ $service->customer_phone }}" class="text-xs text-blue-600 hover:underline">
                                            {{ $service->customer_phone }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600 font-medium">
                                        {{ $service->region->name ?? 'Belirtilmemiş' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs font-bold text-gray-800">{{ $service->device_info ?: 'Su Arıtma Cihazı' }}</div>
                                        <div class="text-[11px] text-gray-400 line-clamp-1">{{ $service->service_type }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-1 text-xs font-extrabold rounded-lg {{ $isOverdue ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                                {{ $dueDate->format('d.m.Y') }}
                                            </span>
                                            <span class="text-[11px] text-gray-400">
                                                ({{ $dueDate->diffForHumans() }})
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-bold space-x-2">
                                        <!-- WhatsApp Hatırlatma Butonu -->
                                        <a :href="getWhatsAppUrl('{{ $service->customer_phone }}', '{{ addslashes($service->customer_name) }}', '{{ $dueDate->format('d.m.Y') }}')" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm transition-colors" title="Müşteriye WhatsApp Hatırlatması Gönder">
                                            <i class="fab fa-whatsapp"></i> Hatırlatma Gönder
                                        </a>

                                        <!-- +6 Ay Uzat / Yenilendi -->
                                        <form action="{{ route('admin.maintenance.renew', $service) }}" method="POST" class="inline" onsubmit="return confirm('Filtre değişimi yapıldı olarak işaretlenip tarih 6 ay sonraya ertelensin mi?');">
                                            @csrf
                                            <input type="hidden" name="months" value="6">
                                            <button type="submit" class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg transition-colors" title="Filtre Değiştirildi (+6 Ay Uzat)">
                                                <i class="fas fa-check"></i> Değiştirildi (+6 Ay)
                                            </button>
                                        </form>

                                        <a href="{{ route('admin.services.show', $service) }}" class="text-gray-400 hover:text-gray-600 px-2 py-1">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                                        <i class="fas fa-filter text-4xl text-gray-200 mb-3"></i>
                                        <p class="font-medium">Bu kriterlere uygun filtre değişim kaydı bulunamadı.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-100">
                    {{ $records->links() }}
                </div>
            </div>

        </div>

        <!-- 📲 TOPLU WHATSAPP HATIRLATICI MODAL (Alpine.js) -->
        <div x-show="showBulkModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <!-- Backdrop -->
                <div x-show="showBulkModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showBulkModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Dialog -->
                <div x-show="showBulkModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100">
                    
                    <div class="bg-gradient-to-r from-emerald-600 to-teal-700 px-6 py-4 text-white flex justify-between items-center">
                        <h3 class="text-base font-black flex items-center gap-2">
                            <i class="fab fa-whatsapp text-xl"></i> Toplu WhatsApp Hatırlatma Şablonu &amp; Sihirbazı
                        </h3>
                        <button type="button" @click="showBulkModal = false" class="text-white/80 hover:text-white text-lg">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                WhatsApp Mesaj Şablonunuz (Canlı Düzenleyebilirsiniz):
                            </label>
                            <textarea x-model="template" rows="5" class="w-full text-xs rounded-2xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 leading-relaxed"></textarea>
                            <p class="text-[11px] text-gray-400 mt-1">
                                💡 İpucu: <code class="text-emerald-700 bg-emerald-50 px-1 py-0.5 rounded font-bold">{musteri_adi}</code> ve <code class="text-emerald-700 bg-emerald-50 px-1 py-0.5 rounded font-bold">{tarih}</code> etiketleri müşterinin gerçek bilgileriyle otomatik değiştirilir.
                            </p>
                        </div>

                        <div class="border-t pt-4">
                            <h4 class="text-xs font-black text-gray-900 mb-3 uppercase tracking-wider">
                                Bu Sayfadaki Vadesi Gelen Müşteriler (Tek Tıkla Mesaj Gönder):
                            </h4>

                            <div class="max-h-60 overflow-y-auto space-y-2 pr-1">
                                @foreach($records as $service)
                                    @php
                                        $dDate = \Carbon\Carbon::parse($service->next_maintenance_date)->format('d.m.Y');
                                    @endphp
                                    <div class="flex items-center justify-between p-3 bg-gray-50 hover:bg-emerald-50/50 rounded-xl border border-gray-200 transition-colors">
                                        <div>
                                            <div class="text-xs font-bold text-gray-900">{{ $service->customer_name }}</div>
                                            <div class="text-[11px] text-gray-500">{{ $service->customer_phone }} • {{ $service->region->name ?? '' }} • Vade: {{ $dDate }}</div>
                                        </div>
                                        <a :href="getWhatsAppUrl('{{ $service->customer_phone }}', '{{ addslashes($service->customer_name) }}', '{{ $dDate }}')" target="_blank"
                                           class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm transition-colors">
                                            <i class="fab fa-whatsapp"></i> Gönder
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-3.5 border-t border-gray-100 flex justify-end">
                        <button type="button" @click="showBulkModal = false" class="bg-gray-800 hover:bg-gray-900 text-white font-bold px-5 py-2 rounded-xl text-xs">
                            Kapat
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>
</x-app-layout>
