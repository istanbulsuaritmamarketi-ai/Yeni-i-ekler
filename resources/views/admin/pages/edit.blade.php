<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sayfa Düzenle: {{ $page->title }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('admin.pages.update', $page) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sayfa Başlığı *</label>
                        <input type="text" name="title" value="{{ old('title', $page->title) }}"
                            class="w-full border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <p class="text-xs text-gray-500 mt-1">Mevcut URL: <code class="bg-gray-100 px-1 rounded">/sayfa/{{ $page->slug }}</code></p>
                        @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sayfa İçeriği *</label>
                        <textarea name="content" rows="20"
                            class="w-full border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm" required>{{ old('content', $page->content) }}</textarea>
                        @error('content')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-6">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ $page->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Aktif (Sitede Görünsün)</span>
                        </label>
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded">
                            Değişiklikleri Kaydet
                        </button>
                        <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="text-green-600 hover:underline text-sm">Sayfayı Görüntüle →</a>
                        <a href="{{ route('admin.pages.index') }}" class="text-gray-600 hover:text-gray-900 ml-auto">← Listeye Dön</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>