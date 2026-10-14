<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Blog & Makale Yönetimi') }}
            </h2>
            <a href="{{ route('admin.posts.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm flex items-center gap-2">
                <i class="fas fa-plus"></i> Yeni Makale Ekle
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 text-green-700 rounded-r shadow-sm font-bold text-xs">
                    {{ session('success') }}
                </div>
            @endif

            <!-- BLOG ARAMA & FİLTRELEME ÇUBUĞU -->
            <div class="mb-6 bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-gray-100">
                <form action="{{ route('admin.posts.index') }}" method="GET" class="flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto flex-1">
                        
                        <!-- Arama Kutusu -->
                        <div class="relative w-full sm:max-w-md">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fas fa-search text-xs"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Makale başlığı, URL veya içerikte ara..."
                                class="w-full pl-9 pr-4 py-2.5 text-xs font-medium rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-blue-500 bg-gray-50/50">
                        </div>

                        <!-- Yayın Durumu Filtresi -->
                        <div class="w-full sm:w-auto">
                            <select name="status" class="w-full sm:w-auto py-2.5 px-3 text-xs font-bold rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-blue-500 bg-gray-50/50 text-gray-700">
                                <option value="">Tüm Durumlar</option>
                                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Sadece Yayında</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Sadece Taslak</option>
                            </select>
                        </div>

                        <!-- Arama Tuşu -->
                        <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fas fa-search"></i>
                            <span>Ara</span>
                        </button>

                        @if(request('search') || request('status'))
                            <a href="{{ route('admin.posts.index') }}" class="w-full sm:w-auto px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold rounded-xl transition-colors flex items-center justify-center gap-1.5">
                                <i class="fas fa-times-circle text-gray-400"></i>
                                <span>Filtreyi Sıfırla</span>
                            </a>
                        @endif
                    </div>

                    <!-- Toplam Makale Sayısı -->
                    <div class="text-xs text-gray-500 font-bold whitespace-nowrap self-end sm:self-center">
                        Toplam <span class="text-blue-600 font-black">{{ $posts->total() }}</span> makale
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Görsel</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Başlık</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Durum</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Okunma</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tarih</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($posts as $post)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($post->image)
                                                <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="h-12 w-16 object-cover rounded-lg">
                                            @else
                                                <div class="h-12 w-16 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900 line-clamp-1">{{ $post->title }}</div>
                                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="text-xs text-blue-600 hover:underline">
                                                /blog/{{ $post->slug }} <i class="fas fa-external-link-alt text-[10px]"></i>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($post->is_published)
                                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Yayında
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Taslak
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <i class="far fa-eye mr-1"></i> {{ $post->views_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $post->created_at->format('d.m.Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <a href="{{ route('admin.posts.edit', $post) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-md text-xs font-bold">
                                                <i class="fas fa-edit"></i> Düzenle
                                            </a>
                                            <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('Bu makaleyi silmek istediğinize emin misiniz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md text-xs font-bold">
                                                    <i class="fas fa-trash"></i> Sil
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            <i class="fas fa-newspaper text-4xl text-gray-300 mb-3"></i>
                                            <p class="font-medium">Henüz blog makalesi eklenmemiş.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
