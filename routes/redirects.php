<?php

use Illuminate\Support\Facades\Route;

// ==========================================
// 🚀 SEO & GOOGLE SEARCH CONSOLE 301 KALICI YÖNLENDİRMELERİ
// Eski siteden kalan ve Google'da indeksli olan bağlantıları
// güncel ürün, kategori ve servis sayfalarına yönlendirir.
// ==========================================

$exactRedirects = [
    // 1. Kategoriler & Genel Sayfalar
    '/aritma-cihazlari' => '/kategori/su-aritma-cihazlari',
    '/su-aritma.html' => '/kategori/su-aritma-cihazlari',
    '/filtre.html' => '/kategori/filtreler',
    '/kategori/filtre' => '/kategori/filtreler',
    '/kategori/aritma cihazi' => '/kategori/su-aritma-cihazlari',
    '/kategori/aritma-cihazi' => '/kategori/su-aritma-cihazlari',
    '/yedek-parca.html' => '/kategori/yedek-parca-musluk',
    '/kategori/parcalar yardimci' => '/kategori/yedek-parca-musluk',
    '/endustriyel-aritmalar' => '/kategori/endustriyel-sistemler',
    '/tanklar' => '/kategori/yedek-parca-musluk',
    '/metal-tanklar-875' => '/kategori/yedek-parca-musluk',
    '/istanbul-su-aritma-marketi' => '/',

    // 2. Cihaz Modelleri
    '/owist.html' => '/urunler?search=owist',
    '/owist-prodijital' => '/urunler?search=owist',
    '/owist-su-aritma-cihazi' => '/urunler?search=owist',
    '/oweco.html' => '/urunler?search=oweco',
    '/owex.html' => '/urunler?search=owex',
    '/okda.html' => '/kategori/su-aritma-cihazlari',
    '/owt.html' => '/kategori/su-aritma-cihazlari',
    '/fe.html' => '/kategori/su-aritma-cihazlari',
    '/kabinet.html' => '/kategori/su-aritma-cihazlari',
    '/dropning-su-aritma-cihazi-12-asamali-filtre-celik-depo.html' => '/kategori/su-aritma-cihazlari',
    '/endustriyel-ro-water-big-blue' => '/kategori/endustriyel-sistemler',
    '/endustruyel-aritma-ro-water-200-600-galon' => '/kategori/endustriyel-sistemler',
    '/endustriyel-ro-water-aritma' => '/kategori/endustriyel-sistemler',

    // 3. Filtreler & Uyumlu Markalar
    '/5-li-filtre.html' => '/urunler?search=5+li+filtre',
    '/5li-filtre-seti-lg-uyumlu' => '/urunler?search=lg+uyumlu',
    '/5li-filtre-seti' => '/urunler?search=5+li+filtre',
    '/5li-filtre-seti-1' => '/urunler?search=5+li+filtre',
    '/3-lu-filtre40.html' => '/urunler?search=3+lu+filtre',
    '/3-lu-12set' => '/urunler?search=3+lu+filtre',
    '/filtre-spun.html' => '/kategori/filtreler',
    '/sediment-12' => '/kategori/filtreler',
    '/gac-12' => '/kategori/filtreler',
    '/cto-12' => '/kategori/filtreler',
    '/endustriyel-membran' => '/kategori/endustriyel-sistemler',
    '/membran' => '/urunler?search=membran',
    '/pages/suaritmafiltresi' => '/kategori/filtreler',
    '/pages/ihlas-uyumlu-filtre' => '/urunler?search=ihlas',
    '/pages/conax-uyumlu-filtre' => '/urunler?search=conax',
    '/pages/waterbox-waterlife-filtre' => '/urunler?search=waterbox',
    '/pages/lg-uyumlu-filtre' => '/urunler?search=lg',
    '/pages/genel-yedek-parca' => '/kategori/yedek-parca-musluk',
    '/ihlas.html' => '/urunler?search=ihlas',

    // 4. İletişim, Servis & Kurumsal Sayfalar
    '/iletisim.html' => '/iletisim',
    '/hakkimizda.html' => '/',
    '/su-aritma-sistemleri-bakimi.html' => '/servis-talebi',
    '/faydali-bilgiler.html' => '/blog',
    '/banka-hesaplarimiz.html' => '/iletisim',
    '/kapida-odeme-kolayligi32.html' => '/',
    '/gizlilik-ve-guvenlik.html' => '/',
    '/mesafeli-satis-sozlesmesi.html' => '/',
    '/watelife' => '/',
];

foreach ($exactRedirects as $from => $to) {
    Route::redirect($from, $to, 301);
}

// 5. Diğer tüm eski .html uzantılı bilinmeyen URL'ler için genel 301 yönlendirmesi
Route::get('/{any}.html', function () {
    return redirect('/', 301);
})->where('any', '.*');
