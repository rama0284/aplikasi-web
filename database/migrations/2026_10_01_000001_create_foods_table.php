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
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('category')->index(); // Makanan Pokok, Lauk Pauk, Sayuran, Buah-buahan, Camilan Sehat, Minuman
            $table->decimal('calories_per_100g', 8, 2);
            $table->decimal('protein_per_100g', 8, 2);
            $table->decimal('carbohydrates_per_100g', 8, 2);
            $table->decimal('fat_per_100g', 8, 2);
            $table->decimal('fiber_per_100g', 8, 2)->nullable()->default(0);
            $table->decimal('sugar_g', 8, 2)->nullable()->default(0);
            $table->decimal('sodium_mg', 8, 2)->nullable()->default(0);
            $table->string('serving_size')->default('1 porsi (100g)');
            $table->string('health_grade', 2)->nullable()->default('A'); // A, B, C, D
            $table->string('icon_emoji', 32)->nullable()->default('🥗');
            $table->text('description')->nullable();
            $table->string('nutrition_source')->nullable()->default('Tabel Komposisi Pangan Indonesia (TKPI) Kemenkes RI');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};
