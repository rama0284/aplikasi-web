<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'food_id',
        'food_scan_id',
        'custom_food_name',
        'meal_type',
        'portion_grams',
        'calories',
        'protein',
        'carbohydrates',
        'fat',
        'fiber',
        'consumed_at',
    ];

    protected function casts(): array
    {
        return [
            'portion_grams' => 'float',
            'calories' => 'float',
            'protein' => 'float',
            'carbohydrates' => 'float',
            'fat' => 'float',
            'fiber' => 'float',
            'consumed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }

    public function foodScan(): BelongsTo
    {
        return $this->belongsTo(FoodScan::class);
    }

    /**
     * Get readable display name for the food item.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->food ? $this->food->name : ($this->custom_food_name ?: 'Makanan Kustom');
    }

    /**
     * URL foto real untuk item makanan pada log ini.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->food) {
            return $this->food->imageUrl();
        }

        $name = strtolower((string) ($this->custom_food_name ?? ''));

        foreach (Food::IMAGE_MAP as $keyword => $file) {
            if (str_contains($name, $keyword)) {
                return asset('images/foods/' . $file);
            }
        }

        return asset('images/foods/salad.jpg');
    }

    /**
     * Get label for Indonesian meal type.
     */
    public function getMealTypeLabelAttribute(): string
    {
        return match ($this->meal_type) {
            'sarapan' => 'Sarapan',
            'makan_siang' => 'Makan Siang',
            'makan_malam' => 'Makan Malam',
            'camilan' => 'Camilan',
            default => ucfirst(str_replace('_', ' ', $this->meal_type)),
        };
    }
}
