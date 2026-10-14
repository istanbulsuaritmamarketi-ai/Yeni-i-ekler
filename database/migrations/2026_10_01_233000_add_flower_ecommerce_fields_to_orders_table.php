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
            if (! Schema::hasColumn('orders', 'delivery_date')) {
                $table->date('delivery_date')->nullable()->after('address');
            }
            if (! Schema::hasColumn('orders', 'delivery_time_slot')) {
                $table->string('delivery_time_slot')->nullable()->after('delivery_date');
            }
            if (! Schema::hasColumn('orders', 'recipient_name')) {
                $table->string('recipient_name')->nullable()->after('customer_name');
            }
            if (! Schema::hasColumn('orders', 'recipient_phone')) {
                $table->string('recipient_phone')->nullable()->after('recipient_name');
            }
            if (! Schema::hasColumn('orders', 'recipient_district')) {
                $table->string('recipient_district')->nullable()->after('recipient_phone');
            }
            if (! Schema::hasColumn('orders', 'card_note')) {
                $table->text('card_note')->nullable()->after('notes');
            }
            if (! Schema::hasColumn('orders', 'card_template_id')) {
                $table->string('card_template_id')->nullable()->after('card_note');
            }
            if (! Schema::hasColumn('orders', 'sender_name')) {
                $table->string('sender_name')->nullable()->after('card_template_id');
            }
            if (! Schema::hasColumn('orders', 'is_anonymous')) {
                $table->boolean('is_anonymous')->default(false)->after('sender_name');
            }
            if (! Schema::hasColumn('orders', 'preparation_photo')) {
                $table->string('preparation_photo')->nullable()->after('status');
            }
            if (! Schema::hasColumn('orders', 'delivery_fee')) {
                $table->decimal('delivery_fee', 10, 2)->default(0)->after('total_amount');
            }
            if (! Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->default('paytr')->after('status');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'item_type')) {
                $table->string('item_type')->default('product')->after('product_id');
            }
            if (! Schema::hasColumn('order_items', 'options')) {
                $table->json('options')->nullable()->after('total');
            }
        });

        Schema::table('gifts', function (Blueprint $table) {
            if (! Schema::hasColumn('gifts', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('description');
            }
            if (! Schema::hasColumn('gifts', 'category')) {
                $table->string('category')->default('upsell')->after('badge');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_date',
                'delivery_time_slot',
                'recipient_name',
                'recipient_phone',
                'recipient_district',
                'card_note',
                'card_template_id',
                'sender_name',
                'is_anonymous',
                'preparation_photo',
                'delivery_fee',
                'payment_method',
            ]);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['item_type', 'options']);
        });

        Schema::table('gifts', function (Blueprint $table) {
            $table->dropColumn(['price', 'category']);
        });
    }
};
