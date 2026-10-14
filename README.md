# 💧 İstanbul Su Arıtma Marketi — E-Ticaret & Hizmet Platformu

Bu proje, **İstanbul Su Arıtma Marketi** (`https://istanbulsuaritmamarketi.com`) için geliştirilmiş; yüksek performanslı, SEO odaklı, Google Ads & Merchant Center entegrasyonlu ve tam teşekküllü modern bir Laravel e-ticaret & kurumsal servis platformudur.

---

## 🏆 Güncel Canlı Metrikler & Performans Başarıları

* 🟢 **Google PageSpeed Performans Skoru:** `96 / 100` *(Yavaş 4G Mobil Cihaz Simülasyonunda)*
* 🟢 **SEO Skoru:** `100 / 100` *(Tam Puan)*
* 🟢 **Erişilebilirlik (A11y):** `96 / 100` *(WCAG 2.1 AA Standartlarında)*
* 🟢 **Ajan Tabanlı Yapay Zeka Taraması:** `2 / 2` *(WebMCP Standartlarında)*
* 📐 **Kümülatif Düzen Kayması (CLS):** `0.000` *(Sıfır Kayma - Kusursuz Görsel Kararlılık)*
* ⚡ **İlk İçerik Boyaması (FCP):** `1.1 sn`
* ⚡ **En Büyük İçerik Boyaması (LCP):** `1.8 sn`
* 🖼️ **Görsel Optimizasyonu:** `772 KB` ➡️ `12 KB WebP` *(%98,5 Boyut Tasarrufu)*

---

## 🚀 Temel Özellikler & Modüller

### 🛒 1. E-Ticaret & Satış Altyapısı
* **Kategori & Ürün Yönetimi:** Çoklu görsel yükleme, dinamik rozetler ("YENİ NESİL", "+ Hediye Seçenekli"), teknik özellik listeleri, filtre değişimi periyodu.
* **Sepet & Sipariş Sistemi:** Kupon kodu indirimleri, ilçe bazlı teslimat, kapıda ödeme (Nakit / Kredi Kartı) ve havale seçenekleri.
* **PayTR Güvenli Ödeme:** 3D Secure kredi kartı ödeme entegrasyonu, webhook callback yönetimi ve sipariş otomatik durum güncellemesi.
* **Sipariş Takibi:** Müşterilerin sipariş numarası ve telefonla canlı kargo/servis durumunu izleyebildiği takip ekranı (`/siparis-takip`).

### 🎯 2. İnteraktif Pazarlama & Müşteri Dönüşüm Araçları
* **💧 İnteraktif Cihaz Bulucu Sihirbazı (Quiz Wizard):** Kullanıcının ev tipi, kişi sayısı ve arıtma ihtiyacına göre en doğru cihazı saniyeler içinde öneren 3 adımlı sihirbaz.
* **💰 Damacana vs Su Arıtma Tasarruf Hesaplayıcısı:** Kullanıcının haftalık damacana tüketimine göre 1 ve 3 yıllık net kârını anlık hesaplayan interaktif simülatör.
* **🎡 Şans Çarkı (Lucky Wheel):** Müşterilerden indirim ve hediye karşılığı telefon/ilçe toplayan potansiyel müşteri (Lead Capture) motoru.
* **🎁 Ücretsiz Montaj & Hediye Promosyon Motoru:** Belirli tutar ve cihaz siparişlerinde otomatik hediye seçimi.

### 🔍 3. Gelişmiş SEO & Arama Motoru Entegrasyonu
* **Dinamik XML Site Haritası (`/sitemap.xml`):** Tüm ürünleri, kategorileri, 39 İstanbul ilçesini ve blog yazılarını otomatik güncelleyen harita.
* **Google Merchant Center XML Feed (`/google-shopping.xml`):** Google Alışveriş sekmesinde ve Google Ads Shopping kampanyalarında otomatik ürün listeleme.
* **39 İlçe Özel SEO Sayfaları (`/istanbul-su-aritma/{ilce}`):** Kadıköy, Ümraniye, Esenyurt vb. ilçelere özel aramalarda 1. sırada çıkmayı sağlayan içerik mimarisi.
* **Dinamik Schema.org & OpenGraph:** `Product`, `AggregateRating`, `Organization`, `LocalBusiness` JSON-LD zengin arama sonuçları.

