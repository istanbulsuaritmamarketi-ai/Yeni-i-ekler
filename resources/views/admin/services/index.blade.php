<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    <i class="fas fa-file-signature text-theme-primary"></i> {{ __('Teklif, Keşif & Servis Talepleri') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Müşteri talep formları, teklif istekleri ve randevu durumları</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.services.export') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-black py-2 px-3.5 rounded-xl text-xs flex items-center gap-1.5 shadow-sm transition-colors">
                    <i class="fas fa-file-excel"></i> Excel'e Aktar
                </a>
                <a href="{{ route('admin.services.trash') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-3 rounded-xl text-xs flex items-center gap-1.5 transition-colors">
                    <i class="fas fa-trash-alt text-gray-500"></i> Çöp ({{ $trashedCount ?? 0 }})
                </a>
                <a href="{{ route('admin.services.create') }}" class="bg-theme-primary hover:bg-theme-primary-hover text-white font-bold py-2 px-3.5 rounded-xl text-xs flex items-center gap-2 shadow-sm transition-colors">
                    <i class="fas fa-plus"></i> Yeni Kayıt
                </a>
                <a href="{{ route('admin.services.calendar') }}" class="bg-slate-900 hover:bg-slate-800 text-white font-bold py-2 px-3.5 rounded-xl text-xs flex items-center gap-2 shadow-sm transition-colors">
                    <i class="fas fa-calendar-alt"></i> Takvim
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 text-green-700 rounded-r shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 text-gray-900">
                    
                    <!-- Tabs -->
                    <div class="mb-6 border-b border-gray-100">
                        <nav class="-mb-px flex flex-wrap gap-2 sm:gap-6">
                            <a href="{{ route('admin.services.index') }}" class="{{ (!isset($tab) || $tab == 'all') ? 'border-blue-600 text-blue-600 font-bold bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-2 px-3 rounded-xl border-b-2 text-xs font-semibold transition-all">
                                Tüm Talepler
                            </a>
                            <a href="{{ route('admin.services.index', ['tab' => 'today']) }}" class="{{ (isset($tab) && $tab == 'today') ? 'border-blue-600 text-blue-600 font-bold bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-2 px-3 rounded-xl border-b-2 text-xs font-semibold transition-all">
                                <i class="fas fa-calendar-day mr-1"></i> Bugünkü Randevular
                            </a>
                            <a href="{{ route('admin.services.index', ['tab' => 'pending']) }}" class="{{ (isset($tab) && $tab == 'pending') ? 'border-amber-600 text-amber-700 font-bold bg-amber-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-2 px-3 rounded-xl border-b-2 text-xs font-semibold transition-all">
                                <i class="fas fa-clock mr-1"></i> Bekleyen &amp; Aktifler
                            </a>
                            <a href="{{ route('admin.services.completed') }}" class="border-transparent text-gray-500 hover:text-emerald-700 hover:border-emerald-300 py-2 px-3 rounded-xl border-b-2 text-xs font-semibold transition-all">
                                <i class="fas fa-clipboard-check mr-1 text-emerald-600"></i> Yapılan İşler (Tamamlananlar)
                            </a>
                            <a href="{{ route('admin.services.index', ['tab' => 'cancelled']) }}" class="{{ (isset($tab) && $tab == 'cancelled') ? 'border-rose-600 text-rose-700 font-bold bg-rose-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-2 px-3 rounded-xl border-b-2 text-xs font-semibold transition-all">
                                İptaller
                            </a>
                        </nav>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tarih & Saat</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Müşteri</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Hizmet Türü</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Bölge / İlçe</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Ücret</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Durum</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($requests as $req)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-bold text-sm text-gray-900">{{ \Carbon\Carbon::parse($req->preferred_date)->format('d.m.Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $req->preferred_time }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-bold text-sm text-gray-900">{{ $req->customer_name }}</div>
                                            <a href="tel:{{ $req->customer_phone }}" class="text-xs text-blue-600 hover:underline">{{ $req->customer_phone }}</a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-blue-50 text-blue-700 border border-blue-100">
                                                {{ $req->service_type }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-700 font-medium">
                                            {{ $req->region->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs">
                                            @if($req->service_price && $req->service_price > 0)
                                                <span class="font-extrabold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200">
                                                    {{ number_format($req->service_price, 0, ',', '.') }} TL
                                                </span>
                                            @else
                                                <span class="text-gray-400 font-medium">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColor = match($req->status) {
                                                    'Bekliyor' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                    'Randevu Verildi', 'Randevu Onaylandı' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                    'Teknisyen Yolda' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                    'Tamamlandı' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                    'İptal Edildi' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                    default => 'bg-gray-50 text-gray-700 border-gray-200'
                                                };
                                            @endphp
                                            <span class="px-2.5 py-1 text-xs font-bold rounded-lg border {{ $statusColor }}">
                                                {{ $req->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-bold space-x-2">
                                            <a href="{{ route('admin.services.show', $req) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                                                <i class="fas fa-eye"></i> Detay &amp; Yönet
                                            </a>
                                            <form action="{{ route('admin.services.destroy', $req) }}" method="POST" class="inline" onsubmit="return confirm('Bu servis kaydını silmek istediğinize emin misiniz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors" title="Servis Kaydını Sil">
                                                    <i class="fas fa-trash-alt"></i> Sil
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                                            <i class="fas fa-tools text-4xl text-gray-300 mb-3"></i>
                                            <p class="font-medium">Henüz servis veya randevu talebi yok.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $requests->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
