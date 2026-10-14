<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductScraperService
{
    /**
     * Verilen URL'den ürün başlığı, açıklaması, fiyatı ve tüm yüksek kaliteli görsellerini çeker.
     */
    public static function scrape(string $url): array
    {
        $result = [
            'name' => '',
            'price' => null,
            'campaign_price' => null,
            'description' => '',
            'short_description' => '',
            'image_url' => '',
            'gallery_images' => [],
            'brand' => '',
        ];

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_ENCODING, ''); // Gzip, deflate, brotli otomatik açma
            curl_setopt($ch, CURLOPT_REFERER, $url);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
                'Cache-Control: no-cache',
                'Sec-Ch-Ua: "Chromium";v="124", "Google Chrome";v="124"',
                'Sec-Ch-Ua-Mobile: ?0',
                'Sec-Ch-Ua-Platform: "Windows"',
            ]);

            $html = curl_exec($ch);
            curl_close($ch);

            if (! $html) {
                return $result;
            }

            $collectedImages = [];

            // 1. JSON-LD Schema.org Ayrıştırma (En güvenilir ve modern yöntem)
            if (preg_match_all('/<script\s+type=["\']application\/ld\+json["\']\s*>(.*?)<\/script>/is', $html, $matches)) {
                foreach ($matches[1] as $jsonString) {
                    $json = json_decode(trim($jsonString), true);
                    if ($json) {
                        $productNodes = [];
                        if (isset($json['@type']) && (is_string($json['@type']) && strtolower($json['@type']) === 'product')) {
                            $productNodes[] = $json;
                        } elseif (isset($json['@graph']) && is_array($json['@graph'])) {
                            foreach ($json['@graph'] as $node) {
                                if (isset($node['@type']) && strtolower(strval($node['@type'])) === 'product') {
                                    $productNodes[] = $node;
                                }
                            }
                        }

                        foreach ($productNodes as $productData) {
                            if (! empty($productData['name']) && empty($result['name'])) {
                                $result['name'] = html_entity_decode($productData['name']);
                            }
                            if (! empty($productData['description']) && empty($result['description'])) {
                                $result['description'] = html_entity_decode($productData['description']);
                            }

                            // JSON-LD Görselleri
                            if (! empty($productData['image'])) {
                                $rawImgs = $productData['image'];
                                if (is_string($rawImgs)) {
                                    $rawImgs = [$rawImgs];
                                } elseif (is_array($rawImgs) && isset($rawImgs['url'])) {
                                    $rawImgs = [$rawImgs['url']];
                                }
                                if (is_array($rawImgs)) {
                                    foreach ($rawImgs as $imgItem) {
                                        $u = is_string($imgItem) ? $imgItem : ($imgItem['url'] ?? ($imgItem['contentUrl'] ?? ''));
                                        if ($u && is_string($u)) {
                                            $resolved = self::resolveUrl($u, $url);
                                            if ($resolved && ! in_array($resolved, $collectedImages)) {
                                                $collectedImages[] = $resolved;
                                            }
                                        }
                                    }
                                }
                            }

                            // Fiyatlar
                            if (! empty($productData['offers'])) {
                                $offers = $productData['offers'];
                                if (isset($offers['price'])) {
                                    $result['price'] = floatval($offers['price']);
                                } elseif (isset($offers['lowPrice'])) {
                                    $result['price'] = floatval($offers['lowPrice']);
                                    if (isset($offers['highPrice']) && floatval($offers['highPrice']) > floatval($offers['lowPrice'])) {
                                        $result['campaign_price'] = floatval($offers['lowPrice']);
                                        $result['price'] = floatval($offers['highPrice']);
                                    }
                                } elseif (is_array($offers) && isset($offers[0]['price'])) {
                                    $result['price'] = floatval($offers[0]['price']);
                                }
                            }
                        }
                    }
                }
            }

            // 2. OpenGraph Meta Etiketleri (Her türlü attribute sıralamasına duyarlı)
            if (empty($result['name'])) {
                if (preg_match('/<meta\s+[^>]*?property=["\']og:title["\'][^>]*?content=["\'](.*?)["\']/is', $html, $m) ||
                    preg_match('/<meta\s+[^>]*?content=["\'](.*?)["\'][^>]*?property=["\']og:title["\']/is', $html, $m)) {
                    $result['name'] = html_entity_decode($m[1]);
                } elseif (preg_match('/<title>(.*?)<\/title>/is', $html, $m)) {
                    $result['name'] = trim(html_entity_decode(strip_tags($m[1])));
                }
            }

            if (empty($result['description'])) {
                if (preg_match('/<meta\s+[^>]*?property=["\']og:description["\'][^>]*?content=["\'](.*?)["\']/is', $html, $m) ||
                    preg_match('/<meta\s+[^>]*?content=["\'](.*?)["\'][^>]*?property=["\']og:description["\']/is', $html, $m) ||
                    preg_match('/<meta\s+[^>]*?name=["\']description["\'][^>]*?content=["\'](.*?)["\']/is', $html, $m)) {
                    $result['description'] = html_entity_decode($m[1]);
                }
            }

            // OpenGraph / Twitter Ana Görsel
            $metaImgCandidates = [
                '/<meta\s+[^>]*?property=["\'](?:og:image|og:image:url|og:image:secure_url)["\'][^>]*?content=["\']([^"\']+)["\']/is',
                '/<meta\s+[^>]*?content=["\']([^"\']+)["\'][^>]*?property=["\'](?:og:image|og:image:url|og:image:secure_url)["\']/is',
                '/<meta\s+[^>]*?name=["\'](?:twitter:image|twitter:image:src)["\'][^>]*?content=["\']([^"\']+)["\']/is',
                '/<meta\s+[^>]*?content=["\']([^"\']+)["\'][^>]*?name=["\'](?:twitter:image|twitter:image:src)["\']/is',
                '/<link\s+[^>]*?rel=["\'](?:image_src|preload)["\'][^>]*?href=["\']([^"\']+\.(?:jpe?g|png|webp|avif)[^"\']*)["\']/is',
            ];
            foreach ($metaImgCandidates as $cand) {
                if (preg_match($cand, $html, $m)) {
                    $resolved = self::resolveUrl(html_entity_decode($m[1]), $url);
                    if ($resolved && ! in_array($resolved, $collectedImages)) {
                        $collectedImages[] = $resolved;
                    }
                }
            }

            // 3. HTML DOM Galeri, Zoom ve Slider Görselleri Taraması
            // 3a. <img> etiketlerindeki yüksek çözünürlüklü veri alanları
            $imgAttrPatterns = [
                '/<img\s+[^>]*?(?:data-zoom-image|data-large-img-url|data-large|data-zoom|data-high-res-image|data-full|data-src|data-original|data-image|data-lazy-src)=["\']([^"\'>]+)["\']/is',
                '/<a\s+[^>]*?(?:data-zoom-image|data-large-img-url|data-image|data-standard|href)=["\']([^"\'>]+\.(?:jpe?g|png|webp|avif)[^"\']*)["\']/is',
                '/<img\s+[^>]*?src=["\']([^"\'>]+\.(?:jpe?g|png|webp|avif)[^"\']*)["\']/is',
            ];

            foreach ($imgAttrPatterns as $pattern) {
                if (preg_match_all($pattern, $html, $domMatches)) {
                    foreach ($domMatches[1] as $foundImg) {
                        $foundImg = self::resolveUrl(html_entity_decode($foundImg), $url);
                        if ($foundImg && self::isValidProductImage($foundImg) && ! in_array($foundImg, $collectedImages)) {
                            $collectedImages[] = $foundImg;
                        }
                    }
                }
            }

            // 3b. srcset veya data-srcset alanlarından en yüksek çözünürlüklü olanı al
            if (preg_match_all('/(?:srcset|data-srcset)=["\']([^"\']+)["\']/is', $html, $srcSetMatches)) {
                foreach ($srcSetMatches[1] as $srcSet) {
                    $parts = array_filter(array_map('trim', explode(',', $srcSet)));
                    if (! empty($parts)) {
                        $lastPart = end($parts); // Genellikle en büyük çözünürlük sonuncusudur
                        $tokens = preg_split('/\s+/', trim($lastPart));
                        if (! empty($tokens[0])) {
                            $srcSetUrl = self::resolveUrl(html_entity_decode($tokens[0]), $url);
                            if ($srcSetUrl && self::isValidProductImage($srcSetUrl) && ! in_array($srcSetUrl, $collectedImages)) {
                                $collectedImages[] = $srcSetUrl;
                            }
                        }
                    }
                }
            }

            // 3c. Script içindeki gömülü JSON görsel listelerini tara (Trendyol, Ticimax, IdeaSoft, Shopify slider verileri)
            if (preg_match_all('/["\'](?:images|gallery|allImages|photos)["\']\s*:\s*\[(.*?)\]/is', $html, $scriptMatches)) {
                foreach ($scriptMatches[1] as $jsonArrayBlock) {
                    if (preg_match_all('/["\'](https?:[^\s"\'>]+\.(?:jpe?g|png|webp|avif)[^\s"\'>]*|(?:\/mnresize\/[^\s"\'>]+))["\']/is', $jsonArrayBlock, $strMatches)) {
                        foreach ($strMatches[1] as $scriptImg) {
                            $scriptImg = stripslashes($scriptImg);
                            // Trendyol özel durumu
                            if (str_starts_with($scriptImg, '/mnresize/')) {
                                $scriptImg = 'https://cdn.dsmcdn.com'.$scriptImg;
                            }
                            $resolved = self::resolveUrl($scriptImg, $url);
                            if ($resolved && self::isValidProductImage($resolved) && ! in_array($resolved, $collectedImages)) {
                                $collectedImages[] = $resolved;
                            }
                        }
                    }
                }
            }

            // Görselleri ayrıştır: İlk görsel ana kapak, diğerleri galeri
            if (! empty($collectedImages)) {
                $result['image_url'] = $collectedImages[0];
                $result['gallery_images'] = array_slice(array_unique(array_slice($collectedImages, 1)), 0, 10);
            }

            // 4. Fiyat Tespiti (Yedek)
            if (empty($result['price'])) {
                if (preg_match('/<meta\s+property=["\']product:price:amount["\']\s+content=["\'](.*?)["\']/is', $html, $m)) {
                    $result['price'] = floatval($m[1]);
                } elseif (preg_match('/class=["\'][^"\']*(?:price|prc|fiyat|current-price)[^"\']*["\'][^>]*>([^<]+)/is', $html, $m)) {
                    $priceStr = trim(strip_tags($m[1]));
                    $priceClean = preg_replace('/[^\d,\.]/', '', $priceStr);
                    if (strpos($priceClean, ',') !== false) {
                        $priceClean = str_replace('.', '', $priceClean);
                        $priceClean = str_replace(',', '.', $priceClean);
                    }
                    $result['price'] = floatval($priceClean);
                }
            }

            // 5. Açıklama Formatı, Yapışık Kelime Onarımı & Paragraf Boşlukları Koruma
            if (! empty($result['description'])) {
                $desc = AiProductService::repairGluedText($result['description']);
                if (! preg_match('/<(?:p|br|div|ul|ol|li|h1|h2|h3|table)\b/i', $desc)) {
                    $sentences = preg_split('/(?<=[.?!])\s+/u', $desc, -1, PREG_SPLIT_NO_EMPTY);
                    $paragraphs = [];
                    $chunk = [];
                    foreach ($sentences as $s) {
                        $s = trim($s);
                        if (empty($s)) {
                            continue;
                        }
                        $chunk[] = htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
                        if (count($chunk) >= 2) {
                            $paragraphs[] = '<p>'.implode(' ', $chunk).'</p>';
                            $chunk = [];
                        }
                    }
                    if (! empty($chunk)) {
                        $paragraphs[] = '<p>'.implode(' ', $chunk).'</p>';
                    }
                    $result['description'] = ! empty($paragraphs) ? implode("\n", $paragraphs) : '<p>'.htmlspecialchars($desc, ENT_QUOTES, 'UTF-8').'</p>';
                } else {
                    $result['description'] = $desc;
                }

                $plain = trim(strip_tags($result['description']));
                $result['short_description'] = Str::limit($plain, 250);
            }

        } catch (\Exception $e) {
            Log::error('ProductScraperService error: '.$e->getMessage());
        }

        return $result;
    }

    /**
     * Uzak görsel URL'sini indirir, referer kontrolüyle CDN korumasını aşar ve yerel storage'a optimize ederek kaydeder.
     */
    public static function downloadAndSaveImage(string $imageUrl, ?string $refererUrl = null, int $timeout = 25): ?string
    {
        if (empty($imageUrl)) {
            return null;
        }

        $imageUrl = trim(html_entity_decode($imageUrl));

        if (str_starts_with($imageUrl, '//')) {
            $imageUrl = 'https:'.$imageUrl;
        }

        // Yapay zeka servisleri görseli gerçek zamanlı render ettiği için timeout daha yüksek tutulur
        if (str_contains($imageUrl, 'pollinations.ai') || str_contains($imageUrl, 'openai') || str_contains($imageUrl, 'blob.core.windows.net')) {
            $timeout = max($timeout, 60);
        }

        try {
            $imageData = null;

            // Referer belirleme (CDN Hotlink Protection aşmak için sayfanın kendi domain'i gönderilir)
            $referer = $refererUrl ?: $imageUrl;
            $parsed = parse_url($referer);
            $origin = ($parsed['scheme'] ?? 'https').'://'.($parsed['host'] ?? '');

            // 1. Modern cURL İsteği
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $imageUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_ENCODING, ''); // Otomatik gzip/brotli açma
            curl_setopt($ch, CURLOPT_REFERER, $referer);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                'Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
                'Origin: '.$origin,
                'Sec-Fetch-Dest: image',
                'Sec-Fetch-Mode: no-cors',
                'Sec-Fetch-Site: cross-site',
            ]);

            $execData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $execData && strlen($execData) > 500) {
                $imageData = $execData;
            } else {
                // 2. Yedek Fallback: Stream context ile file_get_contents
                $opts = [
                    'http' => [
                        'method' => 'GET',
                        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36\r\n".
                                    "Referer: {$referer}\r\n".
                                    "Accept: image/*,*/*\r\n",
                        'timeout' => 15,
                    ],
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ];
                $context = stream_context_create($opts);
                $fgData = @file_get_contents($imageUrl, false, $context);
                if ($fgData && strlen($fgData) > 500) {
                    $imageData = $fgData;
                }
            }

            if (! $imageData) {
                Log::warning("ProductScraperService: Görsel indirilemedi. URL: {$imageUrl} (HTTP: {$httpCode})");

                return null;
            }

            $filename = 'products/'.Str::uuid().'.webp';

            // 3. GD Kütüphanesi ile WebP'ye Çevirme ve Optimize Etme
            if (extension_loaded('gd') && function_exists('imagecreatefromstring')) {
                $img = @imagecreatefromstring($imageData);
                if ($img) {
                    $origWidth = imagesx($img);
                    $origHeight = imagesy($img);
                    $maxWidth = 1200;
                    $maxHeight = 1200;

                    $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight, 1.0);
                    $newWidth = (int) round($origWidth * $ratio);
                    $newHeight = (int) round($origHeight * $ratio);

                    $canvas = imagecreatetruecolor($newWidth, $newHeight);
                    imagealphablending($canvas, false);
                    imagesavealpha($canvas, true);
                    $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
                    imagefilledrectangle($canvas, 0, 0, $newWidth, $newHeight, $transparent);

                    imagecopyresampled($canvas, $img, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

                    ob_start();
                    if (function_exists('imagewebp')) {
                        imagewebp($canvas, null, 85);
                    } else {
                        imagejpeg($canvas, null, 85);
                    }
                    $canvasBuffer = ob_get_clean();

                    imagedestroy($canvas);
                    imagedestroy($img);

                    if ($canvasBuffer && strlen($canvasBuffer) > 0) {
                        Storage::disk('public')->put($filename, $canvasBuffer);

                        return $filename;
                    }
                }
            }

            // GD yoksa veya format çevrimi başarısız olduysa ham veriyi güvenle kaydet
            $parsedUrlPath = parse_url($imageUrl, PHP_URL_PATH) ?? '';
            $ext = strtolower(pathinfo($parsedUrlPath, PATHINFO_EXTENSION));
            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'])) {
                $ext = 'jpg';
            }
            $rawFilename = 'products/'.Str::uuid().'.'.$ext;
            Storage::disk('public')->put($rawFilename, $imageData);

            return $rawFilename;

        } catch (\Exception $e) {
            Log::error('Download scraped image error: '.$e->getMessage().' for URL: '.$imageUrl);
        }

        return null;
    }

    /**
     * Göreceli (relative) veya eksik protokolü olan görsel URL'sini tam HTTPS URL'ye tamamlar.
     */
    public static function resolveUrl(string $path, string $baseUrl): ?string
    {
        $path = trim(html_entity_decode($path));
        if (empty($path) || str_starts_with($path, 'data:')) {
            return null;
        }

        if (str_starts_with($path, '//')) {
            return 'https:'.$path;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $parts = parse_url($baseUrl);
        $scheme = $parts['scheme'] ?? 'https';
        $host = $parts['host'] ?? '';
        if (! $host) {
            return null;
        }

        $baseOrigin = $scheme.'://'.$host;
        if (str_starts_with($path, '/')) {
            return $baseOrigin.$path;
        }

        $baseDir = isset($parts['path']) ? dirname($parts['path']) : '';
        $baseDir = ($baseDir === '/' || $baseDir === '\\') ? '' : $baseDir;

        return $baseOrigin.'/'.trim($baseDir, '/').'/'.ltrim($path, '/');
    }

    /**
     * İkon, sosyal medya veya logo gibi gereksiz grafikleri eler.
     */
    public static function isValidProductImage(string $url): bool
    {
        $urlLower = strtolower($url);

        // Hariç tutulacak şablonlar
        $blacklist = [
            'facebook', 'twitter', 'instagram', 'youtube', 'linkedin', 'pinterest',
            'whatsapp', 'tiktok', 'social', 'share', 'icon', 'logo', 'avatar',
            'banner', 'badge', 'payment', 'flag', 'header', 'footer', 'odeme',
            'kredi', 'visa', 'mastercard', 'troy', 'sprite', 'placeholder',
            'blank', 'loading', 'spinner', 'pixel', 'analytics', 'cart', 'sepet',
            'user', 'default-image', 'no-image', '1x1',
        ];

        foreach ($blacklist as $term) {
            if (str_contains($urlLower, $term)) {
                return false;
            }
        }

        return true;
    }
}
