<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ArimoController extends Controller
{
    /**
     * Arimo Yapay Zeka Asistanı Özel Sayfası
     */
    public function index()
    {
        try {
            $featuredProducts = Product::where('is_active', true)
                ->latest()
                ->take(4)
                ->get();
        } catch (\Throwable $e) {
            $featuredProducts = collect();
        }

        try {
            $brands = Schema::hasTable('brands')
                ? Brand::whereNull('parent_id')->where('is_active', true)->orderBy('sort_order')->take(6)->get()
                : collect();
        } catch (\Throwable $e) {
            $brands = collect();
        }

        return view('arimo', compact('featuredProducts', 'brands'));
    }

    /**
     * Arimo'ya Soru Sorma (AJAX API Endpoint)
     */
    public function ask(Request $request)
    {
        $message = trim($request->input('message', ''));
        if (empty($message)) {
            return response()->json([
                'reply' => 'Lütfen su arıtma ile ilgili sormak istediğiniz konuyu yazınız.',
                'products' => [],
                'action_url' => null,
                'action_text' => null,
            ]);
        }

        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $geminiKey = $settings['gemini_api_key'] ?? env('GEMINI_API_KEY');
        $openaiKey = $settings['openai_api_key'] ?? env('OPENAI_API_KEY');
        $aiProvider = $settings['ai_provider'] ?? 'gemini';

        // 1. Eğer LLM API anahtarı tanımlıysa yapay zekaya sor
        if (! empty($geminiKey) && $aiProvider === 'gemini') {
            $llmResponse = $this->askGemini($message, $geminiKey, $settings);
            if ($llmResponse) {
                return response()->json($llmResponse);
            }
        } elseif (! empty($openaiKey) && $aiProvider === 'openai') {
            $llmResponse = $this->askOpenAi($message, $openaiKey, $settings);
            if ($llmResponse) {
                return response()->json($llmResponse);
            }
        }

        // 2. Akıllı Yerel Kural & Ürün Öneri Motoru (Her zaman %100 çalışır)
        return response()->json($this->smartLocalAssistant($message));
    }

    /**
     * Akıllı Yerel Kural Motoru (API Anahtarı olmadan da anında cevap verir)
     */
    protected function smartLocalAssistant(string $msg): array
    {
        $lower = Str::lower($msg);
        $recommendedProducts = collect();
        $actionUrl = null;
        $actionText = null;
        $reply = '';

        // 1. Marka Filtresi Kontrolü (İhlas, LG, BMS, Conax, Waterbox vb.)
        $matchedBrand = Brand::where(function ($q) use ($lower) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%'.$lower.'%'])
                ->orWhereRaw('LOWER(search_query) LIKE ?', ['%'.$lower.'%']);
        })->first();

        if ($matchedBrand) {
            $targetBrandIds = array_merge([$matchedBrand->id], $matchedBrand->children()->pluck('id')->toArray());
            $recommendedProducts = Product::where('is_active', true)
                ->where(function ($q) use ($targetBrandIds, $matchedBrand) {
                    $q->whereIn('brand_id', $targetBrandIds)
                        ->orWhereHas('brands', fn ($bq) => $bq->whereIn('brands.id', $targetBrandIds))
                        ->orWhere('name', 'like', '%'.$matchedBrand->name.'%');
                })->latest()->take(3)->get();

            $reply = "Harika bir tercih! **{$matchedBrand->name}** su arıtma cihazlarıyla %100 uyumlu, yüksek filtrasyonlu ve NSF onaylı orijinal filtre setlerimiz mevcuttur. Aşağıdaki ürünler doğrudan cihazınıza tam uyum sağlar. Ayrıca montaj desteği isterseniz İstanbul genelinde servisimiz bulunmaktadır.";
            $actionUrl = route('brand.show', $matchedBrand->slug);
            $actionText = "Tüm {$matchedBrand->name} Ürünlerini Gör";
        }
        // 2. Aile / Ev Tipi Cihaz Önerisi
        elseif (Str::contains($lower, ['aile', 'ev', 'kişi', 'kişilik', 'kalabalık', 'ev için', 'ev tipi'])) {
            $recommendedProducts = Product::where('is_active', true)
                ->where(function ($q) {
                    $q->where('name', 'like', '%aşama%')
                        ->orWhere('name', 'like', '%mineral%')
                        ->orWhere('name', 'like', '%alkali%');
                })
                ->latest()
                ->take(3)
                ->get();

            if ($recommendedProducts->isEmpty()) {
                $recommendedProducts = Product::where('is_active', true)->latest()->take(3)->get();
            }

            $reply = 'Eviniz ve aileniz için en sağlıklı seçim **12 Aşamalı Mineral & Alkali** filtreli su arıtma cihazlarıdır! Bu cihazlar sudaki klor, kireç, ağır metal ve pası %99.9 oranında süzerken; mineral ve pH+ filtreleri sayesinde suyu 8.5+ alkali seviyesine yükseltir. Aileniz için önerdiğim popüler cihazlar:';
            $actionUrl = route('products.index');
            $actionText = 'Tüm Ev Tipi Cihazları İncele';
        }
        // 3. Fiyat / En Ucuz / Ekonomik / Bütçe
        elseif (Str::contains($lower, ['ucuz', 'ekonomik', 'fiyat', 'bütçe', 'en uygun', 'kampanya', 'indirim'])) {
            $recommendedProducts = Product::where('is_active', true)
                ->orderBy('price', 'asc')
                ->take(3)
                ->get();

            $reply = 'Bütçenizi sarsmayacak, hem ekonomik hem de birinci sınıf kaliteli su üreten en avantajlı cihazlarımızı listeledim. Bu modeller NSF onaylı filtreleri sayesinde damacana masrafınızı ilk 2 ayda tamamen amorti eder:';
            $actionUrl = route('products.index');
            $actionText = 'Tüm İndirimli Ürünleri Gör';
        }
        // 4. Pompalı mı Pompasız mı? / Basınç Soruları
        elseif (Str::contains($lower, ['pompa', 'pompalı', 'pompasız', 'basınç', 'kat', 'motorlu'])) {
            $recommendedProducts = Product::where('is_active', true)
                ->where('name', 'like', '%pompalı%')
                ->latest()
                ->take(2)
                ->get();

            $reply = "Su arıtma cihazlarında **pompa seçimi su basıncınıza bağlıdır**: \n\n• Eğer 3. kat ve üzeri bir dairede oturuyorsanız veya şebeke basıncınız 3 barın altındaysa, **mutlaka Pompalı (Motorlu)** cihaz tercih etmelisiniz. Pompa, membran filtrenin tam verimle ve sessizce çalışmasını sağlar.\n• Eğer giriş katlarındaysanız ve basıncınız güçlüyse Pompasız modeller de rahatça yeterli olur.";
            $actionUrl = route('products.index', ['search' => 'pompalı']);
            $actionText = 'Pompalı Modelleri İncele';
        }
        // 5. Kireçli Su / Sert Su / Membran Kalitesi
        elseif (Str::contains($lower, ['kireç', 'sert', 'tat', 'koku', 'membran', 'lg'])) {
            $recommendedProducts = Product::where('is_active', true)
                ->where(function ($q) {
                    $q->where('name', 'like', '%lg%')
                        ->orWhere('name', 'like', '%membran%');
                })
                ->latest()
                ->take(3)
                ->get();

            $reply = "İstanbul'un kireçli şebeke sularında en önemli parça **Membran Filtre**dir! Çaydanlıkta kireç kalmaması ve berrak içim için **Orijinal LG Chem veya Vontron Membranlı** sistemlerimizi tavsiye ederim. Gözenekleri 0.0001 mikron inceliğinde olduğu için sudaki kireci ve mikroorganizmaları tamamen engeller.";
            $actionUrl = route('products.index', ['search' => 'lg membran']);
            $actionText = 'LG Membranlı Cihazları İncele';
        }
        // 6. Montaj / Servis / Garanti / Kargo
        elseif (Str::contains($lower, ['montaj', 'servis', 'kurulum', 'ücretsiz', 'garanti', 'kargo', 'teslimat'])) {
            $reply = "İstanbul genelinde **39 ilçemize ücretsiz montaj ve randevulu teknik servis** hizmeti sunuyoruz! 🛠️\n\n• Tüm cihazlarımız 2 yıl resmi garanti ve 10 yıl yedek parça garantisi altındadır.\n• İster cihazı alıp kendiniz taktırabilir, isterseniz uzman teknisyenlerimizin kapınıza gelip kurmasını isteyebilirsiniz.";
            $actionUrl = route('service.create');
            $actionText = 'Ücretsiz Montaj Randevusu Al';
        }
        // 7. Filtre Değişim Zamanı
        elseif (Str::contains($lower, ['filtre değişim', 'ne zaman', 'bakım', 'kaç ayda'])) {
            $reply = "Su arıtma cihazlarında düzenli filtre değişimi suyunuzun lezzeti ve sağlığınız için çok önemlidir:\n\n• **1, 2 ve 3. Ön Filtreler (Sediment & Karbon):** 6 ayda bir\n• **Membran Filtre (Ana Filtre):** 1.5 - 2 yılda bir\n• **Mineral / Alkali / Tatlandırıcı Filtre:** 12 ayda bir değişmelidir.\n\nCihazınız için komple 5'li veya 6'lı filtre takımlarımız indirimdedir:";
            $actionUrl = route('filter.finder');
            $actionText = 'Uyumlu Filtre Setini Bul';
        }
        // 8. Genel Karşılama veya Tanımsız Sorular
        else {
            $recommendedProducts = Product::where('is_active', true)
                ->latest()
                ->take(3)
                ->get();

            $reply = "Size en doğru çözümü sunmak için buradayım! Eviniz veya iş yeriniz için su arıtma cihazı mı bakıyorsunuz, yoksa mevcut bir cihazınız için filtre takımı mı arıyorsunuz? \n\nDilerseniz yukarıdaki hızlı seçeneklerden birini tıklayabilir ya da doğrudan aradığınız marka/modeli yazabilirsiniz.";
        }

        return [
            'reply' => $reply,
            'products' => $this->formatProducts($recommendedProducts),
            'action_url' => $actionUrl,
            'action_text' => $actionText,
        ];
    }

    /**
     * Gemini AI API Çağrısı (Zengin Mağaza & Ürün Bağlamı ile)
     */
    protected function askGemini(string $message, string $apiKey, array $settings): ?array
    {
        try {
            $apiKey = trim($apiKey);
            if (empty($apiKey)) {
                return null;
            }

            // Mağazadaki öne çıkan ürün ve fiyat bağlamı
            $catalogList = '';
            try {
                $catalogItems = Product::where('is_active', true)
                    ->select('id', 'name', 'price', 'campaign_price')
                    ->latest()
                    ->take(10)
                    ->get();

                $catalogList = $catalogItems->map(function ($p) {
                    $pPrice = number_format($p->campaign_price ?: $p->price, 0, ',', '.').' TL';

                    return "- {$p->name} ({$pPrice})";
                })->implode("\n");
            } catch (\Throwable $e) {
            }

            // Marka listesi
            $brandNames = '';
            try {
                if (Schema::hasTable('brands')) {
                    $brandNames = Brand::whereNull('parent_id')->where('is_active', true)->pluck('name')->implode(', ');
                }
            } catch (\Throwable $e) {
            }

            $systemPrompt = "Sen Arimo'sun; İstanbul Su Arıtma Marketi'nin uzman, samimi, satış odaklı ve güven veren yapay zeka danışmanısın.
Müşterinin su arıtma cihazı, filtre değişimi, su kalitesi, pompa/basınç gereksinimi veya montajla ilgili sorularını yanıtla.
Konuşma kuralları:
1. Türkçe konuş, son derece samimi, net ve çözüm odaklı ol.
2. Gereksiz ansiklopedik laf kalabalığı yapma, müşteriye tam ihtiyacı olan nokta atışı tavsiyeyi ver.
3. Önemli cihaz veya filtre isimlerini **bu şekilde kalın** yaz.
4. Firmamızın kurumsal avantajları:
   - İstanbul'un 39 ilçesine ÜCRETSİZ montaj ve randevulu servis
   - NSF onaylı orijinal filtreler (LG Chem, Vontron membran vb.)
   - 2 yıl resmi garanti, 10 yıl parça temin garantisi
   - Kapıda nakit/kredi kartı ödeme ve taksit
   - WhatsApp destek hattı: 0501 379 82 36

Desteklediğimiz başlıca markalar: {$brandNames}

Mağazamızdaki bazı güncel ürünler ve fiyatlar:
{$catalogList}";

            // Modeller: Google'ın en güncel Gemini 3 modelleri
            $models = [
                'gemini-3.6-flash',
                'gemini-3.8-flash',
                'gemini-3.7-flash',
                'gemini-3.5-flash-lite',
                'gemini-3.1-pro',
            ];

            if (! empty($settings['ai_model']) && ! in_array($settings['ai_model'], ['gemini-1.5-flash', 'gemini-1.5-flash-latest', 'gemini-2.0-flash', 'gemini-1.5-pro', 'gemini-2.5-flash', 'gemini-2.5-pro'])) {
                array_unshift($models, trim($settings['ai_model']));
                $models = array_unique($models);
            }

            $reply = null;

            foreach ($models as $model) {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=".urlencode($apiKey);

                $response = Http::withoutVerifying()
                    ->timeout(20)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'x-goog-api-key' => trim($apiKey),
                    ])
                    ->post($url, [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [
                                    ['text' => "{$systemPrompt}\n\nMüşteri Sorusu: {$message}"],
                                ],
                            ],
                        ],
                    ]);

                if ($response->successful()) {
                    $reply = $response->json('candidates.0.content.parts.0.text');
                    if (! empty($reply)) {
                        break;
                    }
                } else {
                    Log::warning("Arimo Gemini model {$model} warning: ".$response->body());
                }
            }

            // Eğer tanımlı modellerden yanıt alınamazsa, Google API'den açık modelleri dinamik keşfet
            if (empty($reply)) {
                try {
                    $listUrl = 'https://generativelanguage.googleapis.com/v1beta/models?key='.urlencode($apiKey);
                    $listResp = Http::withoutVerifying()->timeout(10)->get($listUrl);
                    if ($listResp->successful()) {
                        $available = $listResp->json('models', []);
                        foreach ($available as $av) {
                            $methods = $av['supportedGenerationMethods'] ?? [];
                            if (in_array('generateContent', $methods)) {
                                $autoModel = str_replace('models/', '', $av['name']);
                                $autoUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$autoModel}:generateContent?key=".urlencode($apiKey);
                                $autoPost = Http::withoutVerifying()->timeout(15)->post($autoUrl, [
                                    'contents' => [
                                        ['parts' => [['text' => "{$systemPrompt}\n\nMüşteri Sorusu: {$message}"]]],
                                    ],
                                    'generationConfig' => [
                                        'temperature' => 0.7,
                                        'maxOutputTokens' => 800,
                                    ],
                                ]);
                                if ($autoPost->successful()) {
                                    $reply = $autoPost->json('candidates.0.content.parts.0.text');
                                    if (! empty($reply)) {
                                        break;
                                    }
                                }
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('Arimo dynamic model discovery error: '.$e->getMessage());
                }
            }

            if ($reply) {
                // Müşterinin sorusu ve Gemini'nin cevabına en uygun ürünleri veritabanından çek
                $matchedProducts = $this->findRelevantProducts($message.' '.$reply);

                return [
                    'reply' => trim($reply),
                    'products' => $this->formatProducts($matchedProducts),
                    'action_url' => route('products.index'),
                    'action_text' => 'Tüm Ürünleri İncele',
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Arimo Gemini Error: '.$e->getMessage());
        }

        return null;
    }

    /**
     * OpenAI API Çağrısı
     */
    protected function askOpenAi(string $message, string $apiKey, array $settings): ?array
    {
        try {
            $response = Http::withoutVerifying()->withToken(trim($apiKey))->timeout(15)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => "Sen Arimo'sun; İstanbul Su Arıtma Marketi'nin uzman yapay zeka asistanısın. Müşteriye en uygun su arıtma cihazı ve filtre tavsiyesini ver."],
                    ['role' => 'user', 'content' => $message],
                ],
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $reply = $response->json('choices.0.message.content');
                if ($reply) {
                    $matchedProducts = $this->findRelevantProducts($message.' '.$reply);

                    return [
                        'reply' => trim($reply),
                        'products' => $this->formatProducts($matchedProducts),
                        'action_url' => route('products.index'),
                        'action_text' => 'Ürünleri İncele',
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Arimo OpenAI Error: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Soru ve cevaba en uygun ürünleri akıllıca eşleştirir
     */
    protected function findRelevantProducts(string $text, int $limit = 3)
    {
        $lower = Str::lower($text);

        // 1. Marka eşleşmesi var mı?
        $brand = Brand::where(function ($q) use ($lower) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%'.$lower.'%'])
                ->orWhereRaw('LOWER(search_query) LIKE ?', ['%'.$lower.'%']);
        })->first();

        if ($brand) {
            $brandIds = array_merge([$brand->id], $brand->children()->pluck('id')->toArray());
            $products = Product::where('is_active', true)
                ->where(function ($q) use ($brandIds, $brand) {
                    $q->whereIn('brand_id', $brandIds)
                        ->orWhereHas('brands', fn ($bq) => $bq->whereIn('brands.id', $brandIds))
                        ->orWhere('name', 'like', '%'.$brand->name.'%');
                })->latest()->take($limit)->get();

            if ($products->isNotEmpty()) {
                return $products;
            }
        }

        // 2. Özel anahtar kelimelere göre filtrele
        $keywords = ['pompalı', 'pompasız', 'membran', 'filtre', 'alkali', 'mineral', '12 aşama', 'tezgah altı'];
        foreach ($keywords as $kw) {
            if (Str::contains($lower, $kw)) {
                $products = Product::where('is_active', true)
                    ->where('name', 'like', '%'.$kw.'%')
                    ->latest()
                    ->take($limit)
                    ->get();
                if ($products->isNotEmpty()) {
                    return $products;
                }
            }
        }

        // 3. Fiyat veya ekonomik cihaz arayışı
        if (Str::contains($lower, ['ucuz', 'ekonomik', 'fiyat', 'bütçe', 'en uygun', 'kampanya'])) {
            return Product::where('is_active', true)->orderBy('price', 'asc')->take($limit)->get();
        }

        // 4. Öne çıkan ürünler (Kampanyalı veya en son eklenenler)
        $campaignProducts = Product::where('is_active', true)->whereNotNull('campaign_price')->latest()->take($limit)->get();
        if ($campaignProducts->isNotEmpty()) {
            return $campaignProducts;
        }

        return Product::where('is_active', true)->latest()->take($limit)->get();
    }

    /**
     * Ürünleri Arimo vitrin formatına çevirir
     */
    protected function formatProducts($products): array
    {
        return $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'url' => route('product.show', $p->slug),
                'price' => number_format($p->price, 0, ',', '.').' TL',
                'campaign_price' => $p->campaign_price ? number_format($p->campaign_price, 0, ',', '.').' TL' : null,
                'image' => $p->image_path ? asset('storage/'.$p->image_path) : null,
                'discount_percent' => $p->discount_percent,
            ];
        })->toArray();
    }
}