### 🛡️ 4. Google Ads, Analytics & İzleme Mimarisi
* **Google Tag Manager (`GTM-M66JL23D`):** Tek merkezden yönetilen modern etiket konteyneri.
* **Google Ads Dönüşüm Takibi (`AW-307666873`):** Telefon aramaları, WhatsApp tıklamaları, form gönderimleri ve satın alma dönüşüm pikselleri.
* **Google Analytics 4 (`G-E4301FGKEK`):** Canlı e-ticaret etkinlikleri ve kitle analizleri.
* **Meta Pixel:** Facebook ve Instagram reklam dönüşümleri için entegre takip.

### ⚡ 5. Hız, Görsel & Önbellek Motoru (`ImageOptimizerService`)
* **Otomatik WebP Dönüştürücü:** Admin panelinden 10 MB'a kadar yüklenen devasa PNG/JPG fotoğraflarını kalite kaybı olmadan maksimum 800-1200 piksele boyutlandırıp WebP'ye sıkıştıran yerel GD motoru.
* **1 Tıkla Toplu Optimizasyon:** `/admin/marketing/performance` sayfasından tüm eski görselleri tek tıkla tarayıp optimize eden batch converter.
* **Yerel Hızlı Fontlar (Self-Hosted Figtree):** Dış ağ zincirlerini yok eden yerel WOFF2 font motoru.
* **Cloudflare Edge Cache Kuralı:** Statik dosyaları (`/build/`, `/storage/`, `/fonts/`) Cloudflare İstanbul RAM önbelleğinde 1 ay saklayan kural.

---

## 🛠️ Teknoloji Yığını (Tech Stack)

* **Backend:** PHP 8.3 / 8.5, Laravel 12 (MVC, Eloquent ORM, Blade)
* **Frontend:** TailwindCSS v4, Alpine.js v3, Vite v8
* **Veritabanı:** MySQL / MariaDB
* **Önbellek & Güvenlik:** Cloudflare CDN & WAF, Apache `.htaccess` Immutable Cache
* **Ödeme Gateway:** PayTR iFrame API
* **Görsel İşleme:** PHP GD / Bicubic Resampling / WebP Converter

---

## 📂 Dizin Yapısı & Dağıtım Mimarisi

Cenuta cPanel sunucusunda güvenli **Çift Dizin (Dual Directory)** mimarisi kullanılmaktadır:

```text
/home/istanbul30/
├── laravel_core/         # Tüm PHP kodları, Controller'lar, Model'ler, View'lar (.env gizli)
└── public_html/          # Yalnızca dışarıya açık statik dosyalar (index.php, build/, storage/, fonts/, .htaccess)
```

---

## 💻 Sunucu Dağıtım & Güncelleme Komutları (Cenuta SSH)

Yerel bilgisayarda yapılan değişiklikleri canlı sunucuya aktarmak için:

```bash
cd /home/istanbul30/laravel_core
git pull origin main

# 1. Yeni .htaccess (1 Yıllık Önbellek & GZIP)
cp public/.htaccess /home/istanbul30/public_html/

# 2. Derlenmiş CSS ve JS Dosyaları
rm -rf /home/istanbul30/public_html/build
cp -r public/build /home/istanbul30/public_html/

# 3. Yerel Font Dosyaları
mkdir -p /home/istanbul30/public_html/fonts
cp -r public/fonts/* /home/istanbul30/public_html/fonts/

# 4. Storage Linki & Önbellek Sıfırlama
php artisan optimize:clear
```

---

## 🔑 Önemli Yönetim Paneli Linkleri

* **Dashboard (Ana Panel):** `https://istanbulsuaritmamarketi.com/dashboard`
* **Hız & Performans Merkezi (Start/Stop Optimize):** `https://istanbulsuaritmamarketi.com/admin/marketing/performance`
* **SEO & Analitik Kodları (GTM/GA4/Meta):** `https://istanbulsuaritmamarketi.com/admin/marketing/seo`
* **Cihaz Bulucu Sihirbazı Ayarları:** `https://istanbulsuaritmamarketi.com/admin/marketing/quiz`
* **Şans Çarkı & Hediyeler:** `https://istanbulsuaritmamarketi.com/admin/marketing/wheel`
* **Sipariş & Kargo Yönetimi:** `https://istanbulsuaritmamarketi.com/admin/orders`
* **Müşteri Servis Talepleri:** `https://istanbulsuaritmamarketi.com/admin/service-requests`

---

*Son Güncelleme: 31 Ağustos 2026 — Sürüm 2.4.0 (Ultra Performance & SEO Edition)*
