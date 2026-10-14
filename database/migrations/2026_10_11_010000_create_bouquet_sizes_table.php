<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bouquet_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('badge')->nullable();
            $table->string('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('image_path')->nullable();
            $table->string('icon')->default('💐');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bouquet_sizes');
    }
};
