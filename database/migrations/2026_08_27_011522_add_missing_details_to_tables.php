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
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('wants_service')->default(false)->after('status');
        });

        Schema::table('service_requests', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('issue_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('wants_service');
        });

        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
