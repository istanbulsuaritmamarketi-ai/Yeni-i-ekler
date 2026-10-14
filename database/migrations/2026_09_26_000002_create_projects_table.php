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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->nullable(); // örn: Konut, Ticari, Villa, Web, Mobil
            $table->string('location')->nullable(); // örn: Kadıköy / İstanbul
            $table->string('status')->default('Tamamlandı'); // Tamamlandı, Devam Ediyor, Proje Aşamasında
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->string('featured_image')->nullable();
            $table->json('gallery')->nullable();
            $table->json('specifications')->nullable(); // örn: {"m2": "145", "daire_sayisi": "24", "teslim_tarihi": "2026"}
            $table->string('client')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
