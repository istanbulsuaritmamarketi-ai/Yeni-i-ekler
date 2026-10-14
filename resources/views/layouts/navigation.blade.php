<!-- Sidebar -->
<aside :class="{'translate-x-0': open, '-translate-x-full': !open}" class="bg-slate-900 text-white w-64 space-y-6 py-6 px-3 absolute inset-y-0 left-0 transform lg:relative lg:translate-x-0 transition duration-200 ease-in-out z-20 flex flex-col border-r border-slate-800">
    <!-- Logo -->
    <a href="{{ route('dashboard') }}" class="text-white flex items-center space-x-3 px-3 mb-2">
        <img src="{{ asset('images/logo-badge.png') }}?v=3" alt="Tazecicekler.com Logo" class="w-10 h-10 rounded-2xl object-cover shadow-md shrink-0 border border-slate-700">
        <div class="overflow-hidden">
            <span class="text-sm font-black tracking-tight leading-tight block truncate">{{ config('app.name', 'Yiğit Core') }}</span>
            @if($siteMode === 'core')
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 mt-1 rounded-md text-[9px] font-black uppercase tracking-wider bg-indigo-950/80 text-indigo-300 border border-indigo-800/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                    Master Engine Konsolu
                </span>
            @elseif($siteMode === 'corporate')
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 mt-1 rounded-md text-[9px] font-black uppercase tracking-wider bg-amber-950/80 text-amber-300 border border-amber-800/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    Kurumsal Panel
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 mt-1 rounded-md text-[9px] font-black uppercase tracking-wider bg-emerald-950/80 text-emerald-300 border border-emerald-800/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    E-Ticaret Paneli
                </span>
            @endif
        </div>
    </a>

    <!-- Nav Links -->
    <nav class="flex-1 overflow-y-auto space-y-1 pr-1 custom-scrollbar">
        
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="fas fa-tachometer-alt">
            Genel Bakış
        </x-sidebar-link>

        @if($siteMode === 'core')
            {{-- =================================== --}}
            {{-- YİĞİT CORE MASTER ENGINE MENÜSÜ   --}}
            {{-- =================================== --}}
            
            <div class="px-3 py-2 mt-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Master Komuta &amp; Kalite</div>
            
            <x-sidebar-link :href="route('admin.siteMatrix.index')" :active="request()->routeIs('admin.siteMatrix.*')" icon="fas fa-cubes text-theme-primary">
                Çoklu Site &amp; Modül Matrisi
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.siteAudit.index')" :active="request()->routeIs('admin.siteAudit.*')" icon="fas fa-stethoscope text-emerald-400">
                Otonom Bug Denetçisi
            </x-sidebar-link>

            <div class="px-3 py-2 mt-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Katalog &amp; E-Ticaret Modülü</div>

            <x-sidebar-link :href="route('admin.workshop.craftsman')" :active="request()->routeIs('admin.workshop.craftsman')" icon="fas fa-hammer text-amber-400">
                Usta Hazırlık Ekranı
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.workshop.index')" :active="request()->routeIs('admin.workshop.index')" icon="fas fa-camera text-blue-400">
                Mobil Atölye &amp; Kamera
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.bouquet-items.index')" :active="request()->routeIs('admin.bouquet-items.*')" icon="fas fa-magic text-blue-400">
                Buket Malzemeleri
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')" icon="fas fa-shopping-cart text-emerald-400">
                Siparişler
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')" icon="fas fa-box text-cyan-400">
                Ürün Kataloğu
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')" icon="fas fa-tags text-indigo-400">
                Kategoriler
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.coupons.index')" :active="request()->routeIs('admin.coupons.*')" icon="fas fa-ticket-alt text-amber-400">
                İndirim Kuponları
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.banners.index')" :active="request()->routeIs('admin.banners.*')" icon="fas fa-images text-purple-400">
                Slider &amp; Bannerlar
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.marketing.payment')" :active="request()->routeIs('admin.marketing.payment')" icon="fas fa-credit-card text-emerald-400">
                PayTR Ödeme Ayarları
            </x-sidebar-link>

            <div class="px-3 py-2 mt-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Kurumsal &amp; Portföy Modülü</div>

            <x-sidebar-link :href="route('admin.projects.index')" :active="request()->routeIs('admin.projects.*')" icon="fas fa-building text-amber-400">
                Projeler &amp; Portföy
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.services.index')" :active="request()->routeIs('admin.services.*')" icon="fas fa-file-contract text-emerald-400">
                Teklif &amp; Keşif Talepleri
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.contact-messages.index')" :active="request()->routeIs('admin.contact-messages.*')" icon="fas fa-envelope text-blue-400">
                Gelen Mesajlar
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.leads.index')" :active="request()->routeIs('admin.leads.*')" icon="fas fa-bullseye text-red-400">
                Aday Müşteriler (Leads)
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.customers.index')" :active="request()->routeIs('admin.customers.*')" icon="fas fa-users">
                Müşteri Rehberi
            </x-sidebar-link>

            <div class="px-3 py-2 mt-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">İçerik, SEO &amp; Hukuk Modülü</div>

            <x-sidebar-link :href="route('admin.posts.index')" :active="request()->routeIs('admin.posts.*')" icon="fas fa-newspaper text-cyan-400">
                Haberler &amp; Blog
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.pages.index')" :active="request()->routeIs('admin.pages.*')" icon="fas fa-balance-scale text-amber-400">
                KVKK &amp; Hukuki Sayfalar
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.faqs.index')" :active="request()->routeIs('admin.faqs.*')" icon="fas fa-question-circle text-amber-400">
                Sıkça Sorulan Sorular
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.reviews.index')" :active="request()->routeIs('admin.reviews.*')" icon="fas fa-comments text-blue-400">
                Yorumlar &amp; Puanlar
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.marketing.seo')" :active="request()->routeIs('admin.marketing.seo')" icon="fas fa-search-dollar text-emerald-400">
                Google SEO Ayarları
            </x-sidebar-link>

            <div class="px-3 py-2 mt-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Sistem &amp; Altyapı</div>

            <x-sidebar-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.index')" icon="fas fa-cog text-gray-300">
                Genel Ayarlar &amp; Yapılandırma
            </x-sidebar-link>

            @can('manage-users')
            <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" icon="fas fa-user-shield text-blue-400">
                Yöneticiler &amp; Personel
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.activity-logs.index')" :active="request()->routeIs('admin.activity-logs.*')" icon="fas fa-list-alt text-gray-400">
                Sistem Denetim Logları
            </x-sidebar-link>
            @endcan

        @elseif($siteMode === 'corporate')
            {{-- =================================== --}}
            {{-- KURUMSAL & İNŞAAT MENÜSÜ            --}}
            {{-- =================================== --}}
            
            <div class="px-3 py-2 mt-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Portföy Yönetimi</div>
            
            <x-sidebar-link :href="route('admin.projects.index')" :active="request()->routeIs('admin.projects.*')" icon="fas fa-building text-amber-400">
                Projeler &amp; Portföy
            </x-sidebar-link>

            <div class="px-3 py-2 mt-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Talep &amp; İletişim</div>

            <x-sidebar-link :href="route('admin.services.index')" :active="request()->routeIs('admin.services.*')" icon="fas fa-file-contract text-emerald-400">
                Teklif &amp; Keşif Talepleri
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.contact-messages.index')" :active="request()->routeIs('admin.contact-messages.*')" icon="fas fa-envelope text-blue-400">
                Gelen Mesajlar
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.leads.index')" :active="request()->routeIs('admin.leads.*')" icon="fas fa-bullseye text-red-400">
                Aday Müşteriler (Leads)
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.customers.index')" :active="request()->routeIs('admin.customers.*')" icon="fas fa-users">
                Müşteri Rehberi
            </x-sidebar-link>

            <div class="px-3 py-2 mt-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">İçerik &amp; SEO</div>

            <x-sidebar-link :href="route('admin.posts.index')" :active="request()->routeIs('admin.posts.*')" icon="fas fa-newspaper text-cyan-400">
                Haberler &amp; Blog
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.pages.index')" :active="request()->routeIs('admin.pages.*')" icon="fas fa-balance-scale text-amber-400">
                KVKK &amp; Hukuki Sayfalar
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.faqs.index')" :active="request()->routeIs('admin.faqs.*')" icon="fas fa-question-circle text-amber-400">
                Sıkça Sorulan Sorular
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.reviews.index')" :active="request()->routeIs('admin.reviews.*')" icon="fas fa-comments text-blue-400">
                Yorumlar &amp; Puanlar
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.marketing.seo')" :active="request()->routeIs('admin.marketing.seo')" icon="fas fa-search-dollar text-emerald-400">
                Google SEO Ayarları
            </x-sidebar-link>

            <div class="px-3 py-2 mt-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Sistem</div>

            <x-sidebar-link :href="route('admin.siteMatrix.index')" :active="request()->routeIs('admin.siteMatrix.*')" icon="fas fa-cubes text-theme-primary">
                Çoklu Site &amp; Modül Matrisi
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.siteAudit.index')" :active="request()->routeIs('admin.siteAudit.*')" icon="fas fa-stethoscope text-emerald-400">
                Otonom Bug Denetçisi
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.index')" icon="fas fa-cog text-gray-300">
                Genel Ayarlar &amp; Logo
            </x-sidebar-link>

            @can('manage-users')
            <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" icon="fas fa-user-shield text-blue-400">
                Yöneticiler &amp; Personel
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.activity-logs.index')" :active="request()->routeIs('admin.activity-logs.*')" icon="fas fa-list-alt text-gray-400">
                Sistem Denetim Logları
            </x-sidebar-link>
            @endcan

        @else
            {{-- =================================== --}}
            {{-- E-TİCARET & ÇİÇEKÇİ MENÜSÜ          --}}
            {{-- =================================== --}}

            <div class="px-3 py-2 mt-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Satış &amp; Operasyon</div>
            
            <x-sidebar-link :href="route('admin.workshop.craftsman')" :active="request()->routeIs('admin.workshop.craftsman')" icon="fas fa-hammer text-amber-400">
                Usta Hazırlık Ekranı
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.workshop.index')" :active="request()->routeIs('admin.workshop.index')" icon="fas fa-camera text-blue-400">
                Mobil Atölye &amp; Kamera
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.bouquet-items.index')" :active="request()->routeIs('admin.bouquet-items.*')" icon="fas fa-magic text-blue-400">
                Buket Malzemeleri
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')" icon="fas fa-shopping-bag text-emerald-400">
                Siparişler &amp; Atölye
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.regions.index')" :active="request()->routeIs('admin.regions.*')" icon="fas fa-map-marked-alt text-cyan-400">
                İlçe Kurye &amp; Teslimat
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.coupons.index')" :active="request()->routeIs('admin.coupons.*')" icon="fas fa-ticket-alt text-amber-400">
                İndirim Kuponları
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.marketing.payment')" :active="request()->routeIs('admin.marketing.payment')" icon="fas fa-credit-card text-blue-400">
                PayTR Ödeme Ayarları
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.customers.index')" :active="request()->routeIs('admin.customers.*')" icon="fas fa-users">
                Kayıtlı Müşteriler
            </x-sidebar-link>

            <div class="px-3 py-2 mt-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Katalog &amp; Vitrin</div>

            <x-sidebar-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')" icon="fas fa-tags text-indigo-400">
                Kategoriler
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')" icon="fas fa-spa text-blue-400">
                Çiçek Kataloğu
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.banners.index')" :active="request()->routeIs('admin.banners.*')" icon="fas fa-images text-purple-400">
                Vitrin &amp; Bannerlar
            </x-sidebar-link>

            <div class="px-3 py-2 mt-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">İçerik &amp; SEO</div>

            <x-sidebar-link :href="route('admin.posts.index')" :active="request()->routeIs('admin.posts.*')" icon="fas fa-newspaper text-cyan-400">
                Haberler &amp; Blog
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.pages.index')" :active="request()->routeIs('admin.pages.*')" icon="fas fa-balance-scale text-amber-400">
                KVKK &amp; Hukuki Sayfalar
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.faqs.index')" :active="request()->routeIs('admin.faqs.*')" icon="fas fa-question-circle text-amber-400">
                Sıkça Sorulan Sorular
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.reviews.index')" :active="request()->routeIs('admin.reviews.*')" icon="fas fa-comments text-blue-400">
                Yorumlar &amp; Puanlar
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.marketing.seo')" :active="request()->routeIs('admin.marketing.seo')" icon="fas fa-search-dollar text-emerald-400">
                Google SEO Ayarları
            </x-sidebar-link>

            <div class="px-3 py-2 mt-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Sistem</div>

            <x-sidebar-link :href="route('admin.siteMatrix.index')" :active="request()->routeIs('admin.siteMatrix.*')" icon="fas fa-cubes text-theme-primary">
                Çoklu Site &amp; Modül Matrisi
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.siteAudit.index')" :active="request()->routeIs('admin.siteAudit.*')" icon="fas fa-stethoscope text-emerald-400">
                Otonom Bug Denetçisi
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.marketing.payment')" :active="request()->routeIs('admin.marketing.payment')" icon="fas fa-credit-card text-emerald-400">
                PayTR Ödeme Ayarları
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.index')" icon="fas fa-cog text-gray-300">
                Genel Ayarlar &amp; Logo
            </x-sidebar-link>

            @can('manage-users')
            <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" icon="fas fa-user-shield text-blue-400">
                Yöneticiler &amp; Personel
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.activity-logs.index')" :active="request()->routeIs('admin.activity-logs.*')" icon="fas fa-list-alt text-gray-400">
                Sistem Denetim Logları
            </x-sidebar-link>
            @endcan

        @endif

    </nav>

    <!-- User Profile & Logout -->
    <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2.5 overflow-hidden">
                <div class="w-8 h-8 rounded-xl bg-slate-800 text-white flex items-center justify-center font-bold text-xs shrink-0">
                    <i class="fas fa-user-check text-theme-primary"></i>
                </div>
                <div class="overflow-hidden">
                    <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Yönetici' }}</div>
                    <div class="text-[10px] text-gray-400 truncate">{{ auth()->user()->role ?? 'Admin' }}</div>
                </div>
            </div>
            
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('admin-sidebar-logout-form').submit();"
               title="Güvenli Çıkış" 
               class="p-2 text-gray-400 hover:text-red-400 transition-colors cursor-pointer">
                <i class="fas fa-power-off text-sm"></i>
            </a>
            <form id="admin-sidebar-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                @csrf
            </form>
        </div>
    </div>
</aside>
