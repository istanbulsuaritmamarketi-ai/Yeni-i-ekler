<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Aktif Tema Paleti
    |--------------------------------------------------------------------------
    | Desteklenen Seçenekler: 'blue' (Mavi Çiçekçi), 'rose' (Gül Kurusu),
    | 'amber' (İnşaat / Lüks), 'indigo' (Ajans), 'sky' (Su Arıtma)
    */
    'current' => env('THEME', 'blue'),

    'palettes' => [
        'blue' => [
            'name' => 'Kraliyet Mavisi & Gece Laciverti (Mavi Çiçekçi)',
            'primary' => '#1d4ed8',         // blue-700 / royal blue
            'primary_hover' => '#1e40af',   // blue-800
            'primary_light' => '#eff6ff',   // blue-50
            'primary_dark' => '#0f2356',    // midnight navy
            'accent' => '#f59e0b',          // amber-500 (gold contrast)
            'accent_hover' => '#d97706',
        ],
        'rose' => [
            'name' => 'Gül Kurusu & Zümrüt (Çiçekçi Modu)',
            'primary' => '#1d4ed8',         // Mavi tonuna uyarlandı
            'primary_hover' => '#1e40af',
            'primary_light' => '#eff6ff',
            'primary_dark' => '#0f2356',
            'accent' => '#f59e0b',
            'accent_hover' => '#d97706',
        ],
        'amber' => [
            'name' => 'Lüks Antrasit & Altın (İnşaat / Mimarlık Modu)',
            'primary' => '#d97706',         // amber-600
            'primary_hover' => '#b45309',   // amber-700
            'primary_light' => '#fef3c7',   // amber-100
            'primary_dark' => '#78350f',    // amber-900
            'accent' => '#0f172a',          // slate-900
            'accent_hover' => '#1e293b',
        ],
        'indigo' => [
            'name' => 'Modern Mor & Lacivert (Ajans / Teknoloji Modu)',
            'primary' => '#4f46e5',         // indigo-600
            'primary_hover' => '#4338ca',   // indigo-700
            'primary_light' => '#e0e7ff',   // indigo-100
            'primary_dark' => '#312e81',    // indigo-900
            'accent' => '#06b6d4',          // cyan-500
            'accent_hover' => '#0891b2',
        ],
        'sky' => [
            'name' => 'Gök Mavisi (Su Arıtma / Hizmet Modu)',
            'primary' => '#0284c7',         // sky-600
            'primary_hover' => '#0369a1',   // sky-700
            'primary_light' => '#e0f2fe',   // sky-100
            'primary_dark' => '#0c4a6e',    // sky-900
            'accent' => '#10b981',          // emerald-500
            'accent_hover' => '#059669',
        ],
    ],
];
