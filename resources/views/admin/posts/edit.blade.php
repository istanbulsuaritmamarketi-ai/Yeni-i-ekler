<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-xl text-gray-900 leading-tight">
                    {{ __('Makaleyi Düzenle') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">{{ $post->title }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs font-bold bg-blue-50 px-3 py-1.5 rounded-xl border border-blue-100 transition-colors">
                    <i class="fas fa-external-link-alt"></i> Sitede Gör
                </a>
                <a href="{{ route('admin.posts.index') }}" class="text-gray-600 hover:text-gray-900 text-xs font-bold bg-gray-100 hover:bg-gray-200 px-3.5 py-2 rounded-xl transition-colors">
                    <i class="fas fa-arrow-left"></i> Listeye Dön
                </a>
            </div>
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
            aiInstruction: '',
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
            generateAiPost(targetField = 'all') {
                const titleVal = document.getElementById('title')?.value || '';
                const excerptVal = document.getElementById('excerpt')?.value || '';
                const contentVal = window.articleQuillInstance ? window.articleQuillInstance.root.innerHTML : '';

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
                        this.successMsg = res.body.message || 'Yapay zeka içeriği başarıyla güncelledi!';
                    } else {
                        this.errorMsg = res.body.message || 'Yapay zeka içeriği üretilirken bir hata oluştu.';
                    }
                })
                .catch(err => {
                    this.fieldLoading[targetField] = false;
                    if (targetField === 'all') this.aiLoading = false;
                    this.errorMsg = 'Hata oluştu: ' + err.message;
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

                <!-- YAPAY ZEKA İLE MAKALEYİ DÜZENLEME & ZENGİNLEŞTİRME ASİSTANI -->
                <div class="mb-8 p-5 bg-gradient-to-r from-purple-50 via-indigo-50/40 to-white rounded-3xl border-2 border-purple-300 shadow-sm">
                    <div class="flex items-center justify-between gap-2 pb-3 border-b border-purple-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-9 h-9 rounded-2xl bg-purple-600 text-white flex items-center justify-center text-sm shadow-sm">
                                <i class="fas fa-wand-magic-sparkles"></i>
                            </span>
                            <div>
                                <h3 class="font-black text-sm text-purple-950">Yapay Zeka ile Makaleyi Geliştir & Yeniden Yaz</h3>
                                <p class="text-[11px] text-purple-800/80">Mevcut içeriği daha zengin, akıcı, SEO uyumlu ve otoriter bir uzman rehberine dönüştürün</p>
                            </div>
                        </div>
                        <span class="bg-purple-600 text-white text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase">AI ASİSTAN</span>
                    </div>

                    <div class="mt-3 flex flex-col sm:flex-row items-center gap-2.5">
                        <input type="text" x-model="aiInstruction" placeholder="Yapay zekaya özel talimat verin (örn: başlıkları zenginleştir, detaylandır...)"
                               class="w-full text-xs py-2.5 px-3.5 border border-purple-200 rounded-2xl bg-white focus:ring-purple-500 focus:border-purple-500 font-medium">
                        <button type="button" @click="generateAiPost('all')" :disabled="aiLoading"
                                class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 disabled:opacity-50 text-white text-xs font-black rounded-2xl shadow-md transition-all flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer">
                            <i class="fas fa-spinner fa-spin" x-show="aiLoading" x-cloak></i>
                            <i class="fas fa-wand-magic-sparkles" x-show="!aiLoading"></i>
                            <span x-text="aiLoading ? 'Geliştiriliyor...' : 'Tüm Makaleyi Yeniden Yaz'"></span>
                        </button>
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

                <form id="edit-post-form" action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-gray-700" for="title">Makale Başlığı <span class="text-red-500">*</span></label>
                                <button type="button" @click="generateAiPost('title')" :disabled="fieldLoading.title"
                                        class="text-[10px] font-black text-purple-700 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 px-2 py-0.5 rounded-lg transition-colors flex items-center gap-1 cursor-pointer">
                                    <i class="fas fa-spinner fa-spin" x-show="fieldLoading.title" x-cloak></i>
                                    <i class="fas fa-wand-magic-sparkles text-purple-600" x-show="!fieldLoading.title"></i>
                                    <span>AI ile İyileştir</span>
                                </button>
                            </div>
                            <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" required
                                class="w-full text-xs font-bold py-2.5 px-3.5 rounded-2xl border-gray-300 shadow-xs focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1" for="slug">Özel URL (Slug)</label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $post->slug) }}"
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
                                <span>AI ile Özet Güncelle</span>
                            </button>
                        </div>
                        <textarea name="excerpt" id="excerpt" rows="2"
                            class="w-full text-xs py-2.5 px-3.5 rounded-2xl border-gray-300 shadow-xs focus:border-blue-500 focus:ring-blue-500">{{ old('excerpt', $post->excerpt) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Kapak Görseli</label>
                        @if($post->image)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="h-28 w-48 object-cover rounded-2xl border bg-gray-50 shadow-xs">
                            </div>
                        @endif
                        <input type="file" name="image" accept="image/*"
                            class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 bg-white border border-gray-300 rounded-2xl p-1.5">
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
                                <span>AI ile Makaleyi Zenginleştir & Genişlet</span>
                            </button>
                        </div>
                        <div id="content_editor">{!! old('content', $post->content) !!}</div>
                        <input type="hidden" name="content" id="content_input">
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-black text-gray-800">Arama Motoru Optimizasyonu (SEO)</span>
                            <button type="button" @click="generateAiPost('seo')" :disabled="fieldLoading.seo"
                                    class="text-[10px] font-black text-purple-700 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 px-2 py-0.5 rounded-lg transition-colors flex items-center gap-1 cursor-pointer">
                                <i class="fas fa-spinner fa-spin" x-show="fieldLoading.seo" x-cloak></i>
                                <i class="fas fa-wand-magic-sparkles text-purple-600" x-show="!fieldLoading.seo"></i>
                                <span>AI ile SEO Güncelle</span>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1" for="meta_title">SEO Başlığı (Meta Title)</label>
                                <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $post->meta_title) }}"
                                    class="w-full text-xs py-2.5 px-3.5 rounded-2xl border-gray-300 shadow-xs focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1" for="meta_description">SEO Açıklaması (Meta Description)</label>
                                <input type="text" name="meta_description" id="meta_description" value="{{ old('meta_description', $post->meta_description) }}"
                                    class="w-full text-xs py-2.5 px-3.5 rounded-2xl border-gray-300 shadow-xs focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', $post->is_published) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 w-4 h-4">
                        <label for="is_published" class="text-xs font-black text-gray-900 cursor-pointer">Bu makale yayında olsun</label>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('admin.posts.index') }}" class="px-5 py-3 bg-gray-100 text-gray-700 font-bold text-xs rounded-2xl hover:bg-gray-200 transition-colors">
                            İptal
                        </a>
                        <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-black text-xs rounded-2xl shadow-lg transition-all transform active:scale-95 flex items-center gap-2 cursor-pointer">
                            <i class="fas fa-save"></i> Değişiklikleri Kaydet
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
                placeholder: 'Makalenizin detaylı içeriğini buraya yazın...'
            });
            window.articleQuillInstance = articleQuill;

            const form = document.getElementById('edit-post-form');
            form.addEventListener('submit', function () {
                const html = articleQuill.root.innerHTML;
                document.getElementById('content_input').value = html === '<p><br></p>' ? '' : html;
            });
        });
    </script>
</x-app-layout>

