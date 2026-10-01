<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NutritionGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'calorie_goal',
        'protein_goal',
        'carbohydrates_goal',
        'fat_goal',
        'dietary_preferences',
    ];

    protected function casts(): array
    {
        return [
            'calorie_goal' => 'integer',
            'protein_goal' => 'integer',
            'carbohydrates_goal' => 'integer',
            'fat_goal' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
