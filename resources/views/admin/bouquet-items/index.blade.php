<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2.5">
                    <span class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-lg shadow-sm">
                        <i class="fas fa-magic"></i>
                    </span>
                    <span>Buket Malzemeleri &amp; Sihirbaz Yönetimi</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    "Kendi Buketini Oluştur" sihirbazında müşteriye sunulan tekli dal çiçekleri, ambalaj kağıtlarını ve kurdeleleri yönetin.
                </p>
            </div>
            
            <a href="{{ route('custom-bouquet.index') }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-2xl text-xs font-black border border-blue-200 transition shadow-xs">
                <i class="fas fa-external-link-alt"></i>
                <span>Canlı Buket Sihirbazını Aç</span>
            </a>
        </div>
    </x-slot>

    @php
        $activeTab = request('tab', 'stems');
    @endphp

    <div class="py-6" x-data="{ 
        tab: '{{ $activeTab }}',
        stemModal: false,
        editingStem: null,
        sizeModal: false,
        editingSize: null,
        packageModal: false,
        editingPackage: null,
        ribbonModal: false,
        editingRibbon: null,

        openStemModal(stem = null) {
            this.editingStem = stem;
            this.stemModal = true;
        },
        openSizeModal(sz = null) {
            this.editingSize = sz;
            this.sizeModal = true;
        },
        openPackageModal(pkg = null) {
            this.editingPackage = pkg;
            this.packageModal = true;
        },
        openRibbonModal(rib = null) {
            this.editingRibbon = rib;
            this.ribbonModal = true;
        }
    }">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl text-xs font-bold flex items-center gap-3 shadow-2xs">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- 📸 GÖRSEL VE PİKSEL ÖNERİSİ BİLGİLENDİRME BANDI -->
            <div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 rounded-3xl p-5 border border-blue-200/80 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-start sm:items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-xl shrink-0 shadow-xs">
                        <i class="fas fa-camera"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-sm text-slate-900 flex items-center gap-2">
                            <span>Önerilen Görsel Boyutu: <strong>800 × 800 Piksel (1:1 Kare)</strong></span>
                            <span class="text-[10px] font-black bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full uppercase">Önemli İpucu</span>
                        </h4>
                        <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                            Çiçek veya kurdelelerinizin fotoğraflarını kare (800x800 px) olarak yüklerseniz, buket sihirbazında ve cep telefonlarında pürüzsüz ve profesyonel görünür. Sistem yüklediğiniz görselleri otomatik olarak optimize eder.
                        </p>
                    </div>
                </div>
            </div>

            <!-- TAB GEZİNME BARİ -->
            <div class="bg-white rounded-3xl p-2 border border-slate-200 shadow-xs">
                <nav class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                    <button type="button" @click="tab = 'stems'"
                            class="py-3 px-4 rounded-2xl font-black text-xs transition flex items-center justify-center gap-2"
                            :class="tab === 'stems' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'">
                        <i class="fas fa-seedling"></i>
                        <span>1. Tekli Dal Çiçekler</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px]"
                              :class="tab === 'stems' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-700'">
                            {{ $stems->count() }}
                        </span>
                    </button>

                    <button type="button" @click="tab = 'sizes'"
                            class="py-3 px-4 rounded-2xl font-black text-xs transition flex items-center justify-center gap-2"
                            :class="tab === 'sizes' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'">
                        <i class="fas fa-layer-group"></i>
                        <span>2. Buket Boyutları</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px]"
                              :class="tab === 'sizes' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-700'">
                            {{ $sizes->count() }}
                        </span>
                    </button>

                    <button type="button" @click="tab = 'packages'"
                            class="py-3 px-4 rounded-2xl font-black text-xs transition flex items-center justify-center gap-2"
                            :class="tab === 'packages' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'">
                        <i class="fas fa-gift"></i>
                        <span>3. Ambalaj Kağıtları</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px]"
                              :class="tab === 'packages' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-700'">
                            {{ $packages->count() }}
                        </span>
                    </button>

                    <button type="button" @click="tab = 'ribbons'"
                            class="py-3 px-4 rounded-2xl font-black text-xs transition flex items-center justify-center gap-2"
                            :class="tab === 'ribbons' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'">
                        <i class="fas fa-ribbon"></i>
                        <span>4. Süsleme Kurdeleleri</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px]"
                              :class="tab === 'ribbons' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-700'">
                            {{ $ribbons->count() }}
                        </span>
                    </button>

                    <button type="button" @click="tab = 'settings'"
                            class="py-3 px-4 rounded-2xl font-black text-xs transition flex items-center justify-center gap-2"
                            :class="tab === 'settings' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'">
                        <i class="fas fa-sliders-h"></i>
                        <span>5. Genel Kurallar</span>
                    </button>
                </nav>
            </div>

            <!-- ============================================================== -->
            <!-- TAB 1: 🌸 TEKLİ DAL ÇİÇEKLER (STEMS) -->
            <!-- ============================================================== -->
            <div x-show="tab === 'stems'" class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="font-black text-base text-slate-900">Tekli Dal Çiçek Listesi</h3>
                        <p class="text-xs text-slate-500">Müşterinin adedini seçebildiği çiçek türleri ve dal fiyatları</p>
                    </div>

                    <button type="button" @click="openStemModal()"
                            class="py-3 px-5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-xs flex items-center justify-center gap-2 shadow-sm transition active:scale-98">
                        <i class="fas fa-plus"></i>
                        <span>Yeni Tekli Çiçek Ekle</span>
                    </button>
                </div>

                @if($stems->isEmpty())
                    <div class="bg-white rounded-3xl p-12 text-center border border-dashed border-slate-300 space-y-4">
                        <div class="w-20 h-20 rounded-3xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mx-auto shadow-inner">
                            <i class="fas fa-spa"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-base text-slate-900">Henüz Çiçek Eklenmedi</h4>
                            <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                                Çiçek listeniz şu an sıfır. Yukarıdaki "Yeni Tekli Çiçek Ekle" butonuna basarak ilk dal çiçeğinizi, fotoğrafını ve dal fiyatını ekleyin.
                            </p>
                        </div>
                        <button type="button" @click="openStemModal()"
                                class="py-2.5 px-6 bg-slate-900 hover:bg-black text-white rounded-2xl font-black text-xs inline-flex items-center gap-2 shadow-xs transition">
                            <i class="fas fa-plus"></i>
                            <span>İlk Çiçeği Ekle</span>
                        </button>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($stems as $stem)
                            <div class="bg-white rounded-3xl p-4 border border-slate-200 shadow-2xs hover:shadow-sm transition flex flex-col justify-between space-y-3 relative">
                                <div>
                                    <!-- Çiçek Görseli & İkonu -->
                                    <div class="w-full h-40 rounded-2xl bg-slate-100 overflow-hidden relative border border-slate-100">
                                        <img src="{{ $stem->image_url }}" alt="{{ $stem->name }}" class="w-full h-full object-cover">
                                        <span class="absolute top-2 left-2 text-base bg-white/90 backdrop-blur-xs rounded-xl px-2 py-0.5 shadow-2xs">
                                            {{ $stem->icon }}
                                        </span>
                                        <span class="absolute bottom-2 right-2 text-xs font-black bg-slate-950/80 text-white px-2.5 py-1 rounded-xl backdrop-blur-xs">
                                            ₺{{ number_format($stem->price, 2, ',', '.') }} / Dal
                                        </span>
                                    </div>

                                    <div class="mt-3">
                                        <h4 class="font-black text-sm text-slate-900 leading-snug">{{ $stem->name }}</h4>
                                        <p class="text-[11px] text-slate-500 italic mt-0.5 line-clamp-1">{{ $stem->meaning ?: 'Taze Dal Çiçek' }}</p>
                                    </div>
                                </div>

                                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                    <!-- Aktif/Pasif Toggle -->
                                    <button type="button" 
                                            onclick="toggleStem({{ $stem->id }})"
                                            id="stem-btn-{{ $stem->id }}"
                                            class="px-2.5 py-1 rounded-xl font-black text-[11px] transition flex items-center gap-1 {{ $stem->is_active ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                        <i class="fas {{ $stem->is_active ? 'fa-check-circle text-emerald-600' : 'fa-times-circle text-slate-400' }}"></i>
                                        <span id="stem-text-{{ $stem->id }}">{{ $stem->is_active ? 'Aktif' : 'Pasif' }}</span>
                                    </button>

                                    <!-- Düzenle & Sil -->
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" @click="openStemModal({{ json_encode($stem) }})"
                                                class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>

                                        <form action="{{ route('admin.bouquet-items.stems.destroy', $stem) }}" method="POST" onsubmit="return confirm('Bu çiçeği silmek istediğinize emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 flex items-center justify-center transition">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- ============================================================== -->
            <!-- TAB 2: 💐 BUKET BOYUTLARI (SIZES)                               -->
            <!-- ============================================================== -->
            <div x-show="tab === 'sizes'" x-cloak class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="font-black text-base text-slate-900">Buket Boyut Seçenekleri</h3>
                        <p class="text-xs text-slate-500">Müşterinin seçebileceği buket sarım boyutları ve ek fiyatlandırmalar (Standart, Büyük, Mega vb.)</p>
                    </div>

                    <button type="button" @click="openSizeModal()"
                            class="py-3 px-5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-xs flex items-center justify-center gap-2 shadow-sm transition active:scale-98">
                        <i class="fas fa-plus"></i>
                        <span>Yeni Buket Boyutu Ekle</span>
                    </button>
                </div>

                @if($sizes->isEmpty())
                    <div class="bg-white rounded-3xl p-12 text-center border border-slate-200">
                        <div class="text-3xl mb-3 text-blue-600"><i class="fas fa-layer-group"></i></div>
                        <h4 class="font-black text-slate-800 text-sm">Henüz buket boyutu eklenmemiş</h4>
                        <p class="text-xs text-slate-500 mt-1">Müşterilerinize Standart, Büyük veya Mega buket formu sunmak için boyut ekleyin.</p>
                        <button type="button" @click="openSizeModal()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold">
                            İlk Boyutu Ekle
                        </button>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($sizes as $size)
                            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-2xs hover:shadow-sm transition flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center text-xl font-black shadow-inner">
                                            @if(str_contains($size->icon ?? '', 'fa-'))
                                                <i class="{{ $size->icon }}"></i>
                                            @elseif(!empty($size->icon) && !preg_match('/[\x{1F300}-\x{1FAD6}\x{2600}-\x{27BF}]/u', $size->icon))
                                                {{ $size->icon }}
                                            @elseif($size->id == 1)
                                                <i class="fas fa-spa"></i>
                                            @elseif($size->id == 2)
                                                <i class="fas fa-layer-group"></i>
                                            @elseif($size->id == 3)
                                                <i class="fas fa-crown"></i>
                                            @else
                                                <i class="fas fa-seedling"></i>
                                            @endif
                                        </span>
                                        <div class="flex items-center gap-2">
                                            <button type="button" onclick="toggleSize({{ $size->id }})" id="size-btn-{{ $size->id }}"
                                                    class="px-2.5 py-1 rounded-xl font-black text-[11px] transition flex items-center gap-1 {{ $size->is_active ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                                <i class="fas {{ $size->is_active ? 'fa-check-circle text-emerald-600' : 'fa-times-circle text-slate-400' }}"></i>
                                                <span id="size-text-{{ $size->id }}">{{ $size->is_active ? 'Aktif' : 'Pasif' }}</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <h4 class="font-black text-sm text-slate-900">{{ $size->name }}</h4>
                                        @if($size->badge)
                                            <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-blue-100 text-blue-800">
                                                {{ $size->badge }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $size->description ?: 'Buket boyutu ve sarım formu' }}</p>
                                </div>

                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                    <div class="font-black text-sm text-slate-900">
                                        @if($size->price > 0)
                                            <span class="text-blue-600">+₺{{ number_format($size->price, 2, ',', '.') }}</span>
                                        @else
                                            <span class="text-emerald-600">Standart (+₺0)</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-1.5">
                                        <button type="button" @click="openSizeModal({{ $size->toJson() }})" 
                                                class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 flex items-center justify-center transition">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>

                                        <form action="{{ route('admin.bouquet-items.sizes.destroy', $size) }}" method="POST" 
                                              onsubmit="return confirm('Bu buket boyutunu silmek istediğinize emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 flex items-center justify-center transition">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- ============================================================== -->
            <!-- TAB 3: 🎁 AMBALAJ & PAKET KAĞITLARI (PACKAGES) -->
            <!-- ============================================================== -->
            <div x-show="tab === 'packages'" x-cloak class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="font-black text-base text-slate-900">Buket Ambalaj Kağıtları</h3>
                        <p class="text-xs text-slate-500">Müşterinin seçebileceği lüks kraft, tül ve ithal ambalaj kağıtları</p>
                    </div>

                    <button type="button" @click="openPackageModal()"
                            class="py-3 px-5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-xs flex items-center justify-center gap-2 shadow-sm transition active:scale-98">
                        <i class="fas fa-plus"></i>
                        <span>Yeni Ambalaj Kağıdı Ekle</span>
                    </button>
                </div>

                @if($packages->isEmpty())
                    <div class="bg-white rounded-3xl p-12 text-center border border-dashed border-slate-300 space-y-4">
                        <div class="w-20 h-20 rounded-3xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mx-auto shadow-inner">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-base text-slate-900">Henüz Ambalaj Eklenmedi</h4>
                            <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                                Buket ambalaj kağıdı seçeneklerini ekleyerek müşterilerinizin buket paketini kişiselleştirmesini sağlayın.
                            </p>
                        </div>
                        <button type="button" @click="openPackageModal()"
                                class="py-2.5 px-6 bg-slate-900 hover:bg-black text-white rounded-2xl font-black text-xs inline-flex items-center gap-2 shadow-xs transition">
                            <i class="fas fa-plus"></i>
                            <span>İlk Ambalajı Ekle</span>
                        </button>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($packages as $pkg)
                            <div class="bg-white rounded-3xl p-4 border border-slate-200 shadow-2xs hover:shadow-sm transition flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="w-full h-28 rounded-2xl overflow-hidden relative border border-slate-200 flex items-center justify-center"
                                         style="background-color: {{ $pkg->color_hex ?: '#fbcfe8' }};">
                                        @if($pkg->image_path)
                                            <img src="{{ $pkg->image_url }}" alt="{{ $pkg->name }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-box-open text-white/90 text-2xl drop-shadow-sm"></i>
                                        @endif
                                        @if($pkg->price > 0)
                                            <span class="absolute bottom-2 right-2 text-[10px] font-black bg-slate-900/80 text-white px-2 py-0.5 rounded-lg">
                                                +₺{{ number_format($pkg->price, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="absolute bottom-2 right-2 text-[10px] font-black bg-emerald-700/85 text-white px-2 py-0.5 rounded-lg">
                                                Ücretsiz
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-3">
                                        <h4 class="font-black text-sm text-slate-900 leading-snug">{{ $pkg->name }}</h4>
                                        <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-1">{{ $pkg->description ?: 'Lüks buket ambalaj kağıdı' }}</p>
                                    </div>
                                </div>

                                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                    <button type="button" 
                                            onclick="togglePackage({{ $pkg->id }})"
                                            id="pkg-btn-{{ $pkg->id }}"
                                            class="px-2.5 py-1 rounded-xl font-black text-[11px] transition flex items-center gap-1 {{ $pkg->is_active ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                        <i class="fas {{ $pkg->is_active ? 'fa-check-circle text-emerald-600' : 'fa-times-circle text-slate-400' }}"></i>
                                        <span id="pkg-text-{{ $pkg->id }}">{{ $pkg->is_active ? 'Aktif' : 'Pasif' }}</span>
                                    </button>

                                    <div class="flex items-center gap-1.5">
                                        <button type="button" @click="openPackageModal({{ json_encode($pkg) }})"
                                                class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>

                                        <form action="{{ route('admin.bouquet-items.packages.destroy', $pkg) }}" method="POST" onsubmit="return confirm('Bu ambalajı silmek istediğinize emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 flex items-center justify-center transition">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- ============================================================== -->
            <!-- TAB 3: 🎀 SÜSLEME KURDELELERİ (RIBBONS) -->
            <!-- ============================================================== -->
            <div x-show="tab === 'ribbons'" x-cloak class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="font-black text-base text-slate-900">Süsleme Kurdeleleri</h3>
                        <p class="text-xs text-slate-500">Buket bağlama saten, ipek, jüt ve şifon kurdele çeşitleri</p>
                    </div>

                    <button type="button" @click="openRibbonModal()"
                            class="py-3 px-5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-xs flex items-center justify-center gap-2 shadow-sm transition active:scale-98">
                        <i class="fas fa-plus"></i>
                        <span>Yeni Kurdele Ekle</span>
                    </button>
                </div>

                @if($ribbons->isEmpty())
                    <div class="bg-white rounded-3xl p-12 text-center border border-dashed border-slate-300 space-y-4">
                        <div class="w-20 h-20 rounded-3xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mx-auto shadow-inner">
                            <i class="fas fa-ribbon"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-base text-slate-900">Henüz Kurdele Eklenmedi</h4>
                            <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                                Buket süsleme kurdelesi ekleyerek müşterilerin zevkine uygun kurdeleyi seçmesini sağlayın.
                            </p>
                        </div>
                        <button type="button" @click="openRibbonModal()"
                                class="py-2.5 px-6 bg-slate-900 hover:bg-black text-white rounded-2xl font-black text-xs inline-flex items-center gap-2 shadow-xs transition">
                            <i class="fas fa-plus"></i>
                            <span>İlk Kurdeleyi Ekle</span>
                        </button>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($ribbons as $rib)
                            <div class="bg-white rounded-3xl p-4 border border-slate-200 shadow-2xs hover:shadow-sm transition flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="w-full h-24 rounded-2xl overflow-hidden relative border border-slate-200 flex items-center justify-center"
                                         style="background-color: {{ $rib->color_hex ?: '#e11d48' }};">
                                        @if($rib->image_path)
                                            <img src="{{ $rib->image_url }}" alt="{{ $rib->name }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-ribbon text-white/90 text-xl drop-shadow-sm"></i>
                                        @endif
                                    </div>

                                    <div class="mt-3">
                                        <h4 class="font-black text-sm text-slate-900 leading-snug">{{ $rib->name }}</h4>
                                        @if($rib->price > 0)
                                            <span class="text-[11px] font-bold text-slate-500">+₺{{ number_format($rib->price, 2, ',', '.') }}</span>
                                        @else
                                            <span class="text-[11px] font-bold text-emerald-600">Ücretsiz Seçenek</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                    <button type="button" 
                                            onclick="toggleRibbon({{ $rib->id }})"
                                            id="rib-btn-{{ $rib->id }}"
                                            class="px-2.5 py-1 rounded-xl font-black text-[11px] transition flex items-center gap-1 {{ $rib->is_active ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                        <i class="fas {{ $rib->is_active ? 'fa-check-circle text-emerald-600' : 'fa-times-circle text-slate-400' }}"></i>
                                        <span id="rib-text-{{ $rib->id }}">{{ $rib->is_active ? 'Aktif' : 'Pasif' }}</span>
                                    </button>

                                    <div class="flex items-center gap-1.5">
                                        <button type="button" @click="openRibbonModal({{ json_encode($rib) }})"
                                                class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>

                                        <form action="{{ route('admin.bouquet-items.ribbons.destroy', $rib) }}" method="POST" onsubmit="return confirm('Bu kurdeleyi silmek istediğinize emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 flex items-center justify-center transition">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- ============================================================== -->
            <!-- TAB 4: ⚙️ GENEL ATÖLYE KURALLARI (SETTINGS) -->
            <!-- ============================================================== -->
            <div x-show="tab === 'settings'" x-cloak class="max-w-2xl bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
                <div>
                    <h3 class="font-black text-base text-slate-900">Buket Atölyesi Kuralları</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Sitedeki sihirbazın genel çalışma kurallarını belirleyin</p>
                </div>

                <form action="{{ route('admin.bouquet-items.settings.save') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Buket Sihirbazı Aktif/Pasif -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="font-black text-xs text-slate-900 block">Kendi Buketini Oluştur Sihirbazı</span>
                            <span class="text-[11px] text-slate-500">Müşterilerin buket tasarlama sayfasına erişimini açıp kapatır.</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="custom_bouquet_enabled" value="1" {{ $isEnabled ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Minimum Dal Sayısı -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-black text-slate-800">Minimum Çiçek Dalı Zorunluluğu</label>
                        <div class="flex items-center gap-3">
                            <input type="number" min="1" max="50" name="custom_bouquet_min_stems" value="{{ $minStems }}"
                                   class="w-32 py-2.5 px-3.5 rounded-2xl border border-slate-300 text-xs font-black text-center focus:ring-blue-500 focus:border-blue-500">
                            <span class="text-xs font-bold text-slate-500">Dal (Çiçekçilik standardı: En az 7 dal)</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Müşteri bu sayının altında çiçek seçtiğinde sepete ekleyemez.</p>
                    </div>

                    <button type="submit" class="py-3 px-6 bg-slate-900 hover:bg-black text-white rounded-2xl font-black text-xs flex items-center gap-2 shadow-xs transition">
                        <i class="fas fa-save"></i>
                        <span>Kuralları Kaydet</span>
                    </button>
                </form>
            </div>

        </div>

        <!-- ============================================================== -->
        <!-- MODAL: 🌸 TEKLİ ÇİÇEK EKLE / DÜZENLE -->
        <!-- ============================================================== -->
        <div x-show="stemModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="stemModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-black text-base text-slate-900 flex items-center gap-2">
                        <i class="fas fa-spa text-blue-600"></i>
                        <span x-text="editingStem ? 'Çiçeği Düzenle' : 'Yeni Tekli Çiçek Ekle'"></span>
                    </h3>
                    <button type="button" @click="stemModal = false" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form :action="editingStem ? '{{ url('admin/buket-malzemeleri/cicekler') }}/' + editingStem.id : '{{ route('admin.bouquet-items.stems.store') }}'" 
                      method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <template x-if="editingStem">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <label class="block text-xs font-black text-slate-700 mb-1">Çiçek Adı *</label>
                            <input type="text" name="name" :value="editingStem ? editingStem.name : ''" required placeholder="Örn: Ekvador Kırmızı Gül"
                                   class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-300 text-xs font-bold focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-700 mb-1">Dal Birim Fiyatı (₺) *</label>
                            <input type="number" step="0.50" min="0" name="price" :value="editingStem ? editingStem.price : ''" required placeholder="55.00"
                                   class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-300 text-xs font-black focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-700 mb-1">İkon (FontAwesome)</label>
                            <input type="text" name="icon" :value="editingStem ? editingStem.icon : 'fas fa-spa'" placeholder="fas fa-spa"
                                   class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-300 text-xs font-bold text-center focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-xs font-black text-slate-700 mb-1">Anlamı / Kısa Açıklaması</label>
                            <input type="text" name="meaning" :value="editingStem ? editingStem.meaning : ''" placeholder="Örn: Aşk, Tutku ve Asalet"
                                   class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-300 text-xs font-medium focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-xs font-black text-slate-700 mb-1">
                                Çiçek Fotoğrafı (Önerilen: 800 × 800 px Kare)
                            </label>
                            <input type="file" name="image" accept="image/*"
                                   class="block w-full text-xs text-slate-600 border border-slate-300 rounded-2xl p-2 file:mr-3 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-[10px] text-slate-400 mt-1">Telefonunuzdan çektiğiniz taze çiçek fotoğrafını yükleyebilirsiniz.</p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="stemModal = false" class="py-2.5 px-4 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                            Vazgeç
                        </button>
                        <button type="submit" class="py-2.5 px-5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs shadow-xs transition">
                            <span x-text="editingStem ? 'Güncelle' : 'Çiçeği Kaydet'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- MODAL: 💐 BUKET BOYUTU EKLE / DÜZENLE                          -->
        <!-- ============================================================== -->
        <div x-show="sizeModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="sizeModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-black text-base text-slate-900 flex items-center gap-2">
                        <i class="fas fa-layer-group text-blue-600"></i>
                        <span x-text="editingSize ? 'Buket Boyutunu Düzenle' : 'Yeni Buket Boyutu Ekle'"></span>
                    </h3>
                    <button type="button" @click="sizeModal = false" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form :action="editingSize ? '{{ url('admin/buket-malzemeleri/boyutlar') }}/' + editingSize.id : '{{ route('admin.bouquet-items.sizes.store') }}'" 
                      method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <template x-if="editingSize">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <label class="block text-xs font-black text-slate-700 mb-1">Boyut Adı *</label>
                            <input type="text" name="name" :value="editingSize ? editingSize.name : ''" required placeholder="Örn: Büyük Boy (Deluxe)"
                                   class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-300 text-xs font-bold focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-700 mb-1">Ek Ücret (₺) *</label>
                            <input type="number" step="1" min="0" name="price" :value="editingSize ? editingSize.price : '0'" required placeholder="0"
                                   class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-300 text-xs font-black focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-[10px] text-slate-400 mt-1">Standart boyut için 0 girebilirsiniz.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-700 mb-1">Rozet / Etiket</label>
                            <input type="text" name="badge" :value="editingSize ? editingSize.badge : ''" placeholder="Örn: En Popüler"
                                   class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-300 text-xs font-bold focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-700 mb-1">İkon (FontAwesome)</label>
                            <input type="text" name="icon" :value="editingSize ? editingSize.icon : 'fas fa-layer-group'" placeholder="fas fa-layer-group"
                                   class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-300 text-xs font-bold text-center focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-700 mb-1">Sıralama</label>
                            <input type="number" name="sort_order" :value="editingSize ? editingSize.sort_order : '0'"
                                   class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-300 text-xs font-bold text-center focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-xs font-black text-slate-700 mb-1">Açıklama</label>
                            <input type="text" name="description" :value="editingSize ? editingSize.description : ''" placeholder="Örn: Ekstra dolgun yeşillik ve geniş buket sarımı"
                                   class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-300 text-xs font-medium focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="sizeModal = false" class="py-2.5 px-4 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                            Vazgeç
                        </button>
                        <button type="submit" class="py-2.5 px-5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs shadow-xs transition">
                            <span x-text="editingSize ? 'Güncelle' : 'Boyutu Kaydet'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- MODAL: 🎁 AMBALAJ KAĞIDI EKLE / DÜZENLE -->
        <!-- ============================================================== -->
        <div x-show="packageModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="packageModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-black text-base text-slate-900 flex items-center gap-2">
                        <i class="fas fa-box-open text-blue-600"></i>
                        <span x-text="editingPackage ? 'Ambalajı Düzenle' : 'Yeni Ambalaj Kağıdı Ekle'"></span>
                    </h3>
                    <button type="button" @click="packageModal = false" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form :action="editingPackage ? '{{ url('admin/buket-malzemeleri/ambalajlar') }}/' + editingPackage.id : '{{ route('admin.bouquet-items.packages.store') }}'" 
                      method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <template x-if="editingPackage">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <label class="block text-xs font-black text-slate-700 mb-1">Ambalaj Adı *</label>
                            <input type="text" name="name" :value="editingPackage ? editingPackage.name : ''" required placeholder="Örn: Pastel Pembe Kraft Kağıt"
                                   class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-300 text-xs font-bold focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-700 mb-1">Ek Fiyat (₺, opsiyonel)</label>
                            <input type="number" step="0.50" min="0" name="price" :value="editingPackage ? editingPackage.price : '0'" placeholder="0"
                                   class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-300 text-xs font-black focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-700 mb-1">Renk Kodu (Hex)</label>
                            <input type="text" name="color_hex" :value="editingPackage ? editingPackage.color_hex : '#fbcfe8'" placeholder="#fbcfe8"
                                   class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-300 text-xs font-mono font-bold focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-xs font-black text-slate-700 mb-1">Kısa Açıklaması</label>
                            <input type="text" name="description" :value="editingPackage ? editingPackage.description : ''" placeholder="Örn: Romantik, tatlı pastel tonlar"
                                   class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-300 text-xs font-medium focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-xs font-black text-slate-700 mb-1">
                                Ambalaj Fotoğrafı (Önerilen: 800 × 800 px)
                            </label>
                            <input type="file" name="image" accept="image/*"
                                   class="block w-full text-xs text-slate-600 border border-slate-300 rounded-2xl p-2 file:mr-3 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="packageModal = false" class="py-2.5 px-4 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                            Vazgeç
                        </button>
                        <button type="submit" class="py-2.5 px-5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs shadow-xs transition">
                            <span x-text="editingPackage ? 'Güncelle' : 'Ambalajı Kaydet'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- MODAL: 🎀 KURDELE EKLE / DÜZENLE -->
        <!-- ============================================================== -->
        <div x-show="ribbonModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="ribbonModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-black text-base text-slate-900 flex items-center gap-2">
                        <i class="fas fa-ribbon text-blue-600"></i>
                        <span x-text="editingRibbon ? 'Kurdeleyi Düzenle' : 'Yeni Kurdele Ekle'"></span>
                    </h3>
                    <button type="button" @click="ribbonModal = false" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form :action="editingRibbon ? '{{ url('admin/buket-malzemeleri/kurdeleler') }}/' + editingRibbon.id : '{{ route('admin.bouquet-items.ribbons.store') }}'" 
                      method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <template x-if="editingRibbon">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <label class="block text-xs font-black text-slate-700 mb-1">Kurdele Adı *</label>
                            <input type="text" name="name" :value="editingRibbon ? editingRibbon.name : ''" required placeholder="Örn: Saten Bordo / Kırmızı"
                                   class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-300 text-xs font-bold focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-700 mb-1">Ek Fiyat (₺, opsiyonel)</label>
                            <input type="number" step="0.50" min="0" name="price" :value="editingRibbon ? editingRibbon.price : '0'" placeholder="0"
                                   class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-300 text-xs font-black focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-700 mb-1">Renk Kodu (Hex)</label>
                            <input type="text" name="color_hex" :value="editingRibbon ? editingRibbon.color_hex : '#e11d48'" placeholder="#e11d48"
                                   class="w-full py-2.5 px-3.5 rounded-2xl border border-slate-300 text-xs font-mono font-bold focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-xs font-black text-slate-700 mb-1">
                                Kurdele Fotoğrafı (Önerilen: 800 × 800 px)
                            </label>
                            <input type="file" name="image" accept="image/*"
                                   class="block w-full text-xs text-slate-600 border border-slate-300 rounded-2xl p-2 file:mr-3 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="ribbonModal = false" class="py-2.5 px-4 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                            Vazgeç
                        </button>
                        <button type="submit" class="py-2.5 px-5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs shadow-xs transition">
                            <span x-text="editingRibbon ? 'Güncelle' : 'Kurdeleyi Kaydet'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- AJAX TOGGLE SCRIPTS -->
    <script>
        function toggleStem(id) {
            fetch('{{ url('admin/buket-malzemeleri/cicekler') }}/' + id + '/toggle', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const btn = document.getElementById('stem-btn-' + id);
                    const txt = document.getElementById('stem-text-' + id);
                    if(data.is_active) {
                        btn.className = 'px-2.5 py-1 rounded-xl font-black text-[11px] transition flex items-center gap-1 bg-emerald-50 text-emerald-800 border border-emerald-200';
                        txt.innerText = 'Aktif';
                        btn.querySelector('i').className = 'fas fa-check-circle text-emerald-600';
                    } else {
                        btn.className = 'px-2.5 py-1 rounded-xl font-black text-[11px] transition flex items-center gap-1 bg-slate-100 text-slate-500 border border-slate-200';
                        txt.innerText = 'Pasif';
                        btn.querySelector('i').className = 'fas fa-times-circle text-slate-400';
                    }
                }
            });
        }

        function togglePackage(id) {
            fetch('{{ url('admin/buket-malzemeleri/ambalajlar') }}/' + id + '/toggle', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const btn = document.getElementById('pkg-btn-' + id);
                    const txt = document.getElementById('pkg-text-' + id);
                    if(data.is_active) {
                        btn.className = 'px-2.5 py-1 rounded-xl font-black text-[11px] transition flex items-center gap-1 bg-emerald-50 text-emerald-800 border border-emerald-200';
                        txt.innerText = 'Aktif';
                        btn.querySelector('i').className = 'fas fa-check-circle text-emerald-600';
                    } else {
                        btn.className = 'px-2.5 py-1 rounded-xl font-black text-[11px] transition flex items-center gap-1 bg-slate-100 text-slate-500 border border-slate-200';
                        txt.innerText = 'Pasif';
                        btn.querySelector('i').className = 'fas fa-times-circle text-slate-400';
                    }
                }
            });
        }

        function toggleRibbon(id) {
            fetch('{{ url('admin/buket-malzemeleri/kurdeleler') }}/' + id + '/toggle', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const btn = document.getElementById('rib-btn-' + id);
                    const txt = document.getElementById('rib-text-' + id);
                    if(data.is_active) {
                        btn.className = 'px-2.5 py-1 rounded-xl font-black text-[11px] transition flex items-center gap-1 bg-emerald-50 text-emerald-800 border border-emerald-200';
                        txt.innerText = 'Aktif';
                        btn.querySelector('i').className = 'fas fa-check-circle text-emerald-600';
                    } else {
                        btn.className = 'px-2.5 py-1 rounded-xl font-black text-[11px] transition flex items-center gap-1 bg-slate-100 text-slate-500 border border-slate-200';
                        txt.innerText = 'Pasif';
                        btn.querySelector('i').className = 'fas fa-times-circle text-slate-400';
                    }
                }
            });
        }

        function toggleSize(id) {
            fetch('{{ url('admin/buket-malzemeleri/boyutlar') }}/' + id + '/toggle', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const btn = document.getElementById('size-btn-' + id);
                    const txt = document.getElementById('size-text-' + id);
                    if(data.is_active) {
                        btn.className = 'px-2.5 py-1 rounded-xl font-black text-[11px] transition flex items-center gap-1 bg-emerald-50 text-emerald-800 border border-emerald-200';
                        txt.innerText = 'Aktif';
                        btn.querySelector('i').className = 'fas fa-check-circle text-emerald-600';
                    } else {
                        btn.className = 'px-2.5 py-1 rounded-xl font-black text-[11px] transition flex items-center gap-1 bg-slate-100 text-slate-500 border border-slate-200';
                        txt.innerText = 'Pasif';
                        btn.querySelector('i').className = 'fas fa-times-circle text-slate-400';
                    }
                }
            });
        }
    </script>
</x-app-layout>
