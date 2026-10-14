<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Personel / Kullanıcılar</h2>
            <a href="{{ route('admin.users.create') }}" class="bg-theme-primary hover:bg-theme-primary-hover text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow-xs transition">Yeni Personel Ekle</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ad Soyad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-Posta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Yetki / Rol</th>
                            <th class="px-6 py-3 text-right">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-4 font-medium">{{ $user->name }}</td>
                                <td class="px-6 py-4">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->role == 'superadmin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $user->role == 'superadmin' ? 'Süper Admin' : 'Editör' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-900 mr-4">Düzenle</a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Emin misiniz?')">Sil</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $users->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
