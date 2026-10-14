<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    <i class="fas fa-users text-blue-600"></i> {{ __('Müşteri Rehberi') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Kayıtlı müşteriler, sipariş geçmişleri ve servis kayıtları</p>
            </div>
            <a href="{{ route('admin.customers.export') }}" 
               class="bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs px-4 py-2.5 rounded-2xl shadow-sm transition-all flex items-center gap-2">
                <i class="fas fa-file-excel"></i> Müşterileri Excel'e Aktar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-sm text-gray-500 mb-6">Müşteriler, sistemdeki sipariş geçmişlerine ve telefon numaralarına göre otomatik gruplandırılır.</p>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri Adı</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefon</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siparişler</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Servisler</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kayıt Tarihi</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">İşlemler</span></th>
                                    </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($customers as $customer)
                                    <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $customer->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $customer->phone }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $customer->orders_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $customer->service_requests_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $customer->created_at->format('d.m.Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.customers.show', $customer) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 px-3 py-1 rounded">Detay & Geçmiş</a>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Henüz kayıtlı bir müşteri bulunmuyor.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $customers->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
