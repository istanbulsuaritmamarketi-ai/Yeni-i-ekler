# 💻 YENİÇİÇEK — TEKNOLOJİ VE ALTYAPI BAĞLAMI (TECH CONTEXT)

> **Belge:** `memory/techContext.md`  
> **Son Güncelleme:** 04.10.2026  
> **Kapsam:** Teknoloji Yığını, Geliştirme Araçları, Yerel Ortam, Apache Sanal Konak & Dağıtım  

---

## 🛠️ 1. TEKNOLOJİ YIĞINI (TECH STACK)

| Katman | Teknoloji | Sürüm | Rol / Amaç |
| :--- | :--- | :--- | :--- |
| **Dil** | PHP | 8.4.25 / 8.5.0 | Yüksek performanslı backend motoru |
| **Framework** | Laravel | 13.x (Master Core altyapısı) | MVC omurgası, ORM, Auth, Routing |
| **Veritabanı** | SQLite (Yerel) / MySQL (Canlı) | SQLite 3.x / MySQL 8.4 | Yerelde sıfır konfigürasyon SQLite, canlıda MySQL |
| **CSS Motoru** | TailwindCSS | v4.x | Modern CSS değişkenleri, ferah `rose` teması |
| **JS Motoru** | Alpine.js | v3.x | Reaktif sepet, modallar, tarih/saat seçici |
| **Paketleyici** | Vite | v8.2.2 | Optimize üretim derlemesi (`npm run build`) |
| **Ödeme** | PayTR iFrame | Hosted Token API | 3D Secure kredi kartı & taksit tahsilatı |
| **Web Sunucusu** | Apache 2.4.68 (Laragon) | Yerel Dağıtım | `http://yenicicek.test` |

---

## 📂 2. DİZİN YAPISI VE SANAL KONAK MİMARİSİ

- **Kaynak Çekirdek:** `C:\laragon\www\yigit-core`
- **Yeni Proje Dizini:** `C:\laragon\www\Yenicicek`
- **Apache Sanal Konak Yapılandırması (`C:\laragon\etc\apache2\sites-enabled\auto.Yenicicek.test.conf`):**
  - `ServerName Yenicicek.test`
  - `ServerAlias yenicicek.test *.yenicicek.test *.Yenicicek.test` (Büyük/küçük harf duyarsız tam eşleşme)
  - `DocumentRoot "C:/laragon/www/Yenicicek/public"`
- **Windows Hosts (`C:\Windows\System32\drivers\etc\hosts`):**
  - `127.0.0.1 Yenicicek.test`
  - `127.0.0.1 yenicicek.test`
- **Konfigürasyon İlkeleri (`.env`):**
  - `APP_NAME="Yeniçiçek"`, `APP_URL=http://yenicicek.test`, `THEME=rose`, `SITE_MODE=ecommerce`
  - `SESSION_DRIVER=database`, Cookie: `yenicicek-session`

---

## ⚡ 3. ÇALIŞMA VE TEST KOMUTLARI

```bash
# Web Sunucusu Başlatma (Laragon Apache Daemon)
C:\laragon\bin\apache\httpd-2.4.68-260617-Win64-VS18\bin\httpd.exe -d C:/laragon/bin/apache/httpd-2.4.68-260617-Win64-VS18

# Bağımlılıkları ve veritabanını hazırlama
php artisan migrate
php artisan db:seed

# Frontend Derleme
npm run build

# Test ve Otonom Denetim
php artisan test --compact
php artisan site:audit
```
