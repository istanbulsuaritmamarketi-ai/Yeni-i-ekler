<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\GiftController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\MarketingController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\ServiceRequestController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SiteAuditController;
use App\Http\Controllers\Admin\SiteMatrixController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WorkshopController;
use App\Http\Controllers\ArimoController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CookieConsentController;
use App\Http\Controllers\CustomBouquetController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PayTRController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kategori/{slug}', [FrontendController::class, 'category'])->name('category.show');
Route::get('/kategoriler', [FrontendController::class, 'categories'])->name('categories.index');
Route::get('/tasarruf-hesapla', [FrontendController::class, 'calculator'])->name('calculator');
Route::get('/cihaz-sihirbazi', [FrontendController::class, 'wizard'])->name('wizard');
Route::get('/filtre-bulucu', [FrontendController::class, 'filterFinder'])->name('filter.finder');
Route::get('/marka/{brand:slug}', [FrontendController::class, 'brandShow'])->name('brand.show');
Route::get('/arimo', [ArimoController::class, 'index'])->name('arimo');
Route::post('/arimo/ask', [ArimoController::class, 'ask'])->name('arimo.ask');
Route::get('/urunler', [ProductController::class, 'index'])->name('products.index');
Route::get('/urun/{slug}', [FrontendController::class, 'product'])->name('product.show');
Route::get('/sayfa/{slug}', [FrontendController::class, 'page'])->name('page.show');
Route::get('/hizmet-bolgeleri', [DistrictController::class, 'index'])->name('districts.index');
Route::get('/ilceler', [DistrictController::class, 'index']);
Route::get('/bolge/{slug}', [DistrictController::class, 'show'])->name('district.show.neutral');
Route::get('/istanbul-su-aritma/{slug}', [DistrictController::class, 'show'])->name('district.show');
Route::get('/siparis-takip', [FrontendController::class, 'trackOrder'])->name('order.track');
Route::get('/servis-talebi', [ServiceController::class, 'create'])->name('service.create');
Route::post('/servis-talebi', [ServiceController::class, 'store'])->name('service.store');
Route::get('/sosyal-medya', [FrontendController::class, 'social'])->name('social');

// 🌸 Kendi Buketini Kendin Tasarla / Oluştur Rotaları
Route::get('/kendi-buketini-olustur', [CustomBouquetController::class, 'index'])->name('custom-bouquet.index');
Route::get('/buket-tasarla', fn () => redirect()->route('custom-bouquet.index'));
Route::post('/kendi-buketini-olustur/ekle', [CustomBouquetController::class, 'addToCart'])->name('custom-bouquet.add');

