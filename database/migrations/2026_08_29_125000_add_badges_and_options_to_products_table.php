<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('has_free_installation')->default(true)->after('in_stock');
            $table->string('warranty_period')->default('2 Yıl Garanti')->after('has_free_installation');
            $table->boolean('has_cash_on_delivery')->default(true)->after('warranty_period');
            $table->string('custom_badge')->nullable()->after('has_cash_on_delivery');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'has_free_installation',
                'warranty_period',
                'has_cash_on_delivery',
                'custom_badge',
            ]);
        });
    }
};
