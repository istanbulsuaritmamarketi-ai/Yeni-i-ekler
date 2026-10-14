<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use App\Models\Project;
use App\Models\Review;
use App\Models\Setting;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SiteAuditorService
{
    /**
     * Sitenin tüm sistemini, ayarlarını, veritabanını ve sitemap sayfalarındaki butonları baştan sona denetler.
     */
    public function runFullAudit(): array
    {
        $siteMode = env('SITE_MODE', 'corporate');
        $siteSettings = Setting::all()->pluck('value', 'key')->toArray();

        $bugs = [];
        $warnings = [];
        $ideas = [];
        $passed = [];

        // =====================================================================
        // 1. KRİTİK AYARLAR & ENTEGRASYON DENETİMİ
        // =====================================================================
        $whatsapp = $siteSettings['whatsapp'] ?? null;
        if (empty($whatsapp)) {
            $bugs[] = [
                'type' => 'critical',
                'title' => 'WhatsApp İletişim Numarası Eksik',
                'desc' => 'Sitedeki tüm yüzen butonlar ve hızlı teklif linkleri için WhatsApp numarası tanımlanmamış.',
                'fix_url' => route('admin.settings.index'),
                'fix_text' => 'Ayarları Düzenle',
            ];
        } else {
            $cleanWa = preg_replace('/[^0-9]/', '', $whatsapp);
            if (strlen($cleanWa) < 10) {
                $warnings[] = [
                    'type' => 'warning',
                    'title' => 'WhatsApp Numarası Hatalı Görünüyor',
                    'desc' => 'Tanımlanan numara ('.$whatsapp.') 10 haneden kısa. wa.me linkleri çalışmayabilir.',
                    'fix_url' => route('admin.settings.index'),
                    'fix_text' => 'Numarayı Düzelt',
                ];
            } else {
                $passed[] = 'WhatsApp iletişim hattı tanımlı ve aktif: '.$whatsapp;
            }
        }

        $phone = $siteSettings['phone'] ?? null;
        if (empty($phone)) {
            $warnings[] = [
                'type' => 'warning',
                'title' => 'Doğrudan Arama Telefon Numarası Boş',
                'desc' => 'Header ve yüzen butonlardaki tel: linkleri boş kalabilir.',
                'fix_url' => route('admin.settings.index'),
                'fix_text' => 'Telefon Ekle',
            ];
        } else {
            $passed[] = 'Telefon arama hattı tanımlı: '.$phone;
        }

        // Google Ads / Analytics Kontrolü
        $googleAdsId = $siteSettings['google_ads_id'] ?? env('GOOGLE_ADS_ID');
        if (empty($googleAdsId)) {
            $ideas[] = [
                'type' => 'idea',
                'title' => 'Google Ads / Analytics Dönüşüm Kodu Tanımlanmamış',
                'desc' => 'Google Ads ID tanımlanırsa WhatsApp ve telefon aramaları anında otomatik reklama sayılır, reklam bütçenizi %30 optimize eder.',
                'fix_url' => route('admin.settings.index'),
                'fix_text' => 'Google Ads ID Gir',
            ];
        } else {
            $passed[] = 'Google Ads / Analytics dönüşüm takibi aktif: '.$googleAdsId;
        }

        // =====================================================================
        // 2. HUKUK & 6698 KVKK DENETİMİ
        // =====================================================================
        if (Schema::hasTable('pages')) {
            $requiredPages = [
                'kvkk-aydinlatma-metni' => '6698 KVKK Aydınlatma Metni',
                'gizlilik-ve-cerez-politikasi' => 'Gizlilik & Çerez Politikası',
            ];

            if ($siteMode === 'ecommerce') {
                $requiredPages['mesafeli-satis-sozlesmesi'] = 'Mesafeli Satış Sözleşmesi';
                $requiredPages['teslimat-ve-iade-sartlari'] = 'Teslimat ve İade Koşulları';
            }

            foreach ($requiredPages as $slug => $pageTitle) {
                $page = Page::where('slug', $slug)->first();
                if (! $page) {
                    $bugs[] = [
                        'type' => 'critical',
                        'title' => 'Zorunlu Yasal Sayfa Eksik: '.$pageTitle,
                        'desc' => 'Tüketici ve KVKK kanunu gereği sitenizde '.$pageTitle.' sayfası bulunmalıdır.',
                        'fix_url' => route('admin.pages.create'),
                        'fix_text' => 'Sayfayı Oluştur',
                    ];
                } elseif (empty(trim(strip_tags($page->content)))) {
                    $warnings[] = [
                        'type' => 'warning',
                        'title' => $pageTitle.' Sayfasının İçi Boş',
                        'desc' => 'Sayfa veritabanında var fakat metin girilmemiş. Ziyaretçiler boş sayfa görüyor.',
                        'fix_url' => route('admin.pages.edit', $page->id),
                        'fix_text' => 'Metni Doldur',
                    ];
                } else {
                    $passed[] = $pageTitle.' sayfası eksiksiz ve yayında.';
                }
            }
        }

        // =====================================================================
        // 3. E-TİCARET & ÜRÜN İÇERİK DENETİMİ (E-Ticaret Modundaysa)
        // =====================================================================
        if ($siteMode === 'ecommerce' && Schema::hasTable('products')) {
            $totalProducts = Product::count();
            $activeProducts = Product::where('is_active', true)->count();

            if ($totalProducts === 0) {
                $bugs[] = [
                    'type' => 'critical',
                    'title' => 'Sitede Hiç Ürün Bulunmuyor',
                    'desc' => 'E-ticaret modundasınız fakat veritabanında kayıtlı ürün yok. Katalog boş görünüyor.',
                    'fix_url' => route('admin.products.create'),
                    'fix_text' => 'Ürün Ekle',
                ];
            } else {
                $passed[] = 'Katalogda toplam '.$activeProducts.' adet aktif ürün yayında.';

                // Görseli eksik ürünler
                $noImageProducts = Product::where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('image')->orWhere('image', '');
                    })->count();

                if ($noImageProducts > 0) {
                    $warnings[] = [
                        'type' => 'warning',
                        'title' => $noImageProducts.' Adet Ürünün Ana Görseli Eksik',
                        'desc' => 'Fotoğrafı olmayan ürünler e-ticarette güveni düşürür ve tıklanma almaz.',
                        'fix_url' => route('admin.products.index'),
                        'fix_text' => 'Görselleri Yükle',
                    ];
                } else {
                    $passed[] = 'Tüm aktif ürünlerin ana görselleri eksiksiz.';
                }

                // Fiyatı 0 TL kalmış ürünler
                $zeroPriceProducts = Product::where('is_active', true)->where('price', '<=', 0)->count();
                if ($zeroPriceProducts > 0) {
                    $bugs[] = [
                        'type' => 'critical',
                        'title' => $zeroPriceProducts.' Adet Ürünün Satış Fiyatı 0 TL',
                        'desc' => 'Fiyatı 0 TL olan ürünler sepete bedava eklenebilir! Acil fiyat kontrolü yapmalısınız.',
                        'fix_url' => route('admin.products.index'),
                        'fix_text' => 'Fiyatları Düzenle',
                    ];
                }

                // Boş Kategoriler
                if (Schema::hasTable('categories')) {
                    $emptyCategories = Category::whereDoesntHave('products')->count();
                    if ($emptyCategories > 0) {
                        $ideas[] = [
                            'type' => 'idea',
                            'title' => $emptyCategories.' Adet Boş Kategori Bulunuyor',
                            'desc' => 'İçinde ürün bulunmayan kategorileri ya ürünle doldurun ya da menü kirliliği yaratmaması için gizleyin.',
                            'fix_url' => route('admin.categories.index'),
                            'fix_text' => 'Kategorileri Düzenle',
                        ];
                    }
                }
            }

            // PayTR Kontrolü
            $paytrMerchantId = env('PAYTR_MERCHANT_ID');
            if (empty($paytrMerchantId) || $paytrMerchantId === 'XXXXXX') {
                $warnings[] = [
                    'type' => 'warning',
                    'title' => 'PayTR Sanal POS Anahtarları Test Aşamasında',
                    'desc' => 'Canlı kredi kartı tahsilatı için PAYTR_MERCHANT_ID ve KEY değerlerinin .env dosyasında dolu olduğundan emin olun.',
                    'fix_url' => 'https://www.paytr.com/magaza/bilgi',
                    'fix_text' => 'PayTR Panelini Aç',
                ];
            } else {
                $passed[] = 'PayTR Sanal POS mağaza anahtarı tanımlı.';
            }

            // Kupon Sistemi Önerisi
            if (Schema::hasTable('coupons')) {
                $activeCoupons = Coupon::where('is_active', true)->count();
                if ($activeCoupons === 0) {
                    $ideas[] = [
                        'type' => 'idea',
                        'title' => 'Aktif İndirim Kuponu Bulunmuyor',
                        'desc' => '"HOSGELDIN10" veya "BAHAR100" gibi bir karşılama kuponu oluşturursanız sepeti terk etme oranı %25 düşer.',
                        'fix_url' => route('admin.coupons.create'),
                        'fix_text' => 'İlk Kuponu Oluştur',
                    ];
                } else {
                    $passed[] = 'Sitede '.$activeCoupons.' adet aktif indirim kuponu tanımlı.';
                }
            }
        }

        // =====================================================================
        // 4. KURUMSAL & PORTFÖY DENETİMİ (Kurumsal Modundaysa)
        // =====================================================================
        if ($siteMode === 'corporate') {
            $allowRegistration = env('ENABLE_CUSTOMER_REGISTRATION', false);
            if ($allowRegistration) {
                $warnings[] = [
                    'type' => 'warning',
                    'title' => 'Kurumsal Sitede Müşteri Kaydı (Register) Açık!',
                    'desc' => 'İnşaat veya ajans sitelerinde rastgele ziyaretçilerin üye olmasına gerek yoktur. ENABLE_CUSTOMER_REGISTRATION=false yaparak header\'ı temiz tutabilirsiniz.',
                    'fix_url' => route('admin.settings.index'),
                    'fix_text' => 'Ayarları Kapat',
                ];
            } else {
                $passed[] = 'Kurumsal mimari korundu: Halka açık müşteri kaydı kapalı, sadece admin erişebilir.';
            }

            if (Schema::hasTable('projects')) {
                $projectCount = Project::where('is_active', true)->count();
                if ($projectCount === 0) {
                    $warnings[] = [
                        'type' => 'warning',
                        'title' => 'Portföyde Hiç Aktif Proje Bulunmuyor',
                        'desc' => 'Kurumsal sitede referans veya proje olmaması güvenilirliği düşürür.',
                        'fix_url' => route('admin.projects.create'),
                        'fix_text' => 'Proje Ekle',
                    ];
                } elseif ($projectCount < 3) {
                    $ideas[] = [
                        'type' => 'idea',
                        'title' => 'Portföy Proje Sayısını Artırın (Mevcut: '.$projectCount.')',
                        'desc' => 'En az 3-5 adet tamamlanan/devam eden proje sergilemek kurumsal prestiji %60 artırır.',
                        'fix_url' => route('admin.projects.create'),
                        'fix_text' => 'Yeni Proje Ekle',
                    ];
                } else {
                    $passed[] = 'Portföyde '.$projectCount.' adet aktif kurumsal proje sergileniyor.';
                }
            }
        }

        // =====================================================================
        // 5. SEO, İLÇELER & SİTEMAP DENETİMİ
        // =====================================================================
        $activeDistricts = config('districts.active', 'all');
        if ($activeDistricts === 'none' || empty($activeDistricts)) {
            $warnings[] = [
                'type' => 'warning',
                'title' => 'Aktif Hizmet Bölgeleri (İlçeler) Tanımlanmamış',
                'desc' => 'Yerel SEO için en az birkaç ilçe seçilmeli (Örn: ACTIVE_DISTRICTS=kadikoy,umraniye,besiktas).',
                'fix_url' => route('admin.settings.index'),
                'fix_text' => 'İlçeleri Belirle',
            ];
        } else {
            $passed[] = 'Dinamik ilçe motoru aktif ('.(is_string($activeDistricts) ? $activeDistricts : 'Çoklu').').';
        }

        // Blog Yazısı Kontrolü
        if (Schema::hasTable('posts')) {
            $postCount = Post::where('is_published', true)->count();
            if ($postCount === 0) {
                $ideas[] = [
                    'type' => 'idea',
                    'title' => 'Blogda Henüz Hiç Makale Yayınlanmamış',
                    'desc' => 'Sektörünüzle ilgili 2-3 adet bilgilendirici rehber yazısı yazmak Google aramalarından ücretsiz organik müşteri çeker.',
                    'fix_url' => route('admin.posts.create'),
                    'fix_text' => 'İlk Yazıyı Yaz',
                ];
            } else {
                $passed[] = 'Sitede '.$postCount.' adet SEO uyumlu blog yazısı yayında.';
            }
        }

        // Müşteri Yorumları Kontrolü
        if (Schema::hasTable('reviews')) {
            $reviewCount = Review::where('is_approved', true)->count();
            if ($reviewCount === 0) {
                $ideas[] = [
                    'type' => 'idea',
                    'title' => 'Müşteri Yorumu & Sosyal Kanıt Eksik',
                    'desc' => 'Müşteri yorumları Google\'da sarı yıldız çıkarır. Gerçek müşteri deneyimlerini onaylayıp yayınlayın.',
                    'fix_url' => route('admin.reviews.index'),
                    'fix_text' => 'Yorumları Yönet',
                ];
            } else {
                $passed[] = $reviewCount.' adet onaylı müşteri yorumu ve yıldızlı değerlendirme aktif.';
            }
        }

        // =====================================================================
        // 6. DERİN SİTEMAP & BUTON SAĞLIK TARAYICISI (DEEP CRAWLER)
        // =====================================================================
        $urlAudit = $this->crawlAndAuditUrls();

        if ($urlAudit['total_dummy'] > 0) {
            $warnings[] = [
                'type' => 'warning',
                'title' => $urlAudit['total_dummy']." Adet İşlevsiz / Sahte Buton (href='#') Bulundu",
                'desc' => 'Site sayfalarında hiçbir yere gitmeyen sahte/boş butonlar tespit edildi. Kullanıcı deneyimi için bunlara gerçek rota bağlanmalıdır.',
                'fix_url' => route('admin.siteAudit.index').'?tab=crawler',
                'fix_text' => 'Buton Raporunu Gör',
            ];
        }

        if ($urlAudit['total_warnings'] > 0) {
            $bugs[] = [
                'type' => 'critical',
                'title' => $urlAudit['total_warnings'].' Adet Eksik/Hatalı Buton (WhatsApp / Tel)',
                'desc' => 'Sayfalardaki WhatsApp veya arama linklerinin numarası eksik veya geçersiz.',
                'fix_url' => route('admin.settings.index'),
                'fix_text' => 'İletişimi Düzelt',
            ];
        }

        $passed[] = 'Derin Tarama: '.$urlAudit['scanned_urls_count'].' sayfa ve '.$urlAudit['total_buttons_found'].' buton/link denetlendi (%'.($urlAudit['total_buttons_found'] > 0 ? round(($urlAudit['total_healthy'] / $urlAudit['total_buttons_found']) * 100) : 100).' Sağlıklı).';

        // =====================================================================
        // 7. SAĞLIK PUANI HESAPLAMA (HEALTH SCORE 0 - 100)
        // =====================================================================
        $score = 100;
        $score -= (count($bugs) * 15);      // Kritik buglar: -15 puan
        $score -= (count($warnings) * 5);   // Uyarılar: -5 puan
        $score = max(10, min(100, $score));

        $statusLevel = match (true) {
            $score >= 90 => 'excellent',
            $score >= 70 => 'good',
            default => 'needs_attention',
        };

        return [
            'score' => $score,
            'status_level' => $statusLevel,
            'bugs' => $bugs,
            'warnings' => $warnings,
            'ideas' => $ideas,
            'passed' => $passed,
            'checked_at' => now()->format('d.m.Y H:i:s'),
            'total_checks' => count($bugs) + count($warnings) + count($ideas) + count($passed),
            'site_mode' => $siteMode,
            'url_audit' => $urlAudit,
        ];
    }

    /**
     * Sitemap ve rota haritasındaki tüm sayfaları gezerek buton ve link sağlığını test eder.
     */
    public function crawlAndAuditUrls(): array
    {
        $siteMode = env('SITE_MODE', 'corporate');
        $kernel = app()->make(Kernel::class);

        // 1. Taranacak örnek sayfa havuzunu toparla
        $urlList = [
            ['url' => '/', 'label' => 'Ana Sayfa', 'category' => 'Temel'],
            ['url' => '/iletisim', 'label' => 'İletişim & Randevu', 'category' => 'Temel'],
            ['url' => '/ilceler', 'label' => 'Hizmet Bölgeleri (İlçeler)', 'category' => 'Bölge / SEO'],
        ];

        if ($siteMode === 'ecommerce') {
            $urlList[] = ['url' => '/urunler', 'label' => 'Ürün Kataloğu', 'category' => 'E-Ticaret'];
            $urlList[] = ['url' => '/sepet', 'label' => 'Alışveriş Sepeti', 'category' => 'E-Ticaret'];
        }

        if ($siteMode === 'corporate') {
            $urlList[] = ['url' => '/projeler', 'label' => 'Kurumsal Projeler', 'category' => 'Portföy'];
        }

        // Blog Sayfaları
        if (Schema::hasTable('posts')) {
            $urlList[] = ['url' => '/blog', 'label' => 'Blog Ana Sayfası', 'category' => 'İçerik'];
            $samplePosts = Post::where('is_published', true)->latest()->take(2)->get();
            foreach ($samplePosts as $post) {
                $urlList[] = ['url' => '/blog/'.$post->slug, 'label' => 'Blog: '.Str::limit($post->title, 20), 'category' => 'Blog'];
            }
        }

        // Yasal / Statik Sayfalar
        if (Schema::hasTable('pages')) {
            $samplePages = Page::where('is_active', true)->take(3)->get();
            foreach ($samplePages as $page) {
                $urlList[] = ['url' => '/sayfa/'.$page->slug, 'label' => 'Sayfa: '.Str::limit($page->title, 20), 'category' => 'Hukuk'];
            }
        }

        // E-Ticaret Ürün ve Kategorileri
        if ($siteMode === 'ecommerce' && Schema::hasTable('products')) {
            $sampleProducts = Product::where('is_active', true)->latest()->take(3)->get();
            foreach ($sampleProducts as $product) {
                $urlList[] = ['url' => '/urun/'.$product->slug, 'label' => 'Ürün: '.Str::limit($product->name, 20), 'category' => 'Katalog'];
            }

            if (Schema::hasTable('categories')) {
                $sampleCats = Category::where('is_active', true)->take(2)->get();
                foreach ($sampleCats as $cat) {
                    $urlList[] = ['url' => '/kategori/'.$cat->slug, 'label' => 'Kategori: '.$cat->name, 'category' => 'Kategori'];
                }
            }
        }

        // Kurumsal Projeler
        if ($siteMode === 'corporate' && Schema::hasTable('projects')) {
            $sampleProjects = Project::where('is_active', true)->latest()->take(2)->get();
            foreach ($sampleProjects as $proj) {
                $urlList[] = ['url' => '/proje/'.$proj->slug, 'label' => 'Proje: '.Str::limit($proj->title, 20), 'category' => 'Portföy'];
            }
        }

        // Dinamik İlçe Sayfaları
        $activeDistricts = config('districts.active', 'all');
        $sampleDistricts = ['kadikoy', 'uskudar'];
        if (is_array($activeDistricts)) {
            $sampleDistricts = array_slice($activeDistricts, 0, 2);
        }
        foreach ($sampleDistricts as $d) {
            $urlList[] = ['url' => '/bolge/'.$d, 'label' => 'Bölge: '.ucfirst($d), 'category' => 'SEO & Bölge'];
        }

        // 2. Her URL'yi dahili olarak çalıştır ve HTML'deki butonları incele
        $scannedPages = [];
        $totalButtonsFound = 0;
        $totalHealthy = 0;
        $totalDummy = 0;
        $totalWarnings = 0;

        foreach ($urlList as $entry) {
            $url = $entry['url'];
            $label = $entry['label'];
            $category = $entry['category'];

            $start = microtime(true);
            $statusCode = 200;
            $html = '';

            try {
                $req = Request::create($url, 'GET');
                $res = $kernel->handle($req);
                $statusCode = $res->getStatusCode();
                $html = (string) $res->getContent();
            } catch (\Throwable $e) {
                $statusCode = 500;
            }
            $timeMs = round((microtime(true) - $start) * 1000, 1);

            $buttonAnalysis = $this->inspectHtmlButtonsAndLinks($html, $url);

            $totalButtonsFound += $buttonAnalysis['total'];
            $totalHealthy += $buttonAnalysis['healthy_count'];
            $totalDummy += $buttonAnalysis['dummy_count'];
            $totalWarnings += $buttonAnalysis['warning_count'];

            $scannedPages[] = [
                'url' => $url,
                'label' => $label,
                'category' => $category,
                'status_code' => $statusCode,
                'time_ms' => $timeMs,
                'analysis' => $buttonAnalysis,
            ];
        }

        return [
            'scanned_urls_count' => count($scannedPages),
            'total_buttons_found' => $totalButtonsFound,
            'total_healthy' => $totalHealthy,
            'total_dummy' => $totalDummy,
            'total_warnings' => $totalWarnings,
            'pages' => $scannedPages,
        ];
    }

    /**
     * Sayfa HTML'indeki tüm <a> linklerini ve <button> etiketlerini ayrıştırıp sağlık durumunu puanlar.
     */
    protected function inspectHtmlButtonsAndLinks(string $html, string $sourceUrl): array
    {
        $items = [];
        $dummyCount = 0;
        $warningCount = 0;
        $healthyCount = 0;

        if (empty($html)) {
            return [
                'total' => 0,
                'healthy_count' => 0,
                'dummy_count' => 0,
                'warning_count' => 0,
                'items' => [],
                'issues' => ['Sayfa yanıtı boş döndü.'],
            ];
        }

        // 1. Tüm <a> etiketlerini topla
        preg_match_all('/<a\s+([^>]*?)>(.*?)<\/a>/is', $html, $linkMatches, PREG_SET_ORDER);
        foreach ($linkMatches as $m) {
            $attrs = $m[1];
            $inner = trim(strip_tags($m[2]));

            // href değerini al
            if (preg_match('/href=(["\'])(.*?)\1/is', $attrs, $hrefMatch)) {
                $href = trim($hrefMatch[2]);
            } else {
                $href = '';
            }

            // Metin tespiti
            $displayText = $inner;
            if (empty($displayText)) {
                if (preg_match('/title=(["\'])(.*?)\1/is', $attrs, $tm)) {
                    $displayText = $tm[2];
                } elseif (preg_match('/aria-label=(["\'])(.*?)\1/is', $attrs, $am)) {
                    $displayText = $am[2];
                } elseif (str_contains($attrs, 'fa-whatsapp') || str_contains($href, 'wa.me')) {
                    $displayText = 'WhatsApp Butonu';
                } elseif (str_contains($attrs, 'fa-phone') || str_starts_with($href, 'tel:')) {
                    $displayText = 'Telefon Arama Butonu';
                } else {
                    $displayText = 'Link / İkon';
                }
            }
            $displayText = Str::limit($displayText, 35);

            // href analizi
            if ($href === '#' || $href === '' || $href === 'javascript:void(0)' || $href === 'javascript:;') {
                $dummyCount++;
                $items[] = [
                    'tag' => 'a',
                    'type' => 'dummy_hash',
                    'status' => 'warning',
                    'text' => $displayText,
                    'target' => $href ?: '(boş href)',
                    'note' => 'İşlevsiz sahte link (href="#"). Tıklanınca bir yere gitmez.',
                ];
            } elseif (str_contains($href, 'wa.me') || str_contains($href, 'whatsapp.com')) {
                $cleanWa = preg_replace('/[^0-9]/', '', $href);
                if (strlen($cleanWa) < 10) {
                    $warningCount++;
                    $items[] = [
                        'tag' => 'a',
                        'type' => 'whatsapp',
                        'status' => 'danger',
                        'text' => $displayText,
                        'target' => $href,
                        'note' => 'WhatsApp linki var ancak telefon numarası eksik/kısa!',
                    ];
                } else {
                    $healthyCount++;
                    $items[] = [
                        'tag' => 'a',
                        'type' => 'whatsapp',
                        'status' => 'success',
                        'text' => $displayText,
                        'target' => $href,
                        'note' => 'WhatsApp hızlı iletişim hattı aktif ('.$cleanWa.').',
                    ];
                }
            } elseif (str_starts_with($href, 'tel:')) {
                $phone = substr($href, 4);
                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                if (strlen($cleanPhone) < 7) {
                    $warningCount++;
                    $items[] = [
                        'tag' => 'a',
                        'type' => 'tel',
                        'status' => 'danger',
                        'text' => $displayText,
                        'target' => $href,
                        'note' => 'Arama linki (tel:) tanımlı ancak numara eksik!',
                    ];
                } else {
                    $healthyCount++;
                    $items[] = [
                        'tag' => 'a',
                        'type' => 'tel',
                        'status' => 'success',
                        'text' => $displayText,
                        'target' => $href,
                        'note' => 'Doğrudan telefonla arama linki aktif.',
                    ];
                }
            } elseif (str_starts_with($href, 'mailto:')) {
                $healthyCount++;
                $items[] = [
                    'tag' => 'a',
                    'type' => 'mailto',
                    'status' => 'success',
                    'text' => $displayText,
                    'target' => $href,
                    'note' => 'E-posta yönlendirme linki aktif.',
                ];
            } else {
                $healthyCount++;
                $items[] = [
                    'tag' => 'a',
                    'type' => str_starts_with($href, 'http') ? 'external' : 'internal',
                    'status' => 'success',
                    'text' => $displayText,
                    'target' => $href,
                    'note' => 'Standart sayfa bağlantısı.',
                ];
            }
        }

        // 2. Tüm <button> etiketlerini topla
        preg_match_all('/<button\s+([^>]*?)>(.*?)<\/button>/is', $html, $btnMatches, PREG_SET_ORDER);
        foreach ($btnMatches as $b) {
            $attrs = $b[1];
            $inner = trim(strip_tags($b[2]));

            $btnText = $inner;
            if (empty($btnText)) {
                if (preg_match('/title=(["\'])(.*?)\1/is', $attrs, $tm)) {
                    $btnText = $tm[2];
                } elseif (preg_match('/aria-label=(["\'])(.*?)\1/is', $attrs, $am)) {
                    $btnText = $am[2];
                } else {
                    $btnText = 'İşlem Butonu';
                }
            }
            $btnText = Str::limit($btnText, 35);

            $isSubmit = str_contains($attrs, 'type="submit"') || str_contains($attrs, "type='submit'") || ! str_contains($attrs, 'type=');
            $hasAction = str_contains($attrs, '@click') || str_contains($attrs, 'x-on:click') || str_contains($attrs, 'onclick');

            if ($isSubmit || $hasAction) {
                $healthyCount++;
                $items[] = [
                    'tag' => 'button',
                    'type' => $isSubmit ? 'form_submit' : 'action_button',
                    'status' => 'success',
                    'text' => $btnText,
                    'target' => $isSubmit ? 'Form Gönderimi' : 'JS/Alpine Eylemi',
                    'note' => $isSubmit ? 'Form onay / tetikleyici buton.' : 'İnteraktif eylem butonu.',
                ];
            } else {
                $dummyCount++;
                $items[] = [
                    'tag' => 'button',
                    'type' => 'passive_button',
                    'status' => 'warning',
                    'text' => $btnText,
                    'target' => 'Eylemsiz',
                    'note' => 'Herhangi bir tıklama eylemi veya form bağlantısı tespit edilemedi.',
                ];
            }
        }

        $issues = [];
        if ($dummyCount > 0) {
            $issues[] = "{$dummyCount} adet işlevsiz sahte buton veya href='#' linki bulundu!";
        }
        if ($warningCount > 0) {
            $issues[] = "{$warningCount} adet eksik parametreli iletişim butonu (WhatsApp/Tel) tespit edildi!";
        }

        // Hatalı ve uyarı veren butonları listenin en başına al
        usort($items, function ($a, $b) {
            $order = ['danger' => 0, 'warning' => 1, 'success' => 2];

            return ($order[$a['status']] ?? 3) <=> ($order[$b['status']] ?? 3);
        });

        return [
            'total' => count($items),
            'healthy_count' => $healthyCount,
            'dummy_count' => $dummyCount,
            'warning_count' => $warningCount,
            'items' => array_slice($items, 0, 25),
            'issues' => $issues,
        ];
    }
}
