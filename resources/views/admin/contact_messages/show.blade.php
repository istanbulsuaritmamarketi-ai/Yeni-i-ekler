<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mesaj Detayı
            </h2>
            <a href="{{ route('admin.contact-messages.index') }}" class="text-gray-600 border px-4 py-2 rounded">Geri Dön</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 border-b pb-4">
                        <p class="text-sm text-gray-500">Gönderen</p>
                        <p class="font-bold text-lg">{{ $message->name }}</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 border-b pb-4">
                        <div>
                            <p class="text-sm text-gray-500">Telefon</p>
                            <p class="font-medium"><a href="tel:{{ $message->phone }}" class="text-blue-600">{{ $message->phone }}</a></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">E-Posta</p>
                            <p class="font-medium">{{ $message->email ?? '-' }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Mesaj</p>
                        <div class="bg-gray-50 p-4 rounded border text-gray-800 whitespace-pre-wrap">{{ $message->message }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
