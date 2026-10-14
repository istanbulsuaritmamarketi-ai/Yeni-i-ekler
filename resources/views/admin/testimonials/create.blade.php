
<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Yeni Ekle</h2></x-slot>
    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8"><div class="bg-white overflow-hidden shadow-sm sm:rounded-lg"><div class="p-6">
        <form action="{{ route('admin.testimonials.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700">Customer_name</label>
                <input type="text" name="customer_name" class="w-full rounded border-gray-300" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Comment</label>
                <textarea name="comment" class="w-full rounded border-gray-300" rows="4" required></textarea>
            </div>
            <div class="mb-4"><label class="block text-gray-700">Puan (1-5)</label><input type="number" name="rating" value="5" class="w-full rounded border-gray-300" required></div>
            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300">
                    <span class="ml-2">Aktif</span>
                </label>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Kaydet</button>
        </form>
    </div></div></div></div>
</x-app-layout>