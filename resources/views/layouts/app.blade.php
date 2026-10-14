<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Yiğit Core') }} | Yönetim Paneli</title>

        <!-- Favicon & Brand Icons -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=3">
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=3">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=3">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v=3">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=3">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}?v=3">

        <!-- Fonts & Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Dynamic Theme CSS Variables -->
        <style>
            :root {
                --color-primary: {{ $currentTheme['primary'] ?? '#4f46e5' }};
                --color-primary-hover: {{ $currentTheme['primary_hover'] ?? '#4338ca' }};
                --color-primary-light: {{ $currentTheme['primary_light'] ?? '#e0e7ff' }};
                --color-primary-dark: {{ $currentTheme['primary_dark'] ?? '#312e81' }};
                --color-accent: {{ $currentTheme['accent'] ?? '#06b6d4' }};
                --color-accent-hover: {{ $currentTheme['accent_hover'] ?? '#0891b2' }};
            }
            .bg-theme-primary { background-color: var(--color-primary) !important; }
            .bg-theme-primary-hover:hover { background-color: var(--color-primary-hover) !important; }
            .text-theme-primary { color: var(--color-primary) !important; }
            .border-theme-primary { border-color: var(--color-primary) !important; }
            .bg-theme-light { background-color: var(--color-primary-light) !important; }
            .ring-theme-primary:focus { --tw-ring-color: var(--color-primary) !important; }
            body { font-family: 'Figtree', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }

            /* Admin Paneli Standart Kompakt Tipografi Zırhı */
            body.admin-panel,
            .admin-panel {
                font-size: 14px !important;
            }
            .admin-panel .text-xs,
            .admin-panel [class*="text-xs"] {
                font-size: 12px !important;
                line-height: 16px !important;
            }
            .admin-panel .text-sm,
            .admin-panel [class*="text-sm"] {
                font-size: 14px !important;
                line-height: 20px !important;
            }
            .admin-panel .text-base,
            .admin-panel [class*="text-base"] {
                font-size: 16px !important;
                line-height: 24px !important;
            }
            .admin-panel .text-lg,
            .admin-panel [class*="text-lg"] {
                font-size: 18px !important;
                line-height: 26px !important;
            }
            .admin-panel .text-xl,
            .admin-panel [class*="text-xl"] {
                font-size: 20px !important;
                line-height: 28px !important;
            }
            .admin-panel .text-2xl,
            .admin-panel [class*="text-2xl"] {
                font-size: 22px !important;
                line-height: 28px !important;
            }
            .admin-panel .text-3xl,
            .admin-panel [class*="text-3xl"] {
                font-size: 26px !important;
                line-height: 32px !important;
            }
            .admin-panel .text-4xl,
            .admin-panel [class*="text-4xl"] {
                font-size: 30px !important;
                line-height: 36px !important;
            }
            .admin-panel .text-\[8px\] { font-size: 8px !important; line-height: 10px !important; }
            .admin-panel .text-\[9px\] { font-size: 9px !important; line-height: 12px !important; }
            .admin-panel .text-\[10px\] { font-size: 10px !important; line-height: 14px !important; }
            .admin-panel .text-\[11px\] { font-size: 11px !important; line-height: 15px !important; }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="admin-panel font-sans antialiased text-gray-900 bg-slate-100 flex h-screen overflow-hidden" x-data="{ open: false }">
        
        <!-- Mobile Sidebar Overlay -->
        <div x-show="open" @click="open = false" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-30 lg:hidden" style="display: none;"></div>

        <!-- Sidebar -->
        @include('layouts.navigation')

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            
            <!-- Modern Header (Top bar) -->
            <header class="bg-white/95 backdrop-blur-md sticky top-0 z-20 border-b border-gray-200/80 px-4 sm:px-6 py-2.5 shadow-xs">
                <div class="flex items-center justify-between gap-4">
                    
                    <!-- Left: Header / Breadcrumb -->
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <!-- Mobile Hamburger Button -->
                        <button @click="open = ! open" class="lg:hidden p-2 rounded-xl text-gray-500 hover:text-gray-900 hover:bg-gray-100 focus:outline-none transition">
                            <i class="fas fa-bars text-base"></i>
                        </button>

                        @isset($header)
                            <div class="min-w-0">
                                {{ $header }}
                            </div>
                        @else
                            <h1 class="text-sm font-black text-gray-900 tracking-tight truncate">
                                {{ config('app.name', 'Tazecicekler.com') }}
                            </h1>
                        @endisset
                    </div>

                    <!-- Right: Quick Status & Actions -->
                    <div class="flex items-center gap-2 shrink-0">
                        <!-- 🔔 Canlı Sipariş Zil & Bildirim Motoru -->
                        <x-order-sound-notifier />

                        <!-- Aktif Mod Rozeti -->
                        <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-black uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            E-Ticaret &amp; Satış
                        </span>

                        <!-- Siteyi Önizle / Görüntüle Butonu -->
                        <a href="{{ url('/') }}" target="_blank" 
                           class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-black bg-slate-900 hover:bg-slate-800 text-white shadow-xs transition-all hover:scale-105 active:scale-95"
                           title="Sitenin canlı ön yüzünü yeni sekmede aç">
                            <span>Siteye Git</span>
                            <i class="fas fa-external-link-alt text-[9px] text-gray-300"></i>
                        </a>

                        <!-- Güvenli Çıkış Butonu (GET & POST garantili) -->
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('admin-header-logout-form').submit();"
                           class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-black bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 shadow-xs transition-all hover:scale-105 active:scale-95 cursor-pointer"
                           title="Güvenli Çıkış">
                            <i class="fas fa-power-off text-xs"></i>
                            <span>Çıkış</span>
                        </a>
                        <form id="admin-header-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
                
                <!-- Otomatik Flash Mesajları (Başarı & Hata) -->
                @if(session('success'))
                    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 shadow-xs flex items-center justify-between gap-3 text-xs font-bold animate-fadeIn">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-2xl p-4 shadow-xs flex items-center justify-between gap-3 text-xs font-bold animate-fadeIn">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle text-red-600 text-base"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                {{ $slot }}
            </main>

        </div>
    </body>
</html>
