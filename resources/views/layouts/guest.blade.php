<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Yiğit Core') }} | Giriş &amp; Kayıt</title>

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
            .focus\:ring-theme-primary:focus { --tw-ring-color: var(--color-primary) !important; }
            .focus\:border-theme-primary:focus { border-color: var(--color-primary) !important; }
            body { font-family: 'Figtree', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center py-10 px-4 sm:px-6 bg-gradient-to-b from-gray-100 via-gray-50 to-white">
            <div class="mb-8">
                <a href="/" class="hover:opacity-90 transition-opacity">
                    <x-application-logo />
                </a>
            </div>

            <div class="w-full sm:max-w-md p-6 sm:p-10 bg-white shadow-2xl border border-gray-100 rounded-3xl overflow-hidden">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
