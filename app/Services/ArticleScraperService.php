<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleScraperService
{
    /**
     * URL'den makale/blog içeriğini, başlığını ve görselini çeker.
     */
    public static function scrape(string $url): array
    {
        $result = [
            'title' => null,
            'slug' => null,
            'excerpt' => null,
            'content' => null,
            'image_url' => null,
            'meta_title' => null,
            'meta_description' => null,
            'source_url' => $url,
        ];

        try {
            // cURL veya Http Client ile HTML içeriğini çek
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
            ])->timeout(15)->get($url);

            if (! $response->successful()) {
                return ['success' => false, 'message' => 'Web sayfasına ulaşılamadı (HTTP '.$response->status().')'];
            }

            $html = $response->body();

            // 1. JSON-LD (Schema.org Article / BlogPosting) Taraması
            if (preg_match_all('/<script\s+type=["\']application\/ld\+json["\']\s*>(.*?)<\/script>/is', $html, $matches)) {
                foreach ($matches[1] as $jsonText) {
                    $json = json_decode(trim($jsonText), true);
                    if (! $json) {
                        continue;
                    }

                    $items = isset($json['@graph']) ? $json['@graph'] : [$json];
                    foreach ($items as $item) {
                        $type = $item['@type'] ?? '';
                        if (in_array($type, ['Article', 'BlogPosting', 'NewsArticle', 'WebPage'])) {
                            if (! empty($item['headline'])) {
                                $result['title'] = html_entity_decode($item['headline']);
                            }
                            if (! empty($item['description'])) {
                                $result['excerpt'] = html_entity_decode($item['description']);
                            }
                            if (! empty($item['articleBody'])) {
                                $result['content'] = self::formatRawContent($item['articleBody']);
                            }
                            if (! empty($item['image'])) {
                                $result['image_url'] = is_array($item['image']) ? ($item['image']['url'] ?? ($item['image'][0] ?? null)) : $item['image'];
                            }
                        }
                    }
                }
            }

            // 2. OpenGraph ve Meta Etiketleri Taraması (Yedek)
            if (empty($result['title'])) {
                if (preg_match('/<meta\s+property=["\']og:title["\']\s+content=["\'](.*?)["\']/is', $html, $m)) {
                    $result['title'] = html_entity_decode($m[1]);
                } elseif (preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $html, $m)) {
                    $result['title'] = trim(html_entity_decode(strip_tags($m[1])));
                } elseif (preg_match('/<title>(.*?)<\/title>/is', $html, $m)) {
                    $title = trim(html_entity_decode(strip_tags($m[1])));
                    $titleParts = preg_split('/[-|–—]/', $title);
                    $result['title'] = trim($titleParts[0]);
                }
            }

            if (empty($result['excerpt'])) {
                if (preg_match('/<meta\s+property=["\']og:description["\']\s+content=["\'](.*?)["\']/is', $html, $m)) {
                    $result['excerpt'] = html_entity_decode($m[1]);
                } elseif (preg_match('/<meta\s+name=["\']description["\']\s+content=["\'](.*?)["\']/is', $html, $m)) {
                    $result['excerpt'] = html_entity_decode($m[1]);
                }
            }

            if (empty($result['image_url'])) {
                if (preg_match('/<meta\s+property=["\']og:image["\']\s+content=["\'](.*?)["\']/is', $html, $m)) {
                    $result['image_url'] = $m[1];
                } elseif (preg_match('/<meta\s+name=["\']twitter:image["\']\s+content=["\'](.*?)["\']/is', $html, $m)) {
                    $result['image_url'] = $m[1];
                }
            }

            // 3. Makale Gövdesi / İçerik Çıkarımı (HTML Temizleme)
            if (empty($result['content'])) {
                $result['content'] = self::extractArticleBody($html);
            }

            // 4. Görselin tam URL kontrolü
            if (! empty($result['image_url'])) {
                $result['image_url'] = self::resolveUrl($result['image_url'], $url);
            }

            // 5. SEO Başlığı ve Açıklaması
            $result['meta_title'] = $result['title'];
            $result['meta_description'] = Str::limit(strip_tags($result['excerpt'] ?: $result['content']), 160);
            $result['slug'] = Str::slug($result['title']);

            return [
                'success' => true,
                'data' => $result,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Makale çekilirken bir hata oluştu: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Sayfadaki ana makale içeriğini HTML olarak ayrıştırır ve gereksiz öğeleri temizler.
     */
    private static function extractArticleBody(string $html): string
    {
        $cleaned = preg_replace('/<(script|style|nav|header|footer|noscript|aside|form)[^>]*?>.*?<\/\\1>/is', '', $html);

        $patterns = [
            '/<article[^>]*?>(.*?)<\/article>/is',
            '/<div[^>]*?(?:class|id)=["\'][^"\']*(?:entry-content|post-content|article-content|article-body|post-body|story-content|content-area)[^"\']*["\'][^>]*?>(.*?)<\/div>/is',
            '/<main[^>]*?>(.*?)<\/main>/is',
        ];

        $bodyHtml = '';
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $cleaned, $matches)) {
                $bodyHtml = $matches[1];
                break;
            }
        }

        if (empty($bodyHtml)) {
            if (preg_match_all('/<p[^>]*>.*?<\/p>/is', $cleaned, $pMatches)) {
                $bodyHtml = implode("\n", $pMatches[0]);
            }
        }

        if (empty($bodyHtml)) {
            return '';
        }

        $bodyHtml = preg_replace('/<div[^>]*?(?:class|id)=["\'][^"\']*(?:share|social|author|related|comments|advertisement|banner)[^"\']*["\'][^>]*?>.*?<\/div>/is', '', $bodyHtml);
        $bodyHtml = strip_tags($bodyHtml, '<p><h2><h3><h4><h5><h6><ul><ol><li><strong><b><em><i><a><blockquote><img><br>');
        $bodyHtml = preg_replace('/<p>\s*(?:&nbsp;|<br\s*\/?>|\s*)*<\/p>/i', '', $bodyHtml);

        return trim($bodyHtml);
    }

    /**
     * Düz metni paragraflar haline getirir.
     */
    private static function formatRawContent(string $rawText): string
    {
        $paragraphs = explode("\n\n", str_replace("\r\n", "\n", $rawText));
        $formatted = [];
        foreach ($paragraphs as $p) {
            $p = trim($p);
            if (! empty($p)) {
                $formatted[] = '<p>'.nl2br(e($p)).'</p>';
            }
        }

        return implode("\n", $formatted);
    }

    /**
     * Göreceli görsel URL\'lerini tam URL\'ye dönüştürür.
     */
    private static function resolveUrl(string $relativeUrl, string $baseUrl): string
    {
        if (preg_match('/^https?:\/\//i', $relativeUrl)) {
            return $relativeUrl;
        }

        $parts = parse_url($baseUrl);
        $scheme = $parts['scheme'] ?? 'https';
        $host = $parts['host'] ?? '';

        if (str_starts_with($relativeUrl, '//')) {
            return $scheme.':'.$relativeUrl;
        }

        if (str_starts_with($relativeUrl, '/')) {
            return $scheme.'://'.$host.$relativeUrl;
        }

        $path = dirname($parts['path'] ?? '/');

        return $scheme.'://'.$host.rtrim($path, '/').'/'.$relativeUrl;
    }

    /**
     * Makale kapak görselini indirir, CDN referer korumasını aşar ve optimize edilmiş WebP olarak kaydeder.
     */
    public static function downloadAndSaveImage(string $imageUrl, ?string $refererUrl = null): ?string
    {
        try {
            if (empty($imageUrl)) {
                return null;
            }

            $imageUrl = trim(html_entity_decode($imageUrl));
            if (str_starts_with($imageUrl, '//')) {
                $imageUrl = 'https:'.$imageUrl;
            }

            $referer = $refererUrl ?: $imageUrl;
            $parsed = parse_url($referer);
            $origin = ($parsed['scheme'] ?? 'https').'://'.($parsed['host'] ?? '');

            $imageData = null;

            // 1. cURL ile indirme
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $imageUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_REFERER, $referer);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: image/avif,image/webp,image/*,*/*;q=0.8',
                'Origin: '.$origin,
            ]);

            $execData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $execData && strlen($execData) > 500) {
                $imageData = $execData;
            } else {
                // 2. Stream Context Fallback
                $opts = [
                    'http' => [
                        'method' => 'GET',
                        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\nReferer: {$referer}\r\n",
                        'timeout' => 15,
                    ],
                    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
                ];
                $imageData = @file_get_contents($imageUrl, false, stream_context_create($opts));
            }

            if (! $imageData || strlen($imageData) < 500) {
                return null;
            }

            $filename = 'posts/'.Str::uuid().'.webp';

            if (extension_loaded('gd') && function_exists('imagecreatefromstring')) {
                $img = @imagecreatefromstring($imageData);
                if ($img) {
                    $origWidth = imagesx($img);
                    $origHeight = imagesy($img);
                    $maxWidth = 1200;
                    $maxHeight = 800;

                    $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight, 1.0);
                    $newWidth = (int) round($origWidth * $ratio);
                    $newHeight = (int) round($origHeight * $ratio);

                    $canvas = imagecreatetruecolor($newWidth, $newHeight);
                    imagealphablending($canvas, false);
                    imagesavealpha($canvas, true);

                    imagecopyresampled($canvas, $img, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

                    ob_start();
                    imagewebp($canvas, null, 85);
                    $webpData = ob_get_clean();

                    imagedestroy($canvas);
                    imagedestroy($img);

                    if ($webpData && strlen($webpData) > 0) {
                        Storage::disk('public')->put($filename, $webpData);

                        return $filename;
                    }
                }
            }

            // Ham kayıt yedeği
            $ext = strtolower(pathinfo(parse_url($imageUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION)) ?: 'jpg';
            $rawName = 'posts/'.Str::uuid().'.'.$ext;
            Storage::disk('public')->put($rawName, $imageData);

            return $rawName;

        } catch (\Exception $e) {
            Log::error('ArticleScraperService downloadAndSaveImage error: '.$e->getMessage());

            return null;
        }
    }
}
