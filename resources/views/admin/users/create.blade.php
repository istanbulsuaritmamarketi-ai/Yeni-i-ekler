<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Yeni Personel Ekle</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Ad Soyad</label>
                        <input type="text" name="name" class="mt-1 block w-full rounded border-gray-300 shadow-sm" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">E-Posta</label>
                        <input type="email" name="email" class="mt-1 block w-full rounded border-gray-300 shadow-sm" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Şifre</label>
                        <input type="password" name="password" class="mt-1 block w-full rounded border-gray-300 shadow-sm" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Yetki Rolü</label>
                        <select name="role" class="mt-1 block w-full rounded border-gray-300 shadow-sm" required>
                            <option value="superadmin">Süper Admin (Tam Yetkili)</option>
                            <option value="editor">Editör (Sadece Ürün, Sipariş, Servis)</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Kaydet</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
