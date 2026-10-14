# 🌸 YENİÇİÇEK — AJAN KOKPİTİ VE GELİŞTİRME REHBERİ

> **Proje:** Yeniçiçek (Çiçeksepeti & HızlıÇiçek Standardında Çiçek E-Ticaret Platformu)  
> **Yerel Dağıtım URL:** `http://yenicicek.test`  
> **Çalışma Dizini:** `C:\laragon\www\Yenicicek`  
> **Backend Omurgası:** Yiğit Ajans Core (`C:\laragon\www\yigit-core`)  
> **Teknoloji:** PHP 8.5 | Laravel 13 | TailwindCSS v4 | Alpine.js v3 | SQLite/MySQL | PayTR  

---

## 🧭 1. PROJE VİZYONU & MİMARİ FELSEFE
Yeniçiçek, arka planda **Yiğit Core (YAC)** modüler e-ticaret omurgasını (Sepet, PayTR, Kupon, İlçe Kurye, Sipariş Yönetimi, KVKK) kullanırken; ön yüzde **Çiçeksepeti** ve **HızlıÇiçek** tarzında yüksek dönüşümlü, hızlı, güven veren, zarif ve çiçekçiliğe özel bir kullanıcı deneyimi sunar.

### Ön Yüz (Frontend) Standartları:
- **Tasarım İlhamı:** Çiçeksepeti & HızlıÇiçek akıcılığı, ferah pastel ve çiçek tonları (`THEME=rose`), zümrüt yeşili doğallık.
- **Kritik Çiçekçi Bileşenleri:**
  1. Header ilçe / teslimat semti seçici ("Teslimat Yeri: İstanbul / Kadıköy")
  2. Teslimat tarihi (Bugün, Yarın, İleri Tarih) ve saat aralıkları seçici (09:00-12:00, 12:00-15:00, 15:00-18:00, 18:00-22:00)
  3. Zarflı kart notu & duygu şablonları modalı (Doğum günü, aşk, özür, tebrik)
  4. Çapraz satış (Upsell) eklentileri (Butik çikolata, peluş ayıcık, balon, vazo)
  5. 2 Adımlı hızlı sepet & checkout
  6. Fotoğraflı teslimat ve kurye takip ekranı (`/siparis-takip`)

---

## 📂 2. ANTİGRAVİTY HAFIZA BANKASI
- 📜 Proje Özeti: [`memory/projectbrief.md`](file:///c:/laragon/www/Yenicicek/memory/projectbrief.md)
- 🏛️ Sistem Mimarisi & Desenler: [`memory/systemPatterns.md`](file:///c:/laragon/www/Yenicicek/memory/systemPatterns.md)
- 💻 Teknoloji Bağlamı: [`memory/techContext.md`](file:///c:/laragon/www/Yenicicek/memory/techContext.md)
- ⚡ Aktif Bağlam: [`memory/activeContext.md`](file:///c:/laragon/www/Yenicicek/memory/activeContext.md)
- 📊 İlerleme: [`memory/progress.md`](file:///c:/laragon/www/Yenicicek/memory/progress.md)

---

## ⚡ 3. TEMEL KOMUTLAR

```bash
# Frontend derleme
npm run build
npm run dev

# Veritabanı işlemleri
php artisan migrate
php artisan db:seed

# Kalite ve test
php artisan site:audit
php artisan test --compact
```
