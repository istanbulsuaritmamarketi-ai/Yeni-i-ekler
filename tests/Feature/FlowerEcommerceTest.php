<?php

namespace Tests\Feature;

use App\Models\BouquetPackage;
use App\Models\BouquetRibbon;
use App\Models\BouquetStem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Region;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\YenicicekSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlowerEcommerceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_home_page_loads_successfully_with_flower_theme()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Tazecicekler');
    }

    public function test_flower_catalog_and_category_pages_load()
    {
        $category = Category::where('is_active', true)->first();

        $this->get('/urunler')->assertStatus(200);
        if ($category) {
            $this->get('/kategori/' . $category->slug)->assertStatus(200);
        }
    }

    public function test_flower_product_detail_page_loads_with_time_slots_and_notes()
    {
        $product = Product::where('is_active', true)->first();
        if ($product) {
            $response = $this->get('/urun/' . $product->slug);
            $response->assertStatus(200);
            $response->assertSee($product->name);
            $response->assertSee('Teslimat Tarihi');
            $response->assertSee('Kart Notu');
        }
    }

    public function test_district_pages_load_with_express_delivery_info()
    {
        $response = $this->get('/bolge/kadikoy');
        $response->assertStatus(200);
        $response->assertSee('Kadıköy');
        $response->assertSee('2 Saatte Teslimat');
    }

    public function test_full_flower_order_checkout_flow()
    {
        $product = Product::where('is_active', true)->first();
        $this->assertNotNull($product, 'No active flower product found.');

        // 1. Sepete ekle
        $postData = [
            'quantity' => 1,
            'delivery_date' => date('Y-m-d', strtotime('+1 day')),
            'delivery_time_slot' => '15:00 - 18:00',
            'delivery_district' => 'Beşiktaş',
            'card_note' => 'Yeni işinde sonsuz başarılar dilerim!',
            'card_template_id' => 'tebrik_1',
            'is_anonymous' => 0,
            'sender_name' => 'Burak Çelik',
            'upsell_items' => [
                1 => ['id' => 1, 'name' => 'Kutulu Belçika Çikolatası', 'price' => 249.00]
            ]
        ];

        $response = $this->post(route('cart.add', $product->id), $postData);
        $response->assertSessionHas('cart');
        $response->assertSessionHas('flower_delivery');

        // 2. Sepet görüntüle
        $this->get(route('cart.index'))->assertStatus(200);

        // 3. Checkout görüntüle
        $this->get(route('checkout'))->assertStatus(200);

        // 4. Siparişi tamamla
        $region = \App\Models\Region::where('is_active', true)->first();
        $checkoutData = [
            'customer_name' => 'Burak Çelik',
            'customer_phone' => '05321112233',
            'customer_email' => 'burak@example.com',
            'recipient_name' => 'Elif Yılmaz',
            'recipient_phone' => '05334445566',
            'region_id' => $region->id,
            'recipient_district' => $region->name,
            'address' => 'Levent Mah. Çiçek Sk. No:5 D:2 Beşiktaş / İstanbul',
            'delivery_date' => date('Y-m-d', strtotime('+1 day')),
            'delivery_time_slot' => '15:00 - 18:00',
            'card_note' => 'Yeni işinde sonsuz başarılar dilerim!',
            'sender_name' => 'Burak Çelik',
            'is_anonymous' => 0,
            'payment_method' => 'cod',
            'terms' => 'on',
        ];

        $checkoutResponse = $this->post(route('checkout.process'), $checkoutData);

        // 5. Sipariş DB kontrolü
        $order = Order::where('customer_phone', '05321112233')->latest()->first();
        $this->assertNotNull($order, 'Order was not created in database.');
        $this->assertEquals('Burak Çelik', $order->customer_name);
        $this->assertEquals('Elif Yılmaz', $order->recipient_name);
        $this->assertEquals('15:00 - 18:00', $order->delivery_time_slot);
        $this->assertEquals('Yeni işinde sonsuz başarılar dilerim!', $order->card_note);
        $this->assertEquals('cod', $order->payment_method);

        // 6. Başarılı sipariş sayfasına yönlendirme kontrolü
        $checkoutResponse->assertRedirect(route('cart.success', $order->order_number));

        // 7. Başarılı sayfayı aç
        $this->get(route('cart.success', $order->order_number))
            ->assertStatus(200)
            ->assertSee($order->order_number)
            ->assertSee('Elif Yılmaz');

        // 8. Sipariş Takip ekranında sorgula
        $this->get(route('order.track', ['order_number' => $order->order_number]))
            ->assertStatus(200)
            ->assertSee($order->order_number)
            ->assertSee('Elif Yılmaz');

        // 9. Admin Girişi ve Florist Dashboard Testi
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@yenicicek.com'],
            ['name' => 'Florist Admin', 'password' => bcrypt('cicek123456'), 'is_admin' => true, 'role' => 'admin']
        );

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertStatus(200)
            ->assertSee('Atölye')
            ->assertSee('Bugünkü Teslimat');

        // 10. Admin Siparişler Listesi Testi
        $this->actingAs($admin)
            ->get(route('admin.orders.index'))
            ->assertStatus(200)
            ->assertSee('Sipariş & Atölye')
            ->assertSee($order->order_number);

        // 11. Admin Çiçek Sipariş Detayı Testi
        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertStatus(200)
            ->assertSee($order->order_number)
            ->assertSee('Elif Yılmaz')
            ->assertSee('Atölye Hazırlık Fotoğrafı');

        // 12. Atölye Hazırlık Fotoğrafı Yükleme Testi
        \Illuminate\Support\Facades\Storage::fake('public');
        $fakePhoto = \Illuminate\Http\UploadedFile::fake()->create('hazirlik_buket.jpg', 100, 'image/jpeg');

        $uploadResponse = $this->actingAs($admin)->post(route('admin.orders.uploadPhoto', $order), [
            'preparation_photo' => $fakePhoto,
            'status' => 'Fotoğraf Onayında',
        ]);

        $uploadResponse->assertRedirect(route('admin.orders.show', $order));
        $order->refresh();
        $this->assertNotNull($order->preparation_photo);
        $this->assertEquals('Fotoğraf Onayında', $order->status);
    }

    public function test_home_page_displays_7_grid_banners_with_large_product_images()
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // Verify the 7 banner titles from BannerSeeder are displayed
        $response->assertSee('Gül Buketleri');
        $response->assertSee('Kutuda Tasarımlar');
        $response->assertSee('Asil Orkideler');
        $response->assertSee('Mevsim Çiçekleri');
        $response->assertSee('Doğum Günü');
        $response->assertSee('Romantik Seri');
        $response->assertSee('Haftanın Özel Çiçek Fırsatları');
    }

    public function test_admin_can_manage_and_update_banners()
    {
        $admin = \App\Models\User::where('is_admin', true)->first();

        // 1. Banner Index View
        $indexResponse = $this->actingAs($admin)->get(route('admin.banners.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Ana Sayfa Banner Yönetimi');
        $indexResponse->assertSee('Banner 1');
        $indexResponse->assertSee('Banner 7 (Dikey)');

        // 2. Banner Edit View
        $banner = \App\Models\Banner::where('position', 'grid_1')->first();
        $this->assertNotNull($banner);

        $editResponse = $this->actingAs($admin)->get(route('admin.banners.edit', $banner));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('1. Kutu (1. Sütun Üst)');

        // 3. Update Banner
        $updateResponse = $this->actingAs($admin)->put(route('admin.banners.update', $banner), [
            'position' => 'grid_1',
            'title' => 'Özel Gün Gülleri',
            'subtitle' => 'En Taze Kırmızı Güller',
            'badge' => 'Süper İndirim',
            'button_text' => 'Şimdi Sipariş Ver',
            'link_url' => '/kategori/gul-buketleri',
            'order' => 1,
            'is_active' => '1',
        ]);

        $updateResponse->assertRedirect(route('admin.banners.index'));
        $banner->refresh();
        $this->assertEquals('Özel Gün Gülleri', $banner->title);
        $this->assertEquals('Süper İndirim', $banner->badge);
        $this->assertEquals('Şimdi Sipariş Ver', $banner->button_text);
    }

    public function test_admin_can_manage_81_provinces_and_toggle_districts()
    {
        $admin = \App\Models\User::where('is_admin', true)->first();

        // 1. Regions Index View loads with 81 cities stats
        $response = $this->actingAs($admin)->get(route('admin.regions.index', ['city' => 'İstanbul']));
        $response->assertStatus(200);
        $response->assertSee('Teslimat Yönetimi');
        $response->assertSee('İstanbul');
        $response->assertSee('Kadıköy');

        // 2. Toggle single district via PATCH
        $kadikoy = \App\Models\Region::where('name', 'Kadıköy')->first();
        $this->assertNotNull($kadikoy);
        $initialState = $kadikoy->is_active;

        $toggleResponse = $this->actingAs($admin)->patchJson(route('admin.regions.toggle', $kadikoy), [
            'status' => !$initialState,
        ]);
        $toggleResponse->assertStatus(200);
        $toggleResponse->assertJson(['success' => true, 'is_active' => !$initialState]);

        $kadikoy->refresh();
        $this->assertEquals(!$initialState, $kadikoy->is_active);

        // 3. Bulk toggle all districts of a province
        $cityToggleResponse = $this->actingAs($admin)->post(route('admin.regions.toggleCity'), [
            'city' => 'Adana',
            'status' => 0,
        ]);
        $cityToggleResponse->assertRedirect();

        $adanaActiveCount = \App\Models\Region::where('city', 'Adana')->where('is_active', true)->count();
        $this->assertEquals(0, $adanaActiveCount);

        // Turn Adana back on
        $this->actingAs($admin)->post(route('admin.regions.toggleCity'), [
            'city' => 'Adana',
            'status' => 1,
        ]);
        $adanaActiveCount2 = \App\Models\Region::where('city', 'Adana')->where('is_active', true)->count();
        $this->assertGreaterThan(0, $adanaActiveCount2);

        // 4. Metropolitan preset toggle
        $presetResponse = $this->actingAs($admin)->post(route('admin.regions.toggleAll'), [
            'action' => 'metropolitan',
        ]);
        $presetResponse->assertRedirect();
        
        $istCount = \App\Models\Region::where('city', 'İstanbul')->where('is_active', true)->count();
        $this->assertGreaterThan(0, $istCount);
    }

    public function test_inactive_district_cannot_be_ordered()
    {
        $district = \App\Models\Region::where('name', 'Kadıköy')->first();
        $district->update(['is_active' => false]);

        $product = \App\Models\Product::where('is_active', true)->first();

        // Put item in cart
        $this->withSession([
            'cart' => [
                $product->id => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => 1,
                    'image' => $product->image_path,
                ],
            ],
        ]);

        // Attempt checkout with inactive district
        $response = $this->post(route('checkout.process'), [
            'customer_name' => 'Ahmet Test',
            'customer_phone' => '05551112233',
            'address' => 'Moda Cad. No: 1',
            'region_id' => $district->id,
            'payment_method' => 'cod',
        ]);

        $response->assertSessionHasErrors('region_id');

        // Turn district back on and verify it succeeds
        $district->update(['is_active' => true]);
        $successResponse = $this->post(route('checkout.process'), [
            'customer_name' => 'Ahmet Test',
            'customer_phone' => '05551112233',
            'address' => 'Moda Cad. No: 1',
            'region_id' => $district->id,
            'payment_method' => 'cod',
        ]);

        $successResponse->assertRedirect();
        $this->assertDatabaseHas('orders', ['customer_name' => 'Ahmet Test']);
    }

    public function test_checkout_only_allows_pos_and_cash_payment_methods()
    {
        $product = \App\Models\Product::where('is_active', true)->first();
        $region = \App\Models\Region::where('is_active', true)->first();

        $this->withSession([
            'cart' => [
                $product->id => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => 1,
                    'image' => $product->image_path,
                ],
            ],
        ]);

        // 1. Checkout view has POS and Kapıda Ödeme, does NOT have Havale / EFT
        $viewResponse = $this->get(route('checkout'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Kredi / Banka Kartı (POS)');
        $viewResponse->assertSee('Kapıda Ödeme');
        $viewResponse->assertDontSee('Havale / EFT');

        // 2. Submitting transfer as payment method fails validation
        $failResponse = $this->post(route('checkout.process'), [
            'customer_name' => 'Can Test',
            'customer_phone' => '05552223344',
            'address' => 'Karaköy Cad. No: 12',
            'region_id' => $region->id,
            'payment_method' => 'transfer',
        ]);
        $failResponse->assertSessionHasErrors('payment_method');

        // 3. Submitting cash (Kapıda Ödeme) succeeds
        $cashResponse = $this->post(route('checkout.process'), [
            'customer_name' => 'Can Test',
            'customer_phone' => '05552223344',
            'address' => 'Karaköy Cad. No: 12',
            'region_id' => $region->id,
            'payment_method' => 'cash',
        ]);
        $cashResponse->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Can Test',
            'payment_method' => 'cash',
        ]);
    }

    public function test_admin_can_save_express_courier_settings()
    {
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@yenicicek.com'],
            ['name' => 'Florist Admin', 'password' => bcrypt('cicek123456'), 'is_admin' => true, 'role' => 'admin']
        );

        $response = $this->actingAs($admin)->post(route('admin.regions.saveExpressSettings'), [
            'express_courier_active' => '1',
            'express_courier_fee' => '195',
            'express_courier_title' => 'Süper VIP Motor Kurye',
            'express_courier_subtitle' => '45 Dakikada Işık Hızında Teslimat',
        ]);

        $response->assertRedirect(route('admin.regions.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('1', \App\Models\Setting::get('express_courier_active'));
        $this->assertEquals('195', \App\Models\Setting::get('express_courier_fee'));
        $this->assertEquals('Süper VIP Motor Kurye', \App\Models\Setting::get('express_courier_title'));
    }

    public function test_checkout_with_express_courier_calculates_fee_and_updates_order()
    {
        \App\Models\Setting::set('express_courier_active', '1');
        \App\Models\Setting::set('express_courier_fee', '150');
        \App\Models\Setting::set('express_courier_title', 'VIP Hızlı Kurye (60-90 Dk)');

        $product = Product::where('is_active', true)->first();
        $region = \App\Models\Region::where('is_active', true)->first();

        // 1. Standard delivery (delivery_type = standard)
        $standardCheckout = $this->withSession([
            'cart' => [
                $product->id => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => 500.00,
                    'quantity' => 1,
                    'image' => $product->image_path,
                ],
            ],
        ])->post(route('checkout.process'), [
            'customer_name' => 'Standart Müşteri',
            'customer_phone' => '05551112233',
            'address' => 'Bağdat Cad. No: 10',
            'region_id' => $region->id,
            'payment_method' => 'cash',
            'delivery_type' => 'standard',
        ]);

        $standardCheckout->assertRedirect();
        $standardOrder = Order::where('customer_name', 'Standart Müşteri')->latest()->first();
        $this->assertNotNull($standardOrder);
        $this->assertEquals(0, (float) $standardOrder->delivery_fee);
        $this->assertEquals(500.00, (float) $standardOrder->total_amount);

        // 2. Express courier delivery (delivery_type = express)
        $expressCheckout = $this->withSession([
            'cart' => [
                $product->id => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => 500.00,
                    'quantity' => 1,
                    'image' => $product->image_path,
                ],
            ],
        ])->post(route('checkout.process'), [
            'customer_name' => 'VIP Müşteri',
            'customer_phone' => '05559998877',
            'address' => 'Nispetiye Cad. No: 20',
            'region_id' => $region->id,
            'payment_method' => 'cash',
            'delivery_type' => 'express',
        ]);

        $expressCheckout->assertRedirect();
        $expressOrder = Order::where('customer_name', 'VIP Müşteri')->latest()->first();
        $this->assertNotNull($expressOrder);
        $this->assertEquals(150.00, (float) $expressOrder->delivery_fee);
        $this->assertEquals(650.00, (float) $expressOrder->total_amount);
        $this->assertStringContainsString('VIP Hızlı Kurye', $expressOrder->notes);

        // 3. Admin detail page shows VIP delivery fee
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@yenicicek.com'],
            ['name' => 'Florist Admin', 'password' => bcrypt('cicek123456'), 'is_admin' => true, 'role' => 'admin']
        );
        $adminView = $this->actingAs($admin)->get(route('admin.orders.show', $expressOrder));
        $adminView->assertStatus(200);
        $adminView->assertSee('VIP Hızlı Kurye');
        $adminView->assertSee('150');

        // 4. Test Florist 4-slot perforated print slip (customer note on left, courier info on right)
        $printView = $this->actingAs($admin)->get(route('admin.orders.print', $expressOrder));
        $printView->assertStatus(200);
        $printView->assertSee('Zarf İçi Kart');
        $printView->assertSee('VIP HIZLI');
        $printView->assertSee('KAPIDA');
        $printView->assertSee('Nispetiye Cad. No: 20');
        $printView->assertSee('VIP Müşteri');
    }

    public function test_florist_workshop_mobile_app_loads_and_operates_successfully()
    {
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@yenicicek.com'],
            ['name' => 'Florist Admin', 'password' => bcrypt('cicek123456'), 'is_admin' => true, 'role' => 'admin']
        );

        $region = \App\Models\Region::first();
        $order = Order::first();
        if (!$order) {
            $order = Order::create([
                'order_number' => 'YC-20261009-TEST',
                'customer_name' => 'Atölye Müşterisi',
                'customer_phone' => '05551234567',
                'address' => 'Bağdat Cad. No: 15, Kadıköy',
                'region_id' => $region ? $region->id : 1,
                'total_amount' => 750.00,
                'status' => 'Atölyede Hazırlanıyor',
            ]);
        }

        // 1. Atölye sayfası yükleniyor
        $response = $this->actingAs($admin)->get(route('admin.workshop.index'));
        $response->assertStatus(200);
        $response->assertSee('Çiçekçi Atölye & Mobil Kamera');
        $response->assertSee('Hazırlanacaklar');
        $response->assertSee('Fiş QR Oku');

        // 2. Mobil kamera fotoğrafı yükleme & Fotoğraf Onayına alma
        \Illuminate\Support\Facades\Storage::fake('public');
        $jpegBytes = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('fresh_roses.jpg', $jpegBytes);

        $uploadResponse = $this->actingAs($admin)->post(route('admin.workshop.uploadPhoto', $order), [
            'preparation_photo' => $file,
            'status' => 'Fotoğraf Onayında',
        ]);

        $uploadResponse->assertRedirect();
        $order->refresh();
        $this->assertEquals('Fotoğraf Onayında', $order->status);
        $this->assertNotNull($order->preparation_photo);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($order->preparation_photo);

        // 3. Tek dokunuşla kuryeye teslim etme
        $statusResponse = $this->actingAs($admin)->post(route('admin.workshop.updateStatus', $order), [
            'status' => 'Kuryede / Dağıtımda',
        ]);

        $statusResponse->assertRedirect();
        $order->refresh();
        $this->assertEquals('Kuryede / Dağıtımda', $order->status);
    }

    public function test_order_notification_live_check_api_returns_new_orders()
    {
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@yenicicek.com'],
            ['name' => 'Florist Admin', 'password' => bcrypt('cicek123456'), 'is_admin' => true, 'role' => 'admin']
        );

        $region = \App\Models\Region::first();

        // 1. Mevcut en yüksek sipariş id'sini öğren (başlangıç kalibrasyonu)
        $initResponse = $this->actingAs($admin)->getJson(route('admin.workshop.checkNewOrders'));
        $initResponse->assertStatus(200);
        $initResponse->assertJson(['has_new' => false, 'count' => 0]);
        $lastId = $initResponse->json('latest_id');

        // 2. Yeni bir çiçek siparişi oluştur
        $newOrder = Order::create([
            'order_number' => 'YC-20261009-NOTIF',
            'customer_name' => 'Zil Bildirim Testi',
            'customer_phone' => '05558887766',
            'address' => 'Moda Cad. No: 40, Kadıköy',
            'recipient_name' => 'Sevgi Çiçeksever',
            'recipient_district' => 'Kadıköy',
            'region_id' => $region ? $region->id : 1,
            'total_amount' => 950.00,
            'delivery_fee' => 150.00, // VIP Kurye
            'status' => 'Yeni Sipariş',
        ]);

        // 3. Canlı kontrol API'sini çağır ve yeni siparişin düştüğünü doğrula
        $checkResponse = $this->actingAs($admin)->getJson(route('admin.workshop.checkNewOrders', ['last_id' => $lastId]));
        $checkResponse->assertStatus(200);
        $checkResponse->assertJson([
            'has_new' => true,
            'count' => 1,
        ]);
        $orders = $checkResponse->json('orders');
        $this->assertCount(1, $orders);
        $this->assertEquals('YC-20261009-NOTIF', $orders[0]['order_number']);
        $this->assertEquals('Sevgi Çiçeksever', $orders[0]['recipient_name']);
        $this->assertTrue($orders[0]['is_vip']);
    }

    public function test_custom_bouquet_builder_flow_and_order_checkout()
    {
        // 1. Veritabanı başlangıçta 0 iken sayfa nezaketle yükleniyor mu?
        $emptyResponse = $this->get(route('custom-bouquet.index'));
        $emptyResponse->assertStatus(200);
        $emptyResponse->assertSee('Kendi Çiçek Buketini Kendin Oluştur');
        $emptyResponse->assertSee('Yeni Sezon Çiçekleri Hazırlanıyor');

        // 2. Çiçekçi admin malzemeleri sisteme girer
        $redRose = BouquetStem::create([
            'name' => 'Ekvador Kırmızı Gül',
            'meaning' => 'Aşk & Tutku',
            'price' => 55.00,
            'icon' => '🌹',
            'color_hex' => '#e11d48',
            'is_active' => true,
        ]);
        $daisy = BouquetStem::create([
            'name' => 'Kır Papatyası',
            'meaning' => 'Masumiyet',
            'price' => 35.00,
            'icon' => '🌼',
            'color_hex' => '#facc15',
            'is_active' => true,
        ]);
        $pkg = BouquetPackage::create([
            'name' => 'Pastel Pembe Kraft Kağıt',
            'description' => 'Romantik soft pembe mat buket kağıdı',
            'price' => 0.00,
            'color_hex' => '#fbcfe8',
            'is_active' => true,
        ]);
        $ribbon = BouquetRibbon::create([
            'name' => 'Saten Bordo / Kırmızı',
            'price' => 0.00,
            'color_hex' => '#991b1b',
            'is_active' => true,
        ]);

        // Malzemeler eklendikten sonra sayfa dolu yükleniyor mu?
        $response = $this->get(route('custom-bouquet.index'));
        $response->assertStatus(200);
        $response->assertSee('Ekvador Kırmızı Gül');
        $response->assertSee('Pastel Pembe Kraft Kağıt');
        $response->assertSee('Saten Bordo / Kırmızı');

        // 3. Minimum 7 dal kuralı validasyonu
        $failResponse = $this->post(route('custom-bouquet.add'), [
            'stems' => [
                (string) $redRose->id => 3, // Sadece 3 dal (yetersiz)
            ],
            'package_color' => (string) $pkg->id,
            'ribbon_color' => (string) $ribbon->id,
        ]);
        $failResponse->assertSessionHas('error');

        // 4. Geçerli seçim: 11 Kırmızı Gül + 5 Papatya = 16 dal
        // 11 * 55 TL = 605 TL, 5 * 35 TL = 175 TL => Toplam 780 TL
        $successResponse = $this->post(route('custom-bouquet.add'), [
            'stems' => [
                (string) $redRose->id => 11,
                (string) $daisy->id => 5,
            ],
            'package_color' => (string) $pkg->id,
            'ribbon_color' => (string) $ribbon->id,
            'instructions' => 'Güller gonca olsun lütfen',
        ]);

        $successResponse->assertRedirect(route('cart.index'));
        $successResponse->assertSessionHas('success');

        // Sepette özel buketin varlığını doğrula
        $cart = session('cart');
        $this->assertNotEmpty($cart);
        $customItem = collect($cart)->firstWhere('is_custom_bouquet', true);
        $this->assertNotNull($customItem);
        $this->assertEquals(780.00, (float) $customItem['price']);
        $this->assertEquals(16, $customItem['options']['total_stems']);
        $this->assertStringContainsString('11x Ekvador Kırmızı Gül', $customItem['options']['summary']);

        // 5. Sepeti görüntüle
        $cartView = $this->get(route('cart.index'));
        $cartView->assertStatus(200);
        $cartView->assertSee('Özel Tasarım Buket (16 Dal)');
        $cartView->assertSee('780');

        // 6. Sipariş olarak tamamla
        $region = \App\Models\Region::first();
        $checkoutResponse = $this->post(route('checkout.process'), [
            'customer_name' => 'Özel Buket Müşterisi',
            'customer_phone' => '05553332211',
            'address' => 'Göztepe Mah. Gül Sok. No: 5, Kadıköy',
            'region_id' => $region ? $region->id : 1,
            'payment_method' => 'cash',
            'delivery_type' => 'standard',
        ]);

        $checkoutResponse->assertRedirect();
        $order = Order::where('customer_name', 'Özel Buket Müşterisi')->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals(780.00, (float) $order->total_amount);

        // Sipariş kaleminin custom_bouquet ve options içerdiğini doğrula
        $orderItem = $order->items->firstWhere('item_type', 'custom_bouquet');
        $this->assertNotNull($orderItem);
        $this->assertNotNull($orderItem->options);
        $this->assertEquals(16, $orderItem->options['total_stems']);

        // Sipariş notunda reçetenin yer aldığını doğrula (atölye & fiş için)
        $this->assertStringContainsString('ÖZEL BUKET REÇETESİ', $order->notes);
        $this->assertStringContainsString('11x Ekvador Kırmızı Gül', $order->notes);
    }

    public function test_admin_bouquet_materials_crud_and_workshop_display()
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@yenicicek.com'],
            ['name' => 'Florist Admin', 'password' => bcrypt('cicek123456'), 'is_admin' => true, 'role' => 'admin']
        );

        // 1. Admin buket malzemeleri paneline erişim
        $indexResponse = $this->actingAs($admin)->get(route('admin.bouquet-items.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Buket Malzemeleri &amp; Sihirbaz Yönetimi', false);

        // 2. Admin yeni dal çiçek ekleme
        $storeResponse = $this->actingAs($admin)->post(route('admin.bouquet-items.stems.store'), [
            'name' => 'Beyaz Şakayık',
            'meaning' => 'Zarafet ve Asalet',
            'price' => 65.00,
            'icon' => '💮',
            'color_hex' => '#ffffff',
            'sort_order' => 1,
        ]);
        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('bouquet_stems', [
            'name' => 'Beyaz Şakayık',
            'price' => 65.00,
        ]);

        $stem = BouquetStem::where('name', 'Beyaz Şakayık')->first();
        $this->assertNotNull($stem);

        // 3. Durum aktif/pasif toggle (AJAX PATCH)
        $toggleResponse = $this->actingAs($admin)->patch(route('admin.bouquet-items.stems.toggle', $stem));
        $toggleResponse->assertJson(['success' => true, 'is_active' => false]);
        $stem->update(['is_active' => true]);

        // 3.b Admin yeni buket boyutu ekleme ve durum toggle
        $sizeStoreResponse = $this->actingAs($admin)->post(route('admin.bouquet-items.sizes.store'), [
            'name' => 'Ultra Lüks Dev Boy',
            'badge' => 'Özel Seri',
            'description' => 'Maksimum katmanlı dev buket',
            'price' => 250.00,
            'icon' => '👑',
            'sort_order' => 4,
        ]);
        $sizeStoreResponse->assertRedirect();
        $this->assertDatabaseHas('bouquet_sizes', [
            'name' => 'Ultra Lüks Dev Boy',
            'price' => 250.00,
        ]);

        $customSize = \App\Models\BouquetSize::where('name', 'Ultra Lüks Dev Boy')->first();
        $this->assertNotNull($customSize);

        $sizeToggleResponse = $this->actingAs($admin)->patch(route('admin.bouquet-items.sizes.toggle', $customSize));
        $sizeToggleResponse->assertJson(['success' => true, 'is_active' => false]);
        $customSize->update(['is_active' => true]);

        // 4. Genel atölye ayarlarını güncelleme
        $settingsResponse = $this->actingAs($admin)->post(route('admin.bouquet-items.settings.save'), [
            'custom_bouquet_enabled' => '1',
            'custom_bouquet_min_stems' => '8',
        ]);
        $settingsResponse->assertRedirect();
        $this->assertEquals(8, \App\Http\Controllers\CustomBouquetController::getMinStems());

        // 5. Özel buket siparişi oluştur
        $region = Region::firstOrCreate(
            ['name' => 'Kadıköy', 'slug' => 'kadikoy'],
            ['city' => 'İstanbul', 'delivery_fee' => 0, 'min_order_amount' => 100, 'is_active' => true]
        );

        $category = Category::first();
        $product = Product::firstOrCreate(
            ['slug' => 'kendi-buketini-olustur'],
            ['name' => 'Özel Buket Tasarımı', 'category_id' => $category ? $category->id : 1, 'price' => 520, 'is_active' => true, 'in_stock' => true]
        );

        $order = Order::create([
            'order_number' => 'YC-CUST-'.rand(1000, 9999),
            'customer_name' => 'Tasarım Ustası',
            'customer_phone' => '05551112233',
            'recipient_name' => 'Sevilen Kişi',
            'recipient_phone' => '05554445566',
            'recipient_district' => 'Kadıköy',
            'address' => 'Moda Cad. No: 12',
            'region_id' => $region->id,
            'total_amount' => 520.00,
            'payment_method' => 'cash',
            'status' => 'Atölyede Hazırlanıyor',
            'notes' => '🌸 [ÖZEL BUKET REÇETESİ]: 8x Beyaz Şakayık | Ambalaj: Mat Gece Siyahı & Altın',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 520.00,
            'item_type' => 'custom_bouquet',
            'options' => [
                'custom_bouquet' => true,
                'total_stems' => 8,
                'package_color' => 'Mat Gece Siyahı & Altın',
                'ribbon_color' => 'Altın Yaldızlı Kurdele',
                'summary' => '8x Beyaz Şakayık | Ambalaj: Mat Gece Siyahı & Altın | Kurdele: Altın Yaldızlı Kurdele',
                'instructions' => 'Siyah tül ambalaj sıkı bağlansın',
                'stems' => [
                    [
                        'id' => (string) $stem->id,
                        'name' => 'Beyaz Şakayık',
                        'icon' => '💮',
                        'quantity' => 8,
                        'unit_price' => 65.0,
                        'subtotal' => 520.0,
                    ]
                ],
            ],
        ]);

        // Sipariş Detayı Ekranı
        $showResponse = $this->actingAs($admin)->get(route('admin.orders.show', $order));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Özel Tasarım Buket');
        $showResponse->assertSee('Beyaz Şakayık');

        // Mobil Atölye Ekranı
        $workshopResponse = $this->actingAs($admin)->get(route('admin.workshop.index'));
        $workshopResponse->assertStatus(200);
        $workshopResponse->assertSee('Beyaz Şakayık');

        // A4 Çift Bölmeli Yazdırma Fişi
        $printResponse = $this->actingAs($admin)->get(route('admin.orders.print', $order));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('Özel Buket');
        $printResponse->assertSee('8x Beyaz Şakayık');

        // Müşteri Sipariş Takip Ekranı
        $trackResponse = $this->get(route('order.track', ['order_number' => $order->order_number]));
        $trackResponse->assertStatus(200);
        $trackResponse->assertSee('Beyaz Şakayık');
    }

    public function test_admin_orders_4_slot_perforated_print_and_bulk_print()
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@yenicicek.com'],
            ['name' => 'Florist Admin', 'password' => bcrypt('cicek123456'), 'is_admin' => true, 'role' => 'admin']
        );

        $region = Region::firstOrCreate(
            ['name' => 'Kadıköy', 'slug' => 'kadikoy'],
            ['city' => 'İstanbul', 'delivery_fee' => 0, 'min_order_amount' => 100, 'is_active' => true]
        );

        $order1 = Order::create([
            'order_number' => 'YC-PRNT-001',
            'customer_name' => 'Ahmet Yılmaz',
            'recipient_name' => 'Ayşe Kaya',
            'customer_phone' => '05321112233',
            'recipient_district' => 'Kadıköy',
            'address' => 'Bağdat Cad. No: 10',
            'card_note' => 'Nice mutlu yıllara birtanem!',
            'sender_name' => 'Ahmet',
            'total_amount' => 450.00,
            'region_id' => $region->id,
            'status' => 'Yeni Sipariş',
        ]);

        $order2 = Order::create([
            'order_number' => 'YC-PRNT-002',
            'customer_name' => 'Mehmet Demir',
            'recipient_name' => 'Zeynep Çelik',
            'customer_phone' => '05324445566',
            'recipient_district' => 'Beşiktaş',
            'address' => 'Çarşı Cad. No: 20',
            'card_note' => 'Geçmiş olsun dileklerimle...',
            'sender_name' => 'Mehmet',
            'total_amount' => 600.00,
            'region_id' => $region->id,
            'status' => 'Yeni Sipariş',
        ]);

        // 1. Tekli sipariş fiş yazdırma (4 gözlü A4 sayfası)
        $printResponse = $this->actingAs($admin)->get(route('admin.orders.print', $order1));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('YC-PRNT-001');
        $printResponse->assertSee('Nice mutlu yıllara birtanem!');
        $printResponse->assertSee('Ayşe Kaya');
        $printResponse->assertSee('Zarf İçi Kart');
        $printResponse->assertSee('Kağıtta Hangi Göze Basılsın?');

        // 2. Çoklu toplu yazdırma (bulkPrint)
        $bulkResponse = $this->actingAs($admin)->post(route('admin.orders.bulkPrint'), [
            'order_ids' => [$order1->id, $order2->id],
        ]);
        $bulkResponse->assertStatus(200);
        $bulkResponse->assertSee('YC-PRNT-001');
        $bulkResponse->assertSee('YC-PRNT-002');
        $bulkResponse->assertSee('Ayşe Kaya');
        $bulkResponse->assertSee('Zeynep Çelik');

        // 3. Sipariş seçilmeden bulkPrint çağrılırsa hata mesajıyla yönlendirme
        $emptyBulkResponse = $this->actingAs($admin)->post(route('admin.orders.bulkPrint'), [
            'order_ids' => [],
        ]);
        $emptyBulkResponse->assertRedirect(route('admin.orders.index'));
        $emptyBulkResponse->assertSessionHas('error');
    }

    public function test_craftsman_order_preparation_app_loads_and_live_checks_orders()
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@yenicicek.com'],
            ['name' => 'Florist Admin', 'password' => bcrypt('cicek123456'), 'is_admin' => true, 'role' => 'admin']
        );

        $region = Region::firstOrCreate(
            ['name' => 'Kadıköy', 'slug' => 'kadikoy'],
            ['city' => 'İstanbul', 'delivery_fee' => 150, 'min_order_amount' => 100, 'is_active' => true]
        );

        $category = Category::first();
        $product = Product::firstOrCreate(
            ['slug' => 'kirmizi-gul-buketi'],
            ['name' => '101 Kırmızı Gül Masalı', 'category_id' => $category ? $category->id : 1, 'price' => 1250, 'is_active' => true, 'in_stock' => true]
        );

        $order = Order::create([
            'order_number' => 'YC-USTA-001',
            'customer_name' => 'Murat Usta Testi',
            'customer_phone' => '05329998877',
            'recipient_name' => 'Selin Aksoy',
            'recipient_district' => 'Kadıköy',
            'address' => 'Moda Cad. No: 44',
            'card_note' => 'Yeni yaşın sana huzur getirsin!',
            'sender_name' => 'Murat',
            'region_id' => $region->id,
            'total_amount' => 1400.00,
            'delivery_fee' => 150.00, // VIP Hızlı Kurye
            'status' => 'Atölyede Hazırlanıyor',
            'delivery_time_slot' => '12:00 - 15:00',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 1250.00,
        ]);

        // 1. Kök /usta rotası yönlendirmesi
        $redirectResponse = $this->actingAs($admin)->get(route('craftsman'));
        $redirectResponse->assertRedirect(route('admin.workshop.craftsman'));

        // 2. Usta kokpiti tam ekran arayüzü yükleniyor
        $response = $this->actingAs($admin)->get(route('admin.workshop.craftsman'));
        $response->assertStatus(200);
        $response->assertSee('USTA HAZIRLIK KOKPİTİ');
        $response->assertSee('YC-USTA-001');
        $response->assertSee('VIP 2 SAATTE');
        $response->assertSee('Yeni yaşın sana huzur getirsin!');
        $response->assertSee('Selin Aksoy');
        $response->assertSee('Fotoğraf Çek');
        $response->assertSee('Kuryeye Ver');
        $response->assertSee('Fiş Yazdır');

        // 3. Canlı kontrol API'sinde sesli anons ve zengin reçete verisi dönüyor
        $lastId = $order->id - 1;
        $checkResponse = $this->actingAs($admin)->getJson(route('admin.workshop.craftsman.check', ['last_id' => $lastId]));
        $checkResponse->assertStatus(200);
        $checkResponse->assertJson(['has_new' => true]);
        $ordersData = $checkResponse->json('orders');
        $this->assertNotEmpty($ordersData);
        $this->assertEquals('YC-USTA-001', $ordersData[0]['order_number']);
        $this->assertStringContainsString('Kadıköy', $ordersData[0]['speech_text']);
        $this->assertTrue($ordersData[0]['is_vip']);
    }
}


