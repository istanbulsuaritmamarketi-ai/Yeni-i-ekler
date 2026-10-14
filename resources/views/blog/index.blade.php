<x-frontend-layout 
    title="Su Arıtma Blogu & Filtre Rehberi - İstanbul Su Arıtma Marketi" 
    description="Su arıtma cihazı nasıl seçilir, filtreler ne zaman değişir? Sağlıklı su tüketimi ve teknik servis rehberleri.">
    <!-- Hero / Başlık Bölümü (Tek Renk & Sade) -->
    <div class="bg-rose-700 text-white py-8 sm:py-12 border-b border-rose-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-3">Su Arıtma & Sağlık Rehberi</h1>
            <p class="text-blue-100 text-base sm:text-lg max-w-2xl mx-auto">
                Su arıtma sistemleri, filtre bakımları, sağlıklı su tüketimi ve teknik ipuçları hakkında uzman rehberleri.
            </p>

            <!-- Arama Kutusu & Hızlı Etiketler -->
            <div class="mt-6 max-w-xl mx-auto">
                <form action="{{ route('blog.index') }}" method="GET" class="relative flex items-center bg-white rounded-full shadow-xl p-1.5 border border-blue-200/50">
                    <span class="pl-3.5 pr-2 text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Rehber, filtre değişimi veya makale ara..."
                        class="w-full bg-transparent border-0 text-gray-900 placeholder-gray-400 text-xs sm:text-sm font-medium focus:ring-0 py-2">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-full text-xs font-black transition-all flex items-center gap-1.5 shadow-md flex-shrink-0 cursor-pointer">
                        <i class="fas fa-search text-[11px]"></i>
                        <span>Ara</span>
                    </button>
                </form>

                <!-- Popüler Arama Önerileri -->
                <div class="mt-3 flex items-center justify-center flex-wrap gap-1.5 text-xs text-blue-100">
                    <span class="font-semibold text-blue-200 mr-1"><i class="fas fa-fire-alt text-amber-300"></i> Popüler:</span>
                    <a href="{{ route('blog.index', ['search' => 'filtre']) }}" class="px-2.5 py-0.5 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors">Filtre Değişimi</a>
                    <a href="{{ route('blog.index', ['search' => 'ters ozmoz']) }}" class="px-2.5 py-0.5 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors">Ters Ozmoz</a>
                    <a href="{{ route('blog.index', ['search' => 'alkali']) }}" class="px-2.5 py-0.5 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors">Alkali Su</a>
                    <a href="{{ route('blog.index', ['search' => 'bakım']) }}" class="px-2.5 py-0.5 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors">Periyodik Bakım</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Blog Listesi -->
    <div class="bg-gray-50 py-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(request('search'))
                <div class="mb-6 flex items-center justify-between">
                    <p class="text-gray-600 text-sm">
                        "<strong>{{ request('search') }}</strong>" için arama sonuçları ({{ $posts->total() }} yazı bulundu)
                    </p>
                    <a href="{{ route('blog.index') }}" class="text-blue-600 text-sm font-medium hover:underline flex items-center gap-1">
                        <i class="fas fa-times-circle"></i> Aramayı Temizle
                    </a>
                </div>
            @endif

            @if($posts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($posts as $post)
                        <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all duration-300 flex flex-col group">
                            <a href="{{ route('blog.show', $post->slug) }}" class="block relative aspect-[16/9] overflow-hidden bg-gray-100">
                                @if($post->image)
                                    <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white">
                                        <i class="fas fa-tint text-4xl opacity-50"></i>
                                    </div>
                                @endif
                                <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm text-gray-700 text-xs font-semibold px-2.5 py-1 rounded-full shadow-sm flex items-center gap-1">
                                    <i class="far fa-clock text-blue-600"></i> {{ $post->estimated_reading_time }} dk okuma
                                </div>
                            </a>

                            <div class="p-6 flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="text-xs text-gray-400 mb-2 flex items-center gap-3">
                                        <span><i class="far fa-calendar-alt mr-1"></i> {{ $post->created_at->translatedFormat('d F Y') }}</span>
                                        <span><i class="far fa-eye mr-1"></i> {{ $post->views_count }} okuma</span>
                                    </div>

                                    <h2 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2 mb-2 leading-snug">
                                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                    </h2>

                                    <p class="text-gray-600 text-sm line-clamp-3 mb-4 leading-relaxed">
                                        {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 120) }}
                                    </p>
                                </div>

                                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="text-blue-600 text-sm font-bold inline-flex items-center gap-1 group-hover:gap-2 transition-all">
                                        Devamını Oku <i class="fas fa-arrow-right text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Sayfalama -->
                <div class="mt-10">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="text-center py-16 bg-white rounded-2xl border border-gray-100 p-8 max-w-lg mx-auto shadow-sm">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Henüz Yazı Bulunamadı</h3>
                    <p class="text-gray-500 text-sm mb-6">Aradığınız kriterlere uygun makale bulunamadı veya henüz yazı eklenmedi.</p>
                    <a href="{{ route('blog.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                        Tüm Yazıları Gör
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-frontend-layout>
