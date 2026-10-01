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
        Schema::create('food_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->string('identified_food_name');
            $table->foreignId('food_id')->nullable()->constrained('foods')->nullOnDelete();
            $table->decimal('estimated_portion_grams', 8, 2)->nullable();
            $table->decimal('confidence', 5, 2)->nullable();
            $table->json('ai_response')->nullable();
            $table->string('status')->default('completed'); // pending, completed, failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_scans');
    }
};
