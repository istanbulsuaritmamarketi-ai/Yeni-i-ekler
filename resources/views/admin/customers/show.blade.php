<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Müşteri Detayı: <span class="text-blue-600">{{ $customer->name }}</span>
            </h2>
            <a href="{{ route('admin.customers.index') }}" class="text-gray-600 hover:text-gray-900 border px-4 py-2 rounded">
                Geri Dön
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Müşteri Bilgileri</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Ad Soyad</p>
                            <p class="font-medium">{{ $customer->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Telefon</p>
                            <p class="font-medium">
                                <a href="tel:{{ $customer->phone }}" class="text-blue-600 hover:underline">{{ $customer->phone }}</a>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Kayıt Tarihi</p>
                            <p class="font-medium">{{ $customer->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Adres / Bölge</p>
                            <p class="font-medium">{{ $customer->region->name ?? 'Bilinmiyor' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Sipariş Geçmişi -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Sipariş Geçmişi</h3>
                        @forelse($orders as $order)
                            <div class="mb-4 p-4 border rounded hover:bg-gray-50">
                                <div class="flex justify-between items-center mb-2">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="font-bold text-blue-600 hover:underline">{{ $order->order_number }}</a>
                                    <span class="text-sm font-medium">{{ number_format($order->total_amount, 2) }} TL</span>
                                </div>
                                <div class="text-sm text-gray-500 mb-2">Tarih: {{ $order->created_at->format('d.m.Y') }} | Durum: {{ $order->status }}</div>
                                <p class="text-sm text-gray-700">Adres: {{ $order->address }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500">Bu müşteriye ait sipariş bulunmuyor.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Servis Geçmişi -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Servis ve Randevu Geçmişi</h3>
                        @forelse($services as $service)
                            <div class="mb-4 p-4 border rounded hover:bg-gray-50">
                                <div class="flex justify-between items-center mb-2">
                                    <a href="{{ route('admin.services.show', $service) }}" class="font-bold text-green-600 hover:underline">{{ $service->service_type }}</a>
                                    <span class="text-sm px-2 py-1 bg-yellow-100 text-yellow-800 rounded">{{ $service->status }}</span>
                                </div>
                                <div class="text-sm text-gray-500 mb-2">Randevu: {{ \Carbon\Carbon::parse($service->preferred_date)->format('d.m.Y') }} ({{ $service->preferred_time }})</div>
                                <p class="text-sm text-gray-700">Adres: {{ $service->address }}</p>
                                @if($service->admin_notes)
                                    <div class="mt-2 text-xs bg-gray-100 p-2 rounded italic">
                                        <span class="font-bold">Not:</span> {{ $service->admin_notes }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500">Bu müşteriye ait servis talebi bulunmuyor.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
