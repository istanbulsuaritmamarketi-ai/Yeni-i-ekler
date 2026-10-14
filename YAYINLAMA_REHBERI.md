# 🌸 YENİÇİÇEK — CANLI SUNUCUYA YAYINLAMA REHBERİ (PRODUCTION DEPLOYMENT)

Bu rehber, **Yeniçiçek** e-ticaret platformunu canlı sunucuya (VPS, Linux Sunucu, cPanel veya Plesk) eksiksiz ve en yüksek performansla yayınlamanız için gereken tüm komutları ve adımları içerir.

---

## 🗂️ 1. YAYINLAMA İÇİN HAZIRLANAN DOSYALAR

| Dosya | Açıklama |
|---|---|
| [`deploy.sh`](file:///c:/laragon/www/Yenicicek/deploy.sh) | Linux / VPS sunucular için tek komutla otomatik yayınlama & güncelleme scripti. |
| [`.env.production.example`](file:///c:/laragon/www/Yenicicek/.env.production.example) | Canlı ortam ayarları (MySQL, PayTR, SMTP, Güvenlik, SSL) için hazır şablon. |
| [`.htaccess`](file:///c:/laragon/www/Yenicicek/.htaccess) | Kök dizin Apache/cPanel proxy yönlendiricisi ve güvenlik filtresi. |
| [`public/.htaccess`](file:///c:/laragon/www/Yenicicek/public/.htaccess) | Gzip sıkıştırma, 1 yıl tarayıcı önbellekleme ve genel HTTPS 301 yönlendirmesi. |
| [`nginx.conf.example`](file:///c:/laragon/www/Yenicicek/nginx.conf.example) | Nginx için HTTP/2, SSL, güvenlik başlıkları ve PHP-FPM yapılandırması. |
| [`supervisor.conf.example`](file:///c:/laragon/www/Yenicicek/supervisor.conf.example) | Arka plan kuyruk işçisi (E-posta, SMS, görsel optimizasyon) yapılandırması. |
| [`package_release.ps1`](file:///c:/laragon/www/Yenicicek/package_release.ps1) | Yerel bilgisayardan gereksiz dosyaları arındırarak sunucuya yüklemeye hazır temiz ZIP çıkaran script. |

---

## 🚀 2. SUNUCUDA ÇALIŞTIRILACAK YAYIN KODLARI (SSH / TERMINAL)

### Adım 1: Proje Dosyalarını Sunucuya Alma
```bash
# Git ile çekiyorsanız:
git clone <repo-url> /var/www/yenicicek
cd /var/www/yenicicek

# Veya ZIP ile yüklediyseniz:
cd /var/www/yenicicek
unzip yenicicek_release.zip
```

### Adım 2: Çevre Değişkenlerini (.env) Ayarlama
```bash
# Canlı şablonu kopyalayın
cp .env.production.example .env

# .env dosyasını nano veya vim ile açıp veritabanı ve PayTR bilgilerinizi girin:
nano .env

# Uygulama anahtarını üretin
php artisan key:generate
```

### Adım 3: Bağımlılıkları Kurma & Optimize Etme
```bash
# Üretim moduyla composer paketlerini kurun (Dev paketler hariç)
composer install --no-dev --optimize-autoloader --no-interaction

# (Eğer sunucuda npm/node varsa ve yerelden build yüklemediyseniz)
npm run build
```

### Adım 4: Veritabanı ve Dosya Bağlantıları
```bash
# Tabloları oluşturun
php artisan migrate --force

# (İsteğe bağlı) İlk kurulumda temel kategorileri ve Türkiye il/ilçelerini eklemek için:
php artisan db:seed --force

# Yüklenen fotoğrafların webden erişimi için storage sembolik bağını oluşturun
php artisan storage:link
```

### Adım 5: Canlı Ortam Hızlandırma & Önbellek Kodları (Lighthouse 100/100)
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Adım 6: Dosya ve Dizin İzinleri
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## ⚡ 3. OTOMATİK TEK KOMUTLA YAYINLAMA (`deploy.sh`)

Sunucuya bağlandığınızda gelecekteki tüm güncellemeleri tek tıkla yapmak için:
```bash
chmod +x deploy.sh
./deploy.sh
```
Bu script sırasıyla siteyi bakım moduna alır, kodları çeker, migrasyonları uygular, önbellekleri yeniler ve siteyi tekrar yayına açar.

---

## ⏰ 4. CRON JOB (ZAMANLANMIŞ GÖREVLER) AYARI

Sunucunuzda `crontab -e` komutunu çalıştırın ve en alta şu satırı ekleyin:
```bash
* * * * * cd /var/www/yenicicek && php artisan schedule:run >> /dev/null 2>&1
```
> Bu satır; teslimat saati geçmiş siparişlerin durumunu güncellemek, gecikmiş sipariş uyarıları göndermek ve sistem temizliklerini yürütmek için her dakika çalışır.

---

## 💳 5. PAYTR SANAL POS AYARLARI

Canlıya geçtikten sonra PayTR Mağaza Paneli'nde:
1. **Bildirim URL (Callback URL):**  
   `https://alanadiniz.com/paytr/callback` olarak kaydedilmelidir.
2. **.env Dosyası:**  
   `PAYTR_TEST_MODE=0` yapılmalıdır.

---

## 🖥️ 6. CPANEL / STANDART HOSTİNG İÇİN HIZLI YAYINLAMA

1. Bilgisayarınızda PowerShell ile `powershell .\package_release.ps1` komutunu çalıştırın.
2. Oluşan `.zip` dosyasını cPanel **Dosya Yöneticisi** ile `public_html` dizinine yükleyip çıkartın.
3. cPanel **MySQL Veritabanları** bölümünden yeni veritabanı ve kullanıcı açın.
4. `.env` dosyasını düzenleyip veritabanı adını ve şifresini yazın.
5. cPanel **Terminal** veya **SSH** ekranından:
   ```bash
   php artisan migrate --force
   php artisan storage:link
   php artisan optimize
   ```
   komutlarını çalıştırın. Projeniz kök dizindeki `.htaccess` sayesinde doğrudan güvenli şekilde yayına girecektir.
