<x-frontend-layout 
    :title="$districtName . ' Çiçek Siparişi — Aynı Gün Hızlı Teslimat | Tazecicekler.com'"
    :description="$districtName . ' çiçekçi arayışınızda en taze güller, orkideler, butik buketler ve hediye setleri. ' . $districtName . ' geneline aynı gün teslimat, fotoğraflı onay ve ücretsiz kart notu ile hemen sipariş verin.'"
    :keywords="$districtName . ' çiçekçi, ' . $districtName . ' çiçek siparişi, ' . $districtName . ' gül buketi, ' . $districtName . ' aynı gün çiçek, nöbetçi çiçekçi ' . $districtName">

    <!-- Schema.org LocalBusiness Rich Snippet -->
    @php
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Florist',
            'name' => 'Tazecicekler.com - ' . $districtName . ' Çiçek Siparişi & Butik Çiçekçi',
            'image' => !empty($siteSettings['logo']) ? asset('storage/' . $siteSettings['logo']) : asset('images/logo.png'),
            'telephone' => $siteSettings['phone'] ?? '0850 300 34 34',
            'email' => $siteSettings['email'] ?? 'destek@tazecicekler.com',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $districtName,
                'addressRegion' => 'İstanbul',
                'addressCountry' => 'TR'
            ],
            'areaServed' => $districtName . ', İstanbul',
            'priceRange' => '₺₺',
            'url' => url()->current()
        ];
    @endphp
    <script type="application/ld+json">
    {!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    <!-- Hero / Başlık Alanı (Tek Renk & Sade) -->
    <div class="bg-rose-700 text-white py-10 sm:py-14 border-b border-rose-800">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-12">
            
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs text-rose-200/90 mb-6 font-bold">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">Ana Sayfa</a>
                <i class="fas fa-chevron-right text-[9px] opacity-70"></i>
                <a href="{{ route('districts.index') }}" class="hover:text-white transition-colors">Hizmet Bölgelerimiz</a>
                <i class="fas fa-chevron-right text-[9px] opacity-70"></i>
                <span class="text-white">{{ $districtName }}</span>
            </nav>

            <div class="max-w-3xl">
                <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight mb-4">
                    {{ $districtName }} Çiçek Siparişi &amp; Butik Çiçekçi
                </h1>
                <p class="text-sm sm:text-base text-rose-100/90 leading-relaxed mb-8">
                    {{ $districtName }} ilçesindeki tüm mahallelere usta çiçekçilerimiz tarafından hazırlanan taze güller, orkideler ve aranjmanları <strong>2 saatte ekspres teslimat</strong>, <strong>yola çıkmadan fotoğraflı onay</strong> ve <strong>mühürlü zarfta özel kart notu</strong> ile ulaştırıyoruz.
                </p>

                <div class="flex flex-wrap gap-3">
                    <a href="#cicekler" class="bg-white hover:bg-rose-50 text-rose-700 font-black px-6 py-3.5 rounded-2xl shadow-lg transition-all text-xs sm:text-sm flex items-center gap-2">
                        <i class="fas fa-spa"></i> {{ $districtName }} Çiçeklerini İncele
                    </a>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSettings['whatsapp'] ?? '905013798236') }}?text={{ urlencode('Merhaba, ' . $districtName . ' ilçesi için aynı gün çiçek siparişi vermek istiyorum.') }}" target="_blank" class="bg-emerald-600 hover:bg-emerald-500 text-white font-black px-6 py-3.5 rounded-2xl shadow-lg transition-all text-xs sm:text-sm flex items-center gap-2">
                        <i class="fab fa-whatsapp text-base"></i> WhatsApp Çiçek Danışmanı
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Güven Unsurları Bandı -->
    <div class="bg-white border-b border-rose-100 py-6 sm:py-8">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                <div class="flex items-center gap-2.5 sm:gap-3.5 p-3 sm:p-4 rounded-2xl bg-rose-50/60 border border-rose-100 hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-rose-600 text-white rounded-xl sm:rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md shadow-rose-500/20">
                        <i class="fas fa-bolt text-base sm:text-xl"></i>
                    </div>
                    <div>
                        <div class="text-[11px] sm:text-xs font-black text-slate-900 leading-tight">2 Saatte Teslimat</div>
                        <div class="text-[9px] sm:text-[11px] text-slate-500 mt-0.5 leading-tight">{{ $districtName }} Özel Kurye</div>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 sm:gap-3.5 p-3 sm:p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-600 text-white rounded-xl sm:rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md shadow-emerald-500/20">
                        <i class="fas fa-camera text-base sm:text-xl"></i>
                    </div>
                    <div>
                        <div class="text-[11px] sm:text-xs font-black text-slate-900 leading-tight">Fotoğraflı Onay</div>
                        <div class="text-[9px] sm:text-[11px] text-slate-500 mt-0.5 leading-tight">Yola Çıkmadan Ön Onay</div>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 sm:gap-3.5 p-3 sm:p-4 rounded-2xl bg-pink-50/60 border border-pink-100 hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-pink-600 text-white rounded-xl sm:rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md shadow-pink-500/20">
                        <i class="fas fa-envelope-open-text text-base sm:text-xl"></i>
                    </div>
                    <div>
                        <div class="text-[11px] sm:text-xs font-black text-slate-900 leading-tight">Özel Mühürlü Kart</div>
                        <div class="text-[9px] sm:text-[11px] text-slate-500 mt-0.5 leading-tight">Ücretsiz Duygulu Notlar</div>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 sm:gap-3.5 p-3 sm:p-4 rounded-2xl bg-amber-50/60 border border-amber-100 hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-amber-500 text-white rounded-xl sm:rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md shadow-amber-500/20">
                        <i class="fas fa-certificate text-base sm:text-xl"></i>
                    </div>
                    <div>
                        <div class="text-[11px] sm:text-xs font-black text-slate-900 leading-tight">%100 Tazelik Garantisi</div>
                        <div class="text-[9px] sm:text-[11px] text-slate-500 mt-0.5 leading-tight">A Sınıfı Mezat Çiçekleri</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Öne Çıkan Çiçekler -->
    <div id="cicekler" class="bg-slate-50 py-12 sm:py-16">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-12">
            <div class="text-center mb-8">
                <span class="text-xs font-extrabold text-rose-600 uppercase tracking-widest block mb-1">{{ $districtName }} Hızlı Çiçek Teslimatı</span>
                <h2 class="text-xl sm:text-3xl font-black text-slate-900">{{ $districtName }} İçin En Çok Tercih Edilen Çiçekler</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">İstediğiniz saat aralığında {{ $districtName }} adresine teslim edilmek üzere hazır taze tasarımlar</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 sm:gap-6">
                @foreach($products as $product)
                <div class="bg-white rounded-3xl overflow-hidden border border-rose-100/80 shadow-2xs hover:shadow-xl transition-all duration-300 flex flex-col group relative">
                    
                    <!-- Rozetler -->
                    <div class="absolute top-3 left-3 z-10 flex flex-col gap-1.5 items-start">
                        @if($product->custom_badge)
                            <span class="bg-rose-600 text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow-xs uppercase">
                                {{ $product->custom_badge }}
                            </span>
                        @endif
                        @if($product->discount_percent)
                            <span class="bg-emerald-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-xs">
                                %{{ $product->discount_percent }} İndirim
                            </span>
                        @endif
                    </div>

                    <a href="{{ route('product.show', $product->slug) }}" class="relative aspect-[4/5] bg-rose-50 overflow-hidden block">
                        <img src="{{ $product->image_url }}" 
                             alt="{{ $product->name }} {{ $districtName }}" 
                             loading="lazy" 
                             decoding="async"
                             class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500">
                    </a>

                    <div class="p-3.5 sm:p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-bold text-rose-500 uppercase tracking-wider block">{{ $product->category->name ?? 'Taze Çiçek' }}</span>
                                <div class="flex text-amber-400 text-[10px]">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>

                            <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 line-clamp-2 group-hover:text-rose-600 transition-colors">
                                <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                            </h3>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                @if($product->campaign_price && $product->campaign_price < $product->price)
                                    <div class="text-[10px] text-slate-400 line-through">₺{{ number_format($product->price, 0, ',', '.') }}</div>
                                    <div class="text-base sm:text-lg font-black text-rose-600 leading-tight">₺{{ number_format($product->campaign_price, 0, ',', '.') }}</div>
                                @else
                                    <div class="text-base sm:text-lg font-black text-slate-900 leading-tight">₺{{ number_format($product->price, 0, ',', '.') }}</div>
                                @endif
                            </div>

                            <a href="{{ route('product.show', $product->slug) }}" 
                               class="px-3 py-2 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white rounded-xl text-xs font-black transition-all flex items-center gap-1 shadow-xs">
                                <span>Gönder</span>
                                <i class="fas fa-chevron-right text-[9px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-10 text-center">
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs sm:text-sm px-8 py-3.5 rounded-2xl shadow-lg transition-all transform hover:-translate-y-0.5 active:scale-95">
                    <i class="fas fa-spa"></i> {{ $districtName }} İçin Tüm Çiçek Kataloğunu Görüntüle
                </a>
            </div>
        </div>
    </div>

    <!-- SEO Zengin Bilgilendirme Makalesi (İlçe Bazlı) -->
    <div class="bg-white py-16 border-t border-b border-rose-100">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-12">
            <div class="bg-rose-50/50 rounded-3xl p-8 sm:p-12 border border-rose-100 shadow-xs">
                <div class="max-w-3xl mb-8">
                    <span class="text-xs font-extrabold text-rose-600 uppercase tracking-widest block mb-2">{{ $districtName }} Çiçek Teslimat Rehberi</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 leading-tight">
                        {{ $districtName }} Bölgesinde Çiçek Gönderimi Nasıl Yapılır?
                    </h2>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 text-xs sm:text-sm text-slate-700 leading-relaxed">
                    <div class="space-y-4">
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <i class="fas fa-clock text-rose-600"></i> Aynı Gün ve İleri Tarihli Teslimat Aralıkları
                        </h3>
                        <p>
                            İstanbul'un gözde ilçelerinden biri olan <strong>{{ $districtName }}</strong> geneline, sabah 09:00 ile gece 22:00 saatleri arasında kesintisiz kurye hizmeti sunuyoruz. Sipariş verirken dilediğiniz teslimat saat aralığını (09:00-12:00, 12:00-15:00, 15:00-18:00 veya 18:00-22:00) seçebilirsiniz.
                        </p>
                        <p>
                            Özel iklimlendirmeli teslimat araçlarımız sayesinde çiçekleriniz yazın sıcağından, kışın soğuğundan etkilenmeden taptaze alıcısına teslim edilir.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <i class="fas fa-camera text-emerald-600"></i> Yola Çıkmadan Önce Fotoğraflı Onay Kolaylığı
                        </h3>
                        <p>
                            Hazırlanan buketinizin son halini teslimattan önce görmeniz bizim için çok önemli. Usta çiçekçimiz aranjmanı tamamladığında çiçeğinizin fotoğrafını çeker ve sistemimize yükler. Sipariş takip ekranından çiçeğinizi yola çıkmadan inceleyebilirsiniz.
                        </p>
                        <p>
                            Ayrıca her çiçeğin yanında sunduğumuz mühürlü zarflı kart notu sayesinde duygularınızı en zarif cümlelerle sevdiklerinize aktarabilirsiniz.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Diğer İstanbul İlçeleri (Internal Linking & SEO Cloud) -->
    <div class="bg-slate-50 py-14">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-12">
            <h3 class="text-base font-black text-slate-900 mb-6 text-center">
                İstanbul'un Diğer Tüm İlçelerine de Aynı Gün Çiçek Gönderimi Yapıyoruz:
            </h3>
            <div class="flex flex-wrap justify-center gap-2 max-w-5xl mx-auto">
                @foreach($allDistricts as $dSlug => $dData)
                    <a href="{{ route('district.show', $dSlug) }}" 
                       class="text-xs px-3 py-1.5 rounded-xl border transition-all {{ $dSlug === $slug ? 'bg-rose-600 text-white font-bold border-rose-600 shadow' : 'bg-white text-slate-700 hover:bg-rose-50 hover:text-rose-600 border-slate-200' }}">
                        {{ $dData['name'] }} Çiçekçi
                    </a>
                @endforeach
            </div>
        </div>
    </div>

</x-frontend-layout>
