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
        Schema::create('cookie_consents', function (Blueprint $table) {
            $table->id();
            $table->string('consent_token', 64)->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->enum('consent_type', ['all', 'custom', 'rejected'])->default('all');
            $table->boolean('necessary')->default(true);
            $table->boolean('analytics')->default(true);
            $table->boolean('marketing')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cookie_consents');
    }
};
