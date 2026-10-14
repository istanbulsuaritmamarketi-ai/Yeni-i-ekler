<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('🗑️ Silinen Servis Kayıtları (Geri Dönüşüm Kutusu)') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Silinen servis taleplerini inceleyebilir, geri yükleyebilir veya kalıcı olarak temizleyebilirsiniz</p>
            </div>
            <div class="flex items-center gap-3">
                @if($trashedRequests->count() > 0)
                    <form action="{{ route('admin.services.emptyTrash') }}" method="POST" onsubmit="return confirm('Çöp kutusundaki TÜM silinmiş servis kayıtlarını kalıcı olarak silmek istediğinize emin misiniz? Bu işlem geri alınamaz!');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-xl text-xs flex items-center gap-1.5 shadow-sm transition-colors">
                            <i class="fas fa-trash-alt"></i> Çöp Kutusunu Tamamen Boşalt
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.services.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-xl text-xs flex items-center gap-1.5 transition-colors">
                    <i class="fas fa-arrow-left"></i> Servis Listesine Dön
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
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Müşteri</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Hizmet Türü</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">İlçe / Bölge</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Silinme Tarihi</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($trashedRequests as $req)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-bold text-sm text-gray-900">{{ $req->customer_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $req->customer_phone }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-gray-100 text-gray-700">
                                                {{ $req->service_type }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-700">
                                            {{ $req->region->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                            <span class="font-bold text-red-600">{{ $req->deleted_at->format('d.m.Y H:i') }}</span>
                                            <span class="text-[11px] text-gray-400">({{ $req->deleted_at->diffForHumans() }})</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-bold space-x-2">
                                            <!-- Geri Yükle -->
                                            <form action="{{ route('admin.services.restore', $req->id) }}" method="POST" class="inline" onsubmit="return confirm('Bu servis kaydını geri yüklemek istediğinize emin misiniz?');">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg transition-colors inline-flex items-center gap-1">
                                                    <i class="fas fa-undo"></i> Geri Yükle
                                                </button>
                                            </form>

                                            <!-- Kalıcı Olarak Sil -->
                                            <form action="{{ route('admin.services.forceDelete', $req->id) }}" method="POST" class="inline" onsubmit="return confirm('Bu kaydı KALICI olarak silmek istediğinize emin misiniz? Bu işlem geri alınamaz!');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition-colors inline-flex items-center gap-1">
                                                    <i class="fas fa-times-circle"></i> Kalıcı Sil
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                                            <i class="fas fa-trash text-4xl text-gray-200 mb-3"></i>
                                            <p class="font-medium">Geri dönüşüm kutusu boş. Silinmiş herhangi bir servis kaydı yok.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $trashedRequests->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
