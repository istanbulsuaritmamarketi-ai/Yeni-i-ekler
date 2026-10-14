
<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Düzenle</h2></x-slot>
    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8"><div class="bg-white overflow-hidden shadow-sm sm:rounded-lg"><div class="p-6">
        <form action="{{ route('admin.faqs.update', $faq) }}" method="POST">
            @csrf @method("PUT")
            <div class="mb-4">
                <label class="block text-gray-700">Question</label>
                <input type="text" name="question" value="{{ $faq->question }}" class="w-full rounded border-gray-300" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Answer</label>
                <textarea name="answer" class="w-full rounded border-gray-300" rows="4" required>{{ $faq->answer }}</textarea>
            </div>
            
            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ $faq->is_active ? "checked" : "" }} class="rounded border-gray-300">
                    <span class="ml-2">Aktif</span>
                </label>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Güncelle</button>
        </form>
    </div></div></div></div>
</x-app-layout>