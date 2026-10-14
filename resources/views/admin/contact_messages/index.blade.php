<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gelen İletişim Mesajları
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gönderen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefon</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($messages as $message)
                                <tr class="{{ $message->is_read ? 'bg-white' : 'bg-blue-50 font-semibold' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($message->is_read)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Okundu</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Yeni</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $message->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $message->phone }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $message->created_at->format('d.m.Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.contact-messages.show', $message) }}" class="text-blue-600 hover:text-blue-900">Oku</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Henüz mesaj bulunmuyor.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $messages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
