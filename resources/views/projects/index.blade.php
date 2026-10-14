<x-frontend-layout title="Projelerimiz & Portföy">
    <div class="bg-gray-50 py-12 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-block px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-theme-light text-theme-primary mb-3">
                Portföy &amp; Başarı Hikayeleri
            </span>
            <h1 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">Projelerimiz</h1>
            <p class="mt-3 text-sm sm:text-base text-gray-600 max-w-2xl mx-auto">
                Tamamlanan ve devam eden projelerimizi inceleyebilir, hayalinizdeki proje için bizimle hemen iletişime geçebilirsiniz.
            </p>
            
            @if($categories->count() > 0)
                <div class="mt-8 flex flex-wrap justify-center gap-2">
                    <a href="{{ route('projects.index') }}" 
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ !request('category') ? 'bg-theme-primary text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200' }}">
                        Tümü
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('projects.index', ['category' => $cat]) }}" 
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('category') === $cat ? 'bg-theme-primary text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200' }}">
                            {{ $cat }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($projects->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($projects as $project)
                    <div class="bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col group">
                        <div class="relative h-64 overflow-hidden bg-gray-100">
                            @if($project->featured_image)
                                <img src="{{ asset('storage/' . $project->featured_image) }}" alt="{{ $project->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 text-gray-400">
                                    <i class="fas fa-building text-4xl"></i>
                                </div>
                            @endif
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 bg-white/90 backdrop-blur-md text-[11px] font-black rounded-full shadow-xs text-gray-800">
                                    {{ $project->status }}
                                </span>
                            </div>
                            @if($project->location)
                                <div class="absolute bottom-4 left-4">
                                    <span class="px-3 py-1 bg-black/60 backdrop-blur-md text-[11px] font-semibold rounded-full text-white flex items-center gap-1">
                                        <i class="fas fa-map-marker-alt text-amber-400"></i> {{ $project->location }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <span class="text-xs font-bold text-theme-primary uppercase tracking-wider">{{ $project->category ?? 'Proje' }}</span>
                                <h3 class="text-xl font-black text-gray-900 mt-1 group-hover:text-theme-primary transition-colors">
                                    <a href="{{ route('projects.show', $project->slug) }}">{{ $project->title }}</a>
                                </h3>
                                <p class="text-gray-600 text-xs sm:text-sm mt-2 line-clamp-3">
                                    {{ $project->summary }}
                                </p>
                            </div>
                            <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                                <a href="{{ route('projects.show', $project->slug) }}" class="text-xs font-black text-theme-primary hover:underline flex items-center gap-1">
                                    Detayları İncele <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                                <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905013798236' }}?text={{ urlencode('Merhaba, ' . $project->title . ' projesi hakkında bilgi ve teklif almak istiyorum.') }}" target="_blank" class="p-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-xl text-xs font-bold transition-all" title="WhatsApp'tan Bilgi Al">
                                    <i class="fab fa-whatsapp text-sm"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8">
                {{ $projects->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-3xl border border-gray-100 shadow-sm max-w-xl mx-auto">
                <div class="w-16 h-16 bg-theme-light text-theme-primary rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fas fa-hammer"></i>
                </div>
                <h3 class="text-lg font-black text-gray-900">Henüz Proje Eklenmedi</h3>
                <p class="text-xs sm:text-sm text-gray-500 mt-1 max-w-sm mx-auto">Yönetim panelinden projelerinizi ekleyerek portföyünüzü burada sergileyebilirsiniz.</p>
                @auth
                    <a href="{{ route('dashboard') }}" class="mt-4 inline-block px-5 py-2.5 bg-theme-primary text-white text-xs font-black rounded-xl shadow-md">
                        Yönetim Paneline Git
                    </a>
                @endauth
            </div>
        @endif
    </div>
</x-frontend-layout>
