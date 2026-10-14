<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight">
                    {{ __('Ürünü Düzenle: ') }} <span class="text-blue-600">{{ $product->name }}</span>
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Ürün bilgilerini, fiyatını, görsellerini ve detaylı metinlerini güncelleyin</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs font-bold bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-xl transition-colors flex items-center gap-1.5">
                    <i class="fas fa-external-link-alt"></i> Sitede Gör
                </a>
                <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:text-gray-900 text-xs font-bold bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl transition-colors flex items-center gap-1.5">
                    <i class="fas fa-arrow-left"></i> Listeye Dön
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Quill.js Zengin Görsel Metin Editörü -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .ql-toolbar.ql-snow {
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            background-color: #f8fafc;
            border-color: #cbd5e1;
            padding: 0.75rem;
        }
        .ql-container.ql-snow {
            border-bottom-left-radius: 1rem;
            border-bottom-right-radius: 1rem;
            border-color: #cbd5e1;
            font-family: inherit;
            font-size: 0.875rem;
            background-color: #ffffff;
            min-height: 200px;
        }
        .ql-editor {
            min-height: 200px;
            line-height: 1.8 !important;
            font-size: 0.925rem;
            color: #1e293b;
            white-space: pre-wrap !important;
        }
        .ql-editor p {
            margin-bottom: 0.85rem !important;
        }
        .ql-editor ul, .ql-editor ol {
            margin-bottom: 0.85rem !important;
            padding-left: 1.5rem !important;
        }
        .ql-editor li {
            margin-bottom: 0.35rem !important;
        }
        .ql-editor h1, .ql-editor h2, .ql-editor h3, .ql-editor h4 {
            font-weight: 800 !important;
            color: #0f172a !important;
            margin-top: 1.25rem !important;
            margin-bottom: 0.5rem !important;
        }
        .ql-editor strong {
            font-weight: 700 !important;
            color: #0f172a !important;
        }
    </style>

    <div class="py-8"
         x-data="{
            aiLoading: false,
            fieldLoading: {
                all: false,
                short_description: false,
                description: false,
                technical_features: false,
                seo: false
            },
            aiInstruction: '',
            successMsg: '',
            errorMsg: '',
            showAiImageBox: false,
            aiImagePrompt: '',
            aiImageLoading: false,
            coverLoading: false,
            galleryLoadingId: null,
            coverSrc: '{{ $product->image_path ? asset('storage/' . $product->image_path) : '' }}',
            galleryCount: {{ $product->images->count() }},
            manualCoverPreview: '',
            manualCoverName: '',
            manualCoverSize: '',
            manualGalleryFiles: [],
            hasGift: {{ old('has_gift', $product->has_gift ?? true) ? 'true' : 'false' }},
            handleManualCover(event) {
                const file = event.target.files[0];
                if (file) {
                    this.manualCoverPreview = URL.createObjectURL(file);
                    this.manualCoverName = file.name;
                    this.manualCoverSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                }
            },
            clearManualCover() {
                this.manualCoverPreview = '';
                this.manualCoverName = '';
                this.manualCoverSize = '';
                const input = document.getElementById('image');
                if (input) input.value = '';
            },
            handleManualGallery(event) {
                const files = event.target.files;
                this.manualGalleryFiles = [];
                if (files && files.length > 0) {
                    Array.from(files).forEach(file => {
                        this.manualGalleryFiles.push({
                            name: file.name,
                            size: (file.size / (1024 * 1024)).toFixed(2) + ' MB',
                            url: URL.createObjectURL(file)
                        });
                    });
                }
            },
            clearManualGallery() {
                this.manualGalleryFiles = [];
                const input = document.getElementById('gallery_images');
                if (input) input.value = '';
            },
            deleteGalleryImage(imageId, deleteUrl) {
                if (!confirm('Bu galeri fotoğrafını silmek istediğinize emin misiniz?')) return;
                
                const card = document.getElementById('gallery-card-' + imageId);
                if (card) card.style.opacity = '0.35';

                fetch(deleteUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ _method: 'DELETE' })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        if (card) {
                            card.style.transition = 'all 0.3s ease';
                            card.style.transform = 'scale(0.8)';
                            card.style.opacity = '0';
                            setTimeout(() => {
                                card.remove();
                                this.galleryCount = Math.max(0, this.galleryCount - 1);
                            }, 300);
                        }
                        this.successMsg = (data.message || 'Galeri fotoğrafı başarıyla silindi.');
                    } else {
                        if (card) card.style.opacity = '1';
                        this.errorMsg = data.message || 'Fotoğraf silinemedi.';
                    }
                })
                .catch(err => {
                    if (card) card.style.opacity = '1';
                    this.errorMsg = 'Silme işlemi sırasında hata: ' + err.message;
                });
            },
            setCoverImage(imageId, setCoverUrl) {
                if (!confirm('Bu görseli ana kapak fotoğrafı yapmak istiyor musunuz?')) return;

                fetch(setCoverUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        if (data.new_cover_url) {
                            this.coverSrc = data.new_cover_url + '?v=' + Date.now();
                        }
                        if (data.swapped_gallery_url) {
                            const imgEl = document.getElementById('gallery-img-' + imageId);
                            if (imgEl) imgEl.src = data.swapped_gallery_url + '?v=' + Date.now();
                        } else {
                            const card = document.getElementById('gallery-card-' + imageId);
                            if (card) card.remove();
                            this.galleryCount = Math.max(0, this.galleryCount - 1);
                        }
                        this.successMsg = '⭐ ' + (data.message || 'Kapak fotoğrafı güncellendi.');
                    } else {
                        this.errorMsg = data.message || 'İşlem başarısız oldu.';
                    }
                })
                .catch(err => {
                    this.errorMsg = 'Hata: ' + err.message;
                });
            },
            deleteCoverImage(deleteUrl) {
                if (!confirm('Ürünün ana kapak fotoğrafını silmek istediğinize emin misiniz?')) return;
                
                fetch(deleteUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ _method: 'DELETE' })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        this.coverSrc = '';
                        this.successMsg = (data.message || 'Ana kapak görseli kaldırıldı.');
                    } else {
                        this.errorMsg = data.message || 'Kapak resmi kaldırılamadı.';
                    }
                })
                .catch(err => {
                    this.errorMsg = 'Hata: ' + err.message;
                });
            },
            formatCoverToEcommerce() {
                this.coverLoading = true;
                this.errorMsg = '';
                this.successMsg = '';

                fetch('{{ route('admin.products.formatCover', $product) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(res => {
                    this.coverLoading = false;
                    if (res.success && res.image_asset) {
                        this.coverSrc = res.image_asset + '?v=' + Date.now();
                        this.successMsg = res.message;
                    } else {
                        this.errorMsg = res.message || 'Dönüştürme başarısız.';
                    }
                })
                .catch(err => {
                    this.coverLoading = false;
                    this.errorMsg = 'Hata: ' + err.message;
                });
            },
            formatGalleryImage(imageId) {
                this.galleryLoadingId = imageId;
                this.errorMsg = '';
                this.successMsg = '';

                fetch('/admin/products/images/' + imageId + '/format-ecommerce', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(res => {
                    this.galleryLoadingId = null;
                    if (res.success && res.image_asset) {
                        const imgEl = document.getElementById('gallery-img-' + imageId);
                        if (imgEl) imgEl.src = res.image_asset + '?v=' + Date.now();
                        this.successMsg = res.message;
                    } else {
                        this.errorMsg = res.message || 'Dönüştürme başarısız.';
                    }
                })
                .catch(err => {
                    this.galleryLoadingId = null;
                    this.errorMsg = 'Hata: ' + err.message;
                });
            },
            generateAndAttachAiImage() {
                const nameVal = document.getElementById('name').value;
                const p = this.aiImagePrompt || nameVal;
                if (!p) {
                    this.errorMsg = 'Lütfen üretmek istediğiniz görseli tarif edin.';
                    return;
                }
                this.aiImageLoading = true;
                this.errorMsg = '';
                this.successMsg = '';

                fetch('{{ route('admin.products.aiGenerateAttach', $product) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ prompt: p })
                })
                .then(res => res.json())
                .then(res => {
                    this.aiImageLoading = false;
                    if (res.success) {
                        this.successMsg = res.message + ' Sayfa güncelleniyor...';
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        this.errorMsg = res.message || 'Görsel üretilemedi.';
                    }
                })
                .catch(err => {
                    this.aiImageLoading = false;
                    this.errorMsg = 'Görsel üretim hatası: ' + err.message;
                });
            },
            // Yapay Zeka Görsel Stüdyosu (Canva & Gemini)
            showStudioModal: false,
            studioLoading: false,
            studioTargetType: 'cover',
            studioTargetImageId: null,
            studioOriginalPreview: '',
            studioOriginalPath: '',
            studioResultPreview: '',
            studioResultPath: '',
            studioCustomPrompt: '',
            studioPromptUsed: '',
            studioSuccessMsg: '',
            studioErrorMsg: '',
            openAiStudio(type, idx = null, imageId = null, directPath = null) {
                this.studioTargetType = type;
                this.studioTargetImageId = imageId;
                this.studioResultPreview = '';
                this.studioResultPath = '';
                this.studioCustomPrompt = '';
                this.studioPromptUsed = '';
                this.studioSuccessMsg = '';
                this.studioErrorMsg = '';

                if (type === 'cover') {
                    this.studioOriginalPreview = this.coverSrc;
                    this.studioOriginalPath = directPath || '{{ $product->image_path }}';
                } else if (imageId) {
                    const imgEl = document.getElementById('gallery-img-' + imageId);
                    this.studioOriginalPreview = imgEl ? imgEl.src : '';
                    this.studioOriginalPath = directPath || '';
                }

                if (!this.studioOriginalPath && !this.studioOriginalPreview) {
                    this.errorMsg = 'Düzenlenecek görsel bulunamadı.';
                    return;
                }

                this.showStudioModal = true;
            },
            runStudioAction(action) {
                if (!this.studioOriginalPath && !this.studioTargetImageId) return;

                this.studioLoading = true;
                this.studioErrorMsg = '';
                this.studioSuccessMsg = '';

                const productName = document.getElementById('name') ? document.getElementById('name').value : '{{ addslashes($product->name) }}';

                fetch('{{ route('admin.products.aiEditStudio') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        image_path: this.studioOriginalPath,
                        gallery_image_id: this.studioTargetImageId,
                        product_id: {{ $product->id }},
                        action: action,
                        custom_instruction: this.studioCustomPrompt,
                        product_name: productName
                    })
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(res => {
                    this.studioLoading = false;
                    if (res.status === 200 && res.body.success) {
                        this.studioResultPreview = res.body.image_asset;
                        this.studioResultPath = res.body.image_path;
                        this.studioPromptUsed = res.body.prompt_used || '';
                        this.studioSuccessMsg = res.body.message || 'Yapay zeka görseli başarıyla oluşturuldu!';
                    } else {
                        this.studioErrorMsg = res.body.message || 'Yapay zeka görseli işlenirken bir sorun oluştu.';
                    }
                })
                .catch(err => {
                    this.studioLoading = false;
                    this.studioErrorMsg = 'İşlem hatası: ' + err.message;
                });
            },
            applyStudioResult(mode = 'replace') {
                if (!this.studioResultPath) return;

                this.studioLoading = true;
                const targetMode = mode === 'replace' 
                    ? (this.studioTargetType === 'cover' ? 'cover' : 'gallery_replace')
                    : 'gallery_add';

                fetch('{{ route('admin.products.applyStudioImage') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        product_id: {{ $product->id }},
                        image_path: this.studioResultPath,
                        target_type: targetMode,
                        gallery_image_id: this.studioTargetImageId
                    })
                })
                .then(res => res.json())
                .then(res => {
                    this.studioLoading = false;
                    if (res.success) {
                        if (targetMode === 'cover') {
                            this.coverSrc = res.image_asset + '?v=' + Date.now();
                        } else if (targetMode === 'gallery_replace' && this.studioTargetImageId) {
                            const imgEl = document.getElementById('gallery-img-' + this.studioTargetImageId);
                            if (imgEl) imgEl.src = res.image_asset + '?v=' + Date.now();
                        } else {
                            setTimeout(() => window.location.reload(), 1000);
                        }
                        this.successMsg = (res.message || 'Görsel başarıyla güncellendi!');
                        this.showStudioModal = false;
                    } else {
                        this.studioErrorMsg = res.message || 'Görsel uygulanamadı.';
                    }
                })
                .catch(err => {
                    this.studioLoading = false;
                    this.studioErrorMsg = 'Uygulama hatası: ' + err.message;
                });
            },
            rewriteWithAi(targetField = 'all') {
                const nameVal = document.getElementById('name').value;
                if (!nameVal) {
                    this.errorMsg = 'Lütfen en azından bir ürün adı girin.';
                    return;
                }
                this.fieldLoading[targetField] = true;
                this.aiLoading = true;
                this.errorMsg = '';
                this.successMsg = '';

                const currentDesc = window.descQuillInstance ? window.descQuillInstance.root.innerHTML : '';
                const currentTech = window.techQuillInstance ? window.techQuillInstance.root.innerHTML : '';
                const currentShort = document.getElementById('short_description') ? document.getElementById('short_description').value : '';

                fetch('{{ route('admin.products.aiRewrite') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name: nameVal,
                        description: currentDesc,
                        technical_features: currentTech,
                        short_description: currentShort,
                        custom_instruction: this.aiInstruction,
                        target_field: targetField
                    })
                })
                .then(res => res.json())
                .then(res => {
                    this.fieldLoading[targetField] = false;
                    this.aiLoading = Object.values(this.fieldLoading).some(Boolean);
                    if (res.success && res.data) {
                        const d = res.data;
                        if (targetField === 'all' || targetField === 'name') {
                            if (d.name) document.getElementById('name').value = d.name;
                        }
                        if (d.short_description && document.getElementById('short_description')) {
                            const curShort = document.getElementById('short_description').value.trim();
                            if (!curShort || targetField === 'all' || targetField === 'short_description') {
                                document.getElementById('short_description').value = d.short_description;
                            }
                        }
                        if (targetField === 'all' || targetField === 'description') {
                            if (d.description && window.descQuillInstance) {
                                let cleanHtml = d.description;
                                if (!cleanHtml.includes('<p>') && !cleanHtml.includes('<br>')) {
                                    cleanHtml = cleanHtml.split(/\r?\n/).filter(x => x.trim()).map(x => `<p>${x}</p>`).join('');
                                }
                                window.descQuillInstance.clipboard.dangerouslyPasteHTML(cleanHtml);
                                const descInput = document.getElementById('description_input');
                                if (descInput) descInput.value = cleanHtml;
                            }
                        }
                        if (d.technical_features && window.techQuillInstance) {
                            const curTech = window.techQuillInstance.getText().trim();
                            if (!curTech || targetField === 'all' || targetField === 'technical_features') {
                                let techHtml = d.technical_features;
                                if (!techHtml.includes('<p>') && !techHtml.includes('<br>')) {
                                    techHtml = techHtml.split(/\r?\n/).filter(x => x.trim()).map(x => `<p>${x}</p>`).join('');
                                }
                                window.techQuillInstance.clipboard.dangerouslyPasteHTML(techHtml);
                                const techInput = document.getElementById('technical_features_input');
                                if (techInput) techInput.value = techHtml;
                            }
                        }
                        if (targetField === 'all' || targetField === 'seo') {
                            if (d.seo_title && document.getElementById('seo_title')) {
                                document.getElementById('seo_title').value = d.seo_title;
                            }
                            if (d.seo_description && document.getElementById('seo_description')) {
                                document.getElementById('seo_description').value = d.seo_description;
                            }
                        }
                        this.successMsg = (res.message || 'Yapay zeka içeriği başarıyla güncelledi!');
                        if (d.notice) {
                            this.successMsg += ' — ' + d.notice;
                        }
                    } else {
                        this.errorMsg = res.message || 'Yapay zeka yanıt veremedi.';
                    }
                })
                .catch(err => {
                    this.fieldLoading[targetField] = false;
                    this.aiLoading = Object.values(this.fieldLoading).some(Boolean);
                    this.errorMsg = 'Yapay zeka işleminde hata: ' + err.message;
                });
            }
         }">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-200/80 p-6 sm:p-8">
                
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 text-red-700 rounded-r-xl text-xs font-bold">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="product-form" action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-1" for="category_id">Kategori <span class="text-red-500">*</span></label>
                            <select class="border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-bold focus:border-blue-500 focus:ring-blue-500 bg-white" id="category_id" name="category_id" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (old('category_id', $product->category_id) == $category->id) ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-1" for="name">Ürün Adı <span class="text-red-500">*</span></label>
                            <input class="border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-bold focus:border-blue-500 focus:ring-blue-500" id="name" name="name" type="text" value="{{ old('name', $product->name) }}" required>
                        </div>
                    </div>

                    <!-- 🏷️ UYUMLU MARKALAR & ALT MODELLER (ÇOKLU SEÇİM) -->
                    <div class="border border-gray-200 rounded-2xl p-4 bg-gray-50/60">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-2.5">
                            <label class="block text-gray-800 text-xs font-black flex items-center gap-1.5">
                                <i class="fas fa-tags text-purple-600"></i> Uyumlu Markalar &amp; Alt Modeller (Çoklu Seçim Yapabilirsiniz)
                            </label>
                            <span class="text-[10px] text-gray-500">Bu ürün hangi markalarla/modellerle uyumluysa işaretleyin (Örn: İhlas, Aura Cebilon, LG...)</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 max-h-56 overflow-y-auto p-2 bg-white rounded-xl border border-gray-200">
                            @foreach($brands as $parentBrand)
                                <div class="p-2.5 rounded-xl border border-gray-100 bg-gray-50/70 hover:bg-purple-50/30 transition-colors">
                                    <label class="inline-flex items-center gap-2 cursor-pointer font-black text-xs text-gray-900">
                                        <input type="checkbox" name="brand_ids[]" value="{{ $parentBrand->id }}" 
                                               {{ in_array($parentBrand->id, old('brand_ids', $selectedBrandIds ?? [])) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                        <span>{{ $parentBrand->name }}</span>
                                    </label>
                                    @if($parentBrand->children->count() > 0)
                                        <div class="pl-5 pt-1.5 space-y-1 border-l-2 border-purple-200 ml-2 mt-1">
                                            @foreach($parentBrand->children as $childBrand)
                                                <label class="flex items-center gap-1.5 cursor-pointer text-[11px] font-bold text-gray-700 hover:text-purple-700">
                                                    <input type="checkbox" name="brand_ids[]" value="{{ $childBrand->id }}" 
                                                           {{ in_array($childBrand->id, old('brand_ids', $selectedBrandIds ?? [])) ? 'checked' : '' }}
                                                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 w-3.5 h-3.5">
                                                    <span>↳ {{ $childBrand->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Fiyat ve İndirim Alanları (3'lü Grid) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-1" for="price">
                                Normal Satış Fiyatı (TL) <span class="text-red-500">*</span>
                            </label>
                            <input class="border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-blue-600 font-black text-sm focus:border-blue-500 focus:ring-blue-500" id="price" name="price" type="number" step="0.01" value="{{ old('price', $product->price) }}" required>
                            <p class="text-[10px] text-gray-400 mt-1">İndirim girilirse üzeri çizili eski fiyat olur.</p>
                        </div>
                        <div>
                            <label class="block text-rose-700 text-xs font-extrabold mb-1" for="campaign_price">
                                🏷️ İndirimli / Kampanya Fiyatı (TL)
                            </label>
                            <input class="border border-rose-200 bg-rose-50/40 rounded-2xl w-full py-2.5 px-3.5 text-rose-600 font-black text-sm focus:border-rose-500 focus:ring-rose-500" id="campaign_price" name="campaign_price" type="number" step="0.01" value="{{ old('campaign_price', $product->campaign_price) }}" placeholder="Örn: 4990 (Boşsa indirim yok)">
                            <p class="text-[10px] text-rose-500 mt-1">Doldurulursa sitede % İndirimli olarak satılır.</p>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-1" for="cost_price">
                                Maliyet Fiyatı (TL) <span class="text-gray-400 font-normal">(Gizli)</span>
                            </label>
                            <input class="border border-amber-200 bg-amber-50/50 rounded-2xl w-full py-2.5 px-3.5 text-gray-800 text-xs font-bold focus:border-amber-500 focus:ring-amber-500" id="cost_price" name="cost_price" type="number" step="0.01" value="{{ old('cost_price', $product->cost_price) }}">
                            <p class="text-[10px] text-gray-400 mt-1">Sadece admin panelde kâr hesabı için görünür.</p>
                        </div>
                    </div>

                    <!-- Görseller Bölümü (Ana Görsel + Mevcut Galeri + Yeni Galeri + Yapay Zeka Stüdyosu) -->
                    <div class="bg-blue-50/40 p-6 rounded-3xl border border-blue-100 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-blue-100/70">
                            <h3 class="font-black text-sm text-blue-950 flex items-center gap-2">
                                <i class="fas fa-images text-blue-600"></i> Görselleri Yönet &amp; Güncelle
                            </h3>
                            <button type="button" @click="showAiImageBox = !showAiImageBox"
                                    class="inline-flex items-center gap-1.5 text-xs font-black text-purple-700 hover:text-purple-800 bg-purple-100 hover:bg-purple-200/80 px-3 py-1.5 rounded-xl transition-colors cursor-pointer w-fit">
                                <i class="fas fa-paint-brush"></i>
                                <span x-text="showAiImageBox ? 'Yapay Zeka Panelini Gizle' : '🎨 Yapay Zeka ile Yeni Görsel Üret'"></span>
                            </button>
                        </div>

                        <!-- 🎨 Yapay Zeka ile Stüdyo Görseli Üretme & Galeriye Ekleme -->
                        <div x-show="showAiImageBox" x-cloak class="p-4 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-2xl border border-purple-200 space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h4 class="text-xs font-black text-purple-950 flex items-center gap-1.5">
                                        <i class="fas fa-wand-magic-sparkles text-purple-600"></i> Yapay Zeka E-Ticaret Fotoğraf Stüdyosu
                                    </h4>
                                    <p class="text-[11px] text-purple-800 mt-0.5">
                                        Bu ürün için sıfırdan 4K beyaz stüdyo fonunda e-ticaret fotoğrafı ürettirin ve doğrudan galeriye ekleyin.
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row items-center gap-2">
                                <input type="text" x-model="aiImagePrompt" 
                                       placeholder="Örn: {{ $product->name }} modern mutfak tezgahında paslanmaz musluklu, stüdyo ışığı..."
                                       class="w-full text-xs py-2 px-3 border border-purple-200 rounded-xl bg-white focus:ring-purple-500 focus:border-purple-500">
                                <button type="button" @click="generateAndAttachAiImage()" :disabled="aiImageLoading"
                                        class="w-full sm:w-auto px-4 py-2 bg-purple-600 hover:bg-purple-700 disabled:bg-purple-400 text-white text-xs font-black rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer">
                                    <i class="fas fa-spinner fa-spin" x-show="aiImageLoading" x-cloak></i>
                                    <i class="fas fa-sparkles" x-show="!aiImageLoading"></i>
                                    <span x-text="aiImageLoading ? 'Üretiliyor...' : 'Görsel Üret &amp; Galeriye Ekle'"></span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1" for="image">Ana Kapak Görselini Değiştir</label>
                                @if($product->image_path)
                                    <div class="mb-2 flex items-center justify-between gap-3 p-2.5 bg-white rounded-2xl border border-blue-200 shadow-2xs">
                                        <div class="flex items-center gap-3">
                                            <img :src="coverSrc" alt="Kapak" class="h-14 w-14 object-contain rounded-xl border border-gray-100 bg-gray-50 p-1">
                                            <div>
                                                <span class="text-xs font-bold text-gray-900 block">Mevcut Ana Görsel</span>
                                                <span class="text-[10px] text-gray-400">Değiştirmek için aşağıdan seçin</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <button type="button" @click="openAiStudio('cover', null, null, '{{ $product->image_path }}')"
                                                    class="text-xs font-black text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 px-3 py-1.5 rounded-xl transition-all flex items-center gap-1.5 shadow-xs cursor-pointer"
                                                    title="Yapay Zeka Görsel Stüdyosu (Gemini & Canva)">
                                                <i class="fas fa-wand-magic-sparkles"></i>
                                                <span>🎨 AI ile Düzenle</span>
                                            </button>
                                            <button type="button" @click="formatCoverToEcommerce()" :disabled="coverLoading"
                                                    class="text-xs font-bold text-purple-700 hover:text-white hover:bg-purple-600 bg-purple-50 border border-purple-200 px-2.5 py-1.5 rounded-xl transition-all flex items-center gap-1 cursor-pointer"
                                                    title="Arka Planı Temizle &amp; E-Ticaret Beyaz Fonuna Al">
                                                <i class="fas fa-spinner fa-spin" x-show="coverLoading" x-cloak></i>
                                                <i class="fas fa-crop-alt" x-show="!coverLoading"></i>
                                                <span>Beyaz Fon</span>
                                            </button>
                                            <button type="button" 
                                                    @click="deleteCoverImage('{{ route('admin.products.cover.destroy', $product) }}')" 
                                                    class="text-xs font-bold text-red-600 hover:text-white hover:bg-red-600 bg-red-50 border border-red-200 px-2.5 py-1.5 rounded-xl transition-all flex items-center gap-1 cursor-pointer"
                                                    title="Kapak Resmini Sil">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Yeni Seçilen Kapak Canlı Önizleme -->
                                <div x-show="manualCoverPreview" x-cloak class="mb-2 p-3 bg-emerald-50/80 rounded-2xl border border-emerald-200 flex items-center justify-between gap-3 shadow-2xs">
                                    <div class="flex items-center gap-3">
                                        <img :src="manualCoverPreview" class="w-14 h-14 object-contain rounded-xl border bg-white p-1">
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="bg-emerald-600 text-white text-[9px] font-black px-2 py-0.5 rounded-full uppercase">Yeni Kapak Seçildi</span>
                                                <span class="text-[10px] text-gray-500" x-text="manualCoverSize"></span>
                                            </div>
                                            <span class="text-xs font-bold text-gray-800 block truncate max-w-xs" x-text="manualCoverName"></span>
                                            <span class="text-[10px] text-emerald-700 font-bold">✓ Ürünü Güncelle butonuna basınca kaydedilecek</span>
                                        </div>
                                    </div>
                                    <button type="button" @click="clearManualCover()" class="text-xs text-rose-600 hover:text-rose-800 p-2 rounded-xl hover:bg-rose-50 transition-colors" title="Kaldır">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <input @change="handleManualCover($event)" class="w-full text-xs text-gray-700 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer bg-white p-2 border rounded-2xl" id="image" name="image" type="file" accept="image/*">
                                <p class="text-[11px] text-gray-400 mt-1">JPG, PNG, WEBP veya AVIF seçebilirsiniz (Maks. 25 MB).</p>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1" for="gallery_images">Yeni Ek Galeri Fotoğrafları Ekle</label>
                                
                                <!-- Yeni Seçilen Galeri Fotoğrafları Önizlemesi -->
                                <div x-show="manualGalleryFiles.length > 0" x-cloak class="mb-3 p-3 bg-blue-50/70 rounded-2xl border border-blue-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-black text-gray-800 flex items-center gap-1.5">
                                            <i class="fas fa-images text-blue-600"></i>
                                            Yüklenecek Galeri (<span x-text="manualGalleryFiles.length"></span> Adet)
                                        </span>
                                        <button type="button" @click="clearManualGallery()" class="text-[10px] font-bold text-rose-600 hover:underline cursor-pointer">
                                            Temizle
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-4 sm:grid-cols-5 gap-2 max-h-36 overflow-y-auto p-1">
                                        <template x-for="(gFile, idx) in manualGalleryFiles" :key="idx">
                                            <div class="relative bg-white border border-blue-200 rounded-xl p-1 flex flex-col items-center justify-between shadow-2xs">
                                                <img :src="gFile.url" class="w-12 h-12 object-contain rounded-lg">
                                                <span class="text-[8px] text-gray-500 truncate w-full text-center mt-0.5" x-text="gFile.size"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <input @change="handleManualGallery($event)" class="w-full text-xs text-gray-700 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-800 file:text-white hover:file:bg-gray-900 cursor-pointer bg-white p-2 border rounded-2xl" id="gallery_images" name="gallery_images[]" type="file" accept="image/*" multiple>
                                <p class="text-[11px] text-gray-400 mt-1">Seçtiğiniz yeni fotoğraflar mevcut galeriye eklenecektir.</p>
                            </div>
                        </div>

                        <!-- Mevcut Galeri Fotoğrafları Listesi -->
                        @if($product->images->count() > 0)
                            <div class="pt-4 border-t border-blue-100" x-show="galleryCount > 0">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="block text-xs font-black text-gray-800">
                                        Mevcut Galeri Fotoğrafları (<span x-text="galleryCount"></span> Adet)
                                    </label>
                                    <span class="text-[11px] text-gray-500">İstediğiniz görseli tek tıkla AI ile düzenleyebilir, beyaz fona alabilir veya silebilirsiniz</span>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                                    @foreach($product->images as $img)
                                        <div id="gallery-card-{{ $img->id }}" class="relative group bg-white border border-gray-200 hover:border-blue-400 rounded-2xl p-2 shadow-2xs flex flex-col items-center justify-between transition-all">
                                            <div class="w-full h-24 flex items-center justify-center overflow-hidden">
                                                <img id="gallery-img-{{ $img->id }}" src="{{ asset('storage/' . $img->image_path) }}" class="max-h-full max-w-full object-contain">
                                            </div>
                                            <div class="w-full flex flex-col gap-1 mt-2 pt-2 border-t border-gray-100">
                                                <!-- AI ile Düzenle Butonu -->
                                                <button type="button" @click="openAiStudio('gallery', null, {{ $img->id }}, '{{ $img->image_path }}')"
                                                        class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-[9px] font-black py-1 px-1.5 rounded-lg shadow-2xs transition-all flex items-center justify-center gap-1 cursor-pointer"
                                                        title="Bu Görseli Yapay Zeka ile Düzenle (Gemini & Canva)">
                                                    <i class="fas fa-wand-magic-sparkles text-[8px]"></i>
                                                    <span>🎨 AI Düzenle</span>
                                                </button>

                                                <div class="flex items-center justify-between gap-1">
                                                    <button type="button" 
                                                            @click="setCoverImage({{ $img->id }}, '{{ route('admin.products.images.setCover', [$product, $img]) }}')"
                                                            class="flex-1 bg-amber-50 hover:bg-amber-500 hover:text-white text-amber-700 text-[10px] font-bold py-1 px-1.5 rounded-lg border border-amber-200 transition-all flex items-center justify-center gap-1 cursor-pointer" 
                                                            title="Ana Kapak Yap">
                                                        <i class="fas fa-star text-[9px]"></i> Kapak
                                                    </button>
                                                    <button type="button" 
                                                            @click="deleteGalleryImage({{ $img->id }}, '{{ route('admin.products.images.destroy', [$product, $img]) }}')"
                                                            class="bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 p-1 px-2 rounded-lg border border-rose-200 transition-all text-[10px] font-bold cursor-pointer" 
                                                            title="Fotoğrafı Sil (Sayfa Yenilenmez)">
                                                        <i class="fas fa-trash-alt text-[9px]"></i>
                                                    </button>
                                                </div>
                                                <button type="button" @click="formatGalleryImage({{ $img->id }})" :disabled="galleryLoadingId === {{ $img->id }}"
                                                        class="w-full bg-purple-50 hover:bg-purple-600 hover:text-white text-purple-700 text-[9px] font-black py-0.5 px-1.5 rounded-lg border border-purple-200 transition-all flex items-center justify-center gap-1 cursor-pointer"
                                                        title="Görseli Beyaz E-Ticaret Stüdyo Fonuna Çevir">
                                                    <i class="fas fa-spinner fa-spin text-[8px]" x-show="galleryLoadingId === {{ $img->id }}" x-cloak></i>
                                                    <i class="fas fa-crop-alt text-[8px]" x-show="galleryLoadingId !== {{ $img->id }}"></i>
                                                    <span>Beyaz Fon</span>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Kısa Açıklama -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-gray-700 text-xs font-bold" for="short_description">Kısa Özet Açıklama</label>
                            <button type="button" @click="rewriteWithAi('short_description')" :disabled="fieldLoading.short_description"
                                    class="inline-flex items-center gap-1.5 text-[11px] font-bold text-purple-700 hover:text-white bg-purple-50 hover:bg-purple-600 border border-purple-200 px-2.5 py-1 rounded-xl transition-all cursor-pointer">
                                <i class="fas fa-spinner fa-spin text-[10px]" x-show="fieldLoading.short_description" x-cloak></i>
                                <i class="fas fa-sparkles text-[10px]" x-show="!fieldLoading.short_description"></i>
                                <span x-text="fieldLoading.short_description ? 'Yazılıyor...' : 'AI ile Kısa Özet Yaz'"></span>
                            </button>
                        </div>
                        <textarea class="border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs focus:border-blue-500 focus:ring-blue-500" id="short_description" name="short_description" rows="2" placeholder="Ürünün 1-2 cümlelik vurucu spot açıklaması...">{{ old('short_description', $product->short_description) }}</textarea>
                    </div>

                    <!-- Başarı / Hata Mesajları -->
                    <div x-show="successMsg" x-cloak class="p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                        <span x-text="successMsg"></span>
                    </div>
                    <div x-show="errorMsg" x-cloak class="p-3 bg-rose-50 text-rose-800 border border-rose-200 rounded-xl text-xs font-bold flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-rose-600 text-sm"></i>
                        <span x-text="errorMsg"></span>
                    </div>

                    <!-- 🤖 Yapay Zeka Metin Yazarı Paneli (Tüm Metinler) -->
                    <div class="p-4 bg-gradient-to-r from-purple-50 via-indigo-50/60 to-blue-50 rounded-2xl border border-purple-200 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center text-sm shadow-xs flex-shrink-0">
                                <i class="fas fa-wand-magic-sparkles"></i>
                            </span>
                            <div>
                                <h4 class="text-xs font-black text-purple-950">Yapay Zeka Tüm Metinleri & Açıklamaları Yeniden Yazıcı</h4>
                                <p class="text-[10px] text-purple-800">Ürün başlığını, kısa özetini, detaylı HTML açıklamasını, teknik özelliklerini ve SEO alanlarını tek tıkla firmanıza özel satış diline dönüştürün.</p>
                            </div>
                        </div>
                        <button type="button" @click="rewriteWithAi('all')" :disabled="aiLoading"
                                class="bg-purple-600 hover:bg-purple-700 disabled:bg-purple-400 text-white text-xs font-black px-4 py-2.5 rounded-xl shadow-xs transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer flex-shrink-0">
                            <i class="fas fa-spinner fa-spin" x-show="fieldLoading.all" x-cloak></i>
                            <i class="fas fa-wand-magic-sparkles" x-show="!fieldLoading.all"></i>
                            <span x-text="fieldLoading.all ? 'Tüm Metinler Yazılıyor...' : '✨ Tüm Metinleri AI ile Yeniden Yaz'"></span>
                        </button>
                    </div>

                    <!-- Detaylı Açıklama (Zengin Görsel Editör) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-gray-900 text-xs font-black flex items-center gap-1.5">
                                <i class="fas fa-edit text-blue-600"></i> Detaylı Ürün Açıklaması (Görsel Metin Düzenleyici)
                            </label>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] text-gray-400 font-bold hidden sm:inline">Paragraf ve satır boşlukları tam korunur</span>
                                <button type="button" @click="rewriteWithAi('description')" :disabled="fieldLoading.description"
                                        class="inline-flex items-center gap-1.5 text-[11px] font-bold text-purple-700 hover:text-white bg-purple-50 hover:bg-purple-600 border border-purple-200 px-2.5 py-1 rounded-xl transition-all cursor-pointer">
                                    <i class="fas fa-spinner fa-spin text-[10px]" x-show="fieldLoading.description" x-cloak></i>
                                    <i class="fas fa-wand-magic-sparkles text-[10px]" x-show="!fieldLoading.description"></i>
                                    <span x-text="fieldLoading.description ? 'Yazılıyor...' : 'AI ile Detaylı Açıklama Yaz'"></span>
                                </button>
                            </div>
                        </div>
                        <div id="description_editor">{!! old('description', $product->description) !!}</div>
                        <input type="hidden" name="description" id="description_input">
                    </div>
                    
                    <!-- Teknik Özellikler (Zengin Görsel Editör) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-gray-900 text-xs font-black flex items-center gap-1.5">
                                <i class="fas fa-list-check text-blue-600"></i> Teknik Özellikler (Görsel Metin Düzenleyici)
                            </label>
                            <button type="button" @click="rewriteWithAi('technical_features')" :disabled="fieldLoading.technical_features"
                                    class="inline-flex items-center gap-1.5 text-[11px] font-bold text-purple-700 hover:text-white bg-purple-50 hover:bg-purple-600 border border-purple-200 px-2.5 py-1 rounded-xl transition-all cursor-pointer">
                                <i class="fas fa-spinner fa-spin text-[10px]" x-show="fieldLoading.technical_features" x-cloak></i>
                                <i class="fas fa-list-check text-[10px]" x-show="!fieldLoading.technical_features"></i>
                                <span x-text="fieldLoading.technical_features ? 'Çıkarılıyor...' : 'AI ile Özellikleri Çıkar'"></span>
                            </button>
                        </div>
                        <div id="technical_features_editor">{!! old('technical_features', $product->technical_features) !!}</div>
                        <input type="hidden" name="technical_features" id="technical_features_input">
                    </div>

                    <!-- Hediye Promosyonu Ayarları -->
                    <div class="bg-purple-50/50 p-6 rounded-3xl border border-purple-100 space-y-4"
                         x-data="{ hasGift: {{ old('has_gift', $product->has_gift ?? true) ? 'true' : 'false' }} }">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-sm shadow-2xs">
                                    <i class="fas fa-gift"></i>
                                </div>
                                <div>
                                    <h3 class="font-black text-sm text-purple-950">Hediye &amp; Promosyon Seçenekleri</h3>
                                    <p class="text-[11px] text-purple-800/80">Bu ürünü sipariş eden müşteriye özel hediye sunulsun mu?</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" name="has_gift" value="1" x-model="hasGift" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                <span class="ms-3 text-xs font-bold text-gray-900 whitespace-nowrap" x-text="hasGift ? 'Hediye Promosyonu Aktif' : 'Hediye Kapalı'"></span>
                            </label>
                        </div>

                        <div x-show="hasGift" x-transition.opacity.duration.200ms class="space-y-4 pt-3 border-t border-purple-100">
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1" for="gift_note">Özel Hediye Notu (İsteğe Bağlı)</label>
                                <input class="border border-purple-200 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs focus:border-purple-500 focus:ring-purple-500 bg-white" 
                                       id="gift_note" name="gift_note" type="text" 
                                       value="{{ old('gift_note', $product->gift_note) }}" 
                                       placeholder="Örn: Bu cihaza özel TDS test kalemi + su matarası hediye!">
                            </div>

                            @if(isset($gifts) && $gifts->count() > 0)
                                <div>
                                    <label class="block text-gray-700 text-xs font-bold mb-2">Bu Ürüne Özel Hediyeler (Seçilmezse tüm aktif hediyeler sunulur):</label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                                        @foreach($gifts as $g)
                                            @php
                                                $isSelected = $product->gifts ? $product->gifts->contains($g->id) : false;
                                            @endphp
                                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border bg-white cursor-pointer hover:border-purple-400 transition-colors">
                                                <input type="checkbox" name="gift_ids[]" value="{{ $g->id }}" 
                                                       {{ $isSelected ? 'checked' : '' }}
                                                       class="rounded border-gray-300 text-purple-600 shadow-sm focus:ring-purple-500 w-4 h-4">
                                                <div class="text-xs">
                                                    <div class="font-bold text-gray-900 truncate">{{ $g->name }}</div>
                                                    <div class="text-[10px] text-gray-500">{{ $g->badge ?: 'Hediye' }}</div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Rozetler ve Hizmet Ayarları -->
                    <div class="bg-gray-50/70 p-6 rounded-3xl border border-gray-200/80 space-y-4">
                        <h3 class="font-black text-sm text-gray-900 flex items-center gap-2">
                            <i class="fas fa-certificate text-amber-500"></i> Rozet ve Hizmet Seçenekleri
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1" for="warranty_period">Garanti Durumu / Süresi</label>
                                <input class="border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs" id="warranty_period" name="warranty_period" type="text" value="{{ old('warranty_period', $product->warranty_period) }}" placeholder="Örn: 2 Yıl Garanti, 1 Yıl Garanti">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1" for="custom_badge">Özel Ürün Rozeti (İsteğe Bağlı)</label>
                                <input class="border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs" id="custom_badge" name="custom_badge" type="text" value="{{ old('custom_badge', $product->custom_badge) }}" placeholder="Örn: EN ÇOK SATAN, FIRSAT ÜRÜNÜ">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 pt-2">
                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border bg-white cursor-pointer hover:border-emerald-400 transition-colors">
                                 <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-emerald-600 shadow-sm w-4 h-4" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                <span class="text-xs font-black text-emerald-700"><i class="fas fa-check-circle text-emerald-500 mr-1"></i> Satışta (Aktif)</span>
                            </label>
                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border bg-white cursor-pointer hover:border-blue-400 transition-colors">
                                <input type="checkbox" name="in_stock" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm w-4 h-4" {{ old('in_stock', $product->in_stock) ? 'checked' : '' }}>
                                <span class="text-xs font-black text-gray-800"><i class="fas fa-boxes text-blue-500 mr-1"></i> Stokta Var</span>
                            </label>
                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border bg-white cursor-pointer hover:border-blue-400 transition-colors">
                                <input type="checkbox" name="has_free_installation" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm w-4 h-4" {{ old('has_free_installation', $product->has_free_installation) ? 'checked' : '' }}>
                                <span class="text-xs font-black text-gray-800"><i class="fas fa-truck text-rose-500 mr-1"></i> Özel Kurye Teslimatı</span>
                            </label>
                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border bg-white cursor-pointer hover:border-purple-400 transition-colors">
                                <input type="checkbox" name="has_cash_on_delivery" value="1" class="rounded border-gray-300 text-purple-600 shadow-sm w-4 h-4" {{ old('has_cash_on_delivery', $product->has_cash_on_delivery) ? 'checked' : '' }}>
                                <span class="text-xs font-black text-gray-800"><i class="fas fa-money-bill-wave text-purple-500 mr-1"></i> Kapıda Ödeme</span>
                            </label>
                        </div>
                    </div>

                    <!-- SEO Ayarları -->
                    <div class="border-t border-gray-100 pt-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-black text-sm text-gray-900 flex items-center gap-1.5">
                                <i class="fab fa-google text-blue-500"></i> Google SEO Ayarları (Opsiyonel)
                            </h3>
                            <button type="button" @click="rewriteWithAi('seo')" :disabled="fieldLoading.seo"
                                    class="inline-flex items-center gap-1.5 text-[11px] font-bold text-purple-700 hover:text-white bg-purple-50 hover:bg-purple-600 border border-purple-200 px-2.5 py-1 rounded-xl transition-all cursor-pointer">
                                <i class="fas fa-spinner fa-spin text-[10px]" x-show="fieldLoading.seo" x-cloak></i>
                                <i class="fas fa-wand-magic-sparkles text-[10px]" x-show="!fieldLoading.seo"></i>
                                <span x-text="fieldLoading.seo ? 'Optimize Ediliyor...' : 'AI ile SEO Başlık & Açıklama Üret'"></span>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1" for="seo_title">Özel SEO Başlığı</label>
                                <input class="border border-gray-300 rounded-2xl w-full py-2 px-3 text-gray-900 text-xs" id="seo_title" name="seo_title" type="text" value="{{ old('seo_title', $product->seo_title) }}" placeholder="Boş bırakırsanız ürün adı kullanılır">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1" for="seo_description">Özel SEO Açıklaması</label>
                                <input class="border border-gray-300 rounded-2xl w-full py-2 px-3 text-gray-900 text-xs" id="seo_description" name="seo_description" type="text" value="{{ old('seo_description', $product->seo_description) }}" placeholder="Google arama sonuçlarında çıkacak açıklama">
                            </div>
                        </div>
                    </div>

                    <!-- Kaydet Butonları -->
                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                        <a class="px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-2xl transition-colors" href="{{ route('admin.products.index') }}">İptal</a>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-black py-3 px-8 rounded-2xl shadow-lg transition-all transform active:scale-95 text-xs flex items-center gap-2" type="submit">
                            <i class="fas fa-save"></i> Değişiklikleri Güncelle
                        </button>
                    </div>
                </form>

                @if($product->image_path)
                    <form id="delete-product-cover-form" action="{{ route('admin.products.cover.destroy', $product) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif

                @foreach($product->images as $img)
                    <form id="delete-gallery-img-form-{{ $img->id }}" action="{{ route('admin.products.images.destroy', [$product, $img]) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    <form id="set-cover-form-{{ $img->id }}" action="{{ route('admin.products.images.setCover', [$product, $img]) }}" method="POST" class="hidden">
                        @csrf
                    </form>
                @endforeach

            </div>
        </div>

        <!-- 🎨 YAPAY ZEKA GÖRSEL STÜDYOSU MODALI (Gemini & Canva) -->
        <div x-show="showStudioModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity" @click="showStudioModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
                <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-4xl border border-gray-100 flex flex-col max-h-[90vh]">
                    
                    <!-- Modal Başlığı -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 via-indigo-50/50 to-blue-50">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 text-white flex items-center justify-center shadow-md text-base">
                                <i class="fas fa-wand-magic-sparkles"></i>
                            </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm sm:text-base font-black text-gray-900">Yapay Zeka Görsel Stüdyosu</h3>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-purple-100 text-purple-700 uppercase">Gemini &amp; Canva</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">Seçilen görseli Gemini Vision ile inceleyin, arka planı temizleyin veya 4K stüdyo fotoğrafı olarak yeniden yaratın.</p>
                            </div>
                        </div>
                        <button type="button" @click="showStudioModal = false" class="w-8 h-8 rounded-full bg-white hover:bg-gray-100 text-gray-400 hover:text-gray-700 flex items-center justify-center transition-colors border border-gray-200 cursor-pointer">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>

                    <!-- Modal İçeriği (Kaydırılabilir) -->
                    <div class="p-6 overflow-y-auto space-y-6 flex-1">
                        
                        <!-- Bildirim Mesajları -->
                        <div x-show="studioSuccessMsg" x-cloak class="p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-2xl text-xs font-bold flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                            <span x-text="studioSuccessMsg"></span>
                        </div>
                        <div x-show="studioErrorMsg" x-cloak class="p-3 bg-rose-50 text-rose-800 border border-rose-200 rounded-2xl text-xs font-bold flex items-center gap-2">
                            <i class="fas fa-exclamation-circle text-rose-600 text-sm"></i>
                            <span x-text="studioErrorMsg"></span>
                        </div>

                        <!-- 1. Görsel Karşılaştırma Alanı (Öncesi / Sonrası) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Orijinal Seçilen Görsel -->
                            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 flex flex-col items-center justify-between text-center relative group">
                                <span class="absolute top-3 left-3 bg-gray-800/80 text-white text-[10px] font-black px-2.5 py-1 rounded-lg backdrop-blur-xs">
                                    1. MEVCUT GÖRSEL
                                </span>
                                <span class="absolute top-3 right-3 text-[10px] font-bold text-gray-400" x-text="studioTargetType === 'cover' ? 'Ana Kapak' : 'Galeri Fotoğrafı'"></span>
                                <div class="w-full h-56 flex items-center justify-center my-3">
                                    <img :src="studioOriginalPreview" class="max-h-52 max-w-full object-contain rounded-xl shadow-xs">
                                </div>
                                <span class="text-[11px] text-gray-500 font-medium">Düzenlenmek üzere seçilen mevcut hali</span>
                            </div>

                            <!-- Yapay Zeka Sonucu -->
                            <div class="bg-purple-50/40 border-2 border-dashed border-purple-200 rounded-2xl p-4 flex flex-col items-center justify-between text-center relative">
                                <span class="absolute top-3 left-3 bg-purple-600 text-white text-[10px] font-black px-2.5 py-1 rounded-lg shadow-xs flex items-center gap-1">
                                    <i class="fas fa-sparkles"></i> 2. YAPAY ZEKA STÜDYO ÇEKİMİ
                                </span>

                                <!-- Sonuç Görseli Varsa -->
                                <template x-if="studioResultPreview && !studioLoading">
                                    <div class="w-full h-56 flex items-center justify-center my-3">
                                        <img :src="studioResultPreview" class="max-h-52 max-w-full object-contain rounded-xl shadow-md border border-purple-200 bg-white">
                                    </div>
                                </template>

                                <!-- Yükleniyor Durumu -->
                                <div x-show="studioLoading" x-cloak class="w-full h-56 flex flex-col items-center justify-center gap-3 my-3">
                                    <div class="w-12 h-12 rounded-full border-4 border-purple-200 border-t-purple-600 animate-spin"></div>
                                    <div class="text-center">
                                        <span class="text-xs font-black text-purple-900 block">Yapay Zeka Çalışıyor...</span>
                                        <span class="text-[11px] text-purple-600">Görsel taranıyor, arka plan siliniyor ve stüdyo sahnesi modelleniyor.</span>
                                    </div>
                                </div>

                                <!-- Henüz İşlem Yapılmadıysa -->
                                <div x-show="!studioResultPreview && !studioLoading" class="w-full h-56 flex flex-col items-center justify-center gap-2 my-3 text-purple-400">
                                    <i class="fas fa-camera-retro text-4xl opacity-40"></i>
                                    <span class="text-xs font-bold text-gray-500">Aşağıdaki işlemlerden birini seçerek yapay zekayı başlatın</span>
                                </div>

                                <span class="text-[11px] text-purple-700 font-medium" x-show="studioResultPreview">Saf beyaz fonlu, katalog kalitesinde 1024x1024 e-ticaret görseli</span>
                            </div>
                        </div>

                        <!-- 2. Yapay Zeka İşlem Seçenekleri (3 Buton & Özel Prompt) -->
                        <div class="bg-gray-50/80 p-4 rounded-2xl border border-gray-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-black text-gray-900 flex items-center gap-1.5">
                                    <i class="fas fa-bolt text-amber-500"></i> Bir Yapay Zeka İşlemi Seçin:
                                </span>
                                <span class="text-[11px] text-gray-400">Tek tıkla dönüştürün</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Seçenek 1: Gemini Stüdyo Çekimi (Yeniden Modelle) -->
                                <button type="button" @click="runStudioAction('reimagine')" :disabled="studioLoading"
                                        class="p-3.5 rounded-2xl bg-white hover:bg-purple-50 border border-purple-200 hover:border-purple-400 text-left transition-all shadow-xs group cursor-pointer">
                                    <div class="flex items-center gap-2.5 mb-1">
                                        <span class="w-7 h-7 rounded-xl bg-purple-600 text-white flex items-center justify-center text-xs group-hover:scale-110 transition-transform">
                                            <i class="fas fa-wand-magic-sparkles"></i>
                                        </span>
                                        <span class="text-xs font-black text-purple-950">Gemini Stüdyo Çekimi (Yeniden Yarat)</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 leading-snug">
                                        Gemini Vision cihazı tanır; kirli tezgahı, su lekelerini ve filigranları yok edip bembeyaz katalog fotoğrafı üretir.
                                    </p>
                                </button>

                                <!-- Seçenek 2: Canva Tarzı Arka Planı Temizle (Beyaz Fon) -->
                                <button type="button" @click="runStudioAction('remove_bg')" :disabled="studioLoading"
                                        class="p-3.5 rounded-2xl bg-white hover:bg-blue-50 border border-blue-200 hover:border-blue-400 text-left transition-all shadow-xs group cursor-pointer">
                                    <div class="flex items-center gap-2.5 mb-1">
                                        <span class="w-7 h-7 rounded-xl bg-blue-600 text-white flex items-center justify-center text-xs group-hover:scale-110 transition-transform">
                                            <i class="fas fa-crop-alt"></i>
                                        </span>
                                        <span class="text-xs font-black text-blue-950">Canva Tarzı Arka Planı Temizle</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 leading-snug">
                                        Orijinal ürünü korur; arkasındaki gereksiz fonu silip 1:1 e-ticaret beyaz tuvaline ortalar.
                                    </p>
                                </button>
                            </div>

                            <!-- Seçenek 3: Özel Talimatla Düzenle (Custom Prompt) -->
                            <div class="pt-2 border-t border-gray-200/70">
                                <label class="block text-xs font-black text-gray-800 mb-1 flex items-center gap-1.5">
                                    <i class="fas fa-comment-dots text-purple-600"></i> Özel Talimatla Düzenletin (Opsiyonel Prompt):
                                </label>
                                <div class="flex flex-col sm:flex-row items-center gap-2">
                                    <input type="text" x-model="studioCustomPrompt" 
                                           placeholder="Örn: Krom kuğu musluk ekle, parlat, arkadaki dağınıklığı sil ve stüdyo ışığı ver..."
                                           class="w-full text-xs py-2 px-3 border border-gray-300 rounded-xl bg-white focus:ring-purple-500 focus:border-purple-500">
                                    <button type="button" @click="runStudioAction('custom_prompt')" :disabled="studioLoading"
                                            class="w-full sm:w-auto px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 disabled:opacity-50 text-white text-xs font-black rounded-xl shadow-xs transition-all flex items-center justify-center gap-1.5 whitespace-nowrap cursor-pointer">
                                        <i class="fas fa-paper-plane text-[10px]"></i>
                                        <span>Talimatla Üret</span>
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    <span class="text-[10px] text-gray-400 font-bold self-center">Hızlı Fikirler:</span>
                                    <button type="button" @click="studioCustomPrompt = 'Krom kuğu musluk ve çelik su tankı ekle, stüdyo çekimi'" class="text-[10px] bg-white border border-gray-200 hover:border-purple-300 px-2 py-0.5 rounded-lg text-gray-600 hover:text-purple-700 transition-colors cursor-pointer">+ Krom Kuğu Musluk Ekle</button>
                                    <button type="button" @click="studioCustomPrompt = 'Arka planı tamamen kaldır, saf beyaz katalog fonu yap'" class="text-[10px] bg-white border border-gray-200 hover:border-purple-300 px-2 py-0.5 rounded-lg text-gray-600 hover:text-purple-700 transition-colors cursor-pointer">+ Saf Beyaz Fon</button>
                                    <button type="button" @click="studioCustomPrompt = 'Ürünü 45 derece açıyla göster, pürüzsüz ve parlak olsun'" class="text-[10px] bg-white border border-gray-200 hover:border-purple-300 px-2 py-0.5 rounded-lg text-gray-600 hover:text-purple-700 transition-colors cursor-pointer">+ 45° Açılı Stüdyo Çekimi</button>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Alt Barı (Aksiyonlar) -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="text-[11px] text-gray-500 text-left">
                            <span x-show="studioResultPreview" class="text-emerald-700 font-bold flex items-center gap-1">
                                <i class="fas fa-check"></i> Yeni görsel hazır! Değiştirmek veya eklemek için sağdaki butonları kullanın.
                            </span>
                            <span x-show="!studioResultPreview">
                                Düzenleme sonucunu beğenene kadar dilediğiniz kadar yeniden üretebilirsiniz.
                            </span>
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                            <button type="button" @click="showStudioModal = false"
                                    class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 text-xs font-bold rounded-xl transition-colors cursor-pointer">
                                Vazgeç
                            </button>

                            <button type="button" x-show="studioResultPreview" @click="applyStudioResult('add')" :disabled="studioLoading"
                                    class="px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-black rounded-xl transition-colors border border-blue-200 flex items-center gap-1.5 cursor-pointer">
                                <i class="fas fa-plus"></i>
                                <span>Galeriye Yeni Ekle</span>
                            </button>

                            <button type="button" x-show="studioResultPreview" @click="applyStudioResult('replace')" :disabled="studioLoading"
                                    class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black rounded-xl shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                                <i class="fas fa-check"></i>
                                <span x-text="studioTargetType === 'cover' ? 'Kapak Görselini Değiştir' : 'Bu Galeri Görselini Değiştir'"></span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Quill.js Başlatma Scripti -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toolbarOptions = [
                [{ 'header': [1, 2, 3, 4, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['link', 'clean']
            ];

            const descQuill = new Quill('#description_editor', {
                theme: 'snow',
                modules: { toolbar: toolbarOptions },
                placeholder: 'Ürünün tüm özelliklerini, avantajlarını ve detaylarını buraya yazın veya Word/web sayfasından kopyalayıp yapıştırın...'
            });
            window.descQuillInstance = descQuill;

            const techQuill = new Quill('#technical_features_editor', {
                theme: 'snow',
                modules: { toolbar: toolbarOptions },
                placeholder: 'Teknik maddeleri girin (Örn: • Filtre Aşaması: 5 Aşama, • Membran: LG Membran...)'
            });
            window.techQuillInstance = techQuill;

            const form = document.getElementById('product-form');
            form.addEventListener('submit', function () {
                const descHtml = descQuill.root.innerHTML;
                const techHtml = techQuill.root.innerHTML;
                document.getElementById('description_input').value = descHtml === '<p><br></p>' ? '' : descHtml;
                document.getElementById('technical_features_input').value = techHtml === '<p><br></p>' ? '' : techHtml;
            });
        });
    </script>
</x-app-layout>
