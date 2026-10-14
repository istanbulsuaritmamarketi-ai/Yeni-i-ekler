<!DOCTYPE html>
<html lang="tr" class="scroll-smooth overflow-x-hidden max-w-full w-full">
<head>
    <meta charset="utf-8">
    <meta http-equiv="content-language" content="tr">
    <meta name="language" content="Turkish">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="HandheldFriendly" content="true">
    <meta name="MobileOptimized" content="width">
    <meta name="theme-color" content="#1d4ed8">
    <meta name="format-detection" content="telephone=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $pageTitle = $title ?? ($siteSettings['site_title'] ?? 'Tazecicekler.com — Aynı Gün Taze Çiçek & Hediye Siparişi');
        $pageDescription = $description ?? ($siteSettings['site_slogan'] ?? 'Türkiye genelinde 81 ilde aynı gün hızlı teslimat, fotoğraflı buket onayı, taze ithal çiçekler ve güvenli online ödeme.');
        $canonicalUrl = $canonical ?? url()->current();
        $ogImage = $image ?? asset('images/products/6.png');
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <!-- Favicon & Brand Icons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=3">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=3">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=3">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v=3">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=3">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}?v=3">

    <!-- Open Graph -->
    <meta property="og:locale" content="tr_TR">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:site_name" content="Tazecicekler.com">
    <meta property="og:image" content="{{ $ogImage }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- CSS Variables & Theme Styles -->
    <style>
        :root {
            --color-primary: #1d4ed8;
            --color-primary-hover: #1e40af;
            --color-primary-light: #eff6ff;
            --color-primary-dark: #0f2356;
            --color-accent: #f59e0b;
            --color-accent-hover: #d97706;
            --color-accent-light: #fef3c7;
        }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            font-size: 1.5rem;
        }
        .bg-theme-primary { background-color: var(--color-primary) !important; }
        .bg-theme-primary-hover:hover { background-color: var(--color-primary-hover) !important; }
        .text-theme-primary { color: var(--color-primary) !important; }
        .border-theme-primary { border-color: var(--color-primary) !important; }
        .bg-theme-light { background-color: var(--color-primary-light) !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-800 bg-slate-50 flex flex-col min-h-screen overflow-x-hidden w-full max-w-full pb-16 lg:pb-0"
      x-data="{
          mobileOpen: false,
          cartDrawer: false
      }">

    <!-- ANA HEADER & ARAMA & SEPET ALANI -->
    <header class="bg-white shadow-xs sticky top-0 z-40 border-b border-rose-100 w-full max-w-full overflow-x-hidden">
        <div class="max-w-[1536px] mx-auto px-3 sm:px-6 lg:px-8 xl:px-12 w-full">
            <div class="flex justify-between items-center h-16 sm:h-22 gap-2 sm:gap-4 w-full">
                
                <!-- Logo -->
                <div class="flex items-center gap-2 sm:gap-4 min-w-0 shrink">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 sm:gap-2.5 group min-w-0">
                        <img src="{{ asset('images/logo-badge.png') }}?v=3" alt="Tazecicekler.com" class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl shadow-sm shrink-0 group-hover:scale-105 transition-transform">
                        <div class="min-w-0">
                            <span class="text-base xs:text-lg sm:text-2xl font-black text-slate-900 tracking-tight block leading-none truncate">Tazecicekler<span class="text-rose-600">.com</span></span>
                            <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 tracking-wider uppercase block mt-0.5 truncate hidden xs:block">Taze Çiçek &amp; Hediye</span>
                        </div>
                    </a>
                </div>

                <!-- Canlı Çiçek Arama Çubuğu -->
                <div class="flex-1 max-w-2xl mx-4 hidden sm:block">
                    <form action="{{ route('products.index') }}" method="GET" class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Buket, gül, orkide, papatya veya hediye ara..."
                               class="w-full pl-11 pr-24 py-2.5 sm:py-3 rounded-2xl border-2 border-rose-100 bg-slate-50 focus:bg-white text-xs sm:text-sm font-medium focus:outline-none focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all shadow-inner">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-rose-400 group-focus-within:text-rose-600 transition-colors">
                            <i class="fas fa-search text-sm"></i>
                        </div>
                        <button type="submit"
                                class="absolute right-1.5 top-1/2 -translate-y-1/2 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs px-4 py-2 rounded-xl shadow-sm transition-all active:scale-95">
                            Çiçek Bul
                        </button>
                    </form>
                </div>

                <!-- Sağ Aksiyonlar: Takip, Giriş & Sepet -->
                <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                    
                    <!-- Sipariş Takip Butonu (Desktop) -->
                    <a href="{{ route('order.track') }}" class="hidden lg:flex items-center gap-2 px-3 py-2 rounded-2xl hover:bg-slate-100 text-slate-700 text-xs font-bold transition">
                        <i class="fas fa-truck text-rose-500"></i>
                        <span>Sipariş Takibi</span>
                    </a>

                    <!-- Admin / Giriş Butonları -->
                    @auth
                        @if(auth()->user()->is_admin || in_array(auth()->user()->role, ['admin', 'superadmin']))
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-1.5 p-2 sm:px-3 sm:py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl sm:rounded-2xl text-xs font-extrabold transition shadow-sm shrink-0" title="Yönetim Paneli">
                                <i class="fas fa-shield-alt text-amber-400"></i>
                                <span class="hidden sm:inline">Yönetim</span>
                            </a>
                        @else
                            <a href="{{ route('customer.account') }}" class="flex items-center gap-1.5 p-2 sm:px-3 sm:py-2 bg-rose-50 hover:bg-rose-100 text-rose-900 rounded-xl sm:rounded-2xl text-xs font-extrabold transition shrink-0" title="Hesabım">
                                <i class="fas fa-user text-rose-600"></i>
                                <span class="hidden sm:inline">Hesabım</span>
                            </a>
                        @endif

                        <!-- Güvenli Çıkış Butonu (Hem GET hem POST garantili) -->
                        <a href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('frontend-header-logout-form').submit();"
                           class="flex items-center gap-1.5 p-2 sm:px-3 sm:py-2 rounded-xl sm:rounded-2xl bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 shadow-2xs text-xs font-black transition shrink-0 active:scale-95"
                           title="Güvenli Çıkış Yap">
                            <i class="fas fa-power-off text-xs"></i>
                            <span class="hidden sm:inline">Çıkış</span>
                        </a>
                        <form id="frontend-header-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="flex items-center gap-1.5 p-2 sm:px-3 sm:py-2 rounded-xl sm:rounded-2xl hover:bg-slate-100 text-slate-700 text-xs font-bold transition shrink-0" title="Giriş Yap">
                            <i class="fas fa-user-circle text-slate-400 text-base"></i>
                            <span class="hidden sm:inline">Giriş Yap</span>
                        </a>
                    @endauth

                    <!-- Sepet İkonu & Mini Drawer Tetikleyici -->
                    @php 
                        $cartItems = session()->get('cart', []);
                        $cartTotalCount = 0;
                        $cartSubtotal = 0;
                        foreach($cartItems as $cItem) {
                            $cartTotalCount += $cItem['quantity'];
                            $cartSubtotal += $cItem['price'] * $cItem['quantity'];
                        }
                    @endphp
                    <a href="{{ route('cart.index') }}" 
                       class="relative p-2 sm:px-4 sm:py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl sm:rounded-2xl shadow-md shadow-rose-600/20 transition-all flex items-center gap-2 group shrink-0" title="Sepetim">
                        <i class="fas fa-shopping-bag text-base"></i>
                        <span class="hidden sm:inline text-xs font-black">Sepetim</span>
                        @if($cartTotalCount > 0)
                            <span class="absolute -top-1 -right-1 sm:static inline-flex items-center justify-center w-4 h-4 sm:w-auto sm:h-auto sm:px-2 sm:py-0.5 text-[10px] sm:text-[11px] font-black text-rose-700 bg-white rounded-full shadow-xs">
                                {{ $cartTotalCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Mobil Hamburger Butonu -->
                    <button type="button" @click="mobileOpen = !mobileOpen"
                            class="inline-flex lg:hidden items-center justify-center p-2 sm:p-2.5 rounded-xl sm:rounded-2xl text-slate-700 bg-slate-100 hover:bg-slate-200 transition shrink-0"
                            aria-label="Menü">
                        <i x-show="!mobileOpen" class="fas fa-bars text-base sm:text-lg"></i>
                        <i x-show="mobileOpen" class="fas fa-times text-base sm:text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Mobil Arama Kutusu -->
            <div class="pb-3 px-0.5 block sm:hidden w-full">
                <form action="{{ route('products.index') }}" method="GET" class="relative w-full">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Çiçek, buket veya hediye ara..."
                           class="w-full pl-9 pr-16 py-2 rounded-xl border border-rose-200 bg-slate-50 text-xs font-medium focus:bg-white focus:outline-none focus:border-rose-500 shadow-inner">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-rose-400 text-xs"></i>
                    <button type="submit" class="absolute right-1 top-1/2 -translate-y-1/2 bg-rose-600 text-white text-[11px] font-bold px-3 py-1 rounded-lg">
                        Ara
                    </button>
                </form>
            </div>
        </div>

        <!-- ÇİÇEK KATEGORİ GEZİNTİ ŞERİDİ (Desktop & Mobile Scroll) -->
        <div class="bg-white border-t border-rose-50 shadow-2xs w-full overflow-hidden">
            <div class="max-w-[1536px] mx-auto px-3 sm:px-6 lg:px-8 xl:px-12 w-full overflow-hidden">
                <nav class="flex items-center space-x-1 sm:space-x-2 py-2 sm:py-2.5 overflow-x-auto no-scrollbar text-xs font-bold text-slate-700 whitespace-nowrap touch-pan-x">
                    <a href="{{ route('custom-bouquet.index') }}" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-rose-600 text-white hover:bg-rose-700 transition shadow-xs font-black">
                        <i class="fas fa-magic text-white/80"></i>
                        <span>Kendi Buketini Oluştur</span>
                    </a>
                    <a href="{{ route('home') }}" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl hover:bg-rose-50 hover:text-rose-600 transition {{ request()->routeIs('home') ? 'bg-rose-50 text-rose-600 font-extrabold' : '' }}">
                        <span>Tümü</span>
                    </a>
                    <a href="{{ route('category.show', 'gul-buketleri') }}" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl hover:bg-rose-50 hover:text-rose-600 transition {{ request()->is('kategori/gul-buketleri*') ? 'bg-rose-50 text-rose-600 font-extrabold' : '' }}">
                        <span>Gül Buketleri</span>
                    </a>
                    <a href="{{ route('category.show', 'orkide-ve-saksi-cicekleri') }}" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl hover:bg-rose-50 hover:text-rose-600 transition {{ request()->is('kategori/orkide-ve-saksi-cicekleri*') ? 'bg-rose-50 text-rose-600 font-extrabold' : '' }}">
                        <span>Orkide &amp; Saksı</span>
                    </a>
                    <a href="{{ route('category.show', 'mevsim-cicekleri-ve-lilyum') }}" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl hover:bg-rose-50 hover:text-rose-600 transition {{ request()->is('kategori/mevsim-cicekleri-ve-lilyum*') ? 'bg-rose-50 text-rose-600 font-extrabold' : '' }}">
                        <span>Papatya &amp; Lilyum</span>
                    </a>
                    <a href="{{ route('category.show', 'vazoda-ve-kutuda-tasarimlar') }}" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl hover:bg-rose-50 hover:text-rose-600 transition {{ request()->is('kategori/vazoda-ve-kutuda-tasarimlar*') ? 'bg-rose-50 text-rose-600 font-extrabold' : '' }}">
                        <span>Kutuda Çiçekler</span>
                    </a>
                    <a href="{{ route('category.show', 'dogum-gunu-cicekleri') }}" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl hover:bg-rose-50 hover:text-rose-600 transition {{ request()->is('kategori/dogum-gunu-cicekleri*') ? 'bg-rose-50 text-rose-600 font-extrabold' : '' }}">
                        <span>Doğum Günü</span>
                    </a>
                    <a href="{{ route('category.show', 'yildonumu-ve-sevgiliye') }}" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl hover:bg-rose-50 hover:text-rose-600 transition {{ request()->is('kategori/yildonumu-ve-sevgiliye*') ? 'bg-rose-50 text-rose-600 font-extrabold' : '' }}">
                        <span>Sevgiliye &amp; Yıldönümü</span>
                    </a>
                    <a href="{{ route('category.show', 'cikolata-ve-hediye-eslikcileri') }}" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl hover:bg-rose-50 hover:text-rose-600 transition {{ request()->is('kategori/cikolata-ve-hediye-eslikcileri*') ? 'bg-rose-50 text-rose-600 font-extrabold' : '' }}">
                        <span>Çikolata &amp; Hediye</span>
                    </a>
                    <a href="{{ route('products.index', ['badge' => 'AYNI GÜN']) }}" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-amber-50 text-amber-800 hover:bg-amber-100 transition border border-amber-200">
                        <i class="fas fa-bolt text-amber-500"></i>
                        <span>Aynı Gün Teslimat</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Mobil Menü Açılır Paneli -->
        <div x-show="mobileOpen" x-transition x-cloak class="lg:hidden bg-white border-b border-rose-100 shadow-xl px-4 py-4 space-y-3">
            @auth
                <div class="p-3 rounded-2xl bg-slate-900 text-white flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <div class="w-8 h-8 rounded-xl bg-slate-800 text-rose-400 flex items-center justify-center font-bold text-xs shrink-0">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div class="text-xs font-bold truncate">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <a href="{{ route('logout') }}" 
                       onclick="event.preventDefault(); document.getElementById('frontend-mobile-logout-form').submit();"
                       class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl flex items-center gap-1.5 transition active:scale-95 shadow-xs"
                       title="Güvenli Çıkış">
                        <i class="fas fa-power-off text-xs"></i>
                        <span>Çıkış</span>
                    </a>
                    <form id="frontend-mobile-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                        @csrf
                    </form>
                </div>
                @if(auth()->user()->is_admin || in_array(auth()->user()->role, ['admin', 'superadmin']))
                    <a href="{{ route('dashboard') }}" class="w-full flex items-center justify-center gap-2 p-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow-xs transition">
                        <i class="fas fa-shield-alt text-slate-950"></i>
                        <span>Admin Yönetim Paneline Git</span>
                    </a>
                @endif
            @else
                <div class="flex gap-2">
                    <a href="{{ route('login') }}" class="flex-1 text-center py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-800 hover:text-rose-600 font-bold text-xs transition">
                        <i class="fas fa-sign-in-alt mr-1"></i> Giriş Yap
                    </a>
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="flex-1 text-center py-2.5 px-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition shadow-xs">
                            Üye Ol
                        </a>
                    @endif
                </div>
            @endauth

            <a href="{{ route('custom-bouquet.index') }}" class="w-full p-3 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs flex items-center justify-between shadow-md transition">
                <span class="flex items-center gap-2">
                    <i class="fas fa-magic text-white/80"></i>
                    <span>Kendi Buketini Kendin Oluştur</span>
                </span>
                <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-full font-bold">Özel Tasarla &rarr;</span>
            </a>

            <div class="grid grid-cols-2 gap-2 pt-1">
                <a href="{{ route('products.index') }}" class="p-2.5 rounded-xl bg-slate-50 text-xs font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-th-large text-rose-500"></i> Tüm Çiçekler
                </a>
                <a href="{{ route('order.track') }}" class="p-2.5 rounded-xl bg-slate-50 text-xs font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-truck text-rose-500"></i> Sipariş Takip
                </a>
                <a href="{{ route('cart.index') }}" class="p-2.5 rounded-xl bg-slate-50 text-xs font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-shopping-bag text-rose-500"></i> Sepetim
                </a>
                <a href="https://wa.me/905013798236" target="_blank" class="p-2.5 rounded-xl bg-emerald-50 text-xs font-bold text-emerald-800 flex items-center gap-2">
                    <i class="fab fa-whatsapp text-emerald-600"></i> WhatsApp
                </a>
            </div>
        </div>
    </header>

    <!-- Flash Bildirimleri (Çıkış, Giriş & Bilgi Mesajları) -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
             class="bg-emerald-600 text-white text-xs font-bold px-4 py-2.5 shadow-md relative z-30 transition-all">
            <div class="max-w-[1536px] mx-auto flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle text-sm"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="show = false" class="text-white/80 hover:text-white p-1 text-sm font-black leading-none">&times;</button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" 
             class="bg-red-600 text-white text-xs font-bold px-4 py-2.5 shadow-md relative z-30 transition-all">
            <div class="max-w-[1536px] mx-auto flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-sm"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="show = false" class="text-white/80 hover:text-white p-1 text-sm font-black leading-none">&times;</button>
            </div>
        </div>
    @endif

    <!-- ANA İÇERİK -->
    <main class="flex-grow overflow-x-hidden w-full max-w-full">
        {{ $slot }}
    </main>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-white mt-16 border-t-4 border-rose-600 pb-20 lg:pb-0">
        <!-- 4'lü Güven Bantı -->
        <div class="bg-slate-950/80 py-8 px-4 sm:px-6 lg:px-8 border-b border-slate-800">
            <div class="max-w-[1536px] mx-auto grid grid-cols-2 lg:grid-cols-4 gap-6 text-center sm:text-left">
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-rose-900/50 text-rose-400 flex items-center justify-center text-xl flex-shrink-0">
                        <i class="fas fa-truck-fast"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-sm text-white">Aynı Gün Hızlı Teslimat</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5">81 il ve tüm ilçelerimize güvenli teslimat</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-rose-900/50 text-rose-400 flex items-center justify-center text-xl flex-shrink-0">
                        <i class="fas fa-camera"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-sm text-white">Fotoğraflı Teslimat Onayı</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5">Buketiniz kuryeye verilmeden önce onaylanır</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-rose-900/50 text-rose-400 flex items-center justify-center text-xl flex-shrink-0">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-sm text-white">%100 Taze Çiçek Garantisi</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5">Seradan özenle seçilen en taze goncalar</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-rose-900/50 text-rose-400 flex items-center justify-center text-xl flex-shrink-0">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-sm text-white">256-Bit SSL Güvenli Ödeme</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5">PayTR 3D Secure ve Kapıda Ödeme</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-[1536px] mx-auto pt-14 pb-10 px-4 sm:px-6 lg:px-8 xl:px-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                <!-- Col 1: Brand & Hakkımızda -->
                <div>
                    <div class="flex items-center gap-2.5 mb-4">
                        <img src="{{ asset('images/logo-badge.png') }}?v=3" alt="Tazecicekler.com" class="w-10 h-10 rounded-2xl shadow-md border border-slate-700 shrink-0">
                        <span class="text-2xl font-black text-white tracking-tight">Tazecicekler<span class="text-rose-500">.com</span></span>
                    </div>
                    <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                        Tazecicekler.com, sevdiklerinize en taze duyguları ulaştırmak için taze ithal çiçekler, özenli el bağlama buketler ve fotoğraflı teslimat güvencesi sunar.
                    </p>
                    <div class="flex gap-2.5 mt-6">
                        <a href="https://wa.me/905013798236" target="_blank" class="w-9 h-9 bg-emerald-600 hover:bg-emerald-500 rounded-xl flex items-center justify-center text-white transition">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="#" class="w-9 h-9 bg-rose-600 hover:bg-rose-500 rounded-xl flex items-center justify-center text-white transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Popüler Kategoriler -->
                <div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-rose-400 mb-4">Çiçek Kategorileri</h3>
                    <ul class="space-y-2.5 text-xs text-slate-300 font-medium">
                        <li><a href="{{ route('category.show', 'gul-buketleri') }}" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-rose-500"></i> Gül Buketleri</a></li>
                        <li><a href="{{ route('category.show', 'orkide-ve-saksi-cicekleri') }}" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-rose-500"></i> Kraliyet Orkideleri</a></li>
                        <li><a href="{{ route('category.show', 'vazoda-ve-kutuda-tasarimlar') }}" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-rose-500"></i> Kutuda Çiçek Tasarımları</a></li>
                        <li><a href="{{ route('category.show', 'mevsim-cicekleri-ve-lilyum') }}" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-rose-500"></i> Kokulu Lilyum &amp; Papatyalar</a></li>
                        <li><a href="{{ route('category.show', 'acilis-dugun-ve-celenk') }}" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-rose-500"></i> Açılış &amp; Düğün Çelenkleri</a></li>
                    </ul>
                </div>

                <!-- Col 3: Hızlı Linkler & Hukuki -->
                <div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-rose-400 mb-4">Müşteri Rehberi</h3>
                    <ul class="space-y-2.5 text-xs text-slate-300 font-medium">
                        <li><a href="{{ route('order.track') }}" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-rose-500"></i> Canlı Sipariş Takibi</a></li>
                        <li><a href="{{ route('cart.index') }}" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-rose-500"></i> Alışveriş Sepetim</a></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-rose-500"></i> Çiçek Bakım Kılavuzu</a></li>
                        <li><a href="{{ route('page.show', 'mesafeli-satis-sozlesmesi') }}" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-rose-500"></i> Mesafeli Satış Sözleşmesi</a></li>
                        <li><a href="{{ route('page.show', 'kvkk-aydinlatma-metni') }}" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-rose-500"></i> 6698 KVKK Bilgilendirmesi</a></li>
                    </ul>
                </div>

                <!-- Col 4: İletişim & Kurye Hattı -->
                <div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-rose-400 mb-4">Müşteri Hizmetleri</h3>
                    <ul class="space-y-3 text-xs text-slate-300">
                        <li class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-slate-800 flex items-center justify-center text-rose-400"><i class="fas fa-phone"></i></span>
                            <div>
                                <span class="text-[10px] text-slate-400 block font-bold">Hızlı Sipariş Hattı</span>
                                <span class="font-extrabold text-white text-sm">0850 300 34 34</span>
                            </div>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-slate-800 flex items-center justify-center text-emerald-400"><i class="fab fa-whatsapp"></i></span>
                            <div>
                                <span class="text-[10px] text-slate-400 block font-bold">WhatsApp Sipariş</span>
                                <span class="font-extrabold text-white text-sm">0501 379 82 36</span>
                            </div>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-slate-800 flex items-center justify-center text-rose-400"><i class="fas fa-clock"></i></span>
                            <div>
                                <span class="text-[10px] text-slate-400 block font-bold">Çalışma Saatleri</span>
                                <span>Hergün: 08:00 - 23:00</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Alt Güvenlik & Telif Bandı -->
            <div class="border-t border-slate-800 pt-6 pb-6 lg:pb-0 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
                <span>&copy; {{ date('Y') }} Tazecicekler.com A.Ş. Tüm hakları saklıdır.</span>
                <div class="flex items-center gap-3 text-lg text-slate-400">
                    <i class="fab fa-cc-visa" title="Visa"></i>
                    <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                    <i class="fas fa-shield-alt text-emerald-400 text-sm" title="256-Bit SSL"></i>
                    <span class="text-[11px] font-bold text-slate-300">PayTR Korumalı 3D Secure</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- MOBİL SABİT ALT GEZİNME BARI (Bottom Navigation Bar) -->
    <div class="lg:hidden fixed bottom-0 left-0 right-0 w-full max-w-full bg-white/95 backdrop-blur-md border-t border-rose-100 z-50 shadow-lg px-2 py-1.5 flex items-center justify-around text-center">
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-0.5 p-1 text-rose-600">
            <i class="fas fa-home text-base"></i>
            <span class="text-[10px] font-bold">Vitrin</span>
        </a>
        <a href="{{ route('products.index') }}" class="flex flex-col items-center gap-0.5 p-1 text-slate-600 hover:text-rose-600">
            <i class="fas fa-th-large text-base"></i>
            <span class="text-[10px] font-bold">Çiçekler</span>
        </a>
        <a href="{{ route('order.track') }}" class="flex flex-col items-center gap-0.5 p-1 text-slate-600 hover:text-rose-600">
            <i class="fas fa-truck text-base"></i>
            <span class="text-[10px] font-bold">Takip</span>
        </a>
        <a href="{{ route('cart.index') }}" class="flex flex-col items-center gap-0.5 p-1 text-slate-600 hover:text-rose-600 relative">
            <i class="fas fa-shopping-bag text-base"></i>
            @if($cartTotalCount > 0)
                <span class="absolute top-0 right-1 w-4 h-4 rounded-full bg-rose-600 text-white text-[9px] font-black flex items-center justify-center">
                    {{ $cartTotalCount }}
                </span>
            @endif
            <span class="text-[10px] font-bold">Sepet</span>
        </a>
    </div>

</body>
</html>
