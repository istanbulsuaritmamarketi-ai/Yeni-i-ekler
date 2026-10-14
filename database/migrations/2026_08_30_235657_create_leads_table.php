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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone');
            $table->string('district')->nullable();
            $table->string('source')->default('spin_wheel'); // spin_wheel, water_test, catalog, exit_popup
            $table->string('prize_won')->nullable(); // e.g. 500 TL İndirim, Yedek Filtre Hediye
            $table->string('coupon_code')->nullable();
            $table->string('status')->default('new'); // new, contacted, converted, cancelled
            $table->text('notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
