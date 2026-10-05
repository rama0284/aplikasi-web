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
        'sugar_g',
        'sodium_mg',
        'serving_size',
        'health_grade',
        'icon_emoji',
        'description',
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
            'sugar_g' => 'float',
            'sodium_mg' => 'float',
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
            'fiber' => $this->fiber_per_100g !== null ? round($this->fiber_per_100g * $factor, 1) : 0,
            'sugar' => $this->sugar_g !== null ? round($this->sugar_g * $factor, 1) : 0,
            'sodium' => $this->sodium_mg !== null ? round($this->sodium_mg * $factor, 1) : 0,
        ];
    }

    /**
     * Badge visual status grade kesehatan
     */
    public function getGradeBadgeAttribute(): array
    {
        return match ($this->health_grade) {
            'A' => ['bg' => 'bg-emerald-100 text-emerald-800 border-emerald-300', 'label' => 'Sangat Sehat (Grade A)'],
            'B' => ['bg' => 'bg-teal-100 text-teal-800 border-teal-300', 'label' => 'Baik / Seimbang (Grade B)'],
            'C' => ['bg' => 'bg-amber-100 text-amber-800 border-amber-300', 'label' => 'Konsumsi Wajar (Grade C)'],
            'D' => ['bg' => 'bg-rose-100 text-rose-800 border-rose-300', 'label' => 'Batasi Konsumsi (Grade D)'],
            default => ['bg' => 'bg-stone-100 text-stone-800 border-stone-300', 'label' => 'Grade ' . ($this->health_grade ?: 'A')],
        };
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
