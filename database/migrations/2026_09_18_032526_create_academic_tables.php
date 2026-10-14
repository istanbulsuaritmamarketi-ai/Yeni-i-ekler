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
        // 1. Dersler
        Schema::create('academic_courses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->nullable();
            $table->timestamps();
        });

        // 2. Konu Oturumları
        Schema::create('academic_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('academic_courses')->onDelete('cascade');
            $table->string('title');
            $table->timestamps();
        });

        // 3. Kartlar ve Notlar
        Schema::create('academic_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('academic_topics')->onDelete('cascade');
            $table->integer('step_order')->default(1);
            $table->longText('front_image')->nullable(); // URL veya Base64
            $table->longText('back_text')->nullable();
            $table->json('extra_images_json')->nullable();
            $table->json('tags_json')->nullable();
            $table->timestamps();
        });

        // 4. Aralıklı Tekrar (SM-2) İlerlemesi
        Schema::create('academic_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('card_id')->unique()->constrained('academic_cards')->onDelete('cascade');
            $table->integer('repetition_count')->default(0);
            $table->float('ease_factor')->default(2.5);
            $table->integer('interval_days')->default(0);
            $table->dateTime('next_review_at')->nullable();
            $table->dateTime('last_reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_reviews');
        Schema::dropIfExists('academic_cards');
        Schema::dropIfExists('academic_topics');
        Schema::dropIfExists('academic_courses');
    }
};