// Portföy & Projeler (İnşaat, Mimarlık, Kurumsal)
Route::get('/projeler', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/proje/{slug}', [ProjectController::class, 'show'])->name('projects.show');

// KVKK Çerez Rıza Kaydı (Public Endpoint)
Route::post('/cookie-consent/log', [CookieConsentController::class, 'store'])->name('cookieConsent.store');

// SEO XML Sitemap, Robots.txt & Google Merchant Center (Google Alışveriş)
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');
Route::get('/google-shopping.xml', [FeedController::class, 'googleMerchant'])->name('feed.googleShopping');
Route::get('/feed/google-merchant.xml', [FeedController::class, 'googleMerchant'])->name('feed.googleMerchant');

// Blog & Rehber Rotaları
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Ürün Yorum & Değerlendirme Rotası
Route::post('/urun/{product}/yorum', [ReviewController::class, 'store'])->name('reviews.store');

// Contact Route
Route::get('iletisim', [FrontendController::class, 'contact'])->name('contact');
Route::post('iletisim', [FrontendController::class, 'storeContact'])->name('contact.store');

// Cart routes
Route::get('/sepet', [CartController::class, 'index'])->name('cart.index');
Route::post('/sepet/ekle/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/sepet/guncelle/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/sepet/sil/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/sepet/temizle', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/sepet/kupon-uygula', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::post('/sepet/kupon-kaldir', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
Route::get('/odeme', [CartController::class, 'checkout'])->name('checkout');
Route::post('/odeme', [CartController::class, 'processCheckout'])->name('checkout.process');
Route::get('/siparis-basarili/{order:order_number}', [CartController::class, 'success'])->name('cart.success');
// PayTR Online Ödeme Rotaları (Webhook & Hata Sayfası)
Route::post('/paytr/callback', [PayTRController::class, 'callback'])->name('paytr.callback');
Route::get('/odeme/hata/{order:order_number}', [PayTRController::class, 'fail'])->name('paytr.fail');

// Yönetim Paneli Kısayolları ve Yönlendirmeleri
Route::get('/admin', fn () => redirect()->route('dashboard'));
Route::get('/admin/dashboard', fn () => redirect()->route('dashboard'))->name('admin.dashboard');
Route::get('/yonetim', fn () => redirect()->route('dashboard'));

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', IsAdmin::class])
    ->name('dashboard');

Route::get('/usta', function () {
    if (!auth()->check()) {
        if (app()->isLocal()) {
            $admin = \App\Models\User::where('is_admin', true)->first();
            if ($admin) {
                auth()->login($admin, true);
            }
        } else {
            return redirect()->route('login');
        }
    }
    return redirect()->route('admin.workshop.craftsman');
})->name('craftsman');

Route::middleware('auth')->group(function () {
    Route::get('/hesabim', [CustomerController::class, 'account'])->name('customer.account');
    Route::get('/hesabim/kupon-kullan/{code}', [CustomerController::class, 'applyMyCoupon'])->name('customer.coupons.apply');
    Route::post('/hesabim/profil-guncelle', [CustomerController::class, 'updateProfile'])->name('customer.profile.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::middleware([IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
        Route::get('site-matrix', [SiteMatrixController::class, 'index'])->name('siteMatrix.index');
        Route::get('site-audit', [SiteAuditController::class, 'index'])->name('siteAudit.index');
        Route::resource('projects', App\Http\Controllers\Admin\ProjectController::class);
        Route::delete('categories/{category}/image', [CategoryController::class, 'deleteImage'])->name('categories.image.destroy');
        Route::resource('categories', CategoryController::class);

        // Products & Bulk Excel Import & URL Scraper & Image Management
        Route::patch('products/{product}/toggle', [App\Http\Controllers\Admin\ProductController::class, 'toggle'])->name('products.toggle');
        Route::post('products/scrape-url', [App\Http\Controllers\Admin\ProductController::class, 'scrapeUrl'])->name('products.scrapeUrl');
        Route::post('products/ai-rewrite', [App\Http\Controllers\Admin\ProductController::class, 'aiRewrite'])->name('products.aiRewrite');
        Route::post('products/ai-generate-image', [App\Http\Controllers\Admin\ProductController::class, 'aiGenerateImage'])->name('products.aiGenerateImage');
        Route::post('products/format-ecommerce-image', [App\Http\Controllers\Admin\ProductController::class, 'formatEcommerceImage'])->name('products.formatEcommerceImage');
        Route::post('products/ai-edit-studio', [App\Http\Controllers\Admin\ProductController::class, 'aiEditStudio'])->name('products.aiEditStudio');
        Route::post('products/apply-studio-image', [App\Http\Controllers\Admin\ProductController::class, 'applyStudioImage'])->name('products.applyStudioImage');
        Route::get('products/export/sample-template', [App\Http\Controllers\Admin\ProductController::class, 'downloadSampleTemplate'])->name('products.sampleTemplate');
        Route::post('products/import/excel', [App\Http\Controllers\Admin\ProductController::class, 'importExcel'])->name('products.import');
        Route::delete('products/{product}/cover-image', [App\Http\Controllers\Admin\ProductController::class, 'deleteCoverImage'])->name('products.cover.destroy');
        Route::post('products/{product}/format-cover-ecommerce', [App\Http\Controllers\Admin\ProductController::class, 'formatExistingCover'])->name('products.formatCover');
        Route::post('products/{product}/ai-generate-attach-image', [App\Http\Controllers\Admin\ProductController::class, 'aiGenerateAndAttach'])->name('products.aiGenerateAttach');
        Route::delete('products/{product}/images/{image}', [App\Http\Controllers\Admin\ProductController::class, 'deleteImage'])->name('products.images.destroy');
        Route::post('products/images/{image}/format-ecommerce', [App\Http\Controllers\Admin\ProductController::class, 'formatExistingGalleryImage'])->name('products.images.formatEcommerce');
        Route::post('products/{product}/images/{image}/set-cover', [App\Http\Controllers\Admin\ProductController::class, 'setCoverImage'])->name('products.images.setCover');
        Route::resource('products', App\Http\Controllers\Admin\ProductController::class);

        // Gifts & Promotions Management
        Route::patch('gifts/{gift}/toggle', [GiftController::class, 'toggle'])->name('gifts.toggle');
        Route::resource('gifts', GiftController::class);

        Route::patch('regions/{region}/toggle', [RegionController::class, 'toggle'])->name('regions.toggle');
        Route::post('regions/toggle-city', [RegionController::class, 'toggleCity'])->name('regions.toggleCity');
        Route::post('regions/toggle-all', [RegionController::class, 'toggleAll'])->name('regions.toggleAll');
        Route::post('regions/express-settings', [RegionController::class, 'saveExpressSettings'])->name('regions.saveExpressSettings');
        Route::resource('regions', RegionController::class);
        Route::get('brands/{brand}/products', [BrandController::class, 'manageProducts'])->name('brands.products');
        Route::post('brands/{brand}/products', [BrandController::class, 'syncProducts'])->name('brands.products.sync');
        Route::resource('brands', BrandController::class);

        // Orders & Print & Excel Export & Preparation Photo
        Route::match(['get', 'post'], 'orders/toplu-yazdir', [OrderController::class, 'bulkPrint'])->name('orders.bulkPrint');
        Route::get('orders/export/excel', [OrderController::class, 'exportExcel'])->name('orders.export');
        Route::get('orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
        Route::post('orders/{order}/upload-photo', [OrderController::class, 'uploadPhoto'])->name('orders.uploadPhoto');
        Route::resource('orders', OrderController::class);

        // 🌸 Çiçekçi Mobil Atölye & Hızlı Kamera Uygulaması
        Route::get('atolye/canli-kontrol', [WorkshopController::class, 'checkNewOrders'])->name('workshop.checkNewOrders');
        Route::get('orders/canli-kontrol', [WorkshopController::class, 'checkNewOrders'])->name('orders.checkNewOrders');
        Route::get('atolye', [WorkshopController::class, 'index'])->name('workshop.index');
        Route::post('atolye/{order}/foto', [WorkshopController::class, 'uploadPhoto'])->name('workshop.uploadPhoto');
        Route::post('atolye/{order}/durum', [WorkshopController::class, 'updateStatus'])->name('workshop.updateStatus');

        // 👨‍🌾 Usta Sipariş Hazırlama Uygulaması (KDS - Kitchen/Workshop Display)
        Route::get('usta', [WorkshopController::class, 'craftsman'])->name('workshop.craftsman');
        Route::get('usta/canli-kontrol', [WorkshopController::class, 'checkNewOrders'])->name('workshop.craftsman.check');

        // Leads Excel Export
        Route::get('leads/export/excel', [App\Http\Controllers\Admin\LeadController::class, 'exportExcel'])->name('leads.export');
        Route::resource('leads', App\Http\Controllers\Admin\LeadController::class)->only(['index', 'update', 'destroy']);

        // Services & Print & Maintenance Reminders & Trash & Completed Jobs & Excel Export
        Route::get('services/export/excel', [ServiceRequestController::class, 'exportExcel'])->name('services.export');
        Route::get('services/completed', [ServiceRequestController::class, 'completed'])->name('services.completed');
        Route::get('services/trash', [ServiceRequestController::class, 'trash'])->name('services.trash');
        Route::delete('services/trash/empty', [ServiceRequestController::class, 'emptyTrash'])->name('services.emptyTrash');
        Route::post('services/{id}/restore', [ServiceRequestController::class, 'restore'])->name('services.restore');
        Route::delete('services/{id}/force-delete', [ServiceRequestController::class, 'forceDelete'])->name('services.forceDelete');
        Route::get('services/calendar', [ServiceRequestController::class, 'calendar'])->name('services.calendar');
        Route::get('services/{service}/print', [ServiceRequestController::class, 'print'])->name('services.print');
        Route::resource('services', ServiceRequestController::class);
        Route::get('maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::post('maintenance/{service}/renew', [MaintenanceController::class, 'renew'])->name('maintenance.renew');

        // Coupons
        Route::resource('coupons', CouponController::class);

        // 🌸 Buket Malzemeleri Yönetimi (Tekli Dal Çiçekler, Ambalajlar, Kurdeleler)
        Route::get('buket-malzemeleri', [App\Http\Controllers\Admin\BouquetItemController::class, 'index'])->name('bouquet-items.index');
        Route::post('buket-malzemeleri/cicekler', [App\Http\Controllers\Admin\BouquetItemController::class, 'storeStem'])->name('bouquet-items.stems.store');
        Route::put('buket-malzemeleri/cicekler/{stem}', [App\Http\Controllers\Admin\BouquetItemController::class, 'updateStem'])->name('bouquet-items.stems.update');
        Route::delete('buket-malzemeleri/cicekler/{stem}', [App\Http\Controllers\Admin\BouquetItemController::class, 'destroyStem'])->name('bouquet-items.stems.destroy');
        Route::patch('buket-malzemeleri/cicekler/{stem}/toggle', [App\Http\Controllers\Admin\BouquetItemController::class, 'toggleStem'])->name('bouquet-items.stems.toggle');

        Route::post('buket-malzemeleri/ambalajlar', [App\Http\Controllers\Admin\BouquetItemController::class, 'storePackage'])->name('bouquet-items.packages.store');
        Route::put('buket-malzemeleri/ambalajlar/{package}', [App\Http\Controllers\Admin\BouquetItemController::class, 'updatePackage'])->name('bouquet-items.packages.update');
        Route::delete('buket-malzemeleri/ambalajlar/{package}', [App\Http\Controllers\Admin\BouquetItemController::class, 'destroyPackage'])->name('bouquet-items.packages.destroy');
        Route::patch('buket-malzemeleri/ambalajlar/{package}/toggle', [App\Http\Controllers\Admin\BouquetItemController::class, 'togglePackage'])->name('bouquet-items.packages.toggle');

        Route::post('buket-malzemeleri/kurdeleler', [App\Http\Controllers\Admin\BouquetItemController::class, 'storeRibbon'])->name('bouquet-items.ribbons.store');
        Route::put('buket-malzemeleri/kurdeleler/{ribbon}', [App\Http\Controllers\Admin\BouquetItemController::class, 'updateRibbon'])->name('bouquet-items.ribbons.update');
        Route::delete('buket-malzemeleri/kurdeleler/{ribbon}', [App\Http\Controllers\Admin\BouquetItemController::class, 'destroyRibbon'])->name('bouquet-items.ribbons.destroy');
        Route::patch('buket-malzemeleri/kurdeleler/{ribbon}/toggle', [App\Http\Controllers\Admin\BouquetItemController::class, 'toggleRibbon'])->name('bouquet-items.ribbons.toggle');

        Route::post('buket-malzemeleri/boyutlar', [App\Http\Controllers\Admin\BouquetItemController::class, 'storeSize'])->name('bouquet-items.sizes.store');
        Route::put('buket-malzemeleri/boyutlar/{size}', [App\Http\Controllers\Admin\BouquetItemController::class, 'updateSize'])->name('bouquet-items.sizes.update');
        Route::delete('buket-malzemeleri/boyutlar/{size}', [App\Http\Controllers\Admin\BouquetItemController::class, 'destroySize'])->name('bouquet-items.sizes.destroy');
        Route::patch('buket-malzemeleri/boyutlar/{size}/toggle', [App\Http\Controllers\Admin\BouquetItemController::class, 'toggleSize'])->name('bouquet-items.sizes.toggle');

        Route::post('buket-malzemeleri/ayarlar', [App\Http\Controllers\Admin\BouquetItemController::class, 'saveSettings'])->name('bouquet-items.settings.save');

        // Customers & Excel Export
        Route::get('customers/export/excel', [App\Http\Controllers\Admin\CustomerController::class, 'exportExcel'])->name('customers.export');
        Route::get('customers', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('customers.show');

        // CMS & İçerik Rotaları
        Route::post('posts/scrape-url', [PostController::class, 'scrapeUrl'])->name('posts.scrapeUrl');
        Route::post('posts/ai-generate', [PostController::class, 'aiGenerate'])->name('posts.aiGenerate');
        Route::resource('posts', PostController::class);
        Route::get('reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
        Route::post('reviews/sync-google', [App\Http\Controllers\Admin\ReviewController::class, 'syncGoogle'])->name('reviews.syncGoogle');
        Route::post('reviews/store-google', [App\Http\Controllers\Admin\ReviewController::class, 'storeGoogle'])->name('reviews.storeGoogle');
        Route::patch('reviews/{review}/toggle', [App\Http\Controllers\Admin\ReviewController::class, 'toggleApproval'])->name('reviews.toggle');
        Route::delete('reviews/{review}', [App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

        Route::resource('faqs', FaqController::class)->except('show');
        Route::resource('testimonials', TestimonialController::class)->except('show');
        Route::resource('pages', PageController::class)->except('show');
        Route::resource('banners', BannerController::class)->except('show');

        // Pazarlama & Satış Araçları (Özel Ayrı Sayfalar)
        Route::get('marketing/flash-deal', [MarketingController::class, 'flashDeal'])->name('marketing.flashDeal');
        Route::get('marketing/quiz', [MarketingController::class, 'quiz'])->name('marketing.quiz');
        Route::get('marketing/wheel', [MarketingController::class, 'wheel'])->name('marketing.wheel');
        Route::get('marketing/gifts', [MarketingController::class, 'gifts'])->name('marketing.gifts');
        Route::get('marketing/payment', [MarketingController::class, 'payment'])->name('marketing.payment');
        Route::get('marketing/cookie-consents/export/excel', [MarketingController::class, 'exportCookieConsents'])->name('marketing.cookieConsents.export');
        Route::get('marketing/cookie-consents', [MarketingController::class, 'cookieConsents'])->name('marketing.cookieConsents');
        Route::get('marketing/seo', [MarketingController::class, 'seo'])->name('marketing.seo');
        Route::get('marketing/performance', [MarketingController::class, 'performance'])->name('marketing.performance');
        Route::post('marketing/performance/clear-cache', [MarketingController::class, 'clearCache'])->name('marketing.performance.clearCache');
        Route::post('marketing/store', [MarketingController::class, 'store'])->name('marketing.store');

        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'store'])->name('settings.store');

        // Sadece Superadmin erişimi olması gerekenler
        Route::middleware('can:manage-users')->group(function () {
            Route::resource('users', UserController::class);
            Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        });

        Route::resource('leads', App\Http\Controllers\Admin\LeadController::class)->only(['index', 'update', 'destroy']);
        Route::get('contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
        Route::get('contact-messages/{message}', [ContactMessageController::class, 'show'])->name('contact-messages.show');
    });
});

// Public Lead Capture Route (Şans Çarkı / Kampanya Talepleri)
Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');

// 81 İl ve İlçe Lokasyon API
Route::get('/api/locations', function () {
    return response()->json(
        \App\Models\Region::where('is_active', true)
            ->orderBy('city')
            ->orderBy('name')
            ->get(['id', 'city', 'name'])
            ->groupBy('city')
    );
})->name('api.locations');

require __DIR__.'/auth.php';
require __DIR__.'/redirects.php';
