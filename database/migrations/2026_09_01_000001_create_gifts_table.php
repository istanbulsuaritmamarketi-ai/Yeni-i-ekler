<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gifts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('badge')->nullable(); // Örn: En Çok Tercih Edilen, Özel Hediye
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('icon')->default('fas fa-gift');
            $table->string('color')->default('purple'); // purple, blue, amber, emerald, pink
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Ürün tablosuna hediye alanı ekleme
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'has_gift')) {
                $table->boolean('has_gift')->default(true)->after('custom_badge');
            }
            if (! Schema::hasColumn('products', 'gift_note')) {
                $table->string('gift_note')->nullable()->after('has_gift');
            }
        });

        // Ürüne özel hediye pivot tablosu (İsteğe bağlı belirli hediyeleri seçebilmek için)
        Schema::create('gift_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('gift_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Varsayılan 4 hediyeyi veritabanına ekle
        $defaultGifts = [
            [
                'name' => '🧸 Işıklı & Dans Eden Sevimli Çocuk Oyuncağı',
                'badge' => 'En Çok Tercih Edilen',
                'description' => 'Çocukların çok seveceği neşeli ışıklı figür oyuncak (0 TL Hediye)',
                'icon' => 'fas fa-robot',
                'color' => 'purple',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '🍼 Okul İçin BPA İçermeyen Çocuk Su Matarası',
                'badge' => 'Çocuklar İçin Özel',
                'description' => '500ml sağlıklı sızdırmaz çocuk matarası (0 TL Hediye)',
                'icon' => 'fas fa-flask',
                'color' => 'blue',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '☕ Çift Katmanlı Paslanmaz Çelik Termos Bardak',
                'badge' => 'Ev & Araç İçin',
                'description' => 'Sıcak ve soğuğu 6 saat muhafaza eden şık termos (0 TL Hediye)',
                'icon' => 'fas fa-mug-hot',
                'color' => 'amber',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '🧪 Dijital TDS Su Saflık & Kalite Test Kalemi',
                'badge' => 'Su Kalitesi Ölçer',
                'description' => 'Musluk suyunuzun saflığını ve arıtma kalitesini test edin (0 TL Hediye)',
                'icon' => 'fas fa-vial',
                'color' => 'emerald',
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('gifts')->insert($defaultGifts);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_product');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['has_gift', 'gift_note']);
        });

        Schema::dropIfExists('gifts');
    }
};
