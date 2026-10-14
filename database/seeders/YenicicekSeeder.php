<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Gift;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Region;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class YenicicekSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Yönetici Hesabı
        $admin = User::where('email', 'admin@yenicicek.com')
            ->orWhere('email', 'admin@tazecicekler.com')
            ->first();

        if ($admin) {
            $admin->update([
                'name' => 'Taze Çiçekler Yönetici',
                'email' => 'admin@tazecicekler.com',
                'password' => Hash::make('cicek123456'),
                'is_admin' => true,
                'role' => 'superadmin',
                'phone' => '05550001122',
            ]);
        } else {
            User::create([
                'name' => 'Taze Çiçekler Yönetici',
                'email' => 'admin@tazecicekler.com',
                'password' => Hash::make('cicek123456'),
                'is_admin' => true,
                'role' => 'superadmin',
                'phone' => '05550001122',
            ]);
        }

        // 3. Genel Site & Çiçekçi Ayarları
        $settings = [
            'site_title' => 'Tazecicekler.com — Aynı Gün Taze Çiçek & Hediye Siparişi',
            'site_slogan' => 'Aynı Gün 2 Saatte Teslimat & Fotoğraflı Çiçek Güvencesi',
            'company_name' => 'Taze Çiçekler Çiçekçilik A.Ş.',
            'phone' => '0850 300 34 34',
            'whatsapp_number' => '0501 379 82 36',
            'contact_email' => 'destek@tazecicekler.com',
            'address' => 'Bağdat Caddesi No: 180 Kadıköy / İstanbul',
            'working_hours' => 'Hergün 08:00 - 23:00',
            'ai_provider' => 'gemini',
            'ai_model' => 'gemini-3.6-flash',
            'ai_system_prompt' => "Sen Tazecicekler.com'un uzman, zarif, romantik ve kalbe dokunan duygu danışmanısın. Müşterilere çiçek seçiminde ve kart notu yazımında yardımcı olursun.",
            'paytr_active' => '0',
            'wheel_active' => '1',
            'quiz_active' => '1',
            'flash_deal_active' => '1',
            'flash_deal_title' => 'Günün Çiçek Fırsatı: 101 İthal Kırmızı Gül Buketinde Özel İndirim!',
            'flash_deal_subtitle' => 'Saat 18:00\'e kadar verilen siparişlerde aynı gün ücretsiz kurye ve özel kart notu hediye.',
            'express_courier_active' => '1',
            'express_courier_fee' => '150',
            'express_courier_title' => 'VIP Hızlı Kurye (60-90 Dk)',
            'express_courier_subtitle' => 'Öncelikli atölye hazırlığı & ekspres motorlu kurye',
        ];

        foreach ($settings as $k => $v) {
            Setting::updateOrCreate(['key' => $k], ['value' => $v]);
        }

        // 4. Çiçek Kategorileri
        $categories = [
            [
                'name' => 'Gül Buketleri',
                'slug' => 'gul-buketleri',
                'description' => 'Aşkın ve tutkunun simgesi taze kırmızı, beyaz ve pembe ithal gül buketleri.',
                'image_path' => null,
            ],
            [
                'name' => 'Orkide & Saksı Çiçekleri',
                'slug' => 'orkide-ve-saksi-cicekleri',
                'description' => 'Zarafetin simgesi çift dallı kraliyet orkideleri, bonsai ve dayanıklı iç mekan yeşil bitkileri.',
                'image_path' => null,
            ],
            [
                'name' => 'Vazoda & Kutuda Tasarımlar',
                'slug' => 'vazoda-ve-kutuda-tasarimlar',
                'description' => 'Şık cam vazolarda ve kadife kutularda özenle hazırlanmış lüks çiçek aranjmanları.',
                'image_path' => null,
            ],
            [
                'name' => 'Mevsim Çiçekleri & Lilyum',
                'slug' => 'mevsim-cicekleri-ve-lilyum',
                'description' => 'Mis kokulu taze lilyumlar, krizantemler, frezyalar ve rengarenk kır çiçekleri.',
                'image_path' => null,
            ],
            [
                'name' => 'Açılış, Düğün & Çelenk',
                'slug' => 'acilis-dugun-ve-celenk',
                'description' => 'İş yeri açılışı, düğün, nikah ve kurumsal kutlamalar için görkemli ayaklı sepet ve çelenkler.',
                'image_path' => null,
            ],
            [
                'name' => 'Çikolata & Hediye Eşlikçileri',
                'slug' => 'cikolata-ve-hediye-eslikcileri',
                'description' => 'Çiçeğinizin yanına ekleyebileceğiniz peluş ayıcıklar, spesiyal çikolatalar ve balonlar.',
                'image_path' => null,
            ],
            [
                'name' => 'Doğum Günü Çiçekleri',
                'slug' => 'dogum-gunu-cicekleri',
                'description' => 'Sevdiklerinizin yeni yaşını kutlamak için neşeli, renkli ve taptaze doğum günü aranjmanları.',
                'image_path' => null,
            ],
            [
                'name' => 'Yıldönümü & Sevgiliye',
                'slug' => 'yildonumu-ve-sevgiliye',
                'description' => 'Kalpten gelen sevgiyi anlatmanın en romantik yolu: Kırmızı güller ve özel tasarım kutular.',
                'image_path' => null,
            ],
        ];

        $catModels = [];
        foreach ($categories as $catData) {
            $catModels[$catData['slug']] = Category::updateOrCreate(
                ['slug' => $catData['slug']],
                [
                    'name' => $catData['name'],
                    'description' => $catData['description'],
                    'image_path' => $catData['image_path'],
                    'is_active' => true,
                ]
            );
        }

        // 5. Çiçek Ürünleri
        $products = [
            [
                'category_slug' => 'gul-buketleri',
                'name' => '101 Kırmızı Gül Masalı — Lüks İthal Gül Buketi',
                'slug' => '101-kirmizi-gul-masali-luks-ithal-gul-buketi',
                'price' => 3499.00,
                'campaign_price' => 2899.00,
                'image_path' => null,
                'short_description' => '101 adet birinci sınıf taze Ekvador kırmızı gülü ile sevdiğinize unutamayacağı bir aşk sürprizi yapın. Aynı gün özel kurye ile adrese teslim.',
                'description' => '<p>Aşkın en görkemli ifadesi olan <strong>101 Kırmızı Gül Buketi</strong>, en taze ithal gonca güllerle usta çiçek tasarımcılarımız tarafından elde hazırlanmaktadır.</p><h3>🌸 Buket Özellikleri</h3><ul><li>101 Adet canlı taze kırmızı Ekvador gülü</li><li>Lüks siyah & pudra özel buket kağıdı ve saten kurdele</li><li>Özel besin tozu ile 10 güne kadar canlılık garantisi</li><li>Ücretsiz mühürlü zarfta duygusal kart mesajı</li></ul><p>İstanbul geneli 39 ilçeye aynı gün teslimat yapılmaktadır.</p>',
                'technical_features' => "• Gül Sayısı: 101 Adet Orijinal Taze Gül\n• Buket Çapı: 65 - 75 cm\n• Ambalaj: Çift Kat Lüks Mat Kağıt & Saten Kurdele\n• Teslimat: Aynı Gün 2 Saatte Özel Çiçek Kuryesi\n• Canlılık: 7-10 Gün Vazoda Tazelik Güvencesi",
                'custom_badge' => 'AYNI GÜN TESLİMAT',
                'has_gift' => true,
                'gallery' => [],
            ],
            [
                'category_slug' => 'gul-buketleri',
                'name' => 'Aşkın Zarafeti — 25 Kırmızı Gül Buketi',
                'slug' => 'askin-zarafeti-25-kirmizi-gul-buketi',
                'price' => 1290.00,
                'campaign_price' => 990.00,
                'image_path' => null,
                'short_description' => '25 adet kadife dokulu kırmızı gül ve okaliptus yapraklarıyla süslenmiş romantik ve zarif tasarım buket.',
                'description' => '<p>Doğum günü, yıldönümü ve romantik kutlamalar için en popüler buketimiz olan <strong>Aşkın Zarafeti</strong>, taptaze güllerin okaliptus dallarıyla harmanlanmasıyla hazırlanır.</p>',
                'technical_features' => "• Gül Sayısı: 25 Adet\n• Boyut: Yaklaşık 50 cm yükseklik\n• Aksesuar: Doğal Okaliptus & Gypsophila (Cipso)\n• Teslimat: Aynı Gün Seçilen Saat Diliminde",
                'custom_badge' => 'ÇOK SATAN',
                'has_gift' => true,
                'gallery' => [],
            ],
            [
                'category_slug' => 'orkide-ve-saksi-cicekleri',
                'name' => 'Kraliyet Zarafeti — 2 Dallı Beyaz Phalaenopsis Orkide',
                'slug' => 'kraliyet-zarafeti-2-dalli-beyaz-phalaenopsis-orkide',
                'price' => 1450.00,
                'campaign_price' => 1190.00,
                'image_path' => null,
                'short_description' => 'Özel dekoratif seramik saksısında, bol tomurcuklu ve uzun ömürlü birinci sınıf çift dallı beyaz orkide.',
                'description' => '<p>Zarafetin, masumiyetin ve saygının simgesi olan beyaz orkide, hem ev hem de ofis ortamları için kusursuz bir hediye seçimidir.</p><h3>🪴 Bakım Bilgisi</h3><p>Haftada 1 kez oda sıcaklığında az su verilmeli, doğrudan yakıcı güneş ışığı yerine aydınlık tül arkası ışık tercih edilmelidir.</p>',
                'technical_features' => "• Dal Sayısı: 2 Gövdeli Bol Çiçekli\n• Saksı: Lüks Beyaz Seramik Saksı\n• Yükseklik: 60 - 70 cm\n• Çiçek Ömrü: 2-3 Ay Boyunca Açan Tomurcuklar",
                'custom_badge' => 'FOTOĞRAFLI ONAY',
                'has_gift' => true,
                'gallery' => [],
            ],
            [
                'category_slug' => 'vazoda-ve-kutuda-tasarimlar',
                'name' => 'Pudra Kadife Kutuda Sonsuz Aşk — Kırmızı & Pembe Güller',
                'slug' => 'pudra-kadife-kutuda-sonsuz-ask',
                'price' => 1750.00,
                'campaign_price' => 1390.00,
                'image_path' => null,
                'short_description' => 'Lüks silindir kadife kutuda özel vaha süngeriyle hazırlanmış, suya ihtiyaç duymadan tazeliğini koruyan eşsiz kutu çiçek tasarımı.',
                'description' => '<p>Şık kadife silindir kutu içinde sunulan bu tasarım, masalara ve komodinlere anında asil bir hava katar. Çiçeğin altındaki özel vaha süngeri güllerin günlerce taze kalmasını sağlar.</p>',
                'technical_features' => "• Kutu: 20x20 cm Lüks Kadife Silindir Kutu\n• Çiçekler: Kırmızı Gül, Pembe Çardak Gül & Kuru Çiçekler\n• Bakım: Vaha süngerine 2 günde bir yarım çay bardağı su ilavesi yeterlidir",
                'custom_badge' => 'ÖZEL TASARIM',
                'has_gift' => true,
                'gallery' => [],
            ],
            [
                'category_slug' => 'vazoda-ve-kutuda-tasarimlar',
                'name' => 'Işıklı Kalp Kutuda Kırmızı Güller — Sonsuz Aşk Serisi',
                'slug' => 'isikli-kalp-kutuda-kirmizi-guller-sonsuz-ask-serisi',
                'price' => 1450.00,
                'campaign_price' => 1190.00,
                'image_path' => null,
                'short_description' => 'Lüks pleksi pencereli kalp kutuda taze kırmızı güller. Özel ışıklandırma detayıyla büyüleyici bir hediye.',
                'description' => '<p>Aşkınızı kalbinizin en derinlerinden gelen bir ışıkla taçlandırın. <strong>Işıklı Kalp Kutu Kırmızı Gül Tasarımı</strong>, romantik anların unutulmaz sürprizidir.</p>',
                'technical_features' => "• Kutu: Pencereli Kalp Lüks Kutu\n• Işık: Dahili Peri LED\n• Boyut: 30x30 cm",
                'custom_badge' => 'IŞIKLI TASARIM',
                'has_gift' => true,
                'gallery' => [],
            ],
            [
                'category_slug' => 'mevsim-cicekleri-ve-lilyum',
                'name' => 'Bahar Meltemi — Beyaz Lilyum & Rengarenk Kır Çiçekleri',
                'slug' => 'bahar-meltemi-beyaz-lilyum-kir-cicekleri',
                'price' => 1100.00,
                'campaign_price' => 890.00,
                'image_path' => null,
                'short_description' => 'Evinizi ve ofisinizi mis gibi kokusuyla saracak taze beyaz lilyumlar, pembe güller ve mevsim yeşillikleri aranjmanı.',
                'description' => '<p>Doğallığı ve baharın tazeliğini sevenler için hazırlanan Bahar Meltemi buketi, enerjisiyle girdiği her ortamı neşelendirir.</p>',
                'technical_features' => "• Çiçekler: Kokulu Beyaz Lilyum, Sarı/Pembe Papatyalar, Lisianthus\n• Sunum: Rustik Jüt Ambalaj & Doğal Rafya\n• Koku: Yoğun Doğal Parfüm Etkisi",
                'custom_badge' => 'MİS KOKULU',
                'has_gift' => true,
                'gallery' => [],
            ],
            [
                'category_slug' => 'orkide-ve-saksi-cicekleri',
                'name' => 'Safir Mavi Phalaenopsis Orkide — Özel Tasarım Saksıda',
                'slug' => 'safir-mavi-phalaenopsis-orkide-ozel-tasarim-saksida',
                'price' => 1250.00,
                'campaign_price' => 990.00,
                'image_path' => null,
                'short_description' => 'Göz alıcı safir mavi tonlarıyla nadir ve büyüleyici ithal phalaenopsis orkide. Özel seramik saksısında şık teslimat.',
                'description' => '<p>Özel pigmentleme teknolojisiyle elde edilen derin safir mavi tonlarındaki orkide.</p>',
                'technical_features' => "• Çiçek Türü: Mavi Phalaenopsis Orkide\n• Dal Sayısı: 2 Dal\n• Saksı: Özel Mat Beyaz Seramik\n• Boyut: 60 - 70 cm Yükseklik",
                'custom_badge' => 'NADİR TÜR',
                'has_gift' => true,
                'gallery' => [],
            ],
            [
                'category_slug' => 'vazoda-ve-kutuda-tasarimlar',
                'name' => 'Lüks Kalp Kutuda Pembe Güller — Zarafet Koleksiyonu',
                'slug' => 'luks-kalp-kutuda-pembe-guller-zarafet-koleksiyonu',
                'price' => 1350.00,
                'campaign_price' => 1090.00,
                'image_path' => null,
                'short_description' => 'Pudra ve bebek pembesi taze ithal güllerle hazırlanmış şık kalp kutu aranjmanı.',
                'description' => '<p>Nezaket, zarafet ve içten sevginin rengi pembe güller, lüks kalpli tasarım kutusunda hayat buluyor.</p>',
                'technical_features' => "• Çiçek: İthal Pembe Güller\n• Kutu: Kalp Formlu Lüks Kutu\n• Boyut: 28x28 cm",
                'custom_badge' => 'AYNI GÜN TESLİMAT',
                'has_gift' => true,
                'gallery' => [],
            ],
            [
                'category_slug' => 'vazoda-ve-kutuda-tasarimlar',
                'name' => 'Cam Fanusta Beyaz Papatya & Kırmızı Gül Aranjmanı',
                'slug' => 'cam-fanusta-beyaz-papatya-kirmizi-gul-aranjmani',
                'price' => 680.00,
                'campaign_price' => 550.00,
                'image_path' => null,
                'short_description' => 'Şeffaf cam fanus içinde taze beyaz papatyalar ve kırmızı güllerin eşsiz uyumu.',
                'description' => '<p>Kristal berraklığındaki cam fanusta hazırlanan bu aranjman masalara ferahlık katar.</p>',
                'technical_features' => "• Vazo: Cam Yuvarlak Fanus\n• İçerik: Beyaz Papatya, Kırmızı Güller\n• Boyut: Çap 20 cm",
                'custom_badge' => 'ÇOK SEVİLEN',
                'has_gift' => false,
                'gallery' => [],
            ],
            [
                'category_slug' => 'mevsim-cicekleri-ve-lilyum',
                'name' => 'Siyah Silindir Kutuda Papatya & Başak Aranjmanı',
                'slug' => 'siyah-silindir-kutuda-papatya-basak-aranjmani',
                'price' => 750.00,
                'campaign_price' => 590.00,
                'image_path' => null,
                'short_description' => 'Modern siyah silindir kutuda saf beyaz papatyalar ve doğal başak detayları.',
                'description' => '<p>Siyahın asaleti ile beyaz papatyaların masumiyetini buluşturan modern aranjman.</p>',
                'technical_features' => "• Kutu: Mat Siyah Silindir Kutu\n• Çiçekler: Beyaz Kır Papatyası & Doğal Başak\n• Çap: 22 cm",
                'custom_badge' => 'FIRSAT ÜRÜNÜ',
                'has_gift' => false,
                'gallery' => [],
            ],
            [
                'category_slug' => 'cikolata-ve-hediye-eslikcileri',
                'name' => 'Sevimli Peluş Ayıcık & Papatya Gül Hediye Aranjmanı',
                'slug' => 'sevimli-pelus-ayicik-papatya-gul-hediye-aranjmani',
                'price' => 850.00,
                'campaign_price' => 690.00,
                'image_path' => null,
                'short_description' => 'Yumuşacık sevimli peluş ayıcık, taze beyaz papatyalar ve kırmızı güllerle tatlı bir sürpriz paketi.',
                'description' => '<p>Tebrik, doğum günü ve özür için en tatlı hediye! Peluş ayıcık ve taze papatyalar sevdiklerinizin kalbini fethedecek.</p>',
                'technical_features' => "• İçerik: 25 cm Peluş Ayıcık, Beyaz Papatya, Kırmızı Gül\n• Sunum: Özel Hediye Kutusu",
                'custom_badge' => 'HEDİYE SETİ',
                'has_gift' => false,
                'gallery' => [],
            ],
            [
                'category_slug' => 'acilis-dugun-ve-celenk',
                'name' => 'Tebrik & Başarı — Ayaklı Görkemli Açılış Sepeti',
                'slug' => 'tebrik-basari-ayakli-gorkemli-acilis-sepeti',
                'price' => 2800.00,
                'campaign_price' => 2400.00,
                'image_path' => null,
                'short_description' => 'Yeni iş yeri açılışı, nikah, düğün ve kongreler için kurumsal firma isminizin yazılı olduğu görkemli ayaklı canlı çiçek sepeti.',
                'description' => '<p>Kurumsal kutlamalarda firmanızı en prestijli şekilde temsil edecek dev ayaklı çiçek aranjmanı.</p>',
                'technical_features' => "• Yükseklik: 180 cm Ayaklı Ferforje Sistem\n• Çiçekler: Gerbera, Glayöl, Lilyum ve Canlı Mevsim Çiçekleri\n• Şerit: İpek Kuşak Üzerine Firma İsmi Baskısı",
                'custom_badge' => 'AÇILIŞ & DÜĞÜN',
                'has_gift' => false,
                'gallery' => [],
            ],
            [
                'category_slug' => 'cikolata-ve-hediye-eslikcileri',
                'name' => 'Lüks Spesiyal Kalpli Belçika Çikolatası',
                'slug' => 'luks-spesiyal-kalpli-belcika-cikolatasi',
                'price' => 299.00,
                'campaign_price' => 199.00,
                'image_path' => null,
                'short_description' => 'Çiçeğinizin yanında taze teslim edilen 16 adet spesiyal el yapımı Belçika çikolatası.',
                'description' => '<p>Sevdiklerinizin gününü tatlandıracak lüks kalpli kutuda Belçika spesiyal çikolataları.</p>',
                'technical_features' => "• İçerik: 16 Adet Spesiyal Çikolata\n• Gramaj: 250 gr\n• Sunum: Lüks Kadife Kutu",
                'custom_badge' => 'TATLI SÜRPRİZ',
                'has_gift' => false,
            ],
            [
                'category_slug' => 'cikolata-ve-hediye-eslikcileri',
                'name' => 'Kırmızı Kalpli Sevimli Peluş Ayıcık (25 cm)',
                'slug' => 'kirmizi-kalpli-sevimli-pelus-ayicik',
                'price' => 350.00,
                'campaign_price' => 249.00,
                'image_path' => null,
                'short_description' => 'Çiçeğinize sarılarak teslim edilen yumuşacık sevimli peluş ayıcık.',
                'description' => '<p>Kırmızı kalbiyle sevgiyi anlatan peluş ayıcık çiçeğinizin en tatlı eşlikçisi olacak.</p>',
                'technical_features' => "• Boyut: 25 cm\n• Malzeme: Birinci Sınıf Anti-Alerjik Peluş",
                'custom_badge' => 'ÇOK SEVİLEN',
                'has_gift' => false,
            ],
            [
                'category_slug' => 'cikolata-ve-hediye-eslikcileri',
                'name' => 'Uçan Kalp Helyum Balon — Seni Seviyorum',
                'slug' => 'ucan-kalp-helyum-balon',
                'price' => 220.00,
                'campaign_price' => 149.00,
                'image_path' => null,
                'short_description' => 'Buketinizin sapına bağlanan parlak kırmızı uçan helyum balon.',
                'description' => '<p>Görsel bir şölen sunan kırmızı parlak folyo kalp balon.</p>',
                'technical_features' => "• Çap: 45 cm Folyo Balon\n• Gaz: Yanmaz Saf Helyum",
                'custom_badge' => 'ROMANTİK',
                'has_gift' => false,
            ],
            [
                'category_slug' => 'cikolata-ve-hediye-eslikcileri',
                'name' => 'Paşabahçe Kristal Silindir Cam Vazo',
                'slug' => 'pasabahce-kristal-silindir-cam-vazo',
                'price' => 180.00,
                'campaign_price' => 120.00,
                'image_path' => null,
                'short_description' => 'Buketinizin hemen suya konulabilmesi için kristal şeffaf kalın cam vazo.',
                'description' => '<p>Çiçeklerinizin ilk andan itibaren tazeliğini koruması için şık cam vazo.</p>',
                'technical_features' => "• Yükseklik: 25 cm\n• Malzeme: Kalın Paşabahçe Cam",
                'custom_badge' => 'KULLANIŞLI',
                'has_gift' => false,
            ],
        ];

        foreach ($products as $pData) {
            $cat = $catModels[$pData['category_slug']] ?? null;
            $gallery = $pData['gallery'] ?? [];
            unset($pData['category_slug'], $pData['gallery']);

            $product = Product::updateOrCreate(
                ['slug' => $pData['slug']],
                array_merge($pData, [
                    'category_id' => $cat ? $cat->id : 1,
                    'in_stock' => true,
                    'is_active' => true,
                    'has_free_installation' => false,
                    'has_cash_on_delivery' => true,
                    'cost_price' => ($pData['campaign_price'] ?? $pData['price']) * 0.45,
                    'seo_title' => "{$pData['name']} — Tazecicekler.com",
                    'seo_description' => Str::limit($pData['short_description'], 150),
                ])
            );

            if (!empty($gallery)) {
                foreach ($gallery as $idx => $galImg) {
                    ProductImage::updateOrCreate(
                        ['product_id' => $product->id, 'image_path' => $galImg],
                        ['sort_order' => $idx + 1]
                    );
                }
            }
        }

        // 6. Çiçek Yanı Eklenebilir Çapraz Satış (Upsell) Ürünleri
        $gifts = [
            [
                'name' => 'Lüks Spesiyal Kalpli Çikolata Kutusu',
                'badge' => 'Tatlı Sürpriz',
                'category' => 'upsell',
                'description' => 'Çiçeğinizin yanında teslim edilecek taze Belçika spesiyal çikolata kutusu.',
                'price' => 199.00,
                'market_price' => 299.00,
                'icon' => 'fas fa-cookie-bite',
                'color' => 'pink',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Sevimli Yumuşak Peluş Ayıcık (25 cm)',
                'badge' => 'En Çok Tercih Edilen',
                'category' => 'upsell',
                'description' => 'Kırmızı kalpli, yumuşacık sevimli peluş ayıcık.',
                'price' => 249.00,
                'market_price' => 350.00,
                'icon' => 'fas fa-heart',
                'color' => 'rose',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Kalp Formlu Uçan Helyum Balon',
                'badge' => 'Romantik Dokunuş',
                'category' => 'upsell',
                'description' => 'Kırmızı renkli, parıltılı "Seni Seviyorum" yazılı uçan kalp balon.',
                'price' => 149.00,
                'market_price' => 220.00,
                'icon' => 'fas fa-heart',
                'color' => 'rose',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Şık Paşabahçe Silindir Cam Vazo',
                'badge' => 'Kullanışlı',
                'category' => 'upsell',
                'description' => 'Buketinizin hemen suya konulabilmesi için kristal şeffaf cam vazo.',
                'price' => 120.00,
                'market_price' => 180.00,
                'icon' => 'fas fa-wine-glass-alt',
                'color' => 'emerald',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($gifts as $gData) {
            Gift::updateOrCreate(
                ['name' => $gData['name']],
                $gData
            );
        }

        // 7. İndirim Kuponları
        Coupon::updateOrCreate(
            ['code' => 'YENI10'],
            [
                'type' => 'percent',
                'value' => 10,
                'min_order_amount' => 500,
                'is_active' => true,
            ]
        );

        Coupon::updateOrCreate(
            ['code' => 'CICEK50'],
            [
                'type' => 'fixed',
                'value' => 50,
                'min_order_amount' => 600,
                'is_active' => true,
            ]
        );
    }
}
