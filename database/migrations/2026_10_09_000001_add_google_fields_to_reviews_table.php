<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->string('source')->default('site')->after('is_approved'); // 'site' or 'google'
            $table->string('location')->nullable()->after('source'); // e.g. Kadıköy, İstanbul
            $table->string('author_photo')->nullable()->after('location');
            $table->string('google_review_id')->nullable()->after('author_photo');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['source', 'location', 'author_photo', 'google_review_id']);
        });
    }
};
