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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost_price', 10, 2)->nullable()->after('price');
            $table->string('seo_title')->nullable()->after('technical_features');
            $table->text('seo_description')->nullable()->after('seo_title');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('id')->constrained('customers')->nullOnDelete();
            $table->decimal('total_cost', 10, 2)->nullable()->after('total_amount');
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_content')->nullable();
        });

        Schema::table('service_requests', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('id')->constrained('customers')->nullOnDelete();
            $table->string('assigned_staff')->nullable()->after('status');
            $table->date('next_maintenance_date')->nullable()->after('assigned_staff');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['cost_price', 'seo_title', 'seo_description']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['customer_id', 'total_cost', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_content']);
        });

        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['customer_id', 'assigned_staff', 'next_maintenance_date']);
        });
    }
};
