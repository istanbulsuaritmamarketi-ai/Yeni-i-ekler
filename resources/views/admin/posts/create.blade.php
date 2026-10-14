<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-xl text-gray-900 leading-tight">
                    {{ __('Yeni Blog Makalesi Ekle') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Siteniz için SEO uyumlu rehber veya bilgilendirici blog makalesi oluşturun</p>
            </div>
            <a href="{{ route('admin.posts.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-colors">
                <i class="fas fa-arrow-left"></i> Listeye Dön
            </a>
        </div>
    </x-slot>

    <!-- Quill.js CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .ql-toolbar.ql-snow {
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            border-color: #e2e8f0;
            background: #f8fafc;
        }
        .ql-container.ql-snow {
            border-bottom-left-radius: 1rem;
            border-bottom-right-radius: 1rem;
            border-color: #e2e8f0;
            min-height: 280px;
            font-family: inherit;
            font-size: 14px;
            line-height: 1.8;
            background: #ffffff;
        }
    </style>

    <div class="py-8"
         x-data="{
            scrapeUrl: '',
            useAiOnScrape: true,
            aiTopic: '',
            aiInstruction: '',
            showAiTopicBox: false,
            loading: false,
            aiLoading: false,
            fieldLoading: {
                all: false,
                title: false,
                excerpt: false,
                content: false,
                seo: false
            },
            errorMsg: '',
            successMsg: '',
            previewImg: '',
            coverPath: '',
            fetchArticle() {
                if (!this.scrapeUrl) {
                    this.errorMsg = 'Lütfen geçerli bir makale URL bağlantısı girin.';
                    return;
                }
                this.loading = true;
                this.errorMsg = '';
                this.successMsg = '';
                this.previewImg = '';
                this.coverPath = '';

                fetch('{{ route('admin.posts.scrapeUrl') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ 
                        url: this.scrapeUrl,
                        use_ai: this.useAiOnScrape,
                        custom_instruction: this.aiInstruction
                    })
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(res => {
                    this.loading = false;
                    if (res.status === 200 && res.body.success) {
                        const d = res.body.data;
                        document.getElementById('title').value = d.title || '';
                        document.getElementById('slug').value = d.slug || '';
                        if (d.excerpt) {
                            document.getElementById('excerpt').value = d.excerpt;
                        }
                        if (d.content && window.articleQuillInstance) {
                            window.articleQuillInstance.root.innerHTML = d.content;
                        }
                        if (d.meta_title) {
                            document.getElementById('meta_title').value = d.meta_title;
                        }
                        if (d.meta_description) {
                            document.getElementById('meta_description').value = d.meta_description;
                        }
                        if (d.local_image_path) {
                            this.coverPath = d.local_image_path;
                            this.previewImg = d.local_image_asset;
                            document.getElementById('scraped_image_path').value = d.local_image_path;
                        }
                        let msg = 'Makale başarıyla çekildi!';
                        if (this.useAiOnScrape) {
                            msg += ' Yapay zeka ile kurumsal sitemize özel uyarlandı.';
                        }
                        if (d.ai_notice) {
                            msg += ' (' + d.ai_notice + ')';
                        }
                        this.successMsg = msg;
                    } else {
                        this.errorMsg = res.body.message || 'Makale bilgisi çekilemedi. Lütfen bağlantıyı kontrol edin.';
                    }
                })
                .catch(err => {
                    this.loading = false;
                    this.errorMsg = 'Bağlantı hatası: ' + err.message;
                });
            },
            generateAiPost(targetField = 'all') {
                const titleVal = document.getElementById('title')?.value || '';
                const excerptVal = document.getElementById('excerpt')?.value || '';
                const contentVal = window.articleQuillInstance ? window.articleQuillInstance.root.innerHTML : '';
                const topicVal = this.aiTopic || titleVal;

                if (!topicVal && targetField === 'all') {
                    this.errorMsg = 'Lütfen makale konusu veya anahtar kelimeleri girin.';
                    return;
                }

                this.fieldLoading[targetField] = true;
                if (targetField === 'all') this.aiLoading = true;
                this.errorMsg = '';
                this.successMsg = '';

                fetch('{{ route('admin.posts.aiGenerate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        topic: topicVal,
                        title: titleVal,
                        excerpt: excerptVal,
                        content: contentVal === '<p><br></p>' ? '' : contentVal,
                        custom_instruction: this.aiInstruction,
                        target_field: targetField
                    })
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(res => {
                    this.fieldLoading[targetField] = false;
                    if (targetField === 'all') this.aiLoading = false;

                    if (res.status === 200 && res.body.success) {
                        const d = res.body.data;
                        if (d.title && (targetField === 'all' || targetField === 'title')) {
                            document.getElementById('title').value = d.title;
                        }
                        if (d.slug && (targetField === 'all' || targetField === 'title')) {
                            document.getElementById('slug').value = d.slug;
                        }
                        if (d.excerpt && (targetField === 'all' || targetField === 'excerpt')) {
                            document.getElementById('excerpt').value = d.excerpt;
                        }
                        if (d.content && (targetField === 'all' || targetField === 'content') && window.articleQuillInstance) {
                            window.articleQuillInstance.root.innerHTML = d.content;
                        }
                        if (d.meta_title && (targetField === 'all' || targetField === 'seo')) {
                            document.getElementById('meta_title').value = d.meta_title;
                        }
                        if (d.meta_description && (targetField === 'all' || targetField === 'seo')) {
                            document.getElementById('meta_description').value = d.meta_description;
                        }
                        this.successMsg = res.body.message || 'Yapay zeka içeriği başarıyla üretti!';
                    } else {
                        this.errorMsg = res.body.message || 'Yapay zeka içeriği üretilirken bir hata oluştu.';
                    }
                })
                .catch(err => {
                    this.fieldLoading[targetField] = false;
                    if (targetField === 'all') this.aiLoading = false;
                    this.errorMsg = 'Hata oluştu: ' + err.message;
                });
            },
            removeCover() {
                this.coverPath = '';
                this.previewImg = '';
                document.getElementById('scraped_image_path').value = '';
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

                <!-- SİHİRBAZLAR GRID'İ (URL'den Çekme & AI ile Sıfırdan Yazdırma) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-8">
                    
                    <!-- 1. SİHİRBAZ: 1 TIKLA URL'DEN MAKALE / BLOG ÇEKME -->
                    <div class="p-5 bg-gradient-to-br from-emerald-50 via-teal-50/40 to-white rounded-3xl border-2 border-emerald-300 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 pb-3 border-b border-emerald-100">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xs shadow-sm">
                                        <i class="fas fa-link"></i>
                                    </span>
                                    <div>
                                        <h3 class="font-black text-xs sm:text-sm text-emerald-950">Linkten Makale Çek</h3>
                                        <p class="text-[10px] text-emerald-800/80">Herhangi bir blog veya haber linkinden anında indir</p>
                                    </div>
                                </div>
                                <span class="bg-emerald-600 text-white text-[9px] font-black px-2 py-0.5 rounded-full uppercase">URL ÇEKİCİ</span>
                            </div>

                            <div class="mt-3 space-y-2">
                                <input type="url" x-model="scrapeUrl" placeholder="https://ornek-blog-sitesi.com/makale-detayi..."
                                       class="w-full text-xs py-2 px-3 border border-emerald-200 rounded-xl bg-white focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                                
                                <div class="flex items-center gap-2 pt-1">
                                    <input type="checkbox" id="use_ai_scrape" x-model="useAiOnScrape" class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500 w-3.5 h-3.5">
                                    <label for="use_ai_scrape" class="text-[11px] font-bold text-emerald-950 cursor-pointer">
                                        Çekilen makaleyi yapay zeka ile kurumsal sitemize uyarla
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" @click="fetchArticle()" :disabled="loading"
                                    class="w-full px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:bg-emerald-400 text-white text-xs font-black rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-spinner fa-spin" x-show="loading" x-cloak></i>
                                <i class="fas fa-download" x-show="!loading"></i>
                                <span x-text="loading ? 'Makale İndiriliyor...' : 'Makaleyi Çek & Doldur'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- 2. SİHİRBAZ: YAPAY ZEKA İLE SIFIRDAN BLOG MAKALENİ YAZDIR -->
                    <div class="p-5 bg-gradient-to-br from-purple-50 via-indigo-50/40 to-white rounded-3xl border-2 border-purple-300 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 pb-3 border-b border-purple-100">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-purple-600 text-white flex items-center justify-center text-xs shadow-sm">
                                        <i class="fas fa-wand-magic-sparkles"></i>
                                    </span>
                                    <div>
                                        <h3 class="font-black text-xs sm:text-sm text-purple-950">Yapay Zeka ile Makale Yazdır</h3>
                                        <p class="text-[10px] text-purple-800/80">Konuyu yazın; başlık, spot, detaylı HTML ve SEO tek tıkla üretilsin</p>
                                    </div>
                                </div>
                                <span class="bg-purple-600 text-white text-[9px] font-black px-2 py-0.5 rounded-full uppercase">AI BLOG YAZARI</span>
                            </div>

                            <div class="mt-3 space-y-2">
                                <input type="text" x-model="aiTopic" placeholder="Örn: Çiçek bakımı ve vazoda çiçek tazeliğini koruma yöntemleri..."
                                       class="w-full text-xs py-2 px-3 border border-purple-200 rounded-xl bg-white focus:ring-purple-500 focus:border-purple-500 font-medium">
                                
                                <input type="text" x-model="aiInstruction" placeholder="Özel not: (örn: madde imleri olsun, püf noktalarına değin...)"
                                       class="w-full text-[11px] py-1.5 px-3 border border-purple-200/70 rounded-xl bg-white focus:ring-purple-500 focus:border-purple-500 text-gray-700">
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" @click="generateAiPost('all')" :disabled="aiLoading"
                                    class="w-full px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 disabled:opacity-50 text-white text-xs font-black rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-spinner fa-spin" x-show="aiLoading" x-cloak></i>
                                <i class="fas fa-wand-magic-sparkles" x-show="!aiLoading"></i>
                                <span x-text="aiLoading ? 'Yapay Zeka Makaleyi Yazıyor...' : '1 Tıkla Makaleyi Yazdır'"></span>
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Başarı / Hata Mesajları -->
                <div x-show="successMsg" x-cloak class="mb-6 p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-2xl text-xs font-bold flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                        <span x-text="successMsg"></span>
                    </div>
                    <button type="button" @click="successMsg = ''" class="text-emerald-700 hover:text-emerald-900 text-xs font-black"><i class="fas fa-times"></i></button>
                </div>
                <div x-show="errorMsg" x-cloak class="mb-6 p-3 bg-rose-50 text-rose-800 border border-rose-200 rounded-2xl text-xs font-bold flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-rose-600 text-base"></i>
                        <span x-text="errorMsg"></span>
                    </div>
                    <button type="button" @click="errorMsg = ''" class="text-rose-700 hover:text-rose-900 text-xs font-black"><i class="fas fa-times"></i></button>
                </div>

                <!-- Otomatik Çekilen Kapak Görseli Önizlemesi -->
                <div x-show="previewImg" x-cloak class="mb-6 p-3 bg-white rounded-2xl border border-emerald-100 flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-3">
                        <img :src="previewImg" class="w-16 h-16 object-cover rounded-xl border bg-gray-50">
                        <div>
                            <span class="text-xs font-black text-gray-900 block">Kapak Görseli (İndirildi & WebP Optimize Edildi)</span>
                            <span class="text-[11px] text-emerald-600 font-bold">Makale kapağı olarak kaydedilmeye hazır.</span>
                        </div>
                    </div>
                    <button type="button" @click="removeCover()" title="Görseli Kaldır"
                            class="w-8 h-8 rounded-xl bg-red-100 hover:bg-red-200 text-red-700 flex items-center justify-center text-xs font-black transition-all cursor-pointer">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>

                <form id="post-form" action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="scraped_image_path" id="scraped_image_path" :value="coverPath">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-gray-700" for="title">Makale Başlığı <span class="text-red-500">*</span></label>
                                <button type="button" @click="generateAiPost('title')" :disabled="fieldLoading.title"
                                        class="text-[10px] font-black text-purple-700 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 px-2 py-0.5 rounded-lg transition-colors flex items-center gap-1 cursor-pointer">
                                    <i class="fas fa-spinner fa-spin" x-show="fieldLoading.title" x-cloak></i>
                                    <i class="fas fa-wand-magic-sparkles text-purple-600" x-show="!fieldLoading.title"></i>
                                    <span>AI ile Başlık Üret</span>
                                </button>
                            </div>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                placeholder="Örn: Ev Tipi Su Arıtma Cihazı Filtresi Ne Zaman Değişir?"
                                class="w-full text-xs font-bold py-2.5 px-3.5 rounded-2xl border-gray-300 shadow-xs focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1" for="slug">Özel URL (Slug - İsteğe Bağlı)</label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                                placeholder="filtre-ne-zaman-degisir (Boş bırakırsanız başlıktan otomatik üretilir)"
                                class="w-full text-xs py-2.5 px-3.5 rounded-2xl border-gray-300 shadow-xs focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-gray-700" for="excerpt">Kısa Özet (Spot Metin)</label>
                            <button type="button" @click="generateAiPost('excerpt')" :disabled="fieldLoading.excerpt"
                                    class="text-[10px] font-black text-purple-700 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 px-2 py-0.5 rounded-lg transition-colors flex items-center gap-1 cursor-pointer">
                                <i class="fas fa-spinner fa-spin" x-show="fieldLoading.excerpt" x-cloak></i>
                                <i class="fas fa-wand-magic-sparkles text-purple-600" x-show="!fieldLoading.excerpt"></i>
                                <span>AI ile Özet Çıkar</span>
                            </button>
                        </div>
                        <textarea name="excerpt" id="excerpt" rows="2"
                            placeholder="Arama sonuçlarında ve liste kartlarında görünecek 1-2 cümlelik spot açıklama..."
                            class="w-full text-xs py-2.5 px-3.5 rounded-2xl border-gray-300 shadow-xs focus:border-blue-500 focus:ring-blue-500">{{ old('excerpt') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1" for="image">Manuel Kapak Görseli Yükle</label>
                        <input type="file" name="image" id="image" accept="image/*"
                            class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 bg-white border border-gray-300 rounded-2xl p-1.5">
                        <p class="text-[11px] text-gray-400 mt-1">Önerilen boyut: 1200x675px (16:9), Maksimum 25MB. Linkten çekildiyse boş bırakabilirsiniz.</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-black text-gray-900 flex items-center gap-1.5">
                                <i class="fas fa-edit text-blue-600"></i> Detaylı Makale İçeriği (Görsel Metin Düzenleyici) <span class="text-red-500">*</span>
                            </label>
                            <button type="button" @click="generateAiPost('content')" :disabled="fieldLoading.content"
                                    class="text-[10px] font-black text-purple-700 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 px-2.5 py-1 rounded-lg transition-colors flex items-center gap-1 cursor-pointer">
                                <i class="fas fa-spinner fa-spin" x-show="fieldLoading.content" x-cloak></i>
                                <i class="fas fa-wand-magic-sparkles text-purple-600" x-show="!fieldLoading.content"></i>
                                <span>AI ile Makale Yaz / Genişlet</span>
                            </button>
                        </div>
                        <div id="content_editor">{!! old('content') !!}</div>
                        <input type="hidden" name="content" id="content_input">
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-black text-gray-800">Arama Motoru Optimizasyonu (SEO)</span>
                            <button type="button" @click="generateAiPost('seo')" :disabled="fieldLoading.seo"
                                    class="text-[10px] font-black text-purple-700 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 px-2 py-0.5 rounded-lg transition-colors flex items-center gap-1 cursor-pointer">
                                <i class="fas fa-spinner fa-spin" x-show="fieldLoading.seo" x-cloak></i>
                                <i class="fas fa-wand-magic-sparkles text-purple-600" x-show="!fieldLoading.seo"></i>
                                <span>AI ile SEO Etiketleri Üret</span>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1" for="meta_title">SEO Başlığı (Meta Title)</label>
                                <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" placeholder="Google'da görünecek başlık"
                                    class="w-full text-xs py-2.5 px-3.5 rounded-2xl border-gray-300 shadow-xs focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1" for="meta_description">SEO Açıklaması (Meta Description)</label>
                                <input type="text" name="meta_description" id="meta_description" value="{{ old('meta_description') }}" placeholder="Google arama sonuçlarında çıkacak açıklama"
                                    class="w-full text-xs py-2.5 px-3.5 rounded-2xl border-gray-300 shadow-xs focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 w-4 h-4">
                        <label for="is_published" class="text-xs font-black text-gray-900 cursor-pointer">Bu makaleyi hemen yayına al</label>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('admin.posts.index') }}" class="px-5 py-3 bg-gray-100 text-gray-700 font-bold text-xs rounded-2xl hover:bg-gray-200 transition-colors">
                            İptal
                        </a>
                        <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-black text-xs rounded-2xl shadow-lg transition-all transform active:scale-95 flex items-center gap-2 cursor-pointer">
                            <i class="fas fa-save"></i> Makaleyi Kaydet
                        </button>
                    </div>
                </form>
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
                ['blockquote', 'link', 'clean']
            ];

            const articleQuill = new Quill('#content_editor', {
                theme: 'snow',
                modules: { toolbar: toolbarOptions },
                placeholder: 'Makalenizin detaylı içeriğini buraya yazın veya web sayfasından kopyalayıp yapıştırın...'
            });
            window.articleQuillInstance = articleQuill;

            const form = document.getElementById('post-form');
            form.addEventListener('submit', function () {
                const html = articleQuill.root.innerHTML;
                document.getElementById('content_input').value = html === '<p><br></p>' ? '' : html;
            });
        });
    </script>
</x-app-layout>

