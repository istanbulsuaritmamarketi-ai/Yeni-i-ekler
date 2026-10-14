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
        Schema::table('banners', function (Blueprint $table) {
            $table->string('position')->default('hero_main')->after('link_url')->nullable();
            $table->string('badge')->nullable()->after('position');
            $table->string('button_text')->nullable()->after('badge');
            $table->string('delivery_badge')->nullable()->after('button_text');
            $table->string('category_tag')->nullable()->after('delivery_badge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['position', 'badge', 'button_text', 'delivery_badge', 'category_tag']);
        });
    }
};
