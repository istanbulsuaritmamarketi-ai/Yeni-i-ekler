<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <i class="fas fa-cog text-theme-primary"></i> Genel Ayarlar, Logo &amp; İletişim
                </h2>
                <p class="text-xs text-gray-500 mt-1">Firmanızın iletişim hatlarını, logosunu ve sosyal medya bağlantılarını yönetin</p>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-5xl mx-auto space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 text-emerald-800 rounded-r-2xl shadow-sm text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- 1. İLETİŞİM BİLGİLERİ -->
                    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-200/80 shadow-sm space-y-4">
                        <div class="border-b border-gray-100 pb-3">
                            <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                                <i class="fas fa-phone-alt text-theme-primary"></i> İletişim Bilgileri
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">Sitedeki arama ve WhatsApp butonlarında kullanılır.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Sabit / Çağrı Merkezi Telefonu</label>
                            <input class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-medium focus:ring-theme-primary focus:border-theme-primary" 
                                   name="phone" type="text" value="{{ $settings['phone'] ?? '' }}" placeholder="0850 555 55 55">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">WhatsApp Hattı (905XXXXXXXXX)</label>
                            <input class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-mono font-bold focus:ring-emerald-500 focus:border-emerald-500" 
                                   name="whatsapp" type="text" value="{{ $settings['whatsapp'] ?? '' }}" placeholder="905555555555">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">E-Posta Adresi</label>
                            <input class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-medium focus:ring-theme-primary focus:border-theme-primary" 
                                   name="email" type="email" value="{{ $settings['email'] ?? '' }}" placeholder="info@alanadiniz.com">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Açık Adres</label>
                            <textarea class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-medium focus:ring-theme-primary focus:border-theme-primary" 
                                      name="address" rows="3" placeholder="Merkez Mah. No:1 Kadıköy / İstanbul">{{ $settings['address'] ?? '' }}</textarea>
                        </div>
                    </div>

                    <!-- 2. LOGO, FAVICON & SOSYAL MEDYA -->
                    <div class="space-y-6">
                        
                        <!-- Logo & Favicon -->
                        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-200/80 shadow-sm space-y-5">
                            <div class="border-b border-gray-100 pb-3">
                                <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-palette text-purple-600"></i> Logo &amp; Favicon (Tarayıcı Simgesi)
                                </h3>
                                <p class="text-xs text-gray-400 mt-0.5">Sitenin ana logosunu ve tarayıcı sekmelerinde görünen küçük simgeyi değiştirin.</p>
                            </div>

                            <!-- Site Logosu -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Site Ana Logosu (Header &amp; Footer)</label>
                                @if(!empty($settings['logo']))
                                    <div class="mb-2 p-3 bg-gray-900 rounded-2xl inline-flex items-center gap-3 border border-gray-700">
                                        <img src="{{ asset('storage/' . $settings['logo']) }}" alt="Logo" class="h-8 w-auto">
                                        <span class="text-[10px] text-gray-300 font-bold">Mevcut Logo</span>
                                    </div>
                                @endif
                                <input class="block w-full text-xs text-gray-700 border border-gray-300 rounded-2xl cursor-pointer bg-gray-50 p-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-600 file:text-white hover:file:bg-purple-700" name="logo" type="file" accept="image/*">
                            </div>

                            <!-- Favicon -->
                            <div class="pt-2 border-t border-gray-100">
                                <label class="block text-xs font-bold text-gray-700 mb-1">
                                    Favicon (Tarayıcı Sekmesindeki Küçük Simge)
                                </label>
                                
                                <!-- Tarayıcı Sekme Önizlemesi -->
                                <div class="mb-3 p-2.5 bg-slate-100 rounded-2xl border border-slate-200 inline-flex items-center gap-2 max-w-full">
                                    <div class="w-6 h-6 rounded-lg bg-white shadow-sm flex items-center justify-center p-0.5 overflow-hidden border border-slate-200">
                                        @if(!empty($settings['favicon']))
                                            <img src="{{ asset('storage/' . $settings['favicon']) }}" alt="Favicon" class="w-full h-full object-contain">
                                        @elseif(!empty($settings['logo']))
                                            <img src="{{ asset('storage/' . $settings['logo']) }}" alt="Favicon" class="w-full h-full object-contain">
                                        @else
                                            <img src="{{ asset('favicon.svg') }}" alt="Favicon" class="w-full h-full object-contain">
                                        @endif
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 truncate max-w-[200px]">İstanbul Su Arıtma Marketi</span>
                                    <span class="text-[10px] text-slate-400 font-medium ml-1">×</span>
                                </div>

                                <input class="block w-full text-xs text-gray-700 border border-gray-300 rounded-2xl cursor-pointer bg-gray-50 p-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700" name="favicon" type="file" accept="image/*,.ico,.svg">
                                <p class="text-[11px] text-gray-400 mt-1">Önerilen: 32x32, 64x64 veya 128x128 piksel kare PNG, ICO veya SVG simge.</p>
                            </div>
                        </div>

                        <!-- Sosyal Medya -->
                        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-200/80 shadow-sm space-y-4">
                            <div class="border-b border-gray-100 pb-3">
                                <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-share-alt text-blue-600"></i> Sosyal Medya Bağlantıları
                                </h3>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Instagram Linki</label>
                                <input class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs focus:ring-blue-500 focus:border-blue-500" 
                                       name="instagram" type="url" value="{{ $settings['instagram'] ?? '' }}" placeholder="https://instagram.com/hesabiniz">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Facebook Linki</label>
                                <input class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs focus:ring-blue-500 focus:border-blue-500" 
                                       name="facebook" type="url" value="{{ $settings['facebook'] ?? '' }}" placeholder="https://facebook.com/hesabiniz">
                            </div>
                        </div>

                    </div>

                </div>

                <!-- 3. EN ÜST SABİT DUYURU ŞERİDİ (KAYITSIZ SİPARİŞ BARI) -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-200/80 shadow-sm space-y-4 mt-6">
                    <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                                <i class="fas fa-bullhorn text-emerald-600"></i> En Üst Sabit Duyuru Şeridi (Yeşil / Mavi Bar)
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">Sitenin en tepesindeki sabit güven duyurusu. Artık sağa-sola kaymaz/oynamaz, sabittir.</p>
                        </div>
                        <span class="bg-emerald-50 text-emerald-700 text-[11px] font-black px-3 py-1 rounded-full border border-emerald-100">
                            Sabit Şerit
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Duyuru Şeridi Durumu</label>
                            <select name="top_bar_active" class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-bold focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="1" {{ ($settings['top_bar_active'] ?? '1') == '1' ? 'selected' : '' }}>Aktif (Gösterilsin)</option>
                                <option value="0" {{ ($settings['top_bar_active'] ?? '1') == '0' ? 'selected' : '' }}>Pasif (Gizlensin)</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 mb-1">Duyuru Metni</label>
                            <input class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-bold focus:ring-emerald-500 focus:border-emerald-500" 
                                   name="top_bar_text" type="text" 
                                   value="{{ $settings['top_bar_text'] ?? 'Kayıt olmadan ve kart bilgilerinizi girmeden güvenle sipariş verebilirsiniz' }}" 
                                   placeholder="Örn: Kayıt olmadan ve kart bilgilerinizi girmeden güvenle sipariş verebilirsiniz">
                        </div>
                    </div>
                </div>

                <!-- 4. ANA SAYFA HEDİYE & PROMOSYON ŞERİDİ -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-200/80 shadow-sm space-y-4 mt-6">
                    <div class="border-b border-gray-100 pb-3">
                        <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                            <i class="fas fa-gift text-blue-600"></i> Ana Sayfa Hediye &amp; Promosyon Şeridi
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Ana sayfanın en üstünde, "Tüm Ürünleri İncele" butonuyla birlikte müşterileri karşılayan promosyon duyurusunu buradan belirleyin.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Hediye &amp; Kampanya Duyuru Metni</label>
                        <input class="shadow-sm border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-bold focus:ring-blue-500 focus:border-blue-500" 
                               name="hero_gift_banner" type="text" 
                               value="{{ $settings['hero_gift_banner'] ?? 'Tüm Çiçek Siparişlerinde Aynı Gün Hızlı Teslimat ve Özel Kart Notu Hediye!' }}" 
                               placeholder="Örn: Tüm Çiçek Siparişlerinde Aynı Gün Hızlı Teslimat ve Özel Kart Notu Hediye!">
                        <p class="text-[11px] text-gray-400 mt-1">Bu metin ana sayfanın en tepesinde şerit içinde yer alır ve müşteriyi hemen harekete geçirir.</p>
                    </div>
                </div>

                <!-- 5. ANA SAYFA 3 KATEGORİ KUTUCUĞU -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-200/80 shadow-sm space-y-4 mt-6">
                    <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                                <i class="fas fa-th-large text-blue-600"></i> Ana Sayfa 3 Kategori Kutucuğu
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">Ana sayfada yan yana gösterilecek 2 ana kategoriyi seçin. 3. kutucuk ise otomatik olarak müşterileri <strong>"Diğer Kategoriler"</strong> sayfasına aktarır.</p>
                        </div>
                        <a href="{{ route('admin.categories.index') }}" class="bg-blue-50 text-blue-600 text-[11px] font-black px-3 py-1 rounded-full border border-blue-100 hover:bg-blue-100 transition-colors">
                            + Kategori Ekle / Düzenle
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                        <!-- 1. Kategori -->
                        <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/70">
                            <label class="block text-xs font-black text-gray-800 mb-1 flex items-center gap-1.5">
                                <span class="w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center text-[10px] font-black">1</span>
                                1. Kategori Kutusu
                            </label>
                            <select name="home_cat_1" class="shadow-sm border border-gray-300 rounded-xl w-full py-2 px-3 text-gray-900 text-xs font-semibold focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Varsayılan (Otomatik) --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ ($settings['home_cat_1'] ?? '') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-gray-400 mt-1">Örn: Su Arıtma Cihazları</p>
                        </div>

                        <!-- 2. Kategori -->
                        <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/70">
                            <label class="block text-xs font-black text-gray-800 mb-1 flex items-center gap-1.5">
                                <span class="w-5 h-5 bg-emerald-600 text-white rounded-full flex items-center justify-center text-[10px] font-black">2</span>
                                2. Kategori Kutusu
                            </label>
                            <select name="home_cat_2" class="shadow-sm border border-gray-300 rounded-xl w-full py-2 px-3 text-gray-900 text-xs font-semibold focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">-- Varsayılan (Otomatik) --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ ($settings['home_cat_2'] ?? '') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-gray-400 mt-1">Örn: Orijinal Filtreler</p>
                        </div>

                        <!-- 3. Kutu (Sabit Yönlendirme) -->
                        <div class="p-4 bg-indigo-50/70 rounded-2xl border border-indigo-200/70 flex flex-col justify-between">
                            <div>
                                <label class="block text-xs font-black text-indigo-900 mb-1 flex items-center gap-1.5">
                                    <span class="w-5 h-5 bg-indigo-600 text-white rounded-full flex items-center justify-center text-[10px] font-black">3</span>
                                    3. Kutu: Diğer Kategoriler
                                </label>
                                <p class="text-[11px] text-indigo-700/80 mt-1 leading-snug">
                                    Bu kutucuk sabittir. Tıklayan müşterileri doğrudan sistemdeki tüm kategorilerin listelendiği <strong>/kategoriler</strong> sayfasına yönlendirir.
                                </p>
                            </div>
                            <div class="mt-2 text-[10px] font-bold text-indigo-600 bg-white py-1 px-2.5 rounded-lg border border-indigo-200 inline-block text-center">
                                <i class="fas fa-link mr-1"></i> Rota: /kategoriler (Otomatik)
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. ANA SAYFA 4 FIRSAT ÜRÜNÜ VİTRİNİ -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-200/80 shadow-sm space-y-4 mt-6">
                    <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                                <i class="fas fa-fire text-blue-600"></i> Ana Sayfa 4 Fırsat Ürünü Vitrini
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">Ana sayfanın en tepesinde, büyük buton-kartlar şeklinde öne çıkarılacak 4 temel fiyat/performans ürününü seçin.</p>
                        </div>
                        <span class="bg-blue-50 text-blue-600 text-[11px] font-black px-3 py-1 rounded-full border border-blue-100">
                            En Üst Bölüm
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <!-- 1. Fırsat Ürünü -->
                        <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/70">
                            <label class="block text-xs font-black text-gray-800 mb-1 flex items-center gap-1.5">
                                <span class="w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center text-[10px] font-black">1</span>
                                1. Fırsat Ürünü
                            </label>
                            <select name="top_deal_1" class="shadow-sm border border-gray-300 rounded-xl w-full py-2 px-3 text-gray-900 text-xs font-semibold focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Varsayılan (Otomatik Seç) --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ ($settings['top_deal_1'] ?? '') == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} ({{ number_format($p->campaign_price ?: $p->price, 0, ',', '.') }} ₺)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 2. Fırsat Ürünü -->
                        <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/70">
                            <label class="block text-xs font-black text-gray-800 mb-1 flex items-center gap-1.5">
                                <span class="w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center text-[10px] font-black">2</span>
                                2. Fırsat Ürünü
                            </label>
                            <select name="top_deal_2" class="shadow-sm border border-gray-300 rounded-xl w-full py-2 px-3 text-gray-900 text-xs font-semibold focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Varsayılan (Otomatik Seç) --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ ($settings['top_deal_2'] ?? '') == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} ({{ number_format($p->campaign_price ?: $p->price, 0, ',', '.') }} ₺)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 3. Fırsat Ürünü -->
                        <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/70">
                            <label class="block text-xs font-black text-gray-800 mb-1 flex items-center gap-1.5">
                                <span class="w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center text-[10px] font-black">3</span>
                                3. Fırsat Ürünü
                            </label>
                            <select name="top_deal_3" class="shadow-sm border border-gray-300 rounded-xl w-full py-2 px-3 text-gray-900 text-xs font-semibold focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Varsayılan (Otomatik Seç) --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ ($settings['top_deal_3'] ?? '') == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} ({{ number_format($p->campaign_price ?: $p->price, 0, ',', '.') }} ₺)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 4. Fırsat Ürünü -->
                        <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/70">
                            <label class="block text-xs font-black text-gray-800 mb-1 flex items-center gap-1.5">
                                <span class="w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center text-[10px] font-black">4</span>
                                4. Fırsat Ürünü
                            </label>
                            <select name="top_deal_4" class="shadow-sm border border-gray-300 rounded-xl w-full py-2 px-3 text-gray-900 text-xs font-semibold focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Varsayılan (Otomatik Seç) --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ ($settings['top_deal_4'] ?? '') == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} ({{ number_format($p->campaign_price ?: $p->price, 0, ',', '.') }} ₺)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 5. HIZLI YÖNLENDİRME TUŞLARI (HESAPLAYICI, SİHİRBAZ, FİLTRE BULUCU, SERVİS) -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-200/80 shadow-sm space-y-4 mt-6">
                    <div class="border-b border-gray-100 pb-3">
                        <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                            <i class="fas fa-magic text-blue-600"></i> Hızlı Yönlendirme Tuşları (Arama Altı Butonları)
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Arama çubuğunun altında ve vitrinde yer alan 4 eylem tuşunun başlıklarını buradan güncelleyebilirsiniz.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">1. Tuş Başlığı</label>
                            <input class="shadow-sm border border-gray-300 rounded-xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-bold focus:ring-blue-500 focus:border-blue-500" 
                                   name="quick_btn_calc_title" type="text" 
                                   value="{{ $settings['quick_btn_calc_title'] ?? 'Siparişimi Takip Et' }}" 
                                   placeholder="Siparişimi Takip Et">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">2. Tuş Başlığı</label>
                            <input class="shadow-sm border border-gray-300 rounded-xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-bold focus:ring-blue-500 focus:border-blue-500" 
                                   name="quick_btn_wizard_title" type="text" 
                                   value="{{ $settings['quick_btn_wizard_title'] ?? 'Özel Gün Çiçekleri' }}" 
                                   placeholder="Özel Gün Çiçekleri">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">3. Tuş Başlığı</label>
                            <input class="shadow-sm border border-gray-300 rounded-xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-bold focus:ring-blue-500 focus:border-blue-500" 
                                   name="quick_btn_brand_title" type="text" 
                                   value="{{ $settings['quick_btn_brand_title'] ?? 'İlçe & Bölge Seçin' }}" 
                                   placeholder="İlçe & Bölge Seçin">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">4. Tuş Başlığı</label>
                            <input class="shadow-sm border border-gray-300 rounded-xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-bold focus:ring-blue-500 focus:border-blue-500" 
                                   name="quick_btn_service_title" type="text" 
                                   value="{{ $settings['quick_btn_service_title'] ?? 'Aynı Gün Teslimat Saatleri' }}" 
                                   placeholder="Aynı Gün Teslimat Saatleri">
                        </div>
                    </div>
                </div>

                <!-- 6. YAPAY ZEKA (AI) VE AKILLI ÜRÜN ÖZELLEŞTİRME AYARLARI -->
                <div class="bg-gradient-to-br from-purple-50/70 via-indigo-50/50 to-white p-6 sm:p-8 rounded-3xl border border-purple-200/80 shadow-sm space-y-5 mt-6">
                    <div class="border-b border-purple-200/60 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <h3 class="text-base font-black text-purple-950 flex items-center gap-2">
                                <span class="w-7 h-7 rounded-xl bg-purple-600 text-white flex items-center justify-center text-xs shadow-xs">
                                    <i class="fas fa-robot"></i>
                                </span>
                                Yapay Zeka (AI) ve Otomatik Ürün Özelleştirme
                            </h3>
                            <p class="text-xs text-purple-800/80 mt-1">
                                Çektiğiniz veya eklediğiniz ürünlerin başlıklarını, açıklamalarını ve SEO metinlerini tek tıkla <strong>İstanbul Su Arıtma Marketi</strong> kurumsal diline ve satış hedeflerinize göre uyarlar.
                            </p>
                        </div>
                        <a href="https://aistudio.google.com/app/apikey" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-black bg-white hover:bg-purple-100 text-purple-700 px-3.5 py-2 rounded-xl border border-purple-200 shadow-2xs transition-colors whitespace-nowrap">
                            <i class="fab fa-google text-blue-600"></i> Ücretsiz Gemini API Key Al <i class="fas fa-external-link-alt text-[10px] ml-0.5"></i>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-xs font-black text-gray-800 mb-1">AI Sağlayıcısı</label>
                            <select name="ai_provider" class="shadow-sm border border-gray-300 rounded-xl w-full py-2.5 px-3 text-gray-900 text-xs font-bold focus:ring-purple-500 focus:border-purple-500 bg-white">
                                <option value="gemini" {{ ($settings['ai_provider'] ?? 'gemini') == 'gemini' ? 'selected' : '' }}>Google Gemini (Önerilen)</option>
                                <option value="openai" {{ ($settings['ai_provider'] ?? '') == 'openai' ? 'selected' : '' }}>OpenAI (ChatGPT)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-800 mb-1">AI Modeli</label>
                            <select name="ai_model" class="shadow-sm border border-gray-300 rounded-xl w-full py-2.5 px-3 text-gray-900 text-xs font-bold focus:ring-purple-500 focus:border-purple-500 bg-white">
                                <option value="gemini-3.6-flash" {{ ($settings['ai_model'] ?? 'gemini-3.6-flash') == 'gemini-3.6-flash' ? 'selected' : '' }}>Gemini 3.6 Flash (Hızlı &amp; Önerilen)</option>
                                <option value="gemini-3.8-flash" {{ ($settings['ai_model'] ?? '') == 'gemini-3.8-flash' ? 'selected' : '' }}>Gemini 3.8 Flash (Yüksek Zeka)</option>
                                <option value="gemini-3.7-flash" {{ ($settings['ai_model'] ?? '') == 'gemini-3.7-flash' ? 'selected' : '' }}>Gemini 3.7 Flash</option>
                                <option value="gemini-3.5-flash-lite" {{ ($settings['ai_model'] ?? '') == 'gemini-3.5-flash-lite' ? 'selected' : '' }}>Gemini 3.5 Flash Lite (Tasarruflu)</option>
                                <option value="gemini-3.1-pro" {{ ($settings['ai_model'] ?? '') == 'gemini-3.1-pro' ? 'selected' : '' }}>Gemini 3.1 Pro</option>
                                <option value="gpt-4o-mini" {{ ($settings['ai_model'] ?? '') == 'gpt-4o-mini' ? 'selected' : '' }}>OpenAI GPT-4o Mini</option>
                                <option value="gpt-4o" {{ ($settings['ai_model'] ?? '') == 'gpt-4o' ? 'selected' : '' }}>OpenAI GPT-4o</option>
                            </select>
                            <p class="text-[10px] text-gray-400 mt-1">Google Gemini 3 serisi ve dinamik model keşfi aktiftir.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-800 mb-1">Google Gemini API Anahtarı</label>
                            <input class="shadow-sm border border-gray-300 rounded-xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-mono focus:ring-purple-500 focus:border-purple-500" 
                                   name="gemini_api_key" type="password" 
                                   value="{{ $settings['gemini_api_key'] ?? '' }}" 
                                   placeholder="AIzaSy...">
                            <p class="text-[10px] text-gray-400 mt-1">Google AI Studio'dan ücretsiz alabilirsiniz.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-800 mb-1">OpenAI API Anahtarı (Opsiyonel)</label>
                            <input class="shadow-sm border border-gray-300 rounded-xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-mono focus:ring-purple-500 focus:border-purple-500" 
                                   name="openai_api_key" type="password" 
                                   value="{{ $settings['openai_api_key'] ?? '' }}" 
                                   placeholder="sk-proj-...">
                            <p class="text-[10px] text-gray-400 mt-1">DALL-E 3 ve GPT-4o için kullanılabilir.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-800 mb-1 flex items-center justify-between">
                                <span>Remove.bg API</span>
                                <a href="https://www.remove.bg/api" target="_blank" class="text-purple-600 text-[10px] hover:underline font-bold">Ücretsiz Al</a>
                            </label>
                            <input class="shadow-sm border border-gray-300 rounded-xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-mono focus:ring-purple-500 focus:border-purple-500" 
                                   name="removebg_api_key" type="password" 
                                   value="{{ $settings['removebg_api_key'] ?? '' }}" 
                                   placeholder="rmbg_...">
                            <p class="text-[10px] text-gray-400 mt-1">Görselleri beyaz stüdyo fonuna çevirir.</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-800 mb-1">
                            Firma Adı &amp; Özel Yapay Zeka Talimatı (Prompt)
                        </label>
                        <textarea name="ai_system_prompt" rows="3" 
                                  class="shadow-sm border border-gray-300 rounded-2xl w-full p-3 text-xs text-gray-800 focus:ring-purple-500 focus:border-purple-500"
                                  placeholder="Yapay zekanın ürünleri nasıl yazmasını istediğinizi buraya yazın...">{{ $settings['ai_system_prompt'] ?? "Sen İstanbul Su Arıtma Marketi firmasının uzman e-ticaret metin yazarısın. Çekilen ham ürün verisini inceleyerek rakip marka isimlerini temizle; firmamızın orijinal ürün garantisi, ücretsiz kargo ve montaj teknik servis desteğini vurgulayarak satış odaklı, ferah paragraflı ve zengin HTML olarak yeniden yaz." }}</textarea>
                        <p class="text-[10px] text-gray-500 mt-1">Yapay zeka tüm ürünleri bu talimat doğrultusunda firmanıza özel hale getirir.</p>
                    </div>
                </div>

                <!-- 7. YENİ MÜŞTERİ HOŞ GELDİN PROMOSYONU (KUPON AYARLARI) -->
                <div class="bg-gradient-to-br from-amber-50/60 via-orange-50/40 to-white p-6 sm:p-8 rounded-3xl border border-amber-200/80 shadow-sm space-y-5 mt-6">
                    <div class="border-b border-amber-200/60 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <h3 class="text-base font-black text-amber-950 flex items-center gap-2">
                                <span class="w-7 h-7 rounded-xl bg-amber-500 text-white flex items-center justify-center text-xs shadow-xs">
                                    <i class="fas fa-gift"></i>
                                </span>
                                Yeni Müşteri Hoş Geldin Promosyonu (Otomatik Kupon)
                            </h3>
                            <p class="text-xs text-amber-800/80 mt-1">
                                Web sitenize yeni kayıt olan her müşterinin hesabına otomatik tanımlanacak indirim kuponunu buradan yönetebilirsiniz.
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="welcome_coupon_enabled" value="0">
                                <input type="checkbox" name="welcome_coupon_enabled" value="1" 
                                       {{ ($settings['welcome_coupon_enabled'] ?? '1') == '1' ? 'checked' : '' }} 
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                                <span class="ml-2 text-xs font-black text-amber-950">Promosyon Aktif</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-black text-gray-800 mb-1">Promosyon Kupon Kodu</label>
                            <input class="shadow-sm border border-gray-300 rounded-xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-mono font-black focus:ring-amber-500 focus:border-amber-500 uppercase" 
                                   name="welcome_coupon_code" type="text" 
                                   value="{{ $settings['welcome_coupon_code'] ?? 'HOSGELDIN150' }}" 
                                   placeholder="HOSGELDIN150">
                            <p class="text-[10px] text-gray-400 mt-1">Kayıt olan müşterinin hesabında görünecektir.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-800 mb-1">İndirim Türü</label>
                            <select name="welcome_coupon_type" class="shadow-sm border border-gray-300 rounded-xl w-full py-2.5 px-3 text-gray-900 text-xs font-bold focus:ring-amber-500 focus:border-amber-500 bg-white">
                                <option value="fixed" {{ ($settings['welcome_coupon_type'] ?? 'fixed') == 'fixed' ? 'selected' : '' }}>Sabit Tutar İndirimi (TL)</option>
                                <option value="percent" {{ ($settings['welcome_coupon_type'] ?? '') == 'percent' ? 'selected' : '' }}>Yüzde İndirimi (%)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-800 mb-1">İndirim Miktarı (TL veya %)</label>
                            <input class="shadow-sm border border-gray-300 rounded-xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-bold focus:ring-amber-500 focus:border-amber-500" 
                                   name="welcome_coupon_discount" type="number" step="1" 
                                   value="{{ $settings['welcome_coupon_discount'] ?? 150 }}" 
                                   placeholder="150">
                            <p class="text-[10px] text-gray-400 mt-1">Sabit ise TL, yüzde ise oran giriniz.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-800 mb-1">Minimum Sepet Tutarı (TL)</label>
                            <input class="shadow-sm border border-gray-300 rounded-xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-bold focus:ring-amber-500 focus:border-amber-500" 
                                   name="welcome_coupon_min_order" type="number" step="1" 
                                   value="{{ $settings['welcome_coupon_min_order'] ?? 500 }}" 
                                   placeholder="500">
                            <p class="text-[10px] text-gray-400 mt-1">Kuponun geçerli olacağı en düşük sepet tutarı.</p>
                        </div>
                    </div>
                </div>

                <!-- 8. 🌸 KENDİ BUKETİNİ OLUŞTUR & ÇİÇEK ATÖLYESİ AYARLARI -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6 mt-6">
                    <div class="border-b border-slate-200/60 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                                <span class="w-7 h-7 rounded-xl bg-blue-600 text-white flex items-center justify-center text-xs shadow-xs">
                                    <i class="fas fa-magic"></i>
                                </span>
                                Kendi Buketini Oluştur &amp; Tekli Dal Fiyatları
                            </h3>
                            <p class="text-xs text-slate-500 mt-1">
                                Müşterilerin tekli çiçek seçerek özel buket hazırladığı sihirbazın dal fiyatlarını, stok durumunu ve kurallarını belirleyin.
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="custom_bouquet_enabled" value="0">
                                <input type="checkbox" name="custom_bouquet_enabled" value="1" 
                                       {{ ($settings['custom_bouquet_enabled'] ?? '1') == '1' ? 'checked' : '' }} 
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                <span class="ml-2 text-xs font-black text-slate-900">Buket Sihirbazı Aktif</span>
                            </label>
                        </div>
                    </div>

                    <!-- Minimum Dal Kuralı -->
                    <div class="max-w-xs">
                        <label class="block text-xs font-black text-slate-800 mb-1">
                            Minimum Çiçek Dalı Zorunluluğu
                        </label>
                        <div class="flex items-center gap-2">
                            <input class="shadow-sm border border-gray-300 rounded-xl w-32 py-2 px-3 text-gray-900 text-xs font-black focus:ring-blue-500 focus:border-blue-500 text-center" 
                                   name="custom_bouquet_min_stems" type="number" min="1" max="50" step="1" 
                                   value="{{ $settings['custom_bouquet_min_stems'] ?? 7 }}">
                            <span class="text-xs font-bold text-slate-500">Dal (Önerilen: 7)</span>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-1">Buket hacminin estetik durması için gereken en az çiçek sayısı.</p>
                    </div>

                    <!-- Tekli Dal Fiyatları Izgarası -->
                    <div>
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider mb-3">
                            Tekli Çiçek Çeşitleri, Dal Fiyatları ve Hal Stok Durumu
                        </h4>

                        @php
                            $stemList = [
                                ['id' => 'red_rose', 'name' => 'Ekvador Kırmızı Gül', 'icon' => '🌹', 'default' => 55],
                                ['id' => 'white_rose', 'name' => 'Krem Beyaz Gül', 'icon' => '🤍', 'default' => 50],
                                ['id' => 'pink_rose', 'name' => 'Pudra Pembe Gül', 'icon' => '🌸', 'default' => 50],
                                ['id' => 'yellow_rose', 'name' => 'Sarı Gül', 'icon' => '💛', 'default' => 45],
                                ['id' => 'daisy', 'name' => 'Kır Papatyası', 'icon' => '🌼', 'default' => 35],
                                ['id' => 'tulip', 'name' => 'İthal Pembe/Mor Lale', 'icon' => '🌷', 'default' => 45],
                                ['id' => 'lily', 'name' => 'Kokulu Lilyum (Zambak)', 'icon' => '⚜️', 'default' => 75],
                                ['id' => 'peony', 'name' => 'İthal Şakayık (Peony)', 'icon' => '🌺', 'default' => 85],
                                ['id' => 'hydrangea', 'name' => 'İthal Mavi Ortanca', 'icon' => '💠', 'default' => 110],
                                ['id' => 'gypsophila', 'name' => 'Beyaz Cipsofilya', 'icon' => '✨', 'default' => 30],
                                ['id' => 'eucalyptus', 'name' => 'Gümüşi Okaliptüs', 'icon' => '🌿', 'default' => 30],
                            ];
                        @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            @foreach($stemList as $st)
                                <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-2xs space-y-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5 font-black text-xs text-slate-900">
                                            <span>{{ $st['icon'] }}</span>
                                            <span class="truncate">{{ $st['name'] }}</span>
                                        </div>
                                        <label class="inline-flex items-center cursor-pointer" title="Çiçek Hal Stok Durumu">
                                            <input type="hidden" name="custom_bouquet_active_{{ $st['id'] }}" value="0">
                                            <input type="checkbox" name="custom_bouquet_active_{{ $st['id'] }}" value="1"
                                                   {{ ($settings['custom_bouquet_active_' . $st['id']] ?? '1') == '1' ? 'checked' : '' }}
                                                   class="rounded text-blue-600 focus:ring-blue-500 w-4 h-4">
                                        </label>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Dal Fiyatı (₺):</label>
                                        <input type="number" step="0.50" min="0" 
                                               name="custom_bouquet_stem_{{ $st['id'] }}" 
                                               value="{{ $settings['custom_bouquet_stem_' . $st['id']] ?? $st['default'] }}"
                                               class="w-full text-xs font-black text-slate-900 border border-slate-200 rounded-xl py-1.5 px-2.5 focus:ring-blue-500 focus:border-blue-500 bg-slate-50/50">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- KAYDET BUTONU -->
                <div class="pt-6 flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black text-xs px-8 py-3.5 rounded-2xl shadow-lg transition-all transform active:scale-95 flex items-center gap-2">
                        <i class="fas fa-save"></i> Tüm Ayarları Kaydet
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>
