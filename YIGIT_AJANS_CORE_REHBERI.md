# 👑 YİĞİT AJANS CORE (YAC) - RESMİ MİMARİ VE KULLANIM REHBERİ

**Yiğit Ajans Core**, Yiğit Dijital Ajans'ın tüm sektörlerdeki (E-Ticaret, Çiçekçi, İnşaat & Mimarlık, Su Arıtma, Dijital Ajans) sitelerini **tek tıkla ve sıfır kod tekrarıyla** ayağa kaldırmak için inşa edilmiş **Nötr, Modüler ve Dinamik Temalı Master Yazılım Motorudur.**

---

## 🎨 1. DİNAMİK RENK & TEMA MOTORU (CSS Variables)

Artık hiçbir renk hardcoded değildir. `.env` dosyasındaki `THEME` değişkeni ile tüm sitenin renk paletini 1 saniyede değiştirebilirsin:

```env
# 🌸 1. ÇİÇEKÇİ MODU (Gül Kurusu & Zümrüt Yeşili)
THEME=rose

# 🏗️ 2. İNŞAAT & MİMARLIK MODU (Lüks Antrasit & Altın Sarısı)
THEME=amber

# 👑 3. AJANS & TEKNOLOJİ MODU (Modern İndigo & Turkuaz)
THEME=indigo

# 💧 4. SU ARITMA / HİZMET MODU (Gök Mavisi & Nane Yeşili)
THEME=sky
```

---

## 🎛️ 2. SİHİRLİ MOD DEĞİŞTİRİCİ (Feature Toggle)

Sitenin fonksiyonel kimliğini tek satırla belirle:

```env
SITE_MODE=ecommerce    # Sepet, ürün fiyatları, sipariş takibi ve online satış vitrini
SITE_MODE=corporate    # Sepet gizlenir, Projeler/Portföy açılır, "Teklif Al" ve WhatsApp lead formu gelir
```

### Karşılaştırma Tablosu:
| Özellik | `SITE_MODE=ecommerce` (Çiçek & Ürün) | `SITE_MODE=corporate` (Onur/Reform İnşaat & Ajans) |
| :--- | :--- | :--- |
| **Ana Menü** | Kategoriler, Ürünler, Sepet, Takip | Projelerimiz, Kurumsal, Blog, Teklif Al |
| **Hero Vitrini** | Popüler Ürünler & Kampanya Kartları | Prestijli Şirket Başlığı & Portföy Butonları |
| **Vitrin İçeriği** | İndirimli Ürünler, Sepete Ekle Butonu | Tamamlanan Projeler, Kat Planı, WhatsApp Teklifi |
| **Arama Çubuğu** | Ürün / Kategori Arama | Gizlenir (Sade & Ağırbaşlı Kurumsal Görünüm) |
| **Yüzen AI Butonu** | "Flora AI" (Hediye Danışmanı) | "Mimar AI" (Proje & Keşif Danışmanı) |

---

## 🏗️ 3. PROJELER & PORTFÖY MODÜLÜ (`/projeler`)

İnşaat ve kurumsal müşteriler için geliştirilen özel vitrin:
* **Rota:** `http://yigit-ajans-core.test/projeler`
* **Özellikler:**
  * Kategori filtreleme (*Konut, Villa, Ticari, Tadilat*).
  * Proje durumu rozetleri (*Tamamlandı, Devam Ediyor, Proje Aşamasında*).
  * Kat planları ve görsel galerisi (`gallery` JSON alanı).
  * Proje künyesi (m², daire sayısı, teslim yılı).
  * Projeye özel doğrudan WhatsApp ve Teklif İsteme formu.

---

## ⚖️ 4. HUKUKİ UYUM & TİCARET BAKANLIĞI STANDARTLARI

Tüm sipariş ve form doldurma işlemlerinde resmi tüketici koruma verileri veritabanında saklanır:
* `customer_ip`: Müşterinin gerçek IP adresi
* `customer_user_agent`: Hangi cihaz ve tarayıcıyla onayladı
* `kvkk_accepted_at`: KVKK açık rıza zaman damgası
* `contract_accepted_at`: Mesafeli Satış Sözleşmesi onay zamanı

---

## 🚀 5. YENİ BİR MÜŞTERİ SİTESİ NASIL AYAĞA KALDIRILIR?

Örnek: **Onur İnşaat** için site kuruyorsun:

1. **Klasörü Klonla:**
   ```bash
   robocopy "C:\laragon\www\yigit-ajans-core" "C:\laragon\www\onur-insaat" /E /XD vendor node_modules storage\logs
   ```
2. **Kütüphaneleri ve Anahtarı Bağla:**
   ```bash
   cd C:\laragon\www\onur-insaat
   robocopy "C:\laragon\www\yigit-ajans-core\vendor" "C:\laragon\www\onur-insaat\vendor" /E
   php artisan key:generate
   ```
3. **`.env` Dosyasını Ayarla:**
   ```env
   APP_NAME="Onur İnşaat"
   APP_URL=http://onur-insaat.test
   THEME=amber
   SITE_MODE=corporate
   ```
4. **Bitti!** `http://onur-insaat.test` anında lüks bir inşaat firması vitrini olarak yayında.
