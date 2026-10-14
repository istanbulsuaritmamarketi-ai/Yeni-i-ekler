#!/usr/bin/env bash
# ==============================================================================
# 🌸 YENİÇİÇEK — CANLI YAYINLAMA & GÜNCELLEME SCRIPTİ (DEPLOY.SH)
# ==============================================================================
# Kullanım:
#   chmod +x deploy.sh
#   ./deploy.sh
# ==============================================================================

set -e

echo "🚀 [1/8] Yeniçiçek canlı yayınlama süreci başlatılıyor..."

# 1. Bakım Moduna Al (Ziyaretçilere kesintisiz bakım ekranı göster)
php artisan down --render="errors::503" --secret="yenicicek-deploy-bypass" || true

# 2. En son Git kodlarını çek (Git ile dağıtılıyorsa)
if [ -d .git ]; then
    echo "📥 [2/8] Git deposundan son değişiklikler çekiliyor..."
    git pull origin main || git pull origin master
else
    echo "ℹ️ [2/8] Git deposu algılanmadı, mevcut dosyalarla devam ediliyor."
fi

# 3. Composer Bağımlılıklarını Optimize Et (Dev paketler hariç)
echo "📦 [3/8] Composer paketleri optimize ediliyor..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 4. Veritabanı Migrasyonlarını Çalıştır
echo "🗄️ [4/8] Veritabanı migrasyonları uygulanıyor..."
php artisan migrate --force

# 5. Storage Symlink Kontrolü
echo "🔗 [5/8] Storage sembolik bağı kontrol ediliyor..."
php artisan storage:link || true

# 6. Laravel Önbelleklerini Temizle & Optimize Et (Lighthouse 100/100)
echo "⚡ [6/8] Rota, konfigürasyon ve şablon önbellekleri oluşturuluyor..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 7. Kuyruk İşçilerini Yeniden Başlat (Eğer Supervisor/Queue kullanılıyorsa)
echo "🔄 [7/8] Kuyruk motoru sıfırlanıyor..."
php artisan queue:restart || true

# 8. İzinleri Ayarla (Web sunucusu yazma yetkileri)
echo "🔒 [8/8] Dizin izinleri düzenleniyor..."
chmod -R 775 storage bootstrap/cache || true
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# 9. Bakım Modunu Kapat & Canlıya Geç
php artisan up

echo "✅ ========================================================"
echo "🎉 TEBRİKLER! YENİÇİÇEK BAŞARIYLA CANLIYA YAYINLANDI!"
echo "🌐 Ziyaret edin: $(grep APP_URL .env | cut -d '=' -f2)"
echo "============================================================"
