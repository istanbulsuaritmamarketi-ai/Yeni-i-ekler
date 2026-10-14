<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight">
                    {{ __('Yeni Ürün Ekle') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Kataloğa yeni su arıtma cihazı veya yedek filtre ekleyin</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:text-gray-900 text-xs font-bold bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl transition-colors flex items-center gap-1.5">
                <i class="fas fa-arrow-left"></i> Listeye Dön
            </a>
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
            scrapeUrl: '',
            useAi: true,
            aiInstruction: '',
            loading: false,
            aiLoading: false,
            errorMsg: '',
            successMsg: '',
            previewImg: '',
            coverPath: '',
            galleryPreviews: [],
            galleryPaths: [],
            manualCoverPreview: '',
            manualCoverName: '',
            manualCoverSize: '',
            manualGalleryFiles: [],
            hasGift: {{ old('has_gift', '1') == '1' ? 'true' : 'false' }},
            handleManualCover(event) {
                const file = event.target.files[0];
                if (file) {
                    this.manualCoverPreview = URL.createObjectURL(file);
                    this.manualCoverName = file.name;
                    this.manualCoverSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                    // Manuel kapak seçildiğinde, varsa eski scraped görsel yerine öncelik ver
                    const scrapedInput = document.getElementById('scraped_image_path');
                    if (scrapedInput) scrapedInput.value = '';
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
            fetchProduct() {
                if (!this.scrapeUrl) {
                    this.errorMsg = 'Lütfen geçerli bir ürün URL bağlantısı girin.';
                    return;
                }
                this.loading = true;
                this.errorMsg = '';
                this.successMsg = '';
                this.previewImg = '';
                this.coverPath = '';
                this.galleryPreviews = [];
                this.galleryPaths = [];

                fetch('{{ route('admin.products.scrapeUrl') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ 
                        url: this.scrapeUrl,
                        use_ai: this.useAi,
                        custom_instruction: this.aiInstruction
                    })
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(res => {
                    this.loading = false;
                    if (res.status === 200 && res.body.success) {
                        const d = res.body.data;
                        document.getElementById('name').value = d.name || '';
                        if (d.price) {
                            document.getElementById('price').value = d.price;
                        }
                        if (d.campaign_price) {
                            document.getElementById('campaign_price').value = d.campaign_price;
                        }
                        if (d.short_description) {
                            document.getElementById('short_description').value = d.short_description;
                        }
                        if (d.description && window.descQuillInstance) {
                            let cleanHtml = d.description;
                            if (!cleanHtml.includes('<p>') && !cleanHtml.includes('<br>')) {
                                cleanHtml = cleanHtml.split(/\r?\n/).filter(x => x.trim()).map(x => `<p>${x}</p>`).join('');
                            }
                            window.descQuillInstance.clipboard.dangerouslyPasteHTML(cleanHtml);
                            const descInput = document.getElementById('description_input');
                            if (descInput) descInput.value = cleanHtml;
                        }
                        if (d.technical_features && window.techQuillInstance) {
                            let techHtml = d.technical_features;
                            if (!techHtml.includes('<p>') && !techHtml.includes('<br>')) {
                                techHtml = techHtml.split(/\r?\n/).filter(x => x.trim()).map(x => `<p>${x}</p>`).join('');
                            }
                            window.techQuillInstance.clipboard.dangerouslyPasteHTML(techHtml);
                            const techInput = document.getElementById('technical_features_input');
                            if (techInput) techInput.value = techHtml;
                        }
                        if (d.seo_title && document.getElementById('seo_title')) {
                            document.getElementById('seo_title').value = d.seo_title;
                        }
                        if (d.seo_description && document.getElementById('seo_description')) {
                            document.getElementById('seo_description').value = d.seo_description;
                        }
                        if (d.local_image_path) {
                            this.coverPath = d.local_image_path;
                            this.previewImg = d.local_image_asset;
                            document.getElementById('scraped_image_path').value = d.local_image_path;
                        }
                        if (d.local_gallery_paths && d.local_gallery_paths.length > 0) {
                            this.galleryPaths = [...d.local_gallery_paths];
                            this.galleryPreviews = [...d.local_gallery_assets];
                        }
                        let msg = 'Ürün bilgileri ve fotoğrafları başarıyla çekildi!';
                        if (this.useAi) {
                            msg += ' Yapay zeka ile kurumsal dile özel yeniden yazıldı.';
                        }
                        if (d.ai_notice) {
                            msg += ' (' + d.ai_notice + ')';
                        }
                        this.successMsg = msg;
                    } else {
                        this.errorMsg = res.body.message || 'Ürün bilgisi çekilemedi. Lütfen bağlantıyı kontrol edin.';
                    }
                })
                .catch(err => {
                    this.loading = false;
                    this.errorMsg = 'Bağlantı hatası oluştu: ' + err.message;
                });
            },
            fieldLoading: {
                all: false,
                short_description: false,
                description: false,
                technical_features: false,
                seo: false
            },
            rewriteWithAi(targetField = 'all') {
                const nameVal = document.getElementById('name').value;
                if (!nameVal) {
                    this.errorMsg = 'Lütfen yapay zekanın özelleştirmesi için en azından bir ürün adı girin.';
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
                        this.successMsg = (res.message || 'Ürün metinleri başarıyla firmanıza özel yeniden yazıldı!');
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
            },
            removeGalleryItem(idx) {
                this.galleryPreviews.splice(idx, 1);
                this.galleryPaths.splice(idx, 1);
            },
            makeCover(idx) {
                const oldCoverPath = this.coverPath;
                const oldCoverPreview = this.previewImg;
                
                this.coverPath = this.galleryPaths[idx];
                this.previewImg = this.galleryPreviews[idx];
                document.getElementById('scraped_image_path').value = this.coverPath;

                this.galleryPreviews.splice(idx, 1);
                this.galleryPaths.splice(idx, 1);

                if (oldCoverPath && oldCoverPreview) {
                    this.galleryPaths.unshift(oldCoverPath);
                    this.galleryPreviews.unshift(oldCoverPreview);
                }
            },
            removeCover() {
                if (this.galleryPreviews.length > 0) {
                    this.coverPath = this.galleryPaths[0];
                    this.previewImg = this.galleryPreviews[0];
                    document.getElementById('scraped_image_path').value = this.coverPath;
                    this.galleryPreviews.shift();
                    this.galleryPaths.shift();
                } else {
                    this.coverPath = '';
                    this.previewImg = '';
                    document.getElementById('scraped_image_path').value = '';
                }
            },
            showAiImageBox: false,
            aiImagePrompt: '',
            aiImageLoading: false,
            generateAiImage() {
                const nameVal = document.getElementById('name').value;
                const p = this.aiImagePrompt || nameVal;
                if (!p) {
                    this.errorMsg = 'Lütfen üretmek istediğiniz görseli tarif edin (örn: Modern mutfak tezgahında paslanmaz musluklu su arıtma cihazı).';
                    return;
                }
                this.aiImageLoading = true;
                this.errorMsg = '';
                this.successMsg = '';

                fetch('{{ route('admin.products.aiGenerateImage') }}', {
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
                    if (res.success && res.image_path) {
                        this.galleryPaths.push(res.image_path);
                        this.galleryPreviews.push(res.image_asset);
                        if (!this.coverPath) {
                            this.coverPath = res.image_path;
                            this.previewImg = res.image_asset;
                            document.getElementById('scraped_image_path').value = res.image_path;
                        }
                        this.successMsg = (res.message || 'Yapay zeka ile stüdyo fotoğrafı üretildi ve galeriye eklendi!');
                        this.showAiImageBox = false;
                    } else {
                        this.errorMsg = res.message || 'Yapay zeka görseli üretilemedi.';
                    }
                })
                .catch(err => {
                    this.aiImageLoading = false;
                    this.errorMsg = 'Görsel üretiminde hata: ' + err.message;
                });
            },
            formatLoading: false,
            formatToEcommerce(type, idx = null) {
                const targetPath = type === 'cover' ? this.coverPath : this.galleryPaths[idx];
                if (!targetPath) return;

                this.formatLoading = true;
                this.errorMsg = '';
                this.successMsg = '';

                fetch('{{ route('admin.products.formatEcommerceImage') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ image_path: targetPath })
                })
                .then(res => res.json())
                .then(res => {
                    this.formatLoading = false;
                    if (res.success && res.image_path) {
                        if (type === 'cover') {
                            this.coverPath = res.image_path;
                            this.previewImg = res.image_asset;
                            document.getElementById('scraped_image_path').value = res.image_path;
                        } else if (idx !== null) {
                            this.galleryPaths[idx] = res.image_path;
                            this.galleryPreviews[idx] = res.image_asset;
                        }
                        this.successMsg = (res.message || 'Görsel bembeyaz e-ticaret stüdyo fonuna uyarlandı!');
                    } else {
                        this.errorMsg = res.message || 'Dönüştürme başarısız.';
                    }
                })
                .catch(err => {
                    this.formatLoading = false;
                    this.errorMsg = 'İşlem hatası: ' + err.message;
                });
            },
            // Yapay Zeka Görsel Stüdyosu (Canva & Gemini)
            showStudioModal: false,
            studioLoading: false,
            studioTargetType: 'cover',
            studioTargetIdx: null,
            studioOriginalPreview: '',
            studioOriginalPath: '',
            studioResultPreview: '',
            studioResultPath: '',
            studioCustomPrompt: '',
            studioPromptUsed: '',
            studioSuccessMsg: '',
            studioErrorMsg: '',
            openAiStudio(type, idx = null) {
                this.studioTargetType = type;
                this.studioTargetIdx = idx;
                this.studioResultPreview = '';
                this.studioResultPath = '';
                this.studioCustomPrompt = '';
                this.studioPromptUsed = '';
                this.studioSuccessMsg = '';
                this.studioErrorMsg = '';

                if (type === 'cover') {
                    this.studioOriginalPreview = this.previewImg || this.manualCoverPreview;
                    this.studioOriginalPath = this.coverPath;
                } else if (idx !== null) {
                    this.studioOriginalPreview = this.galleryPreviews[idx];
                    this.studioOriginalPath = this.galleryPaths[idx];
                }

                if (!this.studioOriginalPath) {
                    this.errorMsg = 'Lütfen önce düzenlenecek bir görsel seçin veya ürün bağlantısından fotoğrafları çekin.';
                    return;
                }

                this.showStudioModal = true;
            },
            runStudioAction(action) {
                if (!this.studioOriginalPath) return;

                this.studioLoading = true;
                this.studioErrorMsg = '';
                this.studioSuccessMsg = '';

                const productName = document.getElementById('name') ? document.getElementById('name').value : '';

                fetch('{{ route('admin.products.aiEditStudio') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        image_path: this.studioOriginalPath,
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

                if (mode === 'replace') {
                    if (this.studioTargetType === 'cover') {
                        this.coverPath = this.studioResultPath;
                        this.previewImg = this.studioResultPreview;
                        const el = document.getElementById('scraped_image_path');
                        if (el) el.value = this.studioResultPath;
                    } else if (this.studioTargetIdx !== null) {
                        this.galleryPaths[this.studioTargetIdx] = this.studioResultPath;
                        this.galleryPreviews[this.studioTargetIdx] = this.studioResultPreview;
                    }
                    this.successMsg = 'Yapay zeka ile düzenlenen görsel başarıyla güncellendi!';
                } else if (mode === 'add') {
                    this.galleryPaths.push(this.studioResultPath);
                    this.galleryPreviews.push(this.studioResultPreview);
                    this.successMsg = 'Yapay zeka görseli galeriye yeni fotoğraf olarak eklendi!';
                }

                this.showStudioModal = false;
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

                <!-- 1 TIKLA URL'DEN ÜRÜN ÇEKME SİHİRBAZI -->
                <div class="mb-8 p-5 bg-gradient-to-r from-blue-50 via-indigo-50/40 to-white rounded-3xl border-2 border-blue-300 shadow-sm">
                    
                    <div class="flex items-center justify-between gap-2 pb-3 border-b border-blue-100">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center text-sm shadow-xs">
                                <i class="fas fa-magic"></i>
                            </span>
                            <div>
                                <h3 class="font-black text-sm text-blue-950">1 Tıkla Linkten / Web Sayfasından Ürün Çek</h3>
                                <p class="text-[11px] text-blue-800/80">Herhangi bir sitenin ürün linkini yapıştırın; başlık, fiyat, açıklama, kapak ve tüm galeri fotoğrafları otomatik insin</p>
                            </div>
                        </div>
                        <span class="bg-blue-600 text-white text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase">HIZLI EKLE</span>
                    </div>

                    <div class="mt-3 flex flex-col sm:flex-row items-center gap-2.5">
                        <input type="url" x-model="scrapeUrl" placeholder="https://ornek-site.com/urun-detay-sayfasi..."
                               class="w-full text-xs py-2.5 px-3.5 border border-blue-200 rounded-2xl bg-white focus:ring-blue-500 focus:border-blue-500 font-medium">
                        <button type="button" @click="fetchProduct()" :disabled="loading"
                                class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-xs font-black rounded-2xl shadow-sm transition-all flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer">
                            <i class="fas fa-spinner fa-spin" x-show="loading" x-cloak></i>
                            <i class="fas fa-download" x-show="!loading"></i>
                            <span x-text="loading ? 'Çekiliyor...' : 'Bilgileri & Tüm Fotoğrafları Çek'"></span>
                        </button>
                    </div>

                    <!-- Yapay Zeka ile Otomatik Firma Özelleştirmesi -->
                    <div class="mt-3 pt-3 border-t border-blue-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" x-model="useAi" class="rounded text-purple-600 focus:ring-purple-500 border-gray-300 w-4 h-4">
                            <span class="text-xs font-black text-purple-900 flex items-center gap-1.5">
                                <i class="fas fa-sparkles text-purple-600"></i> Metinleri &amp; Başlığı Yapay Zeka İle Firmama Özel Yeniden Yaz
                            </span>
                        </label>
                        <div class="flex items-center gap-1 text-[11px] text-gray-500 font-medium">
                            <span>(Rakip marka temizliği, ferah paragraflar ve SEO uyumu)</span>
                        </div>
                    </div>

                    <!-- Başarı / Hata Mesajları -->
                    <div x-show="successMsg" x-cloak class="mt-3 p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                        <span x-text="successMsg"></span>
                    </div>
                    <div x-show="errorMsg" x-cloak class="mt-3 p-3 bg-rose-50 text-rose-800 border border-rose-200 rounded-xl text-xs font-bold flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-rose-600 text-sm"></i>
                        <span x-text="errorMsg"></span>
                    </div>

                    <!-- Otomatik Çekilen Görseller Önizlemesi & Yönetimi (Kapak + Galeri) -->
                    <div x-show="previewImg || galleryPreviews.length > 0" x-cloak class="mt-3 p-4 bg-white rounded-2xl border border-blue-100 space-y-4">
                        
                        <!-- Ana Kapak Görseli -->
                        <div x-show="previewImg" class="flex items-center justify-between p-3 bg-blue-50/50 rounded-2xl border border-blue-100">
                            <div class="flex items-center gap-3">
                                <div class="relative group">
                                    <img :src="previewImg" class="w-16 h-16 object-contain rounded-xl border bg-white p-1">
                                    <span class="absolute -top-1.5 -left-1.5 bg-blue-600 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full shadow">KAPAK</span>
                                </div>
                                <div>
                                    <span class="text-xs font-black text-gray-900 block">Ana Kapak Görseli</span>
                                    <span class="text-[11px] text-emerald-600 font-bold">Katalog vitrininde bu fotoğraf görünecek.</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="openAiStudio('cover')" title="Yapay Zeka Görsel Stüdyosu (Gemini & Canva)"
                                        class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-[11px] font-black transition-all flex items-center gap-1.5 shadow-xs cursor-pointer">
                                    <i class="fas fa-wand-magic-sparkles"></i>
                                    <span>AI ile Düzenle</span>
                                </button>
                                <button type="button" @click="formatToEcommerce('cover')" :disabled="formatLoading" title="Arka Planı Temizle & E-Ticaret Beyaz Fonuna Al"
                                        class="px-3 py-1.5 rounded-xl bg-purple-100 hover:bg-purple-200 text-purple-800 text-[11px] font-black transition-all flex items-center gap-1.5 cursor-pointer">
                                    <i class="fas fa-spinner fa-spin" x-show="formatLoading" x-cloak></i>
                                    <i class="fas fa-crop-alt text-purple-600" x-show="!formatLoading"></i>
                                    <span>Beyaz Fon</span>
                                </button>
                                <button type="button" @click="removeCover()" title="Kapağı Kaldır"
                                        class="w-8 h-8 rounded-xl bg-red-100 hover:bg-red-200 text-red-700 flex items-center justify-center text-xs font-black transition-all cursor-pointer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Galeri Görselleri (Silinebilir & Kapak Yapılabilir) -->
                        <div x-show="galleryPreviews.length > 0" class="pt-2 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-black text-gray-800 flex items-center gap-1.5">
                                    <i class="fas fa-images text-blue-600"></i> Galeri Fotoğrafları (<span x-text="galleryPreviews.length"></span> adet)
                                </span>
                                <span class="text-[11px] text-gray-400 font-medium">İstediğiniz fotoğrafı AI ile stüdyo kalitesine getirebilirsiniz.</span>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
                                <template x-for="(gUrl, idx) in galleryPreviews" :key="idx">
                                    <div class="relative group bg-gray-50 rounded-2xl border border-gray-200 p-1 flex flex-col items-center justify-center hover:border-blue-300 transition-all shadow-2xs">
                                        
                                        <img :src="gUrl" class="w-16 h-16 object-contain mix-blend-multiply rounded-xl">
                                        
                                        <!-- Aksiyon Butonları (Sil) -->
                                        <div class="absolute top-1 right-1 flex items-center gap-1">
                                            <button type="button" @click="removeGalleryItem(idx)" title="Bu Fotoğrafı Sil"
                                                    class="w-6 h-6 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center text-[10px] shadow-sm transition-transform hover:scale-110 cursor-pointer">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>

                                        <button type="button" @click="openAiStudio('gallery', idx)" title="Bu fotoğrafı yapay zeka ile düzenle"
                                                class="w-full mt-1 py-0.5 text-[8px] font-black bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-lg transition-colors flex items-center justify-center gap-0.5 cursor-pointer shadow-2xs">
                                            <i class="fas fa-wand-magic-sparkles text-[7px]"></i> AI Düzenle
                                        </button>

                                        <button type="button" @click="makeCover(idx)" title="Bu fotoğrafı ana kapak yap"
                                                class="w-full mt-0.5 py-0.5 text-[9px] font-black bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 rounded-lg transition-colors flex items-center justify-center gap-1 cursor-pointer">
                                            <i class="fas fa-star text-[8px]"></i> Kapak Yap
                                        </button>

                                        <button type="button" @click="formatToEcommerce('gallery', idx)" :disabled="formatLoading" title="Beyaz Stüdyo Fonuna Çevir"
                                                class="w-full mt-0.5 py-0.5 text-[8px] font-black bg-purple-50 hover:bg-purple-600 hover:text-white text-purple-700 rounded-lg transition-colors flex items-center justify-center gap-0.5 cursor-pointer">
                                            <i class="fas fa-crop-alt text-[7px]"></i> Beyaz Fon
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>

                </div>

                <form id="product-form" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="scraped_image_path" id="scraped_image_path" :value="coverPath">
                    
                    <!-- Dinamik Scraped Galeri Hidden Inputları (Varsa) -->
                    <div id="scraped_gallery_inputs_container">
                        <template x-for="(path, idx) in galleryPaths" :key="idx">
                            <input type="hidden" name="scraped_gallery_paths[]" :value="path">
                        </template>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-1" for="category_id">Kategori <span class="text-red-500">*</span></label>
                            <select class="border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-bold focus:border-blue-500 focus:ring-blue-500 bg-white" id="category_id" name="category_id" required>
                                <option value="">-- Kategori Seçin --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-1" for="name">Ürün Adı <span class="text-red-500">*</span></label>
                            <input class="border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-bold focus:border-blue-500 focus:ring-blue-500" id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Örn: 12 Aşamalı Alkali Pompalı Su Arıtma Cihazı" required>
                        </div>
                    </div>

                    <!-- UYUMLU MARKALAR & ALT MODELLER (ÇOKLU SEÇİM) -->
                    <div class="border border-gray-200 rounded-2xl p-4 bg-gray-50/60">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-2.5">
                            <label class="block text-gray-800 text-xs font-black flex items-center gap-1.5">
                                <i class="fas fa-tags text-purple-600"></i> Uyumlu Markalar &amp; Alt Modeller (Çoklu Seçim Yapabilirsiniz)
                            </label>
                            <span class="text-[10px] text-gray-500">Bu ürün hangi markalarla/modellerle uyumluysa işaretleyin</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 max-h-56 overflow-y-auto p-2 bg-white rounded-xl border border-gray-200">
                            @foreach($brands as $parentBrand)
                                <div class="p-2.5 rounded-xl border border-gray-100 bg-gray-50/70 hover:bg-purple-50/30 transition-colors">
                                    <label class="inline-flex items-center gap-2 cursor-pointer font-black text-xs text-gray-900">
                                        <input type="checkbox" name="brand_ids[]" value="{{ $parentBrand->id }}" 
                                               {{ (is_array(old('brand_ids')) && in_array($parentBrand->id, old('brand_ids'))) || request('brand_id') == $parentBrand->id ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                        <span>{{ $parentBrand->name }}</span>
                                    </label>
                                    @if($parentBrand->children->count() > 0)
                                        <div class="pl-5 pt-1.5 space-y-1 border-l-2 border-purple-200 ml-2 mt-1">
                                            @foreach($parentBrand->children as $childBrand)
                                                <label class="flex items-center gap-1.5 cursor-pointer text-[11px] font-bold text-gray-700 hover:text-purple-700">
                                                    <input type="checkbox" name="brand_ids[]" value="{{ $childBrand->id }}" 
                                                           {{ (is_array(old('brand_ids')) && in_array($childBrand->id, old('brand_ids'))) || request('brand_id') == $childBrand->id ? 'checked' : '' }}
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
                            <input class="border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-blue-600 font-black text-sm focus:border-blue-500 focus:ring-blue-500" id="price" name="price" type="number" step="0.01" value="{{ old('price') }}" placeholder="Örn: 6500" required>
                            <p class="text-[10px] text-gray-400 mt-1">İndirim girilirse üzeri çizili eski fiyat olur.</p>
                        </div>
                        <div>
                            <label class="block text-rose-700 text-xs font-extrabold mb-1" for="campaign_price">
                                İndirimli / Kampanya Fiyatı (TL)
                            </label>
                            <input class="border border-rose-200 bg-rose-50/40 rounded-2xl w-full py-2.5 px-3.5 text-rose-600 font-black text-sm focus:border-rose-500 focus:ring-rose-500" id="campaign_price" name="campaign_price" type="number" step="0.01" value="{{ old('campaign_price') }}" placeholder="Örn: 4990 (Opsiyonel)">
                            <p class="text-[10px] text-rose-500 mt-1">Doldurulursa sitede % İndirimli olarak satılır.</p>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-1" for="cost_price">
                                Maliyet Fiyatı (TL) <span class="text-gray-400 font-normal">(Gizli)</span>
                            </label>
                            <input class="border border-amber-200 bg-amber-50/50 rounded-2xl w-full py-2.5 px-3.5 text-gray-800 text-xs font-bold focus:border-amber-500 focus:ring-amber-500" id="cost_price" name="cost_price" type="number" step="0.01" value="{{ old('cost_price') }}" placeholder="Kâr analizi için">
                            <p class="text-[10px] text-gray-400 mt-1">Sadece admin panelde kâr hesabı için görünür.</p>
                        </div>
                    </div>

                    <!-- Görseller Bölümü (Ana Görsel + Galeri + Yapay Zeka Stüdyosu) -->
                    <div class="bg-blue-50/40 p-6 rounded-3xl border border-blue-100 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-blue-100/70">
                            <h3 class="font-black text-sm text-blue-950 flex items-center gap-2">
                                <i class="fas fa-images text-blue-600"></i> Ürün Görselleri
                            </h3>
                            <button type="button" @click="showAiImageBox = !showAiImageBox"
                                    class="inline-flex items-center gap-1.5 text-xs font-black text-purple-700 hover:text-purple-800 bg-purple-100 hover:bg-purple-200/80 px-3 py-1.5 rounded-xl transition-colors cursor-pointer w-fit">
                                <i class="fas fa-paint-brush"></i>
                                <span x-text="showAiImageBox ? 'Yapay Zeka Panelini Gizle' : 'Yapay Zeka ile Yeni Görsel Üret'"></span>
                            </button>
                        </div>

                        <!-- Yapay Zeka ile Stüdyo Görseli Üretme Kutusu -->
                        <div x-show="showAiImageBox" x-cloak class="p-4 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-2xl border border-purple-200 space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h4 class="text-xs font-black text-purple-950 flex items-center gap-1.5">
                                        <i class="fas fa-wand-magic-sparkles text-purple-600"></i> Yapay Zeka E-Ticaret Fotoğraf Stüdyosu
                                    </h4>
                                    <p class="text-[11px] text-purple-800 mt-0.5">
                                        İstediğiniz sahneyi yazın (veya boş bırakırsanız ürün adınıza göre); yapay zeka sıfırdan 4K stüdyo kalitesinde e-ticaret fotoğrafı üretsin!
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row items-center gap-2">
                                <input type="text" x-model="aiImagePrompt" 
                                       placeholder="Örn: Modern lüks mutfak tezgahında paslanmaz musluklu su arıtma cihazı, stüdyo ışığı..."
                                       class="w-full text-xs py-2 px-3 border border-purple-200 rounded-xl bg-white focus:ring-purple-500 focus:border-purple-500">
                                <button type="button" @click="generateAiImage()" :disabled="aiImageLoading"
                                        class="w-full sm:w-auto px-4 py-2 bg-purple-600 hover:bg-purple-700 disabled:bg-purple-400 text-white text-xs font-black rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer">
                                    <i class="fas fa-spinner fa-spin" x-show="aiImageLoading" x-cloak></i>
                                    <i class="fas fa-sparkles" x-show="!aiImageLoading"></i>
                                    <span x-text="aiImageLoading ? 'Üretiliyor...' : 'Görsel Üret'"></span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1" for="image">Ana Kapak Görseli <span class="text-red-500">*</span></label>
                                
                                <!-- Manuel Seçilen Kapak Canlı Önizlemesi -->
                                <div x-show="manualCoverPreview" x-cloak class="mb-3 p-3 bg-blue-50/80 rounded-2xl border border-blue-200 flex items-center justify-between gap-3 shadow-2xs">
                                    <div class="flex items-center gap-3">
                                        <img :src="manualCoverPreview" class="w-14 h-14 object-contain rounded-xl border bg-white p-1">
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="bg-blue-600 text-white text-[9px] font-black px-2 py-0.5 rounded-full uppercase">Seçilen Kapak</span>
                                                <span class="text-[10px] text-gray-500" x-text="manualCoverSize"></span>
                                            </div>
                                            <span class="text-xs font-bold text-gray-800 block truncate max-w-xs" x-text="manualCoverName"></span>
                                            <span class="text-[10px] text-emerald-600 font-bold"><i class="fas fa-check text-[9px] mr-1"></i> Form kaydedilince bu görsel yüklenecek</span>
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
                                <label class="block text-gray-700 text-xs font-bold mb-1" for="gallery_images">Ek Galeri Görselleri (Çoklu Seçilebilir)</label>
                                
                                <!-- Manuel Seçilen Galeri Fotoğrafları Önizlemesi -->
                                <div x-show="manualGalleryFiles.length > 0" x-cloak class="mb-3 p-3 bg-gray-50 rounded-2xl border border-gray-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-black text-gray-800 flex items-center gap-1.5">
                                            <i class="fas fa-images text-purple-600"></i>
                                            Yüklenecek Galeri (<span x-text="manualGalleryFiles.length"></span> Adet)
                                        </span>
                                        <button type="button" @click="clearManualGallery()" class="text-[10px] font-bold text-rose-600 hover:underline cursor-pointer">
                                            Temizle
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-4 sm:grid-cols-5 gap-2 max-h-36 overflow-y-auto p-1">
                                        <template x-for="(gFile, idx) in manualGalleryFiles" :key="idx">
                                            <div class="relative bg-white border border-gray-200 rounded-xl p-1 flex flex-col items-center justify-between shadow-2xs">
                                                <img :src="gFile.url" class="w-12 h-12 object-contain rounded-lg">
                                                <span class="text-[8px] text-gray-500 truncate w-full text-center mt-0.5" x-text="gFile.size"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <input @change="handleManualGallery($event)" class="w-full text-xs text-gray-700 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-800 file:text-white hover:file:bg-gray-900 cursor-pointer bg-white p-2 border rounded-2xl" id="gallery_images" name="gallery_images[]" type="file" accept="image/*" multiple>
                                <p class="text-[11px] text-gray-400 mt-1">Ctrl veya Shift tuşuyla birden fazla ek fotoğraf seçebilirsiniz.</p>
                            </div>
                        </div>
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
                        <textarea class="border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs focus:border-blue-500 focus:ring-blue-500" id="short_description" name="short_description" rows="2" placeholder="Ürün sayfasında fiyatın hemen altında görünecek 1-2 cümlelik spot açıklama...">{{ old('short_description') }}</textarea>
                    </div>

                    <!-- Yapay Zeka Metin Yazarı Paneli (Tüm Metinler) -->
                    <div class="p-4 bg-gradient-to-r from-purple-50 via-indigo-50/60 to-blue-50 rounded-2xl border border-purple-200 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center text-sm shadow-xs flex-shrink-0">
                                <i class="fas fa-wand-magic-sparkles"></i>
                            </span>
                            <div>
                                <h4 class="text-xs font-black text-purple-950">Yapay Zeka Tüm Metinleri & Açıklamaları Yazıcı</h4>
                                <p class="text-[10px] text-purple-800">Ürün adını, kısa özetini, detaylı HTML açıklamasını, teknik özelliklerini ve SEO alanlarını tek tıkla satış diline dönüştürün.</p>
                            </div>
                        </div>
                        <button type="button" @click="rewriteWithAi('all')" :disabled="aiLoading"
                                class="bg-purple-600 hover:bg-purple-700 disabled:bg-purple-400 text-white text-xs font-black px-4 py-2.5 rounded-xl shadow-xs transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer flex-shrink-0">
                            <i class="fas fa-spinner fa-spin" x-show="fieldLoading.all" x-cloak></i>
                            <i class="fas fa-wand-magic-sparkles" x-show="!fieldLoading.all"></i>
                            <span x-text="fieldLoading.all ? 'Tüm Metinler Yazılıyor...' : 'Tüm Metinleri AI ile Yeniden Yaz'"></span>
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
                        <div id="description_editor">{!! old('description') !!}</div>
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
                        <div id="technical_features_editor">{!! old('technical_features') !!}</div>
                        <input type="hidden" name="technical_features" id="technical_features_input">
                    </div>

                    <!-- Hediye Promosyonu Ayarları -->
                    <div class="bg-purple-50/50 p-6 rounded-3xl border border-purple-100 space-y-4">
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
                                       value="{{ old('gift_note') }}" 
                                       placeholder="Örn: Bu cihaza özel TDS test kalemi + su matarası hediye!">
                            </div>

                            @if(isset($gifts) && $gifts->count() > 0)
                                <div>
                                    <label class="block text-gray-700 text-xs font-bold mb-2">Bu Ürüne Özel Hediyeler (Seçilmezse tüm aktif hediyeler sunulur):</label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                                        @foreach($gifts as $g)
                                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border bg-white cursor-pointer hover:border-purple-400 transition-colors">
                                                <input type="checkbox" name="gift_ids[]" value="{{ $g->id }}" 
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
                                <input class="border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs" id="warranty_period" name="warranty_period" type="text" value="{{ old('warranty_period', '2 Yıl Garanti') }}" placeholder="Örn: 2 Yıl Garanti, 1 Yıl Garanti">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1" for="custom_badge">Özel Ürün Rozeti (İsteğe Bağlı)</label>
                                <input class="border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs" id="custom_badge" name="custom_badge" type="text" value="{{ old('custom_badge') }}" placeholder="Örn: EN ÇOK SATAN, FIRSAT ÜRÜNÜ">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 pt-2">
                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border bg-white cursor-pointer hover:border-emerald-400 transition-colors">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-emerald-600 shadow-sm w-4 h-4" {{ old('is_active', true) ? 'checked' : '' }}>
                                <span class="text-xs font-black text-emerald-700"><i class="fas fa-check-circle text-emerald-500 mr-1"></i> Satışta (Aktif)</span>
                            </label>
                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border bg-white cursor-pointer hover:border-blue-400 transition-colors">
                                <input type="checkbox" name="in_stock" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm w-4 h-4" {{ old('in_stock', true) ? 'checked' : '' }}>
                                <span class="text-xs font-black text-gray-800"><i class="fas fa-boxes text-blue-500 mr-1"></i> Stokta Var</span>
                            </label>
                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border bg-white cursor-pointer hover:border-blue-400 transition-colors">
                                <input type="checkbox" name="has_free_installation" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm w-4 h-4" {{ old('has_free_installation', true) ? 'checked' : '' }}>
                                <span class="text-xs font-black text-gray-800"><i class="fas fa-truck text-rose-500 mr-1"></i> Özel Kurye Teslimatı</span>
                            </label>
                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border bg-white cursor-pointer hover:border-purple-400 transition-colors">
                                <input type="checkbox" name="has_cash_on_delivery" value="1" class="rounded border-gray-300 text-purple-600 shadow-sm w-4 h-4" {{ old('has_cash_on_delivery', true) ? 'checked' : '' }}>
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
                                <input class="border border-gray-300 rounded-2xl w-full py-2 px-3 text-gray-900 text-xs" id="seo_title" name="seo_title" type="text" value="{{ old('seo_title') }}" placeholder="Boş bırakırsanız ürün adı kullanılır">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1" for="seo_description">Özel SEO Açıklaması</label>
                                <input class="border border-gray-300 rounded-2xl w-full py-2 px-3 text-gray-900 text-xs" id="seo_description" name="seo_description" type="text" value="{{ old('seo_description') }}" placeholder="Google arama sonuçlarında çıkacak açıklama">
                            </div>
                        </div>
                    </div>

                    <!-- Kaydet Butonları -->
                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                        <a class="px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-2xl transition-colors" href="{{ route('admin.products.index') }}">İptal</a>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-black py-3 px-8 rounded-2xl shadow-lg transition-all transform active:scale-95 text-xs flex items-center gap-2" type="submit">
                            <i class="fas fa-save"></i> Ürünü Kaydet
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- YAPAY ZEKA GÖRSEL STÜDYOSU MODALI (Gemini & Canva) -->
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
                                <span class="absolute top-3 right-3 text-[10px] font-bold text-gray-400" x-text="studioTargetType === 'cover' ? 'Ana Kapak' : 'Galeri #' + ((studioTargetIdx || 0) + 1)"></span>
                                <div class="w-full h-56 flex items-center justify-center my-3">
                                    <img :src="studioOriginalPreview" class="max-h-52 max-w-full object-contain rounded-xl shadow-xs">
                                </div>
                                <span class="text-[11px] text-gray-500 font-medium">Linkten çekilen veya yüklenen orijinal hali</span>
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

                            <button type="button" x-show="studioResultPreview" @click="applyStudioResult('add')"
                                    class="px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-black rounded-xl transition-colors border border-blue-200 flex items-center gap-1.5 cursor-pointer">
                                <i class="fas fa-plus"></i>
                                <span>Galeriye Yeni Ekle</span>
                            </button>

                            <button type="button" x-show="studioResultPreview" @click="applyStudioResult('replace')"
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
