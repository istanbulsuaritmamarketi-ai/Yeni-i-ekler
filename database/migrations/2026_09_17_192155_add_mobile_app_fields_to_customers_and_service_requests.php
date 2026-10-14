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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('device_brand')->nullable()->after('address');
            $table->string('device_model')->nullable()->after('device_brand');
            $table->decimal('tds_value', 8, 2)->nullable()->after('device_model');
        });

        Schema::table('service_requests', function (Blueprint $table) {
            $table->string('changed_filters')->nullable()->after('service_type');
            $table->text('service_note')->nullable()->after('changed_filters');
            $table->decimal('tds_value', 8, 2)->nullable()->after('service_note');
            $table->text('photos')->nullable()->after('image_path');
            $table->boolean('is_reminder_sent')->default(false)->after('next_maintenance_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['device_brand', 'device_model', 'tds_value']);
        });

        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn(['changed_filters', 'service_note', 'tds_value', 'photos', 'is_reminder_sent']);
        });
    }
};
