<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brandsData = [
            [
                'name' => 'A.O. Smith',
                'search_query' => 'ao smith, a.o. smith, aosmith, smith',
                'description' => 'A.O. Smith su arıtma sistemleri ve orijinal uyumlu yedek filtre setleri.',
                'children' => [
                    [
                        'name' => 'Daisy R.O.',
                        'search_query' => 'daisy, daisy ro, daisy r.o.',
                        'description' => 'Ters ozmoz (RO) teknolojili A.O. Smith Daisy modeli filtre ve bakım ekipmanları.',
                    ],
                    [
                        'name' => 'DF 600',
                        'search_query' => 'df 600, df600',
                        'description' => 'Tanksız ve direkt akışlı A.O. Smith DF 600 modeli yüksek debili arıtma sistemi.',
                    ],
                    [
                        'name' => 'Frezya',
                        'search_query' => 'frezya',
                        'description' => 'A.O. Smith Frezya kabinli su arıtma cihazı uyumlu filtreleri.',
                    ],
                ],
            ],
            [
                'name' => 'Aqua Bella',
                'search_query' => 'aqua bella, aquabella',
                'description' => 'Aqua Bella evsel ve kuyu tipi su arıtma cihazları.',
                'children' => [
                    [
                        'name' => 'Açık Sistem Slim',
                        'search_query' => 'acik sistem slim, slim kasa',
                        'description' => 'Açık kasa slim şasili Aqua Bella su arıtma cihazı filtre takımı.',
                    ],
                    [
                        'name' => 'BDR-ACK Pompalı Seri',
                        'search_query' => 'bdr ack, bdr-ack, pompali seri',
                        'description' => 'Açık kasa pompalı Aqua Bella BDR-ACK serisi su arıtma cihazı.',
                    ],
                    [
                        'name' => 'BDR-ACK UV Kuyu Tipi',
                        'search_query' => 'uv kuyu tipi, ultraviyole, bdr ack uv',
                        'description' => 'Ultraviyole (UV) dezenfeksiyonlu açık kasa kuyu suyu arıtma sistemi.',
                    ],
                ],
            ],
            [
                'name' => 'Aquabir',
                'search_query' => 'aquabir, aqua bir',
                'description' => 'Aquabir su arıtma sistemleri.',
                'children' => [
                    [
                        'name' => 'Açık Kasa Pompalı',
                        'search_query' => 'aquabir acik kasa, aquabir pompali',
                        'description' => 'Aquabir açık kasa pompalı klasik su arıtma cihazı filtre seti.',
                    ],
                ],
            ],
            [
                'name' => 'Aquatürk',
                'search_query' => 'aquaturk, aqua turk, aquatürk',
                'description' => 'Aquatürk yerli üretim açık ve kapalı kasa su arıtma cihazları.',
                'children' => [
                    [
                        'name' => 'Ecoplus Standart',
                        'search_query' => 'ecoplus, ecoplus standart',
                        'description' => 'Aquatürk Ecoplus açık kasa standart arıtma cihazı filtreleri.',
                    ],
                    [
                        'name' => 'Ecoplus Pro / Quick',
                        'search_query' => 'ecoplus pro, ecoplus quick',
                        'description' => 'Aquatürk Ecoplus Pro ve Quick açık kasa su arıtma sistemi.',
                    ],
                    [
                        'name' => 'Aquabest',
                        'search_query' => 'aquabest, aqua best',
                        'description' => 'Aquatürk Aquabest kapalı kasa kompakt su arıtma cihazı.',
                    ],
                    [
                        'name' => 'Safir / Stratos',
                        'search_query' => 'safir, stratos, safir stratos',
                        'description' => 'Aquatürk Safir ve Stratos kapalı kabinli su arıtma cihazı filtre seti.',
                    ],
                ],
            ],
            [
                'name' => 'Arçelik',
                'search_query' => 'arcelik, arçelik',
                'description' => 'Arçelik ev tipi kapalı kasa ve sebilli su arıtma cihazları.',
                'children' => [
                    [
                        'name' => '3140 SFC',
                        'search_query' => '3140 sfc, 3140sfc',
                        'description' => 'Arçelik 3140 SFC kapalı kasa tezgah altı su arıtma cihazı.',
                    ],
                    [
                        'name' => '6300 SFC',
                        'search_query' => '6300 sfc, 6300sfc',
                        'description' => 'Arçelik 6300 SFC sebil / tezgâh üstü opsiyonlu kapalı kasa su arıtma cihazı.',
                    ],
                ],
            ],
            [
                'name' => 'BMB Technology',
                'search_query' => 'bmb, bmb technology, bmb teknoloji',
                'description' => 'BMB Technology açık kasa ve nano filtreli su arıtma sistemleri.',
                'children' => [
                    [
                        'name' => 'BMB RO-10 Açık Kasa',
                        'search_query' => 'bmb ro 10, bmb ro-10, ro10',
                        'description' => 'BMB RO-10 açık kasa ters ozmoz su arıtma cihazı.',
                    ],
                    [
                        'name' => 'HQ Twist Nano',
                        'search_query' => 'hq twist, twist nano, hq twist nano',
                        'description' => 'BMB HQ Twist çevir-tak pratik nano filtreli su arıtma cihazı.',
                    ],
                ],
            ],
            [
                'name' => 'BMS',
                'search_query' => 'bms, bms su aritma',
                'description' => 'BMS su arıtma sistemleri açık ve kapalı kasa modelleri.',
                'children' => [
                    [
                        'name' => 'BMS 101 Şasi',
                        'search_query' => 'bms 101, bms 101 sasi',
                        'description' => 'BMS 101 açık kasa şasili klasik su arıtma cihazı filtreleri.',
                    ],
                    [
                        'name' => 'BMS 102 / Plus 5000',
                        'search_query' => 'bms 102, plus 5000, bms plus',
                        'description' => 'BMS 102 ve Plus 5000 kapalı kasa inline filtreli su arıtma cihazı.',
                    ],
                ],
            ],
            [
                'name' => 'Bosch',
                'search_query' => 'bosch, bosch su aritma',
                'description' => 'Bosch premium tezgah altı kapalı kasa ve direkt akışlı su arıtma sistemleri.',
                'children' => [
                    [
                        'name' => 'WU01A2VW0N',
                        'search_query' => 'wu01a2vw0n, wu01',
                        'description' => 'Bosch WU01A2VW0N kapalı kasa kompakt su arıtma cihazı filtre takımı.',
                    ],
                    [
                        'name' => 'WU11A2AW0N',
                        'search_query' => 'wu11a2aw0n, wu11',
                        'description' => 'Bosch WU11A2AW0N kapalı kasa yüksek performanslı su arıtma cihazı.',
                    ],
                    [
                        'name' => 'WU11ADAW0N',
                        'search_query' => 'wu11adaw0n, tanksiz bosch',
                        'description' => 'Bosch WU11ADAW0N tanksız ve doğrudan akışlı yüksek debili su arıtma cihazı.',
                    ],
                ],
            ],
            [
                'name' => 'Conax',
                'search_query' => 'conax, conax su aritma',
                'description' => 'Conax açık kasa ve kapalı kasa ev tipi su arıtma sistemleri.',
                'children' => [
                    [
                        'name' => 'Favori / Elit Açık Seri',
                        'search_query' => 'conax favori, conax elit, acik seri',
                        'description' => 'Conax Favori ve Elit serisi açık kasa su arıtma cihazı filtreleri.',
                    ],
                    [
                        'name' => 'Gold Açık Kasa',
                        'search_query' => 'conax gold, gold acik kasa',
                        'description' => 'Conax Gold serisi açık kasa su arıtma cihazı.',
                    ],
                    [
                        'name' => 'Class Serisi',
                        'search_query' => 'conax class, class serisi',
                        'description' => 'Conax Class kapalı kasa kompakt su arıtma cihazı filtre seti.',
                    ],
                    [
                        'name' => 'Crypto / Vision',
                        'search_query' => 'conax crypto, conax vision, crypto vision',
                        'description' => 'Conax Crypto ve Vision kapalı kasa dijital göstergeli arıtma cihazı.',
                    ],
                ],
            ],
            [
                'name' => 'Cuckoo',
                'search_query' => 'cuckoo, cuckoo su aritma',
                'description' => 'Cuckoo Kore menşeili inovatif su arıtma cihazları.',
                'children' => [
                    [
                        'name' => 'Alesta Direkt Akış',
                        'search_query' => 'alesta, alesta direkt akis, cuckoo alesta',
                        'description' => 'Cuckoo Alesta tanksız ve kapalı kasa doğrudan akışlı su arıtma cihazı.',
                    ],
                ],
            ],
            [
                'name' => 'Dreo',
                'search_query' => 'dreo, dreo su aritma',
                'description' => 'Dreo akıllı ve portatif su arıtma sistemleri.',
                'children' => [
                    [
                        'name' => 'RO 115 Tezgâh Üstü',
                        'search_query' => 'dreo ro 115, ro115, tezgah ustu dreo',
                        'description' => 'Dreo RO 115 su hazneli ve portatif tezgâh üstü arıtma cihazı filtreleri.',
                    ],
                ],
            ],
            [
                'name' => 'Hyundai',
                'search_query' => 'hyundai, hyundai su aritma',
                'description' => 'Hyundai Kore teknolojisi kapalı kasa su arıtma cihazları.',
                'children' => [
                    [
                        'name' => 'HND-35',
                        'search_query' => 'hnd-35, hnd35, hyundai hnd',
                        'description' => 'Hyundai HND-35 kapalı kasa tezgah altı su arıtma cihazı filtre takımı.',
                    ],
                ],
            ],
            [
                'name' => 'İhlas Aura',
                'search_query' => 'ihlas, aura, cebilon, ihlas aura',
                'description' => 'İhlas Aura Cebilon su arıtma sistemleri ve orijinal uyumlu filtreleri.',
                'children' => [
                    [
                        'name' => 'Cebilon Silver',
                        'search_query' => 'cebilon silver, aura silver',
                        'description' => 'İhlas Aura Cebilon Silver kapalı kasa su arıtma cihazı filtre seti.',
                    ],
                    [
                        'name' => 'Cebilon Platinum',
                        'search_query' => 'cebilon platinum, aura platinum',
                        'description' => 'İhlas Aura Cebilon Platinum kapalı kasa su arıtma cihazı filtre seti.',
                    ],
                    [
                        'name' => 'Cebilon Unique',
                        'search_query' => 'cebilon unique, aura unique, tak cevir',
                        'description' => 'İhlas Aura Cebilon Unique tak-çevir kaset filtreli kapalı kasa arıtma cihazı.',
                    ],
                ],
            ],
            [
                'name' => 'LG Chem (Montaj)',
                'search_query' => 'lg, lg chem, lg membran, lg montaj',
                'description' => 'LG Chem orijinal membranlı açık kasa OEM ve kabinli montaj su arıtma sistemleri.',
                'children' => [
                    [
                        'name' => 'Standart Açık Kasa OEM',
                        'search_query' => 'lg oem, standart acik kasa oem',
                        'description' => 'LG Chem membranlı standart açık kasa OEM montaj su arıtma cihazı filtreleri.',
                    ],
                    [
                        'name' => 'Gold / Cool Kabinli',
                        'search_query' => 'lg gold, lg cool, lg kabinli',
                        'description' => 'LG Chem membranlı Gold ve Cool kapalı kabinli su arıtma cihazı.',
                    ],
                ],
            ],
            [
                'name' => 'Neutron',
                'search_query' => 'neutron, neutron su aritma',
                'description' => 'Neutron akıllı ve nesnelerin interneti (IoT) destekli su arıtma cihazları.',
                'children' => [
                    [
                        'name' => 'NTL-SU60WB Akıllı',
                        'search_query' => 'ntl-su60wb, ntl su60wb, neutron akilli',
                        'description' => 'Neutron NTL-SU60WB Wi-Fi bağlantılı, mobil uygulamalı, tanksız doğrudan akış arıtma cihazı.',
                    ],
                ],
            ],
            [
                'name' => 'OverWater',
                'search_query' => 'overwater, over water',
                'description' => 'OverWater ev ve iş yeri su arıtma sistemleri.',
                'children' => [
                    [
                        'name' => 'Açık Kasa Klasik',
                        'search_query' => 'overwater acik kasa, overwater klasik',
                        'description' => 'OverWater açık kasa klasik su arıtma cihazı filtreleri.',
                    ],
                ],
            ],
            [
                'name' => 'Philips',
                'search_query' => 'philips, philips su aritma',
                'description' => 'Philips AquaShield ve QuickTwist filtreli premium su arıtma cihazları.',
                'children' => [
                    [
                        'name' => 'AUT3060/62',
                        'search_query' => 'aut3060, aut3062, aut3060/62',
                        'description' => 'Philips AUT3060/62 kapalı kasa tezgah altı su arıtma cihazı filtre takımı.',
                    ],
                    [
                        'name' => 'AUT3063/62',
                        'search_query' => 'aut3063, aut3063/62, dijital panel',
                        'description' => 'Philips AUT3063/62 dijital filtre takip panelli kapalı kasa su arıtma cihazı.',
                    ],
                    [
                        'name' => 'AUT3200 / AUT3300',
                        'search_query' => 'aut3200, aut3300, tanksiz philips',
                        'description' => 'Philips AUT3200 ve AUT3300 serisi tanksız, doğrudan akışlı yüksek debili arıtma cihazı.',
                    ],
                ],
            ],
            [
                'name' => 'Piksel Aqua',
                'search_query' => 'piksel aqua, piksel arıtma',
                'description' => 'Piksel Aqua su arıtma sistemleri.',
                'children' => [
                    [
                        'name' => 'PKS-0034 / Master',
                        'search_query' => 'pks-0034, pks0034, piksel master',
                        'description' => 'Piksel Aqua PKS-0034 Master açık kasa su arıtma cihazı filtreleri.',
                    ],
                ],
            ],
            [
                'name' => 'Profilo',
                'search_query' => 'profilo, profilo su aritma',
                'description' => 'Profilo dayanıklı ev aletleri kapalı kasa su arıtma cihazları.',
                'children' => [
                    [
                        'name' => 'SU01A2VW0N',
                        'search_query' => 'su01a2vw0n, profilo su01',
                        'description' => 'Profilo SU01A2VW0N kapalı kasa kompakt su arıtma cihazı filtreleri.',
                    ],
                ],
            ],
            [
                'name' => 'Puretron',
                'search_query' => 'puretron, puretron su aritma',
                'description' => 'Puretron alkali ve mineral zengini su arıtma teknolojileri.',
                'children' => [
                    [
                        'name' => 'X-Master Alkali',
                        'search_query' => 'x-master, x master, puretron alkali',
                        'description' => 'Puretron X-Master kapalı kasa yüksek pH alkali su arıtma cihazı.',
                    ],
                    [
                        'name' => 'Diamond Alkali',
                        'search_query' => 'diamond alkali, puretron diamond',
                        'description' => 'Puretron Diamond kapalı kasa alkali mineral takviyeli su arıtma cihazı.',
                    ],
                ],
            ],
            [
                'name' => 'Rainwater',
                'search_query' => 'rainwater, rain water',
                'description' => 'Rainwater lüks tasarım kapalı kasa su arıtma sistemleri.',
                'children' => [
                    [
                        'name' => 'Superior Alkali',
                        'search_query' => 'superior alkali, rainwater superior',
                        'description' => 'Rainwater Superior kapalı kasa alkali filtreli su arıtma cihazı.',
                    ],
                    [
                        'name' => 'Black Diamond Plus',
                        'search_query' => 'black diamond, black diamond plus',
                        'description' => 'Rainwater Black Diamond Plus kapalı kasa tasarım su arıtma cihazı.',
                    ],
                ],
            ],
            [
                'name' => 'Sulax',
                'search_query' => 'sulax, sulax su aritma',
                'description' => 'Sulax ev ve yarı endüstriyel yüksek kapasiteli su arıtma sistemleri.',
                'children' => [
                    [
                        'name' => 'SX50 Açık Sistem',
                        'search_query' => 'sx50, sx 50, sulax sx50',
                        'description' => 'Sulax SX50 açık kasa klasik evsel su arıtma sistemi filtreleri.',
                    ],
                    [
                        'name' => 'Açık Kasa 300 GPD',
                        'search_query' => '300 gpd, acik kasa 300 gpd, sulax 300',
                        'description' => 'Sulax açık kasa 300 GPD yüksek arıtma kapasiteli yarı ticari su arıtma sistemi.',
                    ],
                ],
            ],
        ];

        // Mevcut unlinked markaları temizleyelim
        $unlinkedBrandIds = Brand::whereDoesntHave('products')->pluck('id');
        Brand::whereIn('id', $unlinkedBrandIds)->delete();

        $mainSortOrder = 1;

        foreach ($brandsData as $bData) {
            $mainSlug = Str::slug($bData['name']);

            // Ana Marka Kaydı
            $brand = Brand::updateOrCreate(
                ['slug' => $mainSlug],
                [
                    'parent_id' => null,
                    'name' => $bData['name'],
                    'search_query' => $bData['search_query'],
                    'description' => $bData['description'],
                    'sort_order' => $mainSortOrder++,
                    'is_active' => true,
                ]
            );

            // Alt Modeller / Ürünler Kaydı
            if (! empty($bData['children'])) {
                $childSortOrder = 1;
                foreach ($bData['children'] as $child) {
                    $childSlug = Str::slug($bData['name'].'-'.$child['name']);

                    Brand::updateOrCreate(
                        ['slug' => $childSlug],
                        [
                            'parent_id' => $brand->id,
                            'name' => $child['name'],
                            'search_query' => $child['search_query'] ?? null,
                            'description' => $child['description'] ?? null,
                            'sort_order' => $childSortOrder++,
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
