<x-frontend-layout>
    <x-slot name="title">Kendi Buketini Kendin Oluştur &amp; Tasarla — {{ config('app.name', 'Tazecicekler.com') }}</x-slot>

    <div class="py-6 sm:py-10 bg-slate-50 min-h-screen" x-data="bouquetBuilder()">
        <div class="max-w-[1536px] mx-auto px-3 sm:px-6 lg:px-8 xl:px-12 space-y-8">

            <!-- BAŞLIK & BİLGİ ALANI (SOL ÜSTTE KÜÇÜK VE SADE) -->
            <div class="space-y-1.5 pb-2 border-b border-slate-200/80">
                <nav class="flex items-center gap-1.5 text-[11px] text-slate-400 font-semibold">
                    <a href="{{ route('home') }}" class="hover:text-rose-600 transition">Ana Sayfa</a>
                    <i class="fas fa-chevron-right text-[8px] text-slate-300"></i>
                    <span class="text-slate-600">Kendi Buketini Oluştur</span>
                </nav>
                <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-2">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <i class="fas fa-magic text-rose-600 text-base sm:text-lg"></i>
                            <span>Kendi Çiçek Buketini Kendin Oluştur</span>
                        </h1>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Sevdiğiniz çiçekleri tekli dal olarak seçin, buket ambalaj kağıdı ve kurdele rengini belirleyin.
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 text-[11px] font-bold px-3 py-1 rounded-full border border-blue-200 self-start sm:self-auto">
                        <i class="fas fa-sparkles text-[10px]"></i> Kişiye Özel Atölye Tasarımı
                    </span>
                </div>
            </div>

            <!-- BİLDİRİMLER -->
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
                    <i class="fas fa-exclamation-circle text-red-500 text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('custom-bouquet.add') }}" method="POST" id="custom-bouquet-form">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- SOL ALAN (8 KOLON): ÇİÇEK SEÇİCİ & AMBALAJ / KURDELE -->
                    <div class="lg:col-span-8 space-y-8">
                        
                        <!-- ======================================================== -->
                        <!-- ADIM 1: ÇİÇEKLERİNİ VE ADETLERİNİ SEÇ                    -->
                        <!-- ======================================================== -->
                        <div class="bg-white rounded-3xl p-5 sm:p-8 border border-rose-100 shadow-xs space-y-6">
                            
                            <!-- Adım Başlığı & Sayaç -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-2xl bg-rose-600 text-white flex items-center justify-center text-sm font-black shadow-xs">
                                        1
                                    </span>
                                    <div>
                                        <h2 class="text-base sm:text-lg font-black text-slate-900">Çiçeklerini &amp; Dal Adetlerini Seç</h2>
                                        <p class="text-xs text-slate-500">İstediğiniz çiçeklerden dilediğiniz kadar dal ekleyin</p>
                                    </div>
                                </div>

                                <!-- Minimum Dal Durumu Rozeti -->
                                <div class="flex items-center gap-2 text-xs font-bold">
                                    <span class="text-slate-500">Seçilen Dal:</span>
                                    <span class="px-3 py-1 rounded-full font-black text-xs transition"
                                          :class="totalStems >= minStems ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'"
                                          x-text="totalStems + ' / ' + minStems + ' Dal (Min)'"></span>
                                </div>
                            </div>

                            <!-- Doluluk İlerleme Çubuğu -->
                            <div class="space-y-1.5">
                                <div class="flex justify-between text-[11px] font-bold text-slate-500">
                                    <span x-text="fullnessStatusText"></span>
                                    <span x-text="totalStems >= minStems ? 'Harika! Buket hazır' : (minStems - totalStems) + ' dal daha ekleyin'"></span>
                                </div>
                                <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full transition-all duration-300 rounded-full"
                                         :style="'width: ' + Math.min(100, (totalStems / minStems) * 100) + '%'"
                                         :class="totalStems >= minStems ? 'bg-emerald-500' : 'bg-rose-500'"></div>
                                </div>
                            </div>

                            <!-- ÇİÇEK KARTLARI IZGARASI -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                @if(empty($stems) || count($stems) === 0)
                                    <div class="col-span-full bg-slate-50 rounded-3xl p-10 text-center border border-dashed border-rose-200 space-y-4">
                                        <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mx-auto shadow-inner">
                                            <i class="fas fa-spa"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-black text-base text-slate-900">Atölyemizde Yeni Sezon Çiçekleri Hazırlanıyor</h3>
                                            <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                                                Çiçek ustalarımız günün en taze çiçeklerini atölyeye hazırlıyor. Çok yakında dilediğiniz çiçekleri tek tek seçerek kendi buketinizi oluşturabileceksiniz.
                                            </p>
                                        </div>
                                        @if(auth()->check() && (auth()->user()->is_admin || auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin'))
                                            <div class="pt-2">
                                                <a href="{{ route('admin.bouquet-items.index') }}" 
                                                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl text-xs font-black shadow-sm transition">
                                                    <i class="fas fa-plus"></i>
                                                    <span>Yönetim Paneli: İlk Çiçeklerinizi Ekleyin →</span>
                                                </a>
                                            </div>
                                        @else
                                            <div class="pt-2">
                                                <a href="{{ route('products.index') }}" 
                                                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-black text-white rounded-2xl text-xs font-black shadow-sm transition">
                                                    <i class="fas fa-store"></i>
                                                    <span>Hazır Buketlerimizi İnceleyin →</span>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    @foreach($stems as $stem)
                                        @continue(isset($stem['is_active']) && !$stem['is_active'])
                                        <div class="rounded-2xl border transition-all p-3.5 flex flex-col justify-between bg-white hover:border-rose-300 shadow-2xs relative"
                                             :class="stems['{{ $stem['id'] }}'] > 0 ? 'border-rose-500 ring-2 ring-rose-100 bg-rose-50/20 shadow-xs' : 'border-slate-200'">
                                            
                                            <!-- Çiçek Görseli & Anlamı -->
                                            <div>
                                                <div class="w-full h-32 rounded-xl bg-slate-50 overflow-hidden mb-2.5 relative border border-slate-100">
                                                    <img src="{{ $stem['image'] }}" alt="{{ $stem['name'] }}" class="w-full h-full object-cover">
                                                    <span class="absolute top-2 left-2 text-base bg-white/90 rounded-lg px-1.5 py-0.5 shadow-2xs">
                                                        {{ $stem['icon'] }}
                                                    </span>
                                                    <span class="absolute bottom-2 right-2 text-[10px] font-black bg-slate-900/80 text-white px-2 py-0.5 rounded-md backdrop-blur-xs">
                                                        ₺{{ number_format($stem['price'], 0, ',', '.') }} / Dal
                                                    </span>
                                                </div>

                                                <h3 class="font-extrabold text-xs sm:text-sm text-slate-900 leading-snug">
                                                    {{ $stem['name'] }}
                                                </h3>
                                                <p class="text-[11px] text-slate-400 mt-0.5 italic line-clamp-1">
                                                    {{ $stem['meaning'] }}
                                                </p>
                                            </div>

                                            <!-- Adet Seçici Kontrolleri -->
                                            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between">
                                                <!-- Eksi / Sayı / Artı -->
                                                <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden bg-slate-50">
                                                    <button type="button" 
                                                            @click="decrementStem('{{ $stem['id'] }}')"
                                                            :disabled="!stems['{{ $stem['id'] }}'] || stems['{{ $stem['id'] }}'] <= 0"
                                                            class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-200 disabled:opacity-30 disabled:hover:bg-transparent transition text-xs font-black">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    
                                                    <input type="number" 
                                                           name="stems[{{ $stem['id'] }}]" 
                                                           x-model.number="stems['{{ $stem['id'] }}']"
                                                           min="0" 
                                                           max="99" 
                                                           class="w-10 h-7 text-center text-xs font-black text-slate-900 bg-white border-x border-slate-200 focus:outline-none p-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                                    
                                                    <button type="button" 
                                                            @click="incrementStem('{{ $stem['id'] }}')"
                                                            class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-200 transition text-xs font-black">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>

                                                <!-- Hızlı +3 / +5 Ekle Çipleri -->
                                                <div class="flex gap-1 text-[10px] font-bold">
                                                    <button type="button" @click="addStems('{{ $stem['id'] }}', 3)" class="px-1.5 py-1 rounded bg-slate-100 hover:bg-rose-100 hover:text-rose-700 text-slate-600 transition">
                                                        +3
                                                    </button>
                                                    <button type="button" @click="addStems('{{ $stem['id'] }}', 5)" class="px-1.5 py-1 rounded bg-slate-100 hover:bg-rose-100 hover:text-rose-700 text-slate-600 transition">
                                                        +5
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    @endforeach
                                @endif
                            </div>

                        </div>

                        <!-- ======================================================== -->
                        <!-- ADIM 2: BUKET BOYUTU SEÇİMİ                              -->
                        <!-- ======================================================== -->
                        <div class="bg-white rounded-3xl p-5 sm:p-8 border border-rose-100 shadow-xs space-y-5">
                            
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-2xl bg-rose-600 text-white flex items-center justify-center text-sm font-black shadow-xs">
                                        2
                                    </span>
                                    <div>
                                        <h2 class="text-base sm:text-lg font-black text-slate-900">Buket Boyutunu Seç</h2>
                                        <p class="text-xs text-slate-500">Buketinizin sarım, hacim ve sunum büyüklüğünü belirleyin</p>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-slate-700 hidden sm:inline" x-text="sizeName"></span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                                @foreach($sizes as $size)
                                    <label class="cursor-pointer rounded-2xl border-2 p-4 transition-all flex flex-col justify-between relative group"
                                           :class="selectedSize === '{{ $size['id'] }}' ? 'border-rose-600 ring-2 ring-rose-200 shadow-sm bg-rose-50/20' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                        <input type="radio" name="size" value="{{ $size['id'] }}" x-model="selectedSize" class="hidden">
                                        
                                        <div>
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 group-hover:bg-blue-100 flex items-center justify-center text-base transition">
                                                    @if(str_contains($size['icon'] ?? '', 'fa-'))
                                                        <i class="{{ $size['icon'] }}"></i>
                                                    @elseif(!empty($size['icon']) && !preg_match('/[\x{1F300}-\x{1FAD6}\x{2600}-\x{27BF}]/u', $size['icon']))
                                                        {{ $size['icon'] }}
                                                    @elseif($size['id'] == '1')
                                                        <i class="fas fa-spa"></i>
                                                    @elseif($size['id'] == '2')
                                                        <i class="fas fa-layer-group"></i>
                                                    @elseif($size['id'] == '3')
                                                        <i class="fas fa-crown"></i>
                                                    @else
                                                        <i class="fas fa-seedling"></i>
                                                    @endif
                                                </span>
                                                @if(!empty($size['badge']))
                                                    <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                                        {{ $size['badge'] }}
                                                    </span>
                                                @endif
                                            </div>

                                            <span class="font-extrabold text-sm text-slate-900 block leading-tight">{{ $size['name'] }}</span>
                                            @if(!empty($size['description']))
                                                <span class="text-[11px] text-slate-500 mt-1 block leading-relaxed">{{ $size['description'] }}</span>
                                            @endif
                                        </div>

                                        <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold">
                                            @if((float) $size['price'] > 0)
                                                <span class="text-rose-600 font-black">+₺{{ number_format($size['price'], 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-emerald-600 font-black">Standart (+₺0)</span>
                                            @endif
                                            <i class="fas fa-check-circle text-rose-600 text-base" x-show="selectedSize === '{{ $size['id'] }}'"></i>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                        </div>

                        <!-- ======================================================== -->
                        <!-- ADIM 3: BUKET PAKET & AMBALAJ RENGİ                      -->
                        <!-- ======================================================== -->
                        <div class="bg-white rounded-3xl p-5 sm:p-8 border border-rose-100 shadow-xs space-y-5">
                            
                            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                                <span class="w-8 h-8 rounded-2xl bg-rose-600 text-white flex items-center justify-center text-sm font-black shadow-xs">
                                    3
                                </span>
                                <div>
                                    <h2 class="text-base sm:text-lg font-black text-slate-900">Buket Ambalaj &amp; Paket Rengini Seç</h2>
                                    <p class="text-xs text-slate-500">Buketinizin sarılacağı lüks ambalaj kağıdının rengini belirleyin</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($packageColors as $pkg)
                                    <label class="cursor-pointer rounded-2xl border-2 p-3 transition-all flex flex-col justify-between"
                                           :class="selectedPackage === '{{ $pkg['id'] }}' ? 'border-rose-600 ring-2 ring-rose-200 shadow-sm bg-rose-50/20' : 'border-slate-200 hover:border-slate-300'">
                                        <input type="radio" name="package_color" value="{{ $pkg['id'] }}" x-model="selectedPackage" class="hidden">
                                        
                                        <div>
                                            <div class="w-full h-12 rounded-xl mb-2 flex items-center justify-center shadow-inner {{ $pkg['bg_class'] }}">
                                                <span class="w-5 h-5 rounded-full border-2 border-white shadow-xs" style="background-color: {{ $pkg['color_hex'] }};"></span>
                                            </div>
                                            <span class="font-extrabold text-xs text-slate-900 block">{{ $pkg['name'] }}</span>
                                            <span class="text-[10px] text-slate-400 mt-0.5 line-clamp-1 block">{{ $pkg['desc'] }}</span>
                                        </div>

                                        <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold">
                                            <span class="text-emerald-600">Ücretsiz Dahil</span>
                                            <i class="fas fa-check-circle text-rose-600" x-show="selectedPackage === '{{ $pkg['id'] }}'"></i>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                        </div>

                        <!-- ======================================================== -->
                        <!-- ADIM 4: SÜSLEME KURDELESİ SEÇİMİ                        -->
                        <!-- ======================================================== -->
                        <div class="bg-white rounded-3xl p-5 sm:p-8 border border-rose-100 shadow-xs space-y-5">
                            
                            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                                <span class="w-8 h-8 rounded-2xl bg-rose-600 text-white flex items-center justify-center text-sm font-black shadow-xs">
                                    4
                                </span>
                                <div>
                                    <h2 class="text-base sm:text-lg font-black text-slate-900">Süsleme Kurdelesi</h2>
                                    <p class="text-xs text-slate-500">Buketin beline bağlanacak özel kurdele dokusu ve rengi</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2.5">
                                @foreach($ribbonColors as $ribbon)
                                    <label class="cursor-pointer rounded-2xl border-2 p-2.5 text-center transition-all flex flex-col items-center justify-between"
                                           :class="selectedRibbon === '{{ $ribbon['id'] }}' ? 'border-rose-600 ring-2 ring-rose-200 bg-rose-50/20' : 'border-slate-200 hover:border-slate-300'">
                                        <input type="radio" name="ribbon_color" value="{{ $ribbon['id'] }}" x-model="selectedRibbon" class="hidden">
                                        
                                        <div class="w-7 h-7 rounded-full shadow-xs mb-1.5 border border-slate-300 flex items-center justify-center" style="background-color: {{ $ribbon['color_hex'] }};">
                                            <i class="fas fa-check text-[10px]" :class="'{{ $ribbon['color_hex'] }}' === '#ffffff' ? 'text-slate-800' : 'text-white'" x-show="selectedRibbon === '{{ $ribbon['id'] }}'"></i>
                                        </div>
                                        <span class="text-[11px] font-bold text-slate-800 leading-tight">{{ $ribbon['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>

                        </div>

                        <!-- ======================================================== -->
                        <!-- ADIM 5: ÇİÇEKÇİ USTASINA ÖZEL İSTEK (OPSİYONEL)          -->
                        <!-- ======================================================== -->
                        <div class="bg-white rounded-3xl p-5 sm:p-8 border border-rose-100 shadow-xs space-y-3">
                            <div class="flex items-center gap-3 pb-2 border-b border-slate-100">
                                <span class="w-8 h-8 rounded-2xl bg-rose-600 text-white flex items-center justify-center text-sm font-black shadow-xs">
                                    5
                                </span>
                                <div>
                                    <h2 class="text-base sm:text-lg font-black text-slate-900">Çiçekçi Ustasına Özel Not / İstek (Opsiyonel)</h2>
                                    <p class="text-xs text-slate-500">Buketiniz hazırlanırken atölye ustamızın dikkat etmesini istediğiniz detaylar</p>
                                </div>
                            </div>
                            <textarea name="instructions" rows="2" 
                                      placeholder="Örn: Güllerin gonca olmasına özen gösterilsin, yeşillik ve cipsofilyası bol olsun..."
                                      class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500"></textarea>
                        </div>

                    </div>

                    <!-- SAĞ ALAN (4 KOLON): CANLI BUKET ÖZETİ & SEPETE EKLEME   -->
                    <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-28">
                        
                        <div class="bg-white rounded-3xl p-6 border border-rose-200/80 shadow-xl space-y-5">
                            
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                                    <i class="fas fa-spa text-rose-600"></i>
                                    <span>Buketinizin Özeti</span>
                                </h3>
                                <span class="text-xs font-black px-2.5 py-0.5 rounded-full"
                                      :class="totalStems >= minStems ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'"
                                      x-text="totalStems + ' Dal'"></span>
                            </div>

                            <!-- Seçilen Çiçeklerin Canlı Listesi -->
                            <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                                <template x-if="chosenStemsList.length === 0">
                                    <div class="py-6 text-center text-xs text-slate-400 font-medium">
                                        <i class="fas fa-seedling text-2xl mb-1 text-slate-300 block"></i>
                                        Henüz çiçek eklemediniz.<br>Soldaki çiçeklerden dal seçmeye başlayın.
                                    </div>
                                </template>

                                <template x-for="item in chosenStemsList" :key="item.id">
                                    <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                        <div class="flex items-center gap-2 truncate">
                                            <span x-text="item.icon"></span>
                                            <span class="font-bold text-slate-800 truncate" x-text="item.name"></span>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <span class="font-bold text-slate-500" x-text="item.quantity + 'x'"></span>
                                            <span class="font-black text-rose-600" x-text="'₺' + item.subtotal.toLocaleString('tr-TR')"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Seçilen Boyut, Ambalaj & Kurdele Özeti -->
                            <div class="p-3.5 bg-rose-50/70 border border-rose-100 rounded-2xl text-xs space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500 font-bold">Buket Boyutu:</span>
                                    <div class="text-right">
                                        <span class="font-black text-rose-950 block" x-text="sizeName"></span>
                                        <span class="text-[10px] text-rose-600 font-black" x-show="sizePrice > 0" x-text="'+₺' + sizePrice.toLocaleString('tr-TR')"></span>
                                        <span class="text-[10px] text-emerald-600 font-bold" x-show="sizePrice === 0">Standart (+₺0)</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center pt-1 border-t border-rose-100/60">
                                    <span class="text-slate-500 font-bold">Ambalaj Rengi:</span>
                                    <span class="font-black text-rose-950" x-text="packageName"></span>
                                </div>
                                <div class="flex justify-between items-center pt-1 border-t border-rose-100/60">
                                    <span class="text-slate-500 font-bold">Kurdele:</span>
                                    <span class="font-black text-rose-950" x-text="ribbonName"></span>
                                </div>
                            </div>

                            <!-- Toplam Fiyat ve Buton -->
                            <div class="pt-3 border-t border-slate-100 space-y-4">
                                <div class="flex items-baseline justify-between">
                                    <span class="text-xs font-bold text-slate-500">Toplam Buket Tutarı:</span>
                                    <div class="text-right">
                                        <div class="text-2xl font-black text-rose-600" x-text="'₺' + totalPrice.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></div>
                                        <span class="text-[10px] text-emerald-600 font-bold">KDV Dahil • Ücretsiz Teslimat</span>
                                    </div>
                                </div>

                                <!-- SEPETE EKLE BUTONU (DÜZ MAVİ) -->
                                <button type="submit" 
                                        :disabled="totalStems < minStems"
                                        class="w-full py-4 px-6 rounded-2xl text-white font-black text-sm shadow-lg transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed bg-rose-600 hover:bg-rose-700 shadow-rose-600/30 hover:scale-[1.01] active:scale-98">
                                    <i class="fas fa-shopping-bag"></i>
                                    <span x-text="totalStems >= minStems ? 'BUKETİ SEPETE EKLE' : (minStems - totalStems) + ' DAL DAHA SEÇİNİZ'"></span>
                                </button>

                                <div class="text-center">
                                    <span class="text-[11px] text-slate-400 flex items-center justify-center gap-1.5">
                                        <i class="fas fa-shield-alt text-emerald-500"></i>
                                        <span>Atölyede taze hazırlanır, fotoğraflı teslimat yapılır</span>
                                    </span>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </form>

        </div>
    </div>

    <!-- ALPINE.JS BUKET OLUŞTURUCU VERİ MODELİ -->
    <script>
        function bouquetBuilder() {
            return {
                minStems: {{ $minStems }},
                stemsData: @json(collect($stems)->keyBy('id')),
                sizesData: @json(collect($sizes)->keyBy('id')),
                packageData: @json(collect($packageColors)->keyBy('id')),
                ribbonData: @json(collect($ribbonColors)->keyBy('id')),

                stems: {
                    @foreach($stems as $s)
                        '{{ $s['id'] }}': 0,
                    @endforeach
                },
                selectedSize: '{{ $sizes[0]['id'] ?? '1' }}',
                selectedPackage: '{{ $packageColors[0]['id'] ?? '' }}',
                selectedRibbon: '{{ $ribbonColors[0]['id'] ?? '' }}',

                incrementStem(id) {
                    if (!this.stems[id]) this.stems[id] = 0;
                    this.stems[id]++;
                },

                decrementStem(id) {
                    if (this.stems[id] && this.stems[id] > 0) {
                        this.stems[id]--;
                    }
                },

                addStems(id, count) {
                    if (!this.stems[id]) this.stems[id] = 0;
                    this.stems[id] += count;
                },

                get totalStems() {
                    let total = 0;
                    for (const key in this.stems) {
                        total += (parseInt(this.stems[key]) || 0);
                    }
                    return total;
                },

                get totalPrice() {
                    let sum = 0;
                    for (const key in this.stems) {
                        const qty = parseInt(this.stems[key]) || 0;
                        if (qty > 0 && this.stemsData[key]) {
                            sum += qty * (parseFloat(this.stemsData[key].price) || 0);
                        }
                    }
                    if (this.sizesData[this.selectedSize]) {
                        sum += parseFloat(this.sizesData[this.selectedSize].price) || 0;
                    }
                    if (this.packageData[this.selectedPackage]) {
                        sum += parseFloat(this.packageData[this.selectedPackage].price) || 0;
                    }
                    return sum;
                },

                get fullnessStatusText() {
                    const count = this.totalStems;
                    if (count < this.minStems) return 'Minimum ' + this.minStems + ' dal gereklidir';
                    if (count <= 11) return 'Standart Buket Formu';
                    if (count <= 18) return 'Dolgun & Zengin Buket Formu';
                    return 'İhtişamlı Mega Buket';
                },

                get chosenStemsList() {
                    const list = [];
                    for (const key in this.stems) {
                        const qty = parseInt(this.stems[key]) || 0;
                        if (qty > 0 && this.stemsData[key]) {
                            list.push({
                                id: key,
                                name: this.stemsData[key].name,
                                icon: this.stemsData[key].icon,
                                quantity: qty,
                                subtotal: qty * (parseFloat(this.stemsData[key].price) || 0)
                            });
                        }
                    }
                    return list;
                },

                get sizeName() {
                    return this.sizesData[this.selectedSize] ? this.sizesData[this.selectedSize].name : 'Standart Boy';
                },

                get sizePrice() {
                    return this.sizesData[this.selectedSize] ? (parseFloat(this.sizesData[this.selectedSize].price) || 0) : 0;
                },

                get packageName() {
                    return this.packageData[this.selectedPackage] ? this.packageData[this.selectedPackage].name : 'Standart Ambalaj';
                },

                get ribbonName() {
                    return this.ribbonData[this.selectedRibbon] ? this.ribbonData[this.selectedRibbon].name : 'Standart Kurdele';
                }
            };
        }
    </script>
</x-frontend-layout>
