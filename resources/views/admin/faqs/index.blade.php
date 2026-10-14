
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __("Sık Sorulan Sorular") }}</h2>
            <a href="{{ route('admin.faqs.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Yeni Ekle</a>
        </div>
    </x-slot>
    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8"><div class="bg-white overflow-hidden shadow-sm sm:rounded-lg"><div class="p-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detay</th><th class="px-6 py-3"></th></tr></thead>
            <tbody>
                @foreach($faqs as $item)
                    <tr>
                        <td class="px-6 py-4">{{ $item->question }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.faqs.edit', $item) }}" class="text-blue-600 hover:text-blue-900 mr-3">Düzenle</a>
                            <form action="{{ route('admin.faqs.destroy', $item) }}" method="POST" class="inline">
                                @csrf @method("DELETE")
                                <button type="submit" class="text-red-600 hover:text-red-900">Sil</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div></div></div></div>
</x-app-layout>