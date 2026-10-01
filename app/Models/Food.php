<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Food extends Model
{
    use HasFactory;

    protected $table = 'foods';

    protected $fillable = [
        'name',
        'category',
        'calories_per_100g',
        'protein_per_100g',
        'carbohydrates_per_100g',
        'fat_per_100g',
        'fiber_per_100g',
        'nutrition_source',
    ];

    protected function casts(): array
    {
        return [
            'calories_per_100g' => 'float',
            'protein_per_100g' => 'float',
            'carbohydrates_per_100g' => 'float',
            'fat_per_100g' => 'float',
            'fiber_per_100g' => 'float',
        ];
    }

    /**
     * Hitung nutrisi untuk berat porsi tertentu dalam gram.
     * Rumus: nutrisi aktual = nutrisi per 100 gram * berat porsi / 100
     */
    public function calculateForPortion(float $portionGrams): array
    {
        $factor = max(0, $portionGrams) / 100.0;

        return [
            'portion_grams' => round($portionGrams, 1),
            'calories' => round($this->calories_per_100g * $factor, 1),
            'protein' => round($this->protein_per_100g * $factor, 1),
            'carbohydrates' => round($this->carbohydrates_per_100g * $factor, 1),
            'fat' => round($this->fat_per_100g * $factor, 1),
            'fiber' => $this->fiber_per_100g !== null ? round($this->fiber_per_100g * $factor, 1) : null,
        ];
    }

    public function mealLogs(): HasMany
    {
        return $this->hasMany(MealLog::class);
    }

    public function foodScans(): HasMany
    {
        return $this->hasMany(FoodScan::class);
    }
}
