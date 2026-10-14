<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dinamik & Seçilebilir İlçe / Bölge SEO Motoru
    |--------------------------------------------------------------------------
    | Her web sitesi İstanbul'un 39 ilçesinin tamamına hizmet vermek zorunda
    | değildir. İster 5 ilçe (sadece Anadolu yakası), ister 32 ilçe, ister
    | tamamı seçilebilir.
    |
    | 'active' ayarı:
    | - 'all' => Tüm 39 ilçe aktif
    | - ['kadikoy', 'uskudar', 'besiktas'] => Sadece belirtilen ilçeler aktif
    | - .env üzerinden ACTIVE_DISTRICTS=kadikoy,umraniye,uskudar şeklinde virgülle de verilebilir.
    |
    */

    'active' => env('ACTIVE_DISTRICTS', 'all'),

    /*
    |--------------------------------------------------------------------------
    | Tüm İlçe Havuzu
    |--------------------------------------------------------------------------
    */
    'list' => [
        // --- ANADOLU YAKASI (14 İlçe) ---
        'kadikoy' => ['name' => 'Kadıköy', 'side' => 'Anadolu', 'code' => 3401],
        'umraniye' => ['name' => 'Ümraniye', 'side' => 'Anadolu', 'code' => 3402],
        'uskudar' => ['name' => 'Üsküdar', 'side' => 'Anadolu', 'code' => 3403],
        'atasehir' => ['name' => 'Ataşehir', 'side' => 'Anadolu', 'code' => 3404],
        'maltepe' => ['name' => 'Maltepe', 'side' => 'Anadolu', 'code' => 3405],
        'kartal' => ['name' => 'Kartal', 'side' => 'Anadolu', 'code' => 3406],
        'pendik' => ['name' => 'Pendik', 'side' => 'Anadolu', 'code' => 3407],
        'tuzla' => ['name' => 'Tuzla', 'side' => 'Anadolu', 'code' => 3408],
        'beykoz' => ['name' => 'Beykoz', 'side' => 'Anadolu', 'code' => 3409],
        'cekmekoy' => ['name' => 'Çekmeköy', 'side' => 'Anadolu', 'code' => 3410],
        'sancaktepe' => ['name' => 'Sancaktepe', 'side' => 'Anadolu', 'code' => 3411],
        'sultanbeyli' => ['name' => 'Sultanbeyli', 'side' => 'Anadolu', 'code' => 3412],
        'sile' => ['name' => 'Şile', 'side' => 'Anadolu', 'code' => 3413],
        'adalar' => ['name' => 'Adalar', 'side' => 'Anadolu', 'code' => 3414],

        // --- AVRUPA YAKASI (25 İlçe) ---
        'besiktas' => ['name' => 'Beşiktaş', 'side' => 'Avrupa', 'code' => 3415],
        'sisli' => ['name' => 'Şişli', 'side' => 'Avrupa', 'code' => 3416],
        'sariyer' => ['name' => 'Sarıyer', 'side' => 'Avrupa', 'code' => 3417],
        'beyoglu' => ['name' => 'Beyoğlu', 'side' => 'Avrupa', 'code' => 3418],
        'fatih' => ['name' => 'Fatih', 'side' => 'Avrupa', 'code' => 3419],
        'bakirkoy' => ['name' => 'Bakırköy', 'side' => 'Avrupa', 'code' => 3420],
        'bahcelievler' => ['name' => 'Bahçelievler', 'side' => 'Avrupa', 'code' => 3421],
        'gungoren' => ['name' => 'Güngören', 'side' => 'Avrupa', 'code' => 3422],
        'zeytinburnu' => ['name' => 'Zeytinburnu', 'side' => 'Avrupa', 'code' => 3423],
        'bayrampasa' => ['name' => 'Bayrampaşa', 'side' => 'Avrupa', 'code' => 3424],
        'gaziosmanpasa' => ['name' => 'Gaziosmanpaşa', 'side' => 'Avrupa', 'code' => 3425],
        'eyupsultan' => ['name' => 'Eyüpsultan', 'side' => 'Avrupa', 'code' => 3426],
        'kagithane' => ['name' => 'Kağıthane', 'side' => 'Avrupa', 'code' => 3427],
        'sultangazi' => ['name' => 'Sultangazi', 'side' => 'Avrupa', 'code' => 3428],
        'esenler' => ['name' => 'Esenler', 'side' => 'Avrupa', 'code' => 3429],
        'bagcilar' => ['name' => 'Bağcılar', 'side' => 'Avrupa', 'code' => 3430],
        'kucukcekmece' => ['name' => 'Küçükçekmece', 'side' => 'Avrupa', 'code' => 3431],
        'avcilar' => ['name' => 'Avcılar', 'side' => 'Avrupa', 'code' => 3432],
        'beylikduzu' => ['name' => 'Beylikdüzü', 'side' => 'Avrupa', 'code' => 3433],
        'esenyurt' => ['name' => 'Esenyurt', 'side' => 'Avrupa', 'code' => 3434],
        'basaksehir' => ['name' => 'Başakşehir', 'side' => 'Avrupa', 'code' => 3435],
        'buyukcekmece' => ['name' => 'Büyükçekmece', 'side' => 'Avrupa', 'code' => 3436],
        'catalca' => ['name' => 'Çatalca', 'side' => 'Avrupa', 'code' => 3437],
        'silivri' => ['name' => 'Silivri', 'side' => 'Avrupa', 'code' => 3438],
        'arnavutkoy' => ['name' => 'Arnavutköy', 'side' => 'Avrupa', 'code' => 3439],
    ],
];
