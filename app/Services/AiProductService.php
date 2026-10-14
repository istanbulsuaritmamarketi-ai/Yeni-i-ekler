<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AiProductService
{
    /**
     * Urun bilgilerini firma markasina ve kurallarina gore yapay zeka ile yeniden yazar ve ozellestirir.
     */
    public static function rewriteProduct(array $data, ?string $customInstruction = null, string $targetField = 'all'): array
    {
        $settings = Setting::whereIn('key', [
            'gemini_api_key',
            'openai_api_key',
            'ai_provider',
            'ai_model',
            'ai_system_prompt',
            'company_name',
            'site_title',
        ])->pluck('value', 'key')->toArray();

        $companyName = $settings['company_name'] ?? ($settings['site_title'] ?? 'İstanbul Su Arıtma Marketi');
        $provider = $settings['ai_provider'] ?? 'gemini';
        $geminiKey = $settings['gemini_api_key'] ?? env('GEMINI_API_KEY');
        $openaiKey = $settings['openai_api_key'] ?? env('OPENAI_API_KEY');

        $basePrompt = $settings['ai_system_prompt'] ?? "Sen {$companyName} firmasının kıdemli e-ticaret metin yazarı ve su arıtma uzmanısın. Ham ürün verilerini inceleyerek firmamıza özel, güven veren, satış odaklı ve SEO uyumlu hale getir.";

        // API Anahtarı kontrolü
        if ($provider === 'openai' && ! empty($openaiKey)) {
            return self::callOpenAi($data, $openaiKey, $settings['ai_model'] ?? 'gpt-4o-mini', $companyName, $basePrompt, $customInstruction, $targetField);
        } elseif (! empty($geminiKey)) {
            return self::callGemini($data, $geminiKey, $settings['ai_model'] ?? 'gemini-3.6-flash', $companyName, $basePrompt, $customInstruction, $targetField);
        }

        // Eğer henüz API anahtarı girilmemişse akıllı kural bazlı kurumsal formatlayıcı çalışır
        return self::smartRuleBasedRewrite($data, $companyName, $customInstruction, null, $targetField);
    }

    /**
     * Google Gemini API ile İçerik Üretimi (Gemini 3.6 Flash, 3.8 Flash, 3.7 Flash Destekli)
     */
    protected static function callGemini(array $data, string $apiKey, string $model, string $companyName, string $systemPrompt, ?string $customInstruction, string $targetField = 'all'): array
    {
        $candidateModels = [
            'gemini-3.6-flash',
            'gemini-3.8-flash',
            'gemini-3.7-flash',
            'gemini-3.5-flash-lite',
            'gemini-3.1-pro',
        ];

        if (! empty($model) && ! in_array($model, ['gemini-1.5-flash', 'gemini-1.5-pro', 'gemini-1.5-flash-latest', 'gemini-2.0-flash', 'gemini-2.5-flash', 'gemini-2.5-pro'])) {
            array_unshift($candidateModels, trim($model));
            $candidateModels = array_unique($candidateModels);
        }

        $userPrompt = self::buildPrompt($data, $companyName, $systemPrompt, $customInstruction, $targetField);

        foreach ($candidateModels as $modelName) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key=".urlencode(trim($apiKey));

                // 1. İstek: responseMimeType ile saf JSON çıktısı iste (temperature parametresi olmaksızın)
                $response = Http::withoutVerifying()
                    ->timeout(35)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'x-goog-api-key' => trim($apiKey),
                    ])
                    ->post($url, [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [
                                    ['text' => $userPrompt],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'responseMimeType' => 'application/json',
                        ],
                    ]);

                // Eğer 400 Bad Request dönerse (örn: bazı modeller responseMimeType desteklemediğinde), generationConfig olmadan dene
                if ($response->status() === 400) {
                    $response = Http::withoutVerifying()
                        ->timeout(35)
                        ->withHeaders([
                            'Content-Type' => 'application/json',
                            'x-goog-api-key' => trim($apiKey),
                        ])
                        ->post($url, [
                            'contents' => [
                                [
                                    'role' => 'user',
                                    'parts' => [
                                        ['text' => $userPrompt],
                                    ],
                                ],
                            ],
                        ]);
                }

                if ($response->successful()) {
                    $body = $response->json();
                    $rawJsonText = $body['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    $parsed = self::parseJsonOutput($rawJsonText);
                    if ($parsed) {
                        $parsed['ai_generated'] = true;
                        $parsed['provider'] = 'gemini';
                        $parsed['model_used'] = $modelName;

                        return $parsed;
                    }
                } else {
                    Log::warning("Gemini API Error ({$modelName}): Status ".$response->status().' - '.$response->body());
                }
            } catch (\Exception $e) {
                Log::warning("Gemini Model ({$modelName}) Exception: ".$e->getMessage());
            }
        }

        // Dinamik model keşfi (Google API'deki aktif desteklenen modelleri otomatik listele)
        try {
            $listUrl = 'https://generativelanguage.googleapis.com/v1beta/models?key='.urlencode(trim($apiKey));
            $listResp = Http::withoutVerifying()
                ->timeout(10)
                ->withHeaders(['x-goog-api-key' => trim($apiKey)])
                ->get($listUrl);

            if ($listResp->successful()) {
                $available = $listResp->json('models', []);
                foreach ($available as $av) {
                    $methods = $av['supportedGenerationMethods'] ?? [];
                    if (in_array('generateContent', $methods)) {
                        $dynModel = str_replace('models/', '', $av['name']);
                        // Eski veya kapanmış modelleri atla
                        if (in_array($dynModel, ['gemini-1.5-flash', 'gemini-1.5-pro', 'gemini-2.0-flash'])) {
                            continue;
                        }

                        $dynUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$dynModel}:generateContent?key=".urlencode(trim($apiKey));
                        $dynResp = Http::withoutVerifying()
                            ->timeout(35)
                            ->withHeaders([
                                'Content-Type' => 'application/json',
                                'x-goog-api-key' => trim($apiKey),
                            ])
                            ->post($dynUrl, [
                                'contents' => [
                                    [
                                        'role' => 'user',
                                        'parts' => [
                                            ['text' => $userPrompt],
                                        ],
                                    ],
                                ],
                            ]);

                        if ($dynResp->successful()) {
                            $rawJsonText = $dynResp->json('candidates.0.content.parts.0.text', '');
                            $parsed = self::parseJsonOutput($rawJsonText);
                            if ($parsed) {
                                $parsed['ai_generated'] = true;
                                $parsed['provider'] = 'gemini';
                                $parsed['model_used'] = $dynModel;

                                return $parsed;
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Gemini Dynamic Discovery Exception: '.$e->getMessage());
        }

        return self::smartRuleBasedRewrite($data, $companyName, $customInstruction, 'Yapay zeka servisine bağlanılamadı, akıllı su arıtma şablonu uygulandı.', $targetField);
    }

    /**
     * OpenAI API ile İçerik Üretimi
     */
    protected static function callOpenAi(array $data, string $apiKey, string $model, string $companyName, string $systemPrompt, ?string $customInstruction, string $targetField = 'all'): array
    {
        try {
            $userPrompt = self::buildPrompt($data, $companyName, $systemPrompt, $customInstruction, $targetField);
            $modelName = ! empty($model) ? $model : 'gpt-4o-mini';

            $response = Http::timeout(30)
                ->withToken(trim($apiKey))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $modelName,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $body = $response->json();
                $rawJsonText = $body['choices'][0]['message']['content'] ?? '';
                $parsed = self::parseJsonOutput($rawJsonText);
                if ($parsed) {
                    $parsed['ai_generated'] = true;
                    $parsed['provider'] = 'openai';

                    return $parsed;
                }
            }
        } catch (\Exception $e) {
            Log::error('OpenAI API Exception: '.$e->getMessage());
        }

        return self::smartRuleBasedRewrite($data, $companyName, $customInstruction, 'OpenAI servisine erişilemedi, akıllı kurumsal şablon uygulandı.', $targetField);
    }

    /**
     * Ham metindeki yapışık kelimeleri, blok etiket boşluklarını ve noktalama hatalarını onarır
     */
    public static function repairGluedText(string $text): string
    {
        // 1. HTML blok etiketlerinin arasına satır sonu ekleyerek strip_tags'in kelimeleri birbirine yapıştırmasını engelle
        $text = preg_replace('/<\/(p|div|li|tr|h[1-6])>/iu', "\n\n", $text);
        $text = preg_replace('/<(br|hr)\s*\/?>/iu', "\n", $text);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        // 2. Nokta sonrası bitişik büyük harf: "uygundur.Saatlik" -> "uygundur. Saatlik"
        $text = preg_replace('/([a-z0-9ğüşıöç])\.([A-ZĞÜŞİÖÇ])/u', '$1. $2', $text);

        // 3. Virgül sonrası bitişik karakterler: "filtre,ters" -> "filtre, ters"
        $text = preg_replace('/([a-z0-9ğüşıöç]),([a-z0-9ğüşıöçA-ZĞÜŞİÖÇ])/u', '$1, $2', $text);

        // 4. İki nokta sonrası bitişik filtre görevleri: "Görevi5" veya "GöreviCTO" -> "Görevi: 5 / CTO"
        $text = preg_replace('/(Görev[iı])\s*:?\s*([0-9A-ZĞÜŞİÖÇ])/u', '$1: $2', $text);

        // 5. Parantez kapanışı sonrası bitişik: "membran)Su" -> "membran) Su"
        $text = preg_replace('/(\))\s*([0-9A-ZĞÜŞİÖÇ])/u', '$1 $2', $text);

        // 6. Birim ve kapasite yapışıklıkları
        $text = preg_replace('/(Elektrikli)\s*(Cafe)/iu', '$1 - $2', $text);
        $text = preg_replace('/(\d+)\s*(ton)\s*(\d+)/iu', '$1 Ton, $2', $text);
        $text = preg_replace('/(litre|lt|ton|gpd|bar|mikron|angstron)\s*([A-ZĞÜŞİÖÇ])/u', '$1 $2', $text);
        $text = preg_replace('/(litre\))\s*([A-ZĞÜŞİÖÇ])/iu', '$1 $2', $text);
        $text = preg_replace('/(Musluk)\s*(Shut-Off)/iu', '$1, $2', $text);
        $text = preg_replace('/(Valf)\s*(\d+["”])/iu', '$1 $2', $text);
        $text = preg_replace('/(Filtre)\s*(\d+["”])/iu', '$1 $2', $text);
        $text = preg_replace('/(Filtre)\s*(\d+\s*GPD)/iu', '$1 $2', $text);

        // 7. Rakip pazar yeri veya firma isimlerini temizle
        $competitorFilter = ['trendyol', 'hepsiburada', 'n11', 'amazon', 'pttavm', 'gittigidiyor', 'ciceksepeti'];
        $text = str_ireplace($competitorFilter, '', $text);

        // Çoklu boşlukları teke indir
        $text = preg_replace('/[ \t]{2,}/u', ' ', $text);

        return trim($text);
    }

    /**
     * AI için detaylı ve optimize prompt oluşturur (Tüm alanlar veya hedeflenen alan odaklı)
     */
    protected static function buildPrompt(array $data, string $companyName, string $systemPrompt, ?string $customInstruction, string $targetField = 'all'): string
    {
        $rawName = trim($data['name'] ?? '');
        $rawDesc = self::repairGluedText($data['description'] ?? '');
        $rawTech = self::repairGluedText($data['technical_features'] ?? '');
        $rawShort = self::repairGluedText($data['short_description'] ?? '');

        $prompt = "{$systemPrompt}\n\n";
        $prompt .= "FIRMA ADIMIZ: {$companyName}\n";

        if ($targetField === 'short_description') {
            $prompt .= "HEDEF: Bu ürün için e-ticaret sayfasında fiyatın hemen altında yer alacak, 1-2 çarpıcı cümleden oluşan, satın alma isteği uyandıran kısa spot özet (short_description) üret.\n\n";
        } elseif ($targetField === 'description') {
            $prompt .= "HEDEF: Bu ürün için ferah paragraflı, zengin ve tertemiz HTML etiketleriyle (<p>, <h3>, <ul><li>, <strong>) hazırlanmış detaylı ürün açıklaması (description) oluştur. Rakip pazar yeri veya firma isimlerini temizle, firmamızın orijinal ürün garantisi ve teknik destek güvencelerini ekle.\n\n";
        } elseif ($targetField === 'technical_features') {
            $prompt .= "HEDEF: Bu ürün için filtre aşamaları, tank, membran, musluk ve bağlantı ölçüleri gibi teknik özellikleri madde imleri (•) veya liste şeklinde çıkar (technical_features).\n\n";
        } elseif ($targetField === 'seo') {
            $prompt .= "HEDEF: Bu ürün için Google aramalarında en yüksek tıklamayı alacak SEO başlığı (seo_title, maks 65 karakter) ve meta açıklaması (seo_description, maks 155 karakter) üret.\n\n";
        } else {
            $prompt .= "HEDEF: Bu ham ürün verisini inceleyerek firmamızın prestijine yakışır, ferah paragraflı, güven aşılayan, müşteriyi satın almaya yönlendiren tüm alanları (ad, kısa özet, detaylı açıklama, teknik özellikler, SEO) profesyonelce oluştur.\n\n";
        }

        if (! empty($customInstruction)) {
            $prompt .= "KULLANICININ ÖZEL İSTEKLERİ: {$customInstruction}\n\n";
        }

        $prompt .= "KURALLAR:\n";
        $prompt .= "1. Rakip e-ticaret siteleri (Trendyol, Hepsiburada vb.), yabancı firma isimleri veya eski linkleri TAMAMEN temizle.\n";
        $prompt .= "2. Metni {$companyName} güvencesi, %100 orijinal ürün garantisi, uzman teknik servis desteği ve hızlı kargo avantajlarıyla zenginleştir.\n";
        $prompt .= "3. 'description' alanı temiz HTML olmalı: Paragrafları ferah tutmak için mutlaka <p>...</p> etiketleri kullan, önemli kısımları <strong> ile vurgula, maddeleri <ul><li> şeklinde düzenle, başlıklar için <h3> kullan.\n";
        $prompt .= "4. 'short_description' alanı 1-2 çarpıcı cümle ile ürünün en büyük faydasını özetlemeli (maks 250 karakter).\n";
        $prompt .= "5. 'technical_features' alanında teknik özellikleri maddeler halinde listele.\n";
        $prompt .= "6. 'seo_title' (maks 65 karakter) ve 'seo_description' (maks 155 karakter) üret.\n\n";

        $prompt .= "GİRİLEN HAM VERİLER:\n";
        $prompt .= "Ürün Adı: {$rawName}\n";
        if (! empty($rawShort)) {
            $prompt .= "Mevcut Kısa Açıklama: {$rawShort}\n";
        }
        $prompt .= "Mevcut Açıklama: {$rawDesc}\n";
        $prompt .= "Mevcut Teknik Özellikler: {$rawTech}\n\n";

        $prompt .= "ÇIKTI FORMATI: Yanıtını SADECE aşağıdaki JSON şemasına uygun saf JSON olarak ver (ek açıklama veya markdown bloğu yazma):\n";
        if ($targetField === 'short_description') {
            $prompt .= "{\n  \"short_description\": \"Kısa çarpıcı spot özet...\"\n}\n";
        } elseif ($targetField === 'description') {
            $prompt .= "{\n  \"description\": \"<p>İlk ferah paragraf...</p><h3>Öne Çıkan Avantajlar</h3><ul><li>Madde 1</li></ul><p>Sonuç paragrafı...</p>\"\n}\n";
        } elseif ($targetField === 'technical_features') {
            $prompt .= "{\n  \"technical_features\": \"• Özellik 1\\n• Özellik 2\"\n}\n";
        } elseif ($targetField === 'seo') {
            $prompt .= "{\n  \"seo_title\": \"SEO Başlığı...\",\n  \"seo_description\": \"SEO Açıklaması...\"\n}\n";
        } else {
            $prompt .= "{\n";
            $prompt .= '  "name": "Ürünün Yeniden Yazılmış Adı",'."\n";
            $prompt .= '  "short_description": "Kısa çarpıcı özet...",'."\n";
            $prompt .= '  "description": "<p>İlk ferah paragraf...</p><h3>Öne Çıkan Avantajlar</h3><ul><li>Madde 1</li></ul><p>Sonuç paragrafı...</p>",'."\n";
            $prompt .= '  "technical_features": "• Özellik 1\n• Özellik 2",'."\n";
            $prompt .= '  "seo_title": "SEO Başlığı...",'."\n";
            $prompt .= '  "seo_description": "SEO Açıklaması..."'."\n";
            $prompt .= "}\n";
        }

        return $prompt;
    }

    /**
     * API anahtarı olmadan veya ağ kesintilerinde çalışan ileri seviye akıllı su arıtma kurumsal formatlayıcı
     */
    protected static function smartRuleBasedRewrite(array $data, string $companyName, ?string $customInstruction = null, ?string $notice = null, string $targetField = 'all'): array
    {
        $rawName = trim($data['name'] ?? '');
        $rawDesc = trim($data['description'] ?? '');
        $rawTech = trim($data['technical_features'] ?? '');
        $rawShort = trim($data['short_description'] ?? '');

        // 1. Ham metindeki yapışıklıkları ve noktalama hatalarını onar
        $cleanDesc = self::repairGluedText($rawDesc);
        $cleanName = self::repairGluedText($rawName);

        // 2. Donanım ve kutu içeriği parçalarını tespit et
        $hardwarePatterns = [
            '20["”]?\s*Inline\s*NSF\s*5\s*Mikron\s*(?:Spun\s*)?Filtre[a-z]*',
            '20["”]?\s*Inline\s*NSF\s*GAC\s*Karbon\s*Filtre[a-z]*',
            '20["”]?\s*Inline\s*NSF\s*Block\s*Karbon\s*Filtre[a-z]*',
            '20["”]?\s*Inline\s*Coconat\s*Post\s*Karbon\s*Filtre[a-z]*',
            '20["”]?\s*Inline\s*Mineral\s*Filtre[a-z]*',
            '\b\d+\s*GPD\s*(?:Ters\s*Ozmoz\s*)?Membran[a-z]*',
            '\b\d+(?:\.\d+)?\s*Galon\s*Metal\s*Basınç\s*Denge\s*Tankı[^\(]*\(\d+\s*(?:lt|litre)\)',
            'Lüks\s*(?:Small|Paslanmaz|Krom)?\s*Musluk[a-z]*',
            'Shut-Off\s*Valf[a-z]*',
            'Otomatik\s*Kesici\s*Valf',
            'Basınç\s*Düşürücü\s*Regülatör',
            'Atık\s*Su\s*Kısıtlayıcı\s*Flow',
            'Dört\s*Yollu\s*Vana',
            'Inline\s*Detoks\s*Filtre',
            'Inline\s*Alkalin\s*Filtre',
        ];

        $hardwareFound = [];
        $tempText = $cleanDesc;
        foreach ($hardwarePatterns as $pat) {
            if (preg_match_all('/'.$pat.'/iu', $tempText, $matches)) {
                foreach ($matches[0] as $match) {
                    $item = trim($match);
                    if (! in_array($item, $hardwareFound)) {
                        $hardwareFound[] = $item;
                    }
                    $tempText = str_ireplace($match, '', $tempText);
                }
            }
        }

        // 3. Filtre aşamalarını tespit et (1. Filtre, Birinci Filtre vs.)
        $stageRegex = '/((?:Birinci|İkinci|Üçüncü|Dördüncü|Beşinci|Altıncı|Yedinci|Sekizinci|Dokuzuncu|Onuncu|1\.|2\.|3\.|4\.|5\.|6\.|7\.|8\.|9\.|10\.)\s*Filtre[^\.\:]*(?:\:|\.|\s)?)(.*?)(?=(?:Birinci|İkinci|Üçüncü|Dördüncü|Beşinci|Altıncı|Yedinci|Sekizinci|Dokuzuncu|Onuncu|\d+\.)\s*Filtre|$)/ius';

        $stages = [];
        if (preg_match_all($stageRegex, $tempText, $sMatches, PREG_SET_ORDER)) {
            foreach ($sMatches as $sm) {
                $rawTitle = trim($sm[1], " :.\t\n\r");
                $stageBody = trim($sm[2]);
                if (! empty($rawTitle) && ! empty($stageBody)) {
                    $stages[] = [
                        'title' => $rawTitle,
                        'body' => $stageBody,
                    ];
                    $tempText = str_replace($sm[0], '', $tempText);
                }
            }
        }

        $introText = trim($tempText);

        // 4. Zengin ve ferah HTML Açıklama İnşası
        $html = '';

        // Giriş Paragrafları (Cümleleri 2'şerli bloklara bölerek ferahlat)
        if (! empty($introText)) {
            $sentences = preg_split('/(?<=[.?!])\s+/u', $introText, -1, PREG_SPLIT_NO_EMPTY);
            $chunk = [];
            foreach ($sentences as $s) {
                $s = trim($s);
                if (empty($s)) {
                    continue;
                }
                $chunk[] = $s;
                if (count($chunk) >= 2) {
                    $html .= '<p>'.htmlspecialchars(implode(' ', $chunk), ENT_QUOTES, 'UTF-8')."</p>\n";
                    $chunk = [];
                }
            }
            if (! empty($chunk)) {
                $html .= '<p>'.htmlspecialchars(implode(' ', $chunk), ENT_QUOTES, 'UTF-8')."</p>\n";
            }
        }

        // Aşamalı Filtrasyon Bölümü
        if (! empty($stages)) {
            $html .= "\n<h3>🔬 İleri Çok Aşamalı Filtrasyon Teknolojisi</h3>\n";
            $stageIndex = 1;
            foreach ($stages as $st) {
                $cleanTitle = preg_replace('/^(?:Birinci|İkinci|Üçüncü|Dördüncü|Beşinci|Altıncı|Yedinci|Sekizinci|Dokuzuncu|Onuncu|\d+\.)\s*Filtremiz(?:in)?\s*Görevi\s*:?/iu', '', $st['title']);
                $cleanTitle = trim($cleanTitle, " :.-\t\n\r");
                $displayTitle = ! empty($cleanTitle) ? "{$stageIndex}. Aşama ({$cleanTitle})" : "{$stageIndex}. Aşama Filtrasyon";

                $html .= '<p><strong>🔹 '.htmlspecialchars($displayTitle, ENT_QUOTES, 'UTF-8').':</strong> '.htmlspecialchars($st['body'], ENT_QUOTES, 'UTF-8')."</p>\n";
                $stageIndex++;
            }
        }

        // Kutu ve Donanım İçeriği
        if (! empty($hardwareFound)) {
            $html .= "\n<h3>📦 Kutu &amp; Sistem Donanım İçeriği</h3>\n<ul>\n";
            foreach ($hardwareFound as $hw) {
                $html .= '  <li>'.htmlspecialchars($hw, ENT_QUOTES, 'UTF-8')."</li>\n";
            }
            $html .= "</ul>\n";
        }

        // Kurumsal Güvenceler
        $html .= "\n<h3>🛡️ {$companyName} Güvencesi &amp; Avantajları</h3>\n<ul>\n";
        $html .= "  <li><strong>%100 Orijinal Ürün Garantisi:</strong> Tüm filtrelerimiz ve arıtma ekipmanlarımız fabrikasyon mühürlü, sertifikalı ve üretici garantilidir.</li>\n";
        $html .= "  <li><strong>Ücretsiz Montaj &amp; Teknik Destek:</strong> Ürün uzman ekibimiz tarafından özenle sevk edilir ve montaj adımlarında 7/24 WhatsApp destek hattımız yanınızdadır.</li>\n";
        $html .= "  <li><strong>Hızlı &amp; Güvenli Kargo:</strong> Saat 15:00'e kadar verilen siparişler aynı gün özenle ambalajlanarak kargoya teslim edilir.</li>\n";
        $html .= "</ul>\n";

        // 5. Otomatik Teknik Özellikler Çıkarımı
        $techLines = [];
        if (! empty($rawTech)) {
            $existingLines = preg_split('/\r\n|\r|\n/', self::repairGluedText($rawTech));
            foreach ($existingLines as $el) {
                $el = trim($el, " •-\t\r\n");
                if (! empty($el)) {
                    $techLines[] = '• '.$el;
                }
            }
        }
        if (preg_match('/(\d+[-–]\d+\s*litre)/iu', $cleanDesc, $m)) {
            $techLines[] = '• Arıtma Kapasitesi: '.$m[1].' (Saatlik Yüksek Debi)';
        }
        if (preg_match('/(minimum\s*\d+\s*ton)/iu', $cleanDesc, $m)) {
            $techLines[] = '• Günlük Üretim Kapasitesi: '.$m[1].' / Gün';
        }
        if (preg_match('/(\d+(?:\.\d+)?\s*(?:galon|lt|litre)[^\.\,\<]*tank)/iu', $cleanDesc, $m)) {
            $techLines[] = '• Depolama Tankı: '.$m[1];
        }
        if (preg_match('/(\d+\s*GPD\s*Membran)/iu', $cleanDesc, $m)) {
            $techLines[] = '• Membran Kapasitesi: '.$m[1];
        }
        if (preg_match('/(pompalı)/iu', $cleanDesc.' '.$cleanName)) {
            $techLines[] = '• Sistem Tipi: Pompalı Yüksek Basınçlı Arıtma';
        }
        if (! empty($stages)) {
            $techLines[] = '• Filtrasyon Aşaması: '.count($stages).' Aşamalı Entegre Filtrasyon';
        }
        foreach ($hardwareFound as $hw) {
            $techLines[] = '• Donanım Bileşeni: '.$hw;
        }
        $techLines[] = '• Kurulum / Montaj: Ücretsiz Kurulum & Teknik Destek';
        $techLines[] = "• Garanti: 2 Yıl Resmi Garanti & {$companyName} Güvencesi";

        $techSpecs = implode("\n", array_unique($techLines));

        // 6. Kısa Spot Açıklama
        $firstSentence = ! empty($sentences[0]) ? $sentences[0] : $cleanName;
        $shortDesc = ! empty($rawShort) ? self::repairGluedText($rawShort) : Str::limit($firstSentence." Yüksek kapasiteli arıtma performansı, kaliteli filtreleri ve {$companyName} güvencesiyle işletmeniz ve eviniz için ideal çözüm.", 220);

        return [
            'name' => $cleanName ?: $rawName,
            'short_description' => $shortDesc,
            'description' => trim($html),
            'technical_features' => $techSpecs,
            'seo_title' => Str::limit("{$cleanName} - {$companyName}", 65, ''),
            'seo_description' => Str::limit($shortDesc, 155, ''),
            'ai_generated' => false,
            'notice' => $notice ?: '✨ Akıllı Su Arıtma Formatlayıcısı uygulandı. Daha üst düzey yapay zeka içerikleri için Ayarlar panelinden Google Gemini API anahtarınızı tanımlayabilirsiniz.',
        ];
    }

    /**
     * JSON yanıtını temizler ve diziye dönüştürür
     */
    protected static function parseJsonOutput(string $text): ?array
    {
        $text = trim($text);
        if (str_starts_with($text, '```json')) {
            $text = substr($text, 7);
        } elseif (str_starts_with($text, '```')) {
            $text = substr($text, 3);
        }
        if (str_ends_with($text, '```')) {
            $text = substr($text, 0, -3);
        }
        $text = trim($text);

        $json = json_decode($text, true);
        if (is_array($json) && (! empty($json['name']) || ! empty($json['description']) || ! empty($json['short_description']) || ! empty($json['technical_features']) || ! empty($json['seo_title']))) {
            return $json;
        }

        // Regex ile JSON bloğunu yakalamayı dene
        if (preg_match('/\{[\s\S]*\}/', $text, $matches)) {
            $json = json_decode($matches[0], true);
            if (is_array($json) && (! empty($json['name']) || ! empty($json['description']) || ! empty($json['short_description']) || ! empty($json['technical_features']) || ! empty($json['seo_title']))) {
                return $json;
            }
        }

        return null;
    }

    /**
     * Yapay zeka ile profesyonel e-ticaret urun gorseli uretir ve yerel storage'a kaydeder.
     */
    public static function generateImage(string $prompt): ?string
    {
        $openaiKey = Setting::where('key', 'openai_api_key')->value('value') ?? env('OPENAI_API_KEY');

        try {
            // 1. OpenAI DALL-E 3 varsa öncelikli olarak kullan
            if (! empty($openaiKey)) {
                $response = Http::timeout(60)
                    ->withToken($openaiKey)
                    ->post('https://api.openai.com/v1/images/generations', [
                        'model' => 'dall-e-3',
                        'prompt' => $prompt.', isolated on pure pristine white background, centered, professional e-commerce product catalog shot, commercial studio lighting, 1:1 square, sharp focus, 4k',
                        'n' => 1,
                        'size' => '1024x1024',
                    ]);

                if ($response->successful()) {
                    $imgUrl = $response->json()['data'][0]['url'] ?? null;
                    if ($imgUrl) {
                        return ProductScraperService::downloadAndSaveImage($imgUrl);
                    }
                }
            }

            // 2. Ucretsiz Pollinations AI (API anahtarsiz aninda saf beyaz e-ticaret gorsel uretimi)
            $cleanPrompt = trim($prompt);
            if (! str_contains(strtolower($cleanPrompt), 'isolated on clean pure white background') && ! str_contains(strtolower($cleanPrompt), 'pure white background')) {
                $cleanPrompt .= ', isolated on clean pure white background, centered, commercial e-commerce product photography, 1:1 square aspect ratio, studio lighting, high resolution, no watermark, crisp clean edges';
            }
            $encodedPrompt = urlencode($cleanPrompt);
            $pollinationsUrl = "https://image.pollinations.ai/prompt/{$encodedPrompt}?width=1024&height=1024&nologo=true&seed=".mt_rand(1000, 999999);

            return ProductScraperService::downloadAndSaveImage($pollinationsUrl, null, 60);
        } catch (\Exception $e) {
            Log::error('AI Generate Image Error: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Seçilen görseli Gemini Vision ve Yapay Zeka ile stüdyo e-ticaret fotoğrafı olarak yeniden üretir.
     */
    public static function reimagineImage(string $relativePath, ?string $customInstruction = null, ?string $productName = null): ?array
    {
        // 1. Dosya yolunu tespit et
        $fullPath = storage_path('app/public/'.$relativePath);
        if (! file_exists($fullPath)) {
            $fullPath = public_path($relativePath);
        }
        if (! file_exists($fullPath)) {
            // storage/ öneki ile gelmiş olabilir
            $cleaned = str_replace('storage/', '', $relativePath);
            $fullPath = storage_path('app/public/'.$cleaned);
        }
        if (! file_exists($fullPath)) {
            Log::warning("AI reimagine: image file not found for path: {$relativePath}");

            return null;
        }

        $geminiKey = Setting::where('key', 'gemini_api_key')->value('value') ?? env('GEMINI_API_KEY');
        $model = Setting::where('key', 'ai_model')->value('value') ?? 'gemini-3.6-flash';

        $generatedPrompt = null;

        // 2. Gemini API Key varsa Gemini Multimodal Vision ile görseli tara
        if (! empty($geminiKey)) {
            $generatedPrompt = self::analyzeImageWithGeminiVision($fullPath, $geminiKey, $model, $customInstruction, $productName);
        }

        // 3. Gemini Vision yoksa veya prompt üretemediyse, akıllı e-ticaret stüdyo promptu oluştur
        if (empty($generatedPrompt)) {
            $cleanName = ! empty($productName) ? $productName : 'water purifier system, reverse osmosis';
            $promptParts = [];
            if (! empty($customInstruction)) {
                $promptParts[] = $customInstruction;
            }
            $promptParts[] = "Commercial studio product photography of {$cleanName}";
            $promptParts[] = 'sleek modern design, isolated on clean pure white background, centered, professional studio softbox lighting, 1:1 square aspect ratio, sharp focus, 8k resolution, crisp clean details, no watermarks, no logos, no text, premium catalog shot';
            $generatedPrompt = implode(', ', $promptParts);
        }

        // 4. Görsel üretim motorunu çağır
        $savedPath = self::generateImage($generatedPrompt);
        if (! $savedPath) {
            return null;
        }

        return [
            'success' => true,
            'image_path' => $savedPath,
            'image_asset' => asset('storage/'.$savedPath),
            'prompt_used' => $generatedPrompt,
            'message' => 'Görsel yapay zeka ile stüdyo ortamında yeniden modellendi ve oluşturuldu!',
        ];
    }

    /**
     * Gemini Vision Multimodal ile görseli inceler ve e-ticaret stüdyo promptu çıkarır.
     */
    protected static function analyzeImageWithGeminiVision(string $filePath, string $apiKey, string $preferredModel, ?string $customInstruction = null, ?string $productName = null): ?string
    {
        $mimeType = mime_content_type($filePath) ?: 'image/jpeg';
        $fileData = @file_get_contents($filePath);
        if (! $fileData) {
            return null;
        }
        $base64Image = base64_encode($fileData);

        $instructionText = 'You are an expert commercial product photographer and e-commerce visual director.
Examine this product image carefully.
Describe the central product item in clear, descriptive English (its physical shape, housing color, parts like filter canisters, chrome faucet, pressure tank, digital display, valves) so an AI image generator can recreate it as a brand-new, ultra-high-end e-commerce catalog photograph.

CRITICAL INSTRUCTIONS:
1. Completely IGNORE and REMOVE any watermarks, stamps, seller logos, competitor text, messy kitchen backgrounds, dirty countertops, or blurriness in this original image.
2. The recreated image MUST be: centered, isolated on a seamless pure white background (#ffffff), perfectly lit with commercial studio softbox lighting, sharp focus, 1:1 square ratio, catalog quality.
3. Keep the authentic product design, structure and primary colors intact.';

        if (! empty($productName)) {
            $instructionText .= "\nProduct Name Context: {$productName}";
        }
        if (! empty($customInstruction)) {
            $instructionText .= "\nUser's Custom Editing Instruction: {$customInstruction}";
        }

        $instructionText .= "\nOutput ONLY the final English prompt for the image generator. Do not include markdown code blocks, quotes, or introductory conversational text.";

        $candidateModels = [
            'gemini-3.6-flash',
            'gemini-2.5-flash',
            'gemini-2.5-pro',
            'gemini-2.0-flash',
        ];

        if (! empty($preferredModel)) {
            array_unshift($candidateModels, trim($preferredModel));
            $candidateModels = array_unique($candidateModels);
        }

        foreach ($candidateModels as $model) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=".urlencode(trim($apiKey));
                $response = Http::withoutVerifying()->timeout(30)->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data' => $base64Image,
                                    ],
                                ],
                                [
                                    'text' => $instructionText,
                                ],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.4,
                        'maxOutputTokens' => 300,
                    ],
                ]);

                if ($response->successful()) {
                    $promptText = trim($response->json('candidates.0.content.parts.0.text', ''));
                    if (! empty($promptText)) {
                        return $promptText;
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Gemini Vision ({$model}) failed: ".$e->getMessage());
            }
        }

        return null;
    }

    /**
     * Görseli profesyonel e-ticaret formatına (Arka plan temizleme / Beyaz Stüdyo Fonu) dönüştürür.
     */
    public static function formatToEcommerceWhiteBackground(string $relativePath): ?string
    {
        $fullPath = storage_path('app/public/'.$relativePath);
        if (! file_exists($fullPath)) {
            return null;
        }

        $removeBgKey = Setting::where('key', 'removebg_api_key')->value('value') ?? env('REMOVEBG_API_KEY');

        try {
            $imageData = null;

            // 1. Eğer Remove.bg API Anahtarı tanımlıysa arka planı yapay zeka ile kaldırıp beyaz yap
            if (! empty($removeBgKey)) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://api.remove.bg/v1.0/removebg');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, [
                    'image_file' => new \CURLFile($fullPath),
                    'size' => 'auto',
                    'bg_color' => 'white', // Bembeyaz e-ticaret stüdyo fonu
                    'format' => 'jpg',
                ]);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'X-Api-Key: '.trim($removeBgKey),
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                $result = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode === 200 && ! empty($result)) {
                    $imageData = $result;
                } else {
                    Log::warning('Remove.bg API error code: '.$httpCode.' body: '.substr($result, 0, 100));
                }
            }

            // 2. Eğer GD kütüphanesi varsa beyaz e-ticaret tuvali (1:1 kare) oluştur
            if (extension_loaded('gd') && function_exists('imagecreatetruecolor')) {
                $rawSource = $imageData ? @imagecreatefromstring($imageData) : @imagecreatefromstring(file_get_contents($fullPath));
                if ($rawSource) {
                    $origW = imagesx($rawSource);
                    $origH = imagesy($rawSource);

                    // E-ticaret standart 1:1 kare tuval (1000x1000)
                    $targetSize = max(1000, max($origW, $origH));
                    $canvas = imagecreatetruecolor($targetSize, $targetSize);

                    // Beyaz arka plan doldur
                    $white = imagecolorallocate($canvas, 255, 255, 255);
                    imagefill($canvas, 0, 0, $white);

                    // Ürünü %88 boyutunda ortala (Amazon / Trendyol standardı)
                    $maxProductSize = (int) ($targetSize * 0.88);
                    $ratio = min($maxProductSize / $origW, $maxProductSize / $origH);
                    $newW = (int) ($origW * $ratio);
                    $newH = (int) ($origH * $ratio);

                    $dstX = (int) (($targetSize - $newW) / 2);
                    $dstY = (int) (($targetSize - $newH) / 2);

                    imagecopyresampled($canvas, $rawSource, $dstX, $dstY, 0, 0, $newW, $newH, $origW, $origH);

                    $newFilename = 'products/'.Str::uuid().'.webp';
                    ob_start();
                    imagewebp($canvas, null, 90);
                    $canvasData = ob_get_clean();

                    imagedestroy($canvas);
                    imagedestroy($rawSource);

                    if ($canvasData) {
                        Storage::disk('public')->put($newFilename, $canvasData);

                        return $newFilename;
                    }
                }
            }

            // 3. Remove.bg'den gelen doğrudan veriyi kaydet
            if ($imageData) {
                $newFilename = 'products/'.Str::uuid().'.jpg';
                Storage::disk('public')->put($newFilename, $imageData);

                return $newFilename;
            }

        } catch (\Exception $e) {
            Log::error('Format to ecommerce error: '.$e->getMessage());
        }

        return null;
    }
}
