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
        // İnşaat ve Tadilat Projeleri Tablosu
        Schema::create('renovation_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('contractor_name')->default('Reform İnşaat'); // Reform İnşaat, Onur İnşaat
            $table->string('category')->default('Mutfak Tadilatı'); // Mutfak, Banyo, Komple Daire, Villa/Ofis
            $table->string('location')->nullable(); // Kadıköy, Beylikdüzü, Beşiktaş vb.
            $table->text('description')->nullable();
            $table->string('before_image_path')->nullable(); // Öncesi görseli
            $table->string('after_image_path')->nullable(); // Sonrası görseli (Ana kapak)
            $table->json('gallery_images')->nullable(); // Ek proje fotoğrafları
            $table->string('duration')->nullable(); // Örn: 14 Gün
            $table->decimal('budget_approx', 12, 2)->nullable(); // Yaklaşık bütçe
            $table->boolean('has_free_purifier_gift')->default(true); // Arıtma hediyesi dahil mi?
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Ücretsiz Keşif & Tadilat Teklif Talepleri (Leads)
        Schema::create('renovation_leads', function (Blueprint $table) {
            $table->id();
            $table->string('contractor_name')->default('Reform İnşaat');
            $table->string('customer_name');
            $table->string('phone');
            $table->string('district')->nullable(); // İlçe
            $table->string('service_type')->nullable(); // Mutfak, Banyo, Komple Daire vb.
            $table->integer('property_size_sqm')->nullable(); // m²
            $table->decimal('estimated_budget', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('new'); // new, contacted, appointment_set, proposal_sent, completed, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renovation_leads');
        Schema::dropIfExists('renovation_projects');
    }
};
