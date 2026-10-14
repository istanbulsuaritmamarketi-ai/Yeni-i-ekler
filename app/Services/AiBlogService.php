<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiBlogService
{
    /**
     * Blog makalesini yapay zeka ile sıfırdan yazar veya mevcut makaleyi geliştirir.
     */
    public static function generateOrRewritePost(array $data, ?string $customInstruction = null, string $targetField = 'all'): array
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

        $basePrompt = "Sen {$companyName} firmasının kıdemli blog editörü, su arıtma ve filtreleme teknolojileri uzmanısın. Okuyucuya rehberlik eden, güven veren, akıcı, SEO uyumlu ve bilgilendirici blog makaleleri hazırlarsın.";

        // API Anahtarı kontrolü
        if ($provider === 'openai' && ! empty($openaiKey)) {
            return self::callOpenAi($data, $openaiKey, $settings['ai_model'] ?? 'gpt-4o-mini', $companyName, $basePrompt, $customInstruction, $targetField);
        } elseif (! empty($geminiKey)) {
            return self::callGemini($data, $geminiKey, $settings['ai_model'] ?? 'gemini-3.6-flash', $companyName, $basePrompt, $customInstruction, $targetField);
        }

        // Eğer API anahtarı yoksa kural bazlı kurumsal şablon çalışır
        return self::smartRuleBasedBlog($data, $companyName, $customInstruction, null, $targetField);
    }

    /**
     * Google Gemini API ile Blog İçeriği Üretimi
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

        $userPrompt = self::buildBlogPrompt($data, $companyName, $systemPrompt, $customInstruction, $targetField);

        foreach ($candidateModels as $modelName) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key=".urlencode(trim($apiKey));

                $response = Http::withoutVerifying()
                    ->timeout(45)
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

                if ($response->status() === 400) {
                    $response = Http::withoutVerifying()
                        ->timeout(45)
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
                    Log::warning("Gemini Blog API Error ({$modelName}): Status ".$response->status().' - '.$response->body());
                }
            } catch (\Exception $e) {
                Log::warning("Gemini Blog Model ({$modelName}) Exception: ".$e->getMessage());
            }
        }

        // Dinamik model keşfi
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
                        if (in_array($dynModel, ['gemini-1.5-flash', 'gemini-1.5-pro', 'gemini-2.0-flash'])) {
                            continue;
                        }

                        $dynUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$dynModel}:generateContent?key=".urlencode(trim($apiKey));
                        $dynResp = Http::withoutVerifying()
                            ->timeout(45)
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
            Log::error('Gemini Blog Dynamic Discovery Exception: '.$e->getMessage());
        }

        return self::smartRuleBasedBlog($data, $companyName, $customInstruction, 'Yapay zeka servisine erişilemedi, akıllı kurumsal taslak uygulandı.', $targetField);
    }

    /**
     * OpenAI API ile Blog İçeriği Üretimi
     */
    protected static function callOpenAi(array $data, string $apiKey, string $model, string $companyName, string $systemPrompt, ?string $customInstruction, string $targetField = 'all'): array
    {
        try {
            $userPrompt = self::buildBlogPrompt($data, $companyName, $systemPrompt, $customInstruction, $targetField);
            $modelName = ! empty($model) ? $model : 'gpt-4o-mini';

            $response = Http::timeout(45)
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
            Log::error('OpenAI Blog API Exception: '.$e->getMessage());
        }

        return self::smartRuleBasedBlog($data, $companyName, $customInstruction, 'OpenAI servisine erişilemedi, akıllı kurumsal taslak uygulandı.', $targetField);
    }

    /**
     * Blog için optimize edilmiş prompt oluşturur
     */
    protected static function buildBlogPrompt(array $data, string $companyName, string $systemPrompt, ?string $customInstruction, string $targetField = 'all'): string
    {
        $rawTitle = $data['title'] ?? ($data['topic'] ?? '');
        $rawExcerpt = $data['excerpt'] ?? '';
        $rawContent = strip_tags($data['content'] ?? '');

        $prompt = "{$systemPrompt}\n\n";
        $prompt .= "FİRMA ADI: {$companyName}\n";

        if ($targetField === 'title') {
            $prompt .= "HEDEF: Bu konu veya mevcut başlık için yüksek tıklama oranı (CTR) sağlayacak, merak uyandıran, profesyonel ve SEO uyumlu bir blog makale başlığı (title) üret.\n\n";
        } elseif ($targetField === 'excerpt') {
            $prompt .= "HEDEF: Bu makale için liste kartlarında ve arama motorlarında görünecek 1-2 çarpıcı cümleden oluşan spot özet (excerpt) üret (maks 300 karakter).\n\n";
        } elseif ($targetField === 'content') {
            $prompt .= "HEDEF: Bu konu için zengin, akıcı, bilgilendirici, ferah paragraflı, tertemiz HTML formatında (<p>, <h2>, <h3>, <ul><li>, <blockquote>, <strong>) hazırlanmış detaylı blog makale içeriği (content) yaz.\n\n";
        } elseif ($targetField === 'seo') {
            $prompt .= "HEDEF: Bu makale için Google'da en üst sıralara çıkacak SEO başlığı (meta_title, maks 65 karakter) ve SEO açıklaması (meta_description, maks 155 karakter) üret.\n\n";
        } else {
            $prompt .= "HEDEF: Bu makale konusunu inceleyerek sıfırdan veya mevcut verileri geliştirerek eksiksiz bir blog makalesi oluştur (başlık, özel url slug, spot özet, detaylı HTML içerik, SEO etiketleri).\n\n";
        }

        if (! empty($customInstruction)) {
            $prompt .= "KULLANICININ ÖZEL TALİMATI: {$customInstruction}\n\n";
        }

        $prompt .= "KURALLAR:\n";
        $prompt .= "1. Rakip site, yabancı firma veya pazar yeri isimlerini temizle. Bilgiyi uzman gözüyle ve tarafsız bir otorite diliyle aktar.\n";
        $prompt .= "2. Makale metni ('content') mutlaka temiz ve ferah HTML olmalıdır. Paragraflar <p>...</p>, ana başlıklar <h2>...</h2>, alt başlıklar <h3>...</h3>, maddeler <ul><li>...</li></ul> ve önemli tavsiyeler <blockquote>...</blockquote> etiketleriyle yapılandırılmalıdır.\n";
        $prompt .= "3. Gerektiğinde su tasarrufu, sağlıklı mineral dengesi, filtre değişim periyotları ve düzenli teknik bakımın önemine değin.\n";
        $prompt .= "4. 'slug' alanı Türkçe karakter içermeyen, küçük harfli ve tireli URL formatında olmalıdır (örn: filtre-ne-zaman-degisir).\n";
        $prompt .= "5. Çıktıyı SADECE aşağıdaki JSON şemasında ver:\n";
        $prompt .= "{\n";
        $prompt .= '  "title": "Çarpıcı ve SEO Uyumlu Makale Başlığı",'."\n";
        $prompt .= '  "slug": "makale-ozel-url",'."\n";
        $prompt .= '  "excerpt": "1-2 cümlelik dikkat çekici spot özet metin",'."\n";
        $prompt .= '  "content": "<p>Giriş paragrafı...</p><h2>Alt Başlık</h2><p>Detaylı açıklama...</p><ul><li>Madde 1</li></ul>",'."\n";
        $prompt .= '  "meta_title": "Google SEO Başlığı (Maks 65 karakter)",'."\n";
        $prompt .= '  "meta_description": "Google SEO Açıklaması (Maks 155 karakter)"'."\n";
        $prompt .= "}\n\n";

        $prompt .= "GİRİLEN VERİLER:\n";
        $prompt .= "Konu / Başlık: {$rawTitle}\n";
        if (! empty($rawExcerpt)) {
            $prompt .= "Mevcut Özet: {$rawExcerpt}\n";
        }
        if (! empty($rawContent)) {
            $prompt .= 'Mevcut İçerik / Taslak: '.Str::limit($rawContent, 1500)."\n";
        }

        return $prompt;
    }

    /**
     * JSON yanıtını güvenle ayrıştırır
     */
    protected static function parseJsonOutput(string $rawText): ?array
    {
        $clean = trim($rawText);
        $clean = preg_replace('/^```(?:json)?\s*/i', '', $clean);
        $clean = preg_replace('/\s*```$/', '', $clean);

        $json = json_decode($clean, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
            return $json;
        }

        if (preg_match('/\{[\s\S]*\}/', $clean, $matches)) {
            $json = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                return $json;
            }
        }

        return null;
    }

    /**
     * API anahtarı olmadığında veya kota dolduğunda akıllı kurumsal şablon üretici
     */
    protected static function smartRuleBasedBlog(array $data, string $companyName, ?string $customInstruction, ?string $notice = null, string $targetField = 'all'): array
    {
        $rawTitle = trim($data['title'] ?? ($data['topic'] ?? 'Su Arıtma Sistemleri ve Filtre Bakım Rehberi'));
        $slug = Str::slug($rawTitle);

        $title = $rawTitle;
        if (! str_contains($title, 'Rehberi') && ! str_contains($title, 'Nasıl')) {
            $title = "{$rawTitle} - Uzman Tavsiyeleri ve Rehber";
        }

        $excerpt = "{$rawTitle} hakkında bilmeniz gereken tüm detaylar, uzman önerileri ve filtre bakım ipuçları bu rehberimizde.";

        $content = "<p>Evlerimizde ve iş yerlerimizde sağlıklı, lezzetli ve temiz içme suyuna ulaşmanın en güvenilir yolu düzenli bakımı yapılan su arıtma sistemleridir. <strong>{$companyName}</strong> uzman ekibi olarak, <em>{$rawTitle}</em> konusunda en çok merak edilen soruları yanıtladık.</p>";
        $content .= '<h2>Neden Düzenli Bakım ve Kaliteli Filtre Önemlidir?</h2>';
        $content .= '<p>Su arıtma cihazları şebeke suyundaki klor, ağır metaller, tortu, pas ve mikroorganizmaları çok aşamalı filtrasyon teknolojisiyle temizler. Zamanla filtrelerin doygunluğa ulaşması arıtma verimini etkileyebilir.</p>';
        $content .= '<ul>';
        $content .= '<li><strong>Tortu ve Sediment Filtre:</strong> Kaba parçacıkları ve pası tutarak sonraki aşamaları korur.</li>';
        $content .= '<li><strong>Aktif Karbon Filtre:</strong> Klor, koku ve kötü tatları tamamen nötralize eder.</li>';
        $content .= '<li><strong>Membran Filtre:</strong> Ters ozmoz teknolojisi ile suyu moleküler düzeyde saflaştırır.</li>';
        $content .= '<li><strong>Post Karbon & Mineral Filtre:</strong> Suya doğal mineraller ve taze tat kazandırır.</li>';
        $content .= '</ul>';
        $content .= '<blockquote>Temiz su tüketimi, bağışıklık sisteminin güçlenmesi ve sindirim sağlığı açısından hayati rol oynar. Periyodik filtre değişimi cihazınızın ömrünü uzatır.</blockquote>';
        $content .= '<h2>Sonuç ve Uzman Desteği</h2>';
        $content .= "<p>{$companyName} güvencesiyle cihazınızın orijinal filtre setlerine ve profesyonel teknik servis desteğine her zaman ulaşabilirsiniz.</p>";

        $metaTitle = Str::limit("{$rawTitle} | {$companyName} Blog", 65, '');
        $metaDescription = Str::limit("{$rawTitle} hakkında uzman rehberi. Sağlıklı su tüketimi, filtre değişimi ve dikkat edilmesi gerekenler {$companyName}'nde.", 155, '');

        $result = [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => $content,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'ai_generated' => false,
            'notice' => $notice ?: 'Kurumsal şablon başarıyla uygulandı. Gemini API anahtarı ekleyerek yapay zeka gücünden tam yararlanabilirsiniz.',
        ];

        return $result;
    }
}
