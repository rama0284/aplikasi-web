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
        Schema::create('nutrition_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('calorie_goal')->default(2000)->nullable();
            $table->unsignedInteger('protein_goal')->default(60)->nullable(); // grams
            $table->unsignedInteger('carbohydrates_goal')->default(250)->nullable(); // grams
            $table->unsignedInteger('fat_goal')->default(65)->nullable(); // grams
            $table->text('dietary_preferences')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrition_goals');
    }
};
