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
            $table->string('customer_ip', 45)->nullable()->after('notes');
            $table->text('customer_user_agent')->nullable()->after('customer_ip');
            $table->timestamp('kvkk_accepted_at')->nullable()->after('customer_user_agent');
            $table->timestamp('contract_accepted_at')->nullable()->after('kvkk_accepted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'customer_ip',
                'customer_user_agent',
                'kvkk_accepted_at',
                'contract_accepted_at',
            ]);
        });
    }
};
