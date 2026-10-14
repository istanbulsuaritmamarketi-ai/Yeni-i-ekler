<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <i class="fas fa-box text-theme-primary"></i> Ürün Kataloğu
                </h2>
                <p class="text-xs text-gray-500 mt-1">Katalogdaki tüm ürünleri, fiyatları ve stok durumunu yönetin</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2.5" x-data="{ showImportModal: false }">
                <!-- Linkten Ürün Çek -->
                <a href="{{ route('admin.products.create') }}" 
                   class="bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow-xs transition-colors flex items-center gap-2"
                   title="Herhangi bir web sitesinin ürün linkini yapıştırarak fotoğrafı ve bilgileri otomatik çekin">
                    <i class="fas fa-magic text-sm"></i> Linkten Ürün Çek
                </a>

                <!-- Toplu Excel/CSV Yükle Butonu -->
                <button type="button" @click="showImportModal = true" 
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow-xs transition-colors flex items-center gap-2">
                    <i class="fas fa-file-excel text-sm"></i> Toplu Excel/CSV
                </button>

                <!-- Yeni Ürün Ekle -->
                <a href="{{ route('admin.products.create') }}" 
                   class="bg-theme-primary hover:bg-theme-primary-hover text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow-xs transition-colors flex items-center gap-2">
                    <i class="fas fa-plus text-xs"></i> Yeni Ürün Ekle
                </a>

                <!-- TOPLU İÇE AKTAR MODAL -->
                <div x-show="showImportModal" x-cloak 
                     class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4"
                     @keydown.escape.window="showImportModal = false">
                    
                    <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-gray-100 relative"
                         @click.away="showImportModal = false">
                        
                        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                            <div class="flex items-center gap-2.5">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                                    <i class="fas fa-file-excel"></i>
                                </div>
                                <div>
                                    <h3 class="font-black text-base text-gray-900">Toplu Ürün İçe Aktar</h3>
                                    <p class="text-xs text-gray-500">Excel (.xlsx) veya CSV dosyası ile tek tıkla ürünleri yükleyin</p>
                                </div>
                            </div>
                            <button type="button" @click="showImportModal = false" class="text-gray-400 hover:text-gray-600 p-2 rounded-xl">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>

                        <!-- Şablon İndirme Alanı -->
                        <div class="my-5 p-4 bg-emerald-50/60 rounded-2xl border border-emerald-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <div>
                                <div class="font-bold text-xs text-emerald-950 flex items-center gap-1.5">
                                    <i class="fas fa-info-circle text-emerald-600"></i> Hazır Excel Şablonu
                                </div>
                                <div class="text-[11px] text-emerald-800 mt-0.5">Sütun başlıkları hazır örnek şablonu indirin, ürünlerinizi yazıp yükleyin.</div>
                            </div>
                            <a href="{{ route('admin.products.sampleTemplate') }}" 
                               class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow-2xs transition-colors whitespace-nowrap">
                                <i class="fas fa-download"></i> Şablonu İndir
                            </a>
                        </div>

                        <!-- Yükleme Formu -->
                        <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                    Excel veya CSV Dosyasını Seçin (.xlsx, .csv, .txt):
                                </label>
                                <input type="file" name="file" accept=".xlsx,.xls,.csv,.txt" required
                                       class="block w-full text-xs text-gray-900 border border-gray-300 rounded-2xl cursor-pointer bg-gray-50 p-2.5 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700">
                            </div>

                            <div class="bg-gray-50 rounded-2xl p-3.5 border border-gray-200 text-[11px] text-gray-600 space-y-1">
                                <p class="font-bold text-gray-800 flex items-center gap-1"><i class="fas fa-check-circle text-blue-600"></i> Bilgilendirme:</p>
                                <p>• Kategoriler dosyada yazılan isme göre otomatik eşleşir veya yeni oluşturulur.</p>
                                <p>• Ürün görsellerini ürünler yüklendikten sonra listeden düzenleyerek kolayca yükleyebilirsiniz.</p>
                                <p>• Türkçe karakterler (ğ, ş, ı, İ, ç, ö, ü) %100 uyumlu olarak aktarılır.</p>
                            </div>

                            <div class="pt-3 flex items-center justify-end gap-2.5">
                                <button type="button" @click="showImportModal = false" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-colors">
                                    İptal
                                </button>
                                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition-colors flex items-center gap-2">
                                    <i class="fas fa-upload"></i> İçe Aktarımı Başlat
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-r-2xl shadow-sm text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-r-2xl shadow-sm text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-rose-600 text-lg"></i>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Ürün Listesi Tablosu -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-200/80">
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-base text-gray-900 flex items-center gap-2">
                            <i class="fas fa-boxes text-blue-600"></i> Kayıtlı Ürünler ({{ $products->count() }})
                        </h3>
                        <span class="text-xs text-gray-500">Ürünlerin üzerine tıklayarak düzenleyebilir veya marka atayabilirsiniz</span>
                    </div>

                    <!-- Marka & Kategori Hızlı Filtre -->
                    <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                        <select name="brand_id" onchange="this.form.submit()" class="text-xs font-bold border-gray-300 rounded-xl py-2 px-3 bg-white focus:ring-purple-500 focus:border-purple-500">
                            <option value="">-- Tüm Markalar --</option>
                            @foreach($brands as $b)
                                <option value="{{ $b->id }}" {{ request('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                        <select name="category_id" onchange="this.form.submit()" class="text-xs font-bold border-gray-300 rounded-xl py-2 px-3 bg-white focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Tüm Kategoriler --</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @if(request()->filled('brand_id') || request()->filled('category_id'))
                            <a href="{{ route('admin.products.index') }}" class="text-xs text-rose-600 hover:underline font-bold px-2">Temizle</a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                        <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-[11px]">
                            <tr>
                                <th class="px-6 py-3.5">Görsel</th>
                                <th class="px-6 py-3.5">Ürün Adı</th>
                                <th class="px-6 py-3.5">Kategori / Marka</th>
                                <th class="px-6 py-3.5">Fiyat</th>
                                <th class="px-6 py-3.5 text-center">Hediye</th>
                                <th class="px-6 py-3.5 text-center">Durum</th>
                                <th class="px-6 py-3.5 text-right">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($products as $product)
                                <tr class="hover:bg-gray-50/70 transition-colors">
                                    <td class="px-6 py-4">
                                        @if($product->image_path)
                                            <div class="w-12 h-12 rounded-xl bg-white border border-gray-200 p-1 flex items-center justify-center overflow-hidden shadow-2xs">
                                                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain">
                                            </div>
                                        @else
                                            <div class="w-12 h-12 rounded-xl bg-gray-100 border border-dashed border-gray-300 text-gray-400 flex flex-col items-center justify-center text-[9px] font-bold">
                                                <i class="fas fa-image text-sm mb-0.5"></i>
                                                Foto Yok
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 text-sm hover:text-blue-600">
                                            <a href="{{ route('admin.products.edit', $product) }}">{{ $product->name }}</a>
                                        </div>
                                        <div class="flex items-center gap-2 mt-1">
                                            @if($product->custom_badge)
                                                <span class="bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                                    {{ $product->custom_badge }}
                                                </span>
                                            @endif
                                            @if($product->has_free_installation)
                                                <span class="text-[10px] text-blue-600 font-semibold flex items-center gap-1">
                                                    <i class="fas fa-tools text-[9px]"></i> Ücretsiz Montaj
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-700">
                                        <span class="bg-gray-100 text-gray-700 px-2.5 py-1 rounded-lg text-xs font-bold inline-block">
                                            {{ $product->category->name ?? '-' }}
                                        </span>
                                        @if($product->brands->count() > 0)
                                            <div class="flex flex-wrap gap-1 mt-1.5 max-w-xs">
                                                @foreach($product->brands as $pb)
                                                    <span class="inline-flex items-center gap-1 text-[9px] font-black bg-purple-50 text-purple-700 px-1.5 py-0.5 rounded border border-purple-200/60">
                                                        <i class="fas fa-tag text-[7px]"></i> {{ $pb->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @elseif($product->brand)
                                            <span class="inline-flex items-center gap-1 mt-1.5 text-[10px] font-black bg-purple-50 text-purple-700 px-2 py-0.5 rounded-md border border-purple-200/60 block w-fit">
                                                <i class="fas fa-tag text-[8px]"></i> {{ $product->brand->name }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-black text-gray-900 text-sm">
                                            {{ $product->price ? number_format($product->price, 0, ',', '.') . ' TL' : '-' }}
                                        </div>
                                        @if($product->cost_price)
                                            <div class="text-[10px] text-gray-400 font-medium">Maliyet: {{ number_format($product->cost_price, 0, ',', '.') }} TL</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($product->has_gift)
                                            <span class="inline-flex items-center gap-1 bg-purple-50 text-purple-700 border border-purple-200 text-[10px] font-bold px-2 py-0.5 rounded-full" title="{{ $product->gifts->count() ? $product->gifts->count() . ' Özel Hediye Seçili' : 'Tüm Genel Hediyeler Geçerli' }}">
                                                <i class="fas fa-gift text-purple-600"></i> {{ $product->gifts->count() ? $product->gifts->count() . ' Hediye' : 'Aktif' }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <form action="{{ route('admin.products.toggle', $product) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1 inline-flex items-center gap-1.5 text-xs font-black rounded-full transition-all cursor-pointer shadow-2xs hover:scale-105 {{ $product->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}" title="Durumu değiştirmek için tıklayın">
                                                <i class="fas {{ $product->is_active ? 'fa-check-circle text-emerald-600' : 'fa-times-circle text-gray-400' }}"></i>
                                                <span>{{ $product->is_active ? 'Aktif' : 'Pasif' }}</span>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="p-2 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-lg text-xs font-bold transition-colors" title="Sitede Gör">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            <a href="{{ route('admin.products.edit', $product) }}" class="p-2 bg-purple-50 text-purple-700 hover:bg-purple-100 rounded-lg text-xs font-bold transition-colors" title="Metin ve Görselleri Özelleştir">
                                                <i class="fas fa-magic text-purple-600"></i>
                                            </a>
                                            <a href="{{ route('admin.products.edit', $product) }}" class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-bold transition-colors" title="Düzenle">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('Bu ürünü silmek istediğinize emin misiniz?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-bold transition-colors" title="Sil">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <i class="fas fa-box-open text-4xl text-gray-300 mb-3"></i>
                                        <p class="font-bold text-sm">Henüz kayıtlı ürün bulunmuyor.</p>
                                        <p class="text-xs text-gray-400 mt-1">Yukarıdaki butonları kullanarak yeni ürün ekleyebilir veya Excel ile toplu yükleyebilirsiniz.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
