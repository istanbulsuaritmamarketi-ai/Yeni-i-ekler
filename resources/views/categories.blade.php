<x-frontend-layout 
    title="Tüm Çiçek Kategorileri &amp; Özel Koleksiyonlar — Tazecicekler.com" 
    description="Güller, orkideler, kutuda çiçekler, doğum günü, yıldönümü ve çelenk modelleri. Türkiye geneli aynı gün hızlı teslimat.">

    <!-- Breadcrumb & Header (Sade & Kompakt) -->
    <div class="bg-rose-700 text-white py-6 sm:py-8 px-4 sm:px-6 lg:px-8 border-b border-rose-800">
        <div class="max-w-[1536px] mx-auto text-center">
            <h1 class="text-xl sm:text-3xl font-black text-white tracking-tight leading-tight mb-1">
                Tüm Çiçek Kategorilerimiz
            </h1>
            <p class="text-xs sm:text-sm text-rose-100 max-w-2xl mx-auto leading-relaxed">
                Her duygu ve her özel an için usta eller tarafından hazırlanan butik aranjmanlar.
            </p>
        </div>
    </div>

    <!-- Kategoriler Listesi -->
    <div class="bg-slate-50 py-10 sm:py-16 min-h-screen">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-12">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                @forelse($categories as $category)
                    <a href="{{ route('category.show', $category->slug) }}" 
                       class="group block p-5 sm:p-7 bg-white border border-rose-100 rounded-3xl shadow-2xs hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 text-center relative overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center p-3 group-hover:scale-105 transition-all shadow-xs overflow-hidden">
                                @if($category->image_url)
                                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" width="64" height="64" loading="lazy" class="w-full h-full object-contain">
                                @else
                                    <i class="fas fa-seedling text-2xl sm:text-3xl text-rose-500 group-hover:scale-110 transition-transform"></i>
                                @endif
                            </div>
                            <h2 class="font-extrabold text-sm sm:text-base text-slate-900 group-hover:text-rose-600 transition-colors">
                                {{ $category->name }}
                            </h2>
                            @if($category->description)
                                <p class="text-[11px] text-slate-400 mt-1 line-clamp-2 leading-relaxed">
                                    {{ $category->description }}
                                </p>
                            @endif
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-slate-500 group-hover:text-rose-600 transition-colors">
                            <span>{{ $category->products_count ?? 0 }} Çiçek</span>
                            <span class="inline-flex items-center gap-1 text-rose-600 font-black">
                                İncele <i class="fas fa-chevron-right text-[9px] group-hover:translate-x-0.5 transition-transform"></i>
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center text-slate-400 py-12">
                        Henüz aktif bir çiçek kategorisi tanımlanmamış.
                    </div>
                @endforelse
            </div>

            <!-- Tüm Ürünler Butonu -->
            <div class="mt-12 text-center">
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs sm:text-sm px-8 py-3.5 rounded-2xl shadow-lg transition-all transform hover:-translate-y-0.5 active:scale-95">
                    <i class="fas fa-spa"></i> Tüm Çiçek Kataloğunu İncele
                </a>
            </div>
        </div>
    </div>
</x-frontend-layout>
