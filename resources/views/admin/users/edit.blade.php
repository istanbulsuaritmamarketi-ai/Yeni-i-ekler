<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Personel Düzenle: {{ $user->name }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Ad Soyad</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="mt-1 block w-full rounded border-gray-300 shadow-sm" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">E-Posta</label>
                        <input type="email" name="email" value="{{ $user->email }}" class="mt-1 block w-full rounded border-gray-300 shadow-sm" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Şifre (Değiştirmek istemiyorsanız boş bırakın)</label>
                        <input type="password" name="password" class="mt-1 block w-full rounded border-gray-300 shadow-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Yetki Rolü</label>
                        <select name="role" class="mt-1 block w-full rounded border-gray-300 shadow-sm" required>
                            <option value="superadmin" {{ $user->role == 'superadmin' ? 'selected' : '' }}>Süper Admin (Tam Yetkili)</option>
                            <option value="editor" {{ $user->role == 'editor' ? 'selected' : '' }}>Editör (Sadece Ürün, Sipariş, Servis)</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Güncelle</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
