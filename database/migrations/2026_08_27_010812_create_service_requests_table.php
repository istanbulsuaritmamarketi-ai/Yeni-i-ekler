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
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->text('address');
            $table->string('service_type'); // Filtre Değişimi, Arıza/Bakım, Yeni Kurulum
            $table->string('device_info')->nullable();
            $table->text('issue_description')->nullable();
            $table->date('preferred_date');
            $table->string('preferred_time'); // Sabah, Öğle, Akşam
            $table->string('status')->default('Bekliyor');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
