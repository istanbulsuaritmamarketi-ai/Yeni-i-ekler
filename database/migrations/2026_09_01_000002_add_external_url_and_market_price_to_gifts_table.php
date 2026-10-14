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
        Schema::table('gifts', function (Blueprint $table) {
            if (! Schema::hasColumn('gifts', 'external_url')) {
                $table->string('external_url', 1000)->nullable()->after('description');
            }
            if (! Schema::hasColumn('gifts', 'market_price')) {
                $table->decimal('market_price', 10, 2)->nullable()->after('external_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gifts', function (Blueprint $table) {
            if (Schema::hasColumn('gifts', 'external_url')) {
                $table->dropColumn('external_url');
            }
            if (Schema::hasColumn('gifts', 'market_price')) {
                $table->dropColumn('market_price');
            }
        });
    }
};
