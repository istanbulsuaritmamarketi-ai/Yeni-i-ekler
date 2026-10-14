<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-sm font-black">
                        <i class="fas fa-boxes"></i>
                    </span>
                    <h2 class="font-black text-2xl text-gray-800 leading-tight">
                        {{ $brand->name }} - Uyumlu Ürünleri Yönet
                    </h2>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    @if($brand->parent)
                        <span class="font-bold text-purple-600">({{ $brand->parent->name }} &gt; {{ $brand->name }})</span>
                    @endif
                    Aşağıdaki aktif ürünler arasından bu marka veya alt modelle uyumlu olanları işaretleyin
                </p>
            </div>
            <a href="{{ route('admin.brands.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-700 flex items-center gap-1 bg-white border border-gray-200 px-4 py-2 rounded-xl shadow-xs">
                <i class="fas fa-arrow-left"></i> Markalara Dön
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        search: '',
        selectedCount: {{ count($selectedProductIds) }},
        toggleAll(check) {
            const boxes = document.querySelectorAll('.product-checkbox');
            boxes.forEach(b => {
                // only toggle visible ones
                if (b.closest('.product-item').style.display !== 'none') {
                    b.checked = check;
                }
            });
            this.updateCount();
        },
        updateCount() {
            this.selectedCount = document.querySelectorAll('.product-checkbox:checked').length;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.brands.products.sync', $brand->id) }}" method="POST">
                @csrf

                <!-- Kontrol Barı -->
                <div class="bg-white rounded-3xl p-5 mb-6 border border-gray-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="w-full sm:w-80 relative">
                        <input type="text" x-model="search" placeholder="Listede ürün ara..." 
                               class="w-full pl-9 pr-4 py-2 text-xs font-bold border border-gray-300 rounded-xl focus:border-purple-500 focus:ring-purple-500">
                        <i class="fas fa-search text-gray-400 absolute left-3 top-3 text-xs"></i>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                        <button type="button" @click="toggleAll(true)" class="text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 px-3 py-2 rounded-xl transition-colors">
                            <i class="fas fa-check-double mr-1"></i> Tümünü Seç
                        </button>
                        <button type="button" @click="toggleAll(false)" class="text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-xl transition-colors">
                            <i class="fas fa-times mr-1"></i> Temizle
                        </button>
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-black px-5 py-2 rounded-xl shadow-sm transition-all transform active:scale-95 flex items-center gap-1.5">
                            <i class="fas fa-save"></i> Seçilenleri Kaydet (<span x-text="selectedCount"></span> Ürün)
                        </button>
                    </div>
                </div>

                <!-- Ürün Listesi Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($products as $product)
                        @php
                            $isChecked = in_array($product->id, $selectedProductIds);
                        @endphp
                        <label class="product-item relative bg-white rounded-2xl p-4 border transition-all cursor-pointer flex items-start gap-3 shadow-2xs hover:shadow-md"
                               :class="{'border-purple-500 bg-purple-50/20 ring-2 ring-purple-500/20': document.getElementById('prod_{{ $product->id }}')?.checked}"
                               x-show="!search || '{{ strtolower(addslashes($product->name)) }}'.includes(search.toLowerCase()) || '{{ strtolower(addslashes($product->category->name ?? '')) }}'.includes(search.toLowerCase())">
                            
                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" 
                                   id="prod_{{ $product->id }}"
                                   class="product-checkbox mt-1 w-4 h-4 rounded text-purple-600 focus:ring-purple-500 border-gray-300"
                                   {{ $isChecked ? 'checked' : '' }}
                                   @change="updateCount()">

                            <!-- Görsel -->
                            <div class="w-14 h-14 rounded-xl bg-gray-50 border border-gray-200 p-1 flex-shrink-0 flex items-center justify-center overflow-hidden">
                                @if($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain">
                                @else
                                    <i class="fas fa-filter text-gray-300 text-lg"></i>
                                @endif
                            </div>

                            <!-- Bilgiler -->
                            <div class="flex-grow min-w-0">
                                <span class="text-[10px] font-black uppercase tracking-wider text-purple-700 block">
                                    {{ $product->category->name ?? 'Kategori Yok' }}
                                </span>
                                <h4 class="text-xs font-bold text-gray-900 leading-snug line-clamp-2 mt-0.5">
                                    {{ $product->name }}
                                </h4>
                                <div class="text-xs font-black text-blue-600 font-mono mt-1">
                                    {{ number_format($product->campaign_price ?: $product->price, 0, ',', '.') }} TL
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="col-span-full bg-white rounded-3xl p-12 text-center text-gray-400">
                            Henüz kayıtlı aktif ürün bulunamadı.
                        </div>
                    @endforelse
                </div>

                <!-- Alt Kaydet Butonu -->
                <div class="mt-8 bg-white rounded-3xl p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
                    <span class="text-xs text-gray-500 font-bold">
                        Toplam <span class="font-black text-purple-600" x-text="selectedCount"></span> ürün bu marka ile eşleştirilecek.
                    </span>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-black px-6 py-3 rounded-2xl shadow-md transition-all transform active:scale-95 flex items-center gap-2">
                        <i class="fas fa-check"></i> Değişiklikleri Kaydet
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>
