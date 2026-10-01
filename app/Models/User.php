<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relation to food scans.
     */
    public function foodScans(): HasMany
    {
        return $this->hasMany(FoodScan::class);
    }

    /**
     * Relation to meal logs.
     */
    public function mealLogs(): HasMany
    {
        return $this->hasMany(MealLog::class);
    }

    /**
     * Relation to user's daily nutrition goal.
     */
    public function nutritionGoal(): HasOne
    {
        return $this->hasOne(NutritionGoal::class);
    }

    /**
     * Get or create default nutrition goal for user.
     */
    public function getOrCreateGoal(): NutritionGoal
    {
        return $this->nutritionGoal ?: $this->nutritionGoal()->create([
            'calorie_goal' => 2000,
            'protein_goal' => 60,
            'carbohydrates_goal' => 250,
            'fat_goal' => 65,
        ]);
    }
}
