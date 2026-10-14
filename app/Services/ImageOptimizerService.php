<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizerService
{
    /**
     * Yüklenen görseli kalitesini bozmadan WebP formatına çevirir, piksel boyutunu optimize eder ve kaydeder.
     *
     * @param  string  $folder  (Örn: 'products', 'categories', 'settings', 'banners')
     * @param  int  $maxWidth  (Maksimum genişlik, varsayılan 800px - E-ticaret için ideal)
     * @param  int  $maxHeight  (Maksimum yükseklik, varsayılan 800px)
     * @param  int  $quality  (Sıkıştırma kalitesi 1-100, varsayılan 85 - kayıpsız netlik)
     * @return string (Kaydedilen relative storage path)
     */
    public static function optimizeAndStore(
        UploadedFile $file,
        string $folder = 'products',
        int $maxWidth = 800,
        int $maxHeight = 800,
        int $quality = 85
    ): string {
        // Yüksek çözünürlüklü görsellerde bellek taşmasını önlemek için geçici yükseltme
        @ini_set('memory_limit', '512M');

        try {
            if (! $file->isValid()) {
                return $file->store($folder, 'public');
            }

            $extension = strtolower($file->getClientOriginalExtension());
            $mime = strtolower($file->getMimeType() ?: '');

            // SVG veya ICO ise piksel yeniden boyutlandırmaya gerek yok
            if (in_array($extension, ['svg', 'ico']) || in_array($mime, ['image/svg+xml', 'image/x-icon'])) {
                return $file->store($folder, 'public');
            }

            // GD Kütüphanesi kontrolü
            if (! extension_loaded('gd') || ! function_exists('imagecreatetruecolor')) {
                return $file->store($folder, 'public');
            }

            $sourcePath = $file->getRealPath() ?: $file->getPathname();
            if (! $sourcePath || ! file_exists($sourcePath)) {
                return $file->store($folder, 'public');
            }

            $fileData = @file_get_contents($sourcePath);
            if (! $fileData) {
                return $file->store($folder, 'public');
            }

            // Evrensel Görsel Yükleme (Header imzasına göre JPEG, PNG, GIF, WEBP, BMP vb. otomatik tanır)
            $sourceImage = function_exists('imagecreatefromstring') ? @imagecreatefromstring($fileData) : null;

            // imagecreatefromstring başarısız olduysa formata özel fonksiyonları dene
            if (! $sourceImage) {
                if (str_contains($mime, 'jpeg') || str_contains($mime, 'jpg') || in_array($extension, ['jpg', 'jpeg', 'jfif'])) {
                    if (function_exists('imagecreatefromjpeg')) {
                        $sourceImage = @imagecreatefromjpeg($sourcePath);
                    }
                } elseif (str_contains($mime, 'png') || $extension === 'png') {
                    if (function_exists('imagecreatefrompng')) {
                        $sourceImage = @imagecreatefrompng($sourcePath);
                    }
                } elseif (str_contains($mime, 'webp') || $extension === 'webp') {
                    if (function_exists('imagecreatefromwebp')) {
                        $sourceImage = @imagecreatefromwebp($sourcePath);
                    }
                } elseif (str_contains($mime, 'gif') || $extension === 'gif') {
                    if (function_exists('imagecreatefromgif')) {
                        $sourceImage = @imagecreatefromgif($sourcePath);
                    }
                }
            }

            if (! $sourceImage) {
                return $file->store($folder, 'public');
            }

            // Akıllı Telefon EXIF Oryantasyon Düzeltmesi (Telefon yan/ters çekimlerini düzeltir)
            if (function_exists('exif_read_data') && function_exists('imagerotate') && (str_contains($mime, 'jpeg') || in_array($extension, ['jpg', 'jpeg']))) {
                try {
                    $exif = @exif_read_data($sourcePath);
                    if (! empty($exif['Orientation'])) {
                        switch ($exif['Orientation']) {
                            case 3:
                                $rotated = @imagerotate($sourceImage, 180, 0);
                                if ($rotated) {
                                    imagedestroy($sourceImage);
                                    $sourceImage = $rotated;
                                }
                                break;
                            case 6:
                                $rotated = @imagerotate($sourceImage, -90, 0);
                                if ($rotated) {
                                    imagedestroy($sourceImage);
                                    $sourceImage = $rotated;
                                }
                                break;
                            case 8:
                                $rotated = @imagerotate($sourceImage, 90, 0);
                                if ($rotated) {
                                    imagedestroy($sourceImage);
                                    $sourceImage = $rotated;
                                }
                                break;
                        }
                    }
                } catch (\Throwable $e) {
                    // EXIF okuma hatası olursa görmezden gel
                }
            }

            $origWidth = imagesx($sourceImage);
            $origHeight = imagesy($sourceImage);

            if ($origWidth <= 0 || $origHeight <= 0) {
                if (is_resource($sourceImage) || (is_object($sourceImage) && get_class($sourceImage) === 'GdImage')) {
                    imagedestroy($sourceImage);
                }

                return $file->store($folder, 'public');
            }

            // Boyutlandırma hesabı (En-Boy Oranı Kusursuz Korunur)
            $newWidth = $origWidth;
            $newHeight = $origHeight;

            if ($origWidth > $maxWidth || $origHeight > $maxHeight) {
                $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
                $newWidth = (int) round($origWidth * $ratio);
                $newHeight = (int) round($origHeight * $ratio);
            }

            // Yeni tuval oluştur ve şeffaflığı koru
            $targetImage = imagecreatetruecolor($newWidth, $newHeight);

            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
            imagefilledrectangle($targetImage, 0, 0, $newWidth, $newHeight, $transparent);

            // Yüksek kaliteli bicubic yeniden örnekleme
            imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

            // WebP formatı destekleniyorsa doğrudan .webp olarak kaydet
            $canWebP = function_exists('imagewebp');
            $outExt = $canWebP ? 'webp' : ($extension === 'png' ? 'png' : 'jpg');
            $filename = Str::random(40).'.'.$outExt;
            $relativePath = trim($folder, '/').'/'.$filename;

            // Görseli doğrudan hafızada buffer'a render et
            ob_start();
            if ($canWebP) {
                imagewebp($targetImage, null, $quality);
            } elseif ($extension === 'png') {
                $pngQuality = (int) round((100 - $quality) / 10);
                imagepng($targetImage, null, min(9, max(0, $pngQuality)));
            } else {
                imagejpeg($targetImage, null, $quality);
            }
            $imageBuffer = ob_get_clean();

            // Belleği temizle
            if (is_resource($sourceImage) || (is_object($sourceImage) && get_class($sourceImage) === 'GdImage')) {
                imagedestroy($sourceImage);
            }
            if (is_resource($targetImage) || (is_object($targetImage) && get_class($targetImage) === 'GdImage')) {
                imagedestroy($targetImage);
            }

            // Laravel Storage Disk üzerinden kaydet (Klasör izinleri ve yolları otomatik yönetilir)
            if ($imageBuffer && strlen($imageBuffer) > 0) {
                Storage::disk('public')->put($relativePath, $imageBuffer);
                if (Storage::disk('public')->exists($relativePath)) {
                    return $relativePath;
                }
            }

            // Güvenli yedek fallback
            return $file->store($folder, 'public');
        } catch (\Throwable $e) {
            Log::warning('ImageOptimizer error: '.$e->getMessage());

            return $file->store($folder, 'public');
        }
    }

    /**
     * Sitedeki Mevcut Tüm Eski Ürün, Kategori ve Promosyon Resimlerini WebP Formatına Çevirir ve Optimize Eder.
     */
    public static function optimizeExistingStoredImages(): array
    {
        $count = 0;
        $savedKb = 0;

        if (! extension_loaded('gd') || ! function_exists('imagecreatetruecolor')) {
            return ['status' => 'error', 'message' => 'GD Kütüphanesi aktif değil.', 'count' => 0];
        }

        // 1. Ürün Ana Resimleri
        $products = Product::whereNotNull('image_path')->get();
        foreach ($products as $product) {
            $res = self::optimizeSingleStoredFile($product->image_path, 'products', 800, 800, 85);
            if ($res) {
                $product->update(['image_path' => $res['new_path']]);
                $count++;
                $savedKb += $res['saved_kb'];
            }
        }

        // 2. Ürün Galeri Resimleri
        $galleryImages = ProductImage::whereNotNull('image_path')->get();
        foreach ($galleryImages as $img) {
            $res = self::optimizeSingleStoredFile($img->image_path, 'products/gallery', 800, 800, 85);
            if ($res) {
                $img->update(['image_path' => $res['new_path']]);
                $count++;
                $savedKb += $res['saved_kb'];
            }
        }

        // 3. Kategori Resimleri
        $categories = Category::whereNotNull('image_path')->get();
        foreach ($categories as $cat) {
            $res = self::optimizeSingleStoredFile($cat->image_path, 'categories', 600, 600, 85);
            if ($res) {
                $cat->update(['image_path' => $res['new_path']]);
                $count++;
                $savedKb += $res['saved_kb'];
            }
        }

        // 4. Hediye Resimleri
        $giftKeys = ['gift_1_image', 'gift_2_image', 'gift_3_image', 'gift_4_image', 'logo'];
        foreach ($giftKeys as $key) {
            $setting = Setting::where('key', $key)->first();
            if ($setting && ! empty($setting->value)) {
                $res = self::optimizeSingleStoredFile($setting->value, 'settings', 800, 800, 88);
                if ($res) {
                    $setting->update(['value' => $res['new_path']]);
                    $count++;
                    $savedKb += $res['saved_kb'];
                }
            }
        }

        return [
            'status' => 'success',
            'count' => $count,
            'saved_kb' => round($savedKb, 1),
            'message' => "Toplam {$count} adet görsel başarıyla modern WebP formatına dönüştürüldü ve optimize edildi.",
        ];
    }

    /**
     * Tek bir disk dosyasını optimize eder ve yeni yolunu döner.
     */
    private static function optimizeSingleStoredFile(string $relativePath, string $folder, int $maxWidth, int $maxHeight, int $quality): ?array
    {
        $fullPath = storage_path('app/public/'.$relativePath);

        if (! file_exists($fullPath)) {
            return null;
        }

        $origSize = filesize($fullPath);
        $imageInfo = @getimagesize($fullPath);

        if (! $imageInfo) {
            return null;
        }

        $mime = $imageInfo['mime'] ?? '';
        $origWidth = $imageInfo[0];
        $origHeight = $imageInfo[1];

        // Zaten küçük bir webp ise tekrar dönüştürmeye gerek yok
        if ($mime === 'image/webp' && $origWidth <= $maxWidth && $origHeight <= $maxHeight) {
            return null;
        }

        $sourceImage = null;
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                if (function_exists('imagecreatefromjpeg')) {
                    $sourceImage = @imagecreatefromjpeg($fullPath);
                }
                break;
            case 'image/png':
                if (function_exists('imagecreatefrompng')) {
                    $sourceImage = @imagecreatefrompng($fullPath);
                }
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $sourceImage = @imagecreatefromwebp($fullPath);
                }
                break;
        }

        if (! $sourceImage) {
            return null;
        }

        // Boyutlandırma
        $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight, 1.0);
        $newWidth = (int) round($origWidth * $ratio);
        $newHeight = (int) round($origHeight * $ratio);

        $targetImage = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($targetImage, false);
        imagesavealpha($targetImage, true);
        $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
        imagefilledrectangle($targetImage, 0, 0, $newWidth, $newHeight, $transparent);

        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        $canWebP = function_exists('imagewebp');
        $outExt = $canWebP ? 'webp' : 'jpg';
        $newFilename = Str::random(40).'.'.$outExt;
        $newRelativePath = trim($folder, '/').'/'.$newFilename;
        $newFullPath = storage_path('app/public/'.$newRelativePath);

        if ($canWebP) {
            imagewebp($targetImage, $newFullPath, $quality);
        } else {
            imagejpeg($targetImage, $newFullPath, $quality);
        }

        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        $newSize = file_exists($newFullPath) ? filesize($newFullPath) : $origSize;
        $savedKb = max(0, ($origSize - $newSize) / 1024);

        // Eski dosyayı sil (eğer adı/yolu değiştiyse)
        if ($fullPath !== $newFullPath && file_exists($fullPath)) {
            @unlink($fullPath);
        }

        return [
            'new_path' => $newRelativePath,
            'saved_kb' => $savedKb,
        ];
    }
}
