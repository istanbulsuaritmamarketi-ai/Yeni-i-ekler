# 🏛️ YENİÇİÇEK — SİSTEM MİMARİSİ VE TASARIM DESENLERİ (SYSTEM PATTERNS)

> **Belge:** `memory/systemPatterns.md`  
> **Son Güncelleme:** 01.10.2026  
> **Kapsam:** Çiçekçi E-Ticaret Modülleri, Veri Akışı, Teslimat Mantığı ve Arayüz Bileşenleri  

---

## 🏗️ 1. GENEL MİMARİ OMURGA

Yeniçiçek, **Yiğit Core (YAC)** modüler mimarisini temel alır ve e-ticaret çiçekçilik dikeyine özel geliştirilmiş ek katmanlarla zenginleştirilir:

```
[ Ziyaretçi / Mobil Web ] 
         │
         ▼
[ Çiçeksepeti Tarzı UI (Tailwind v4 + Alpine.js) ]
 ├── Teslimat İlçe/Zaman Seçici Widget
 ├── Kart Notu & Duygu Şablonları Modalı
 ├── Çapraz Satış (Upsell) Eklenti Seçici
 └── 2 Adımlı Hızlı Sepet / Checkout
         │
         ▼
[ Laravel 13 Uygulama Katmanı ]
 ├── CartController (Sepet + Teslimat Bilgisi + Upsell)
 ├── CheckoutController (PayTR Pos + Kapıda Ödeme)
 ├── OrderTrackingController (Fotoğraflı Teslimat Akışı)
 └── Admin Controllers (Sipariş, Ürün, Kupon, İlçe Yönetimi)
         │
         ▼
[ Veritabanı & Model Katmanı (SQLite / MySQL) ]
 ├── products, categories, product_images
 ├── orders, order_items (delivery_date, delivery_slot, card_note, sender_name, is_anonymous)
 ├── gifts / add_ons (çikolata, ayıcık, balon, vazo)
 ├── districts & regions (teslimat ücretleri & süreleri)
 └── cookie_consents, activity_logs, reviews
```

---

## 🌺 2. ÇİÇEKÇİLİĞE ÖZEL KRİTİK VERİ VE DAVRANIŞ DESENLERİ

### A. Teslimat Takvimi & Zaman Dilimi Deseni (Delivery Slot Pattern)
Çiçek e-ticaretinde ürün sepete eklenirken veya ödemede şu bilgiler zorunludur:
- `delivery_city`: İl (Varsayılan: İstanbul)
- `delivery_district`: İlçe (Kadıköy, Beşiktaş, Esenyurt vb.)
- `delivery_date`: YYYY-MM-DD formatında teslimat günü (Bugün, Yarın veya takvimden seçilen gün)
- `delivery_time_slot`: Teslimat saat aralığı (09:00-12:00, 12:00-15:00, 15:00-18:00, 18:00-22:00 veya VIP Özel Saat)

### B. Kart Notu ve Zarf Deseni (Card Note Pattern)
- `recipient_name`: Alıcı Adı Soyadı
- `recipient_phone`: Alıcı Telefonu
- `card_note`: Çiçek kartına el yazısıyla yazılacak mesaj (Maks. 250 karakter)
- `card_template_id`: Hazır mesajlardan seçilen şablon (Örn: "Doğum Günün Kutlu Olsun Birtanem")
- `sender_name`: Gönderen Adı Soyadı
- `is_anonymous`: Gönderici adı alıcıya gizlensin mi? (Boolean `true`/`false`)

### C. Çapraz Satış (Upsell / Add-ons Pattern)
- Ürün detay sayfasında ve sepette çiçeğe uyumlu ek ürünlerin (`gifts`) 1-tıkla sepete eklenmesi:
  - Peluş Ayıcık
  - Premium Çikolata Kutusu
  - Balon (Kalpli / Happy Birthday)
  - Özel Cam Vazo

### D. Fotoğraflı Hazırlık & Teslimat Kütüğü (Photo Proof Pattern)
- Sipariş hazırlandığında atölye tarafından yüklenen `preparation_photo`
- Müşterinin `/siparis-takip/{order_number}` üzerinden hazırlanan çiçeği yola çıkmadan görmesi

---

## 🎨 3. ÇİÇEKSEPETİ & HIZLIÇİÇEK BENZERİ FRONTEND BİLEŞENLERİ

1. **Header & Hızlı Arama Barı:**
   - Teslimat Yeri Butonu ("📍 İstanbul, Kadıköy")
   - Arama kutusu + popüler arama etiketleri
   - Sipariş Takip linki
   - Sepet çekmecesi (Drawer / Dropdown)

2. **Hızlı Kategori Karuseli (Iconic Categories):**
   - Yuvarlak görselli / ikonlu kategori hapları: Güller, Orkide, Papatya, Saksı Bitkileri, Doğum Günü, Yıldönümü, Çikolata

3. **Güven ve Teslimat Rozetleri (Trust Bar):**
   - "Aynı Gün Ücretsiz Teslimat"
   - "Fotoğraflı Teslimat Güvencesi"
   - "Taze Çiçek Garantisi"
   - "7/24 Müşteri Desteği"

4. **Hediye / Çiçek Sihirbazı (Gift Finder Widget):**
   - Kime? (Sevgiliye, Anneye, Arkadaşa)
   - Ne zaman? (Bugün, Yarın)
   - Bütçe? (₺, ₺₺, ₺₺₺)
   - Sonuçları tek tıkla filtreleme

5. **Zengin Ürün Kartları:**
   - Çift görsel hover efekti
   - İndirim yüzdesi ve rozetler
   - Net fiyatlandırma ve "Aynı Gün Teslimat" vurgusu
   - Hızlı Sepete Ekle / "Hemen Gönder" aksiyonu

---

## 📋 4. MASAÜSTÜ GELİŞİM RAPORU DESENİ (DESKTOP PROGRESS REPORT)
- Kullanıcının diğer projelerinde (`PETEK_GELISIM_RAPORU.md`, `YIGIT_CORE_GELISIM_RAPORU.md`) olduğu gibi, Yeniçiçek projesine ait tüm aşamalar, metrikler, zaman tüneli ve sıradaki adımlar doğrudan kullanıcının Masaüstündeki şu dosyada tutulur:
  - **Konum:** `C:\Users\yigit\Desktop\YENICICEK_GELISIM_RAPORU.md`
- Yapılan her majör geliştirme ve güncelleme sonrasında bu dosya düzenli ve senkronize olarak güncellenir.
