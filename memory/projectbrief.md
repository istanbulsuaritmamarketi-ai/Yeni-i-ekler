# 🌸 YENİÇİÇEK — PROJE ÖZETİ (PROJECT BRIEF)

> **Belge:** `memory/projectbrief.md`  
> **Son Güncelleme:** 04.10.2026  
> **Durum:** 🟢 Aktif & Canlıda (Backend, Çiçeksepeti Standartları, 1536px Düzeni, Atölye Komuta Paneli Tamamlandı)  
> **Proje Sahibi:** Yiğit Uzar (Yiğit Dijital Ajans)  
> **Yerel Domain:** `http://yenicicek.test`  
> **Dizin:** `C:\laragon\www\Yenicicek`  
> **Sistem Sağlık Skoru:** %90 (Kritik Hata: 0, Testler: 36/36 Başarılı)  

---

## 🎯 1. VİZYON VE PROJE AMACI
**Yeniçiçek (`yenicicek.test`)**, Türkiye'nin lider çiçek e-ticaret platformları olan **Çiçeksepeti** ve **HızlıÇiçek**'in kullanıcı alışkanlıklarını, hızlı satın alma ergonomisini ve zengin hediye deneyimini örnek alan; arka planda ise **Yiğit Core (YAC)**'ın sağlam, güvenli ve modüler e-ticaret altyapısını kullanan yeni nesil butik çiçek e-ticaret platformudur.

### Temel Hedefler:
1. **Çiçeksepeti & HızlıÇiçek Seviyesinde Frontend:** 
   - İlçe/Semt bazlı teslimat tarihi & saat dilimi seçici (Bugün 09:00-12:00, 12:00-15:00 vb.).
   - Zarflı çiçek kartı notu yazma ve hazır duygu mesajları (Doğum günü, aşk, tebrik, özür).
   - Sipariş öncesi ve kurye aşamasında fotoğraflı teslimat onay rozetleri.
   - Birlikte gönderilebilir çapraz satış (Upsell) eklentileri (Çikolata, peluş oyuncak, vazo, balon).
   - Mobile-first, hızlı, akıcı ve yüksek dönüşümlü (High Conversion Rate) sepet & ödeme akışı.

2. **Yiğit Core Master Backend Omurgası:**
   - Sipariş yönetimi, PayTR sanal POS ve kapıda ödeme motoru.
   - Kupon ve kampanya yönetim sistemi.
   - İstanbul ilçe bazlı kurye & teslimat ücretlendirme mantığı.
   - Müşteri IP, User-Agent ve zaman damgalı 6698 KVKK & Mesafeli Satış Sözleşmesi kütüğü.
   - Ziyaretçi geri kazanma araçları (Şans Çarkı, SMS/WhatsApp bildirimleri).
   - Güçlü, pratik Türkçe admin yönetim kokpiti (`/admin`).

3. **Üst Düzey Performans ve SEO:**
   - Google PageSpeed 95+ mobil/masaüstü performansı.
   - CLS: 0.000, WebP görseller, Tailwind CSS v4 ve hafif Alpine.js reaktivitesi.
   - İlçe bazlı yerel çiçekçi iniş sayfaları ve Google Merchant XML ürün beslemeleri.

---

## 👥 2. KULLANICI PROFİLLERİ & KULLANIM SENARYOLARI
- **Hızlı Hediye Gönderen Ziyaretçi:** Son dakika doğum günü veya özel gün için aynı gün 2 saatte teslim edilecek çiçeği seçer, ilçesini girer, saatini seçer, kart notunu yazar ve 60 saniyede siparişi tamamlar.
- **Planlı Hediye Gönderen Müşteri:** İleri bir tarih için (örneğin 3 gün sonraki evlilik yıldönümü) siparişini şimdiden rezerve eder, çiçeğin yanına butik çikolata ekler.
- **Çiçek Atölyesi & Yönetici:** Admin panelinden yeni siparişleri görür, hazırlanan buketi fotoğraflayıp sisteme ekler, kuryeye teslim eder ve durumu günceller.
