<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sistem İşlem Kayıtları</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Personel</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlem Türü</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detay</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Adresi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($logs as $log)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->created_at->format('d.m.Y H:i:s') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $log->user->name ?? 'Sistem' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->action }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $log->details }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400">{{ $log->ip_address }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $logs->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
