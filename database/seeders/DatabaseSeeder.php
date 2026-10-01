<?php

namespace Database\Seeders;

use App\Models\Food;
use App\Models\MealLog;
use App\Models\NutritionGoal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Indonesian foods database
        $this->call(FoodSeeder::class);

        // 2. Create standard demo user
        $demoUser = User::updateOrCreate(
            ['email' => 'demo@nutriscan.ai'],
            [
                'name' => 'Rama Putra',
                'password' => Hash::make('password123'),
            ]
        );

        // 3. Set nutrition goals
        NutritionGoal::updateOrCreate(
            ['user_id' => $demoUser->id],
            [
                'calorie_goal' => 2100,
                'protein_goal' => 70,
                'carbohydrates_goal' => 260,
                'fat_goal' => 60,
                'dietary_preferences' => 'Mengurangi gorengan berlebih, target protein harian optimal.',
            ]
        );

        // 4. Create sample initial meal logs for today if empty
        if ($demoUser->mealLogs()->count() === 0) {
            $nasi = Food::where('name', 'Nasi Putih')->first();
            $telur = Food::where('name', 'Telur Dadar')->first();
            $ayam = Food::where('name', 'Ayam Bakar')->first();
            $bayam = Food::where('name', 'Sayur Bayam Bening')->first();

            if ($nasi && $telur) {
                // Sarapan
                $nasiSarapan = $nasi->calculateForPortion(150);
                MealLog::create([
                    'user_id' => $demoUser->id,
                    'food_id' => $nasi->id,
                    'meal_type' => 'sarapan',
                    'portion_grams' => $nasiSarapan['portion_grams'],
                    'calories' => $nasiSarapan['calories'],
                    'protein' => $nasiSarapan['protein'],
                    'carbohydrates' => $nasiSarapan['carbohydrates'],
                    'fat' => $nasiSarapan['fat'],
                    'fiber' => $nasiSarapan['fiber'],
                    'consumed_at' => Carbon::today()->setTime(7, 30),
                ]);

                $telurSarapan = $telur->calculateForPortion(60);
                MealLog::create([
                    'user_id' => $demoUser->id,
                    'food_id' => $telur->id,
                    'meal_type' => 'sarapan',
                    'portion_grams' => $telurSarapan['portion_grams'],
                    'calories' => $telurSarapan['calories'],
                    'protein' => $telurSarapan['protein'],
                    'carbohydrates' => $telurSarapan['carbohydrates'],
                    'fat' => $telurSarapan['fat'],
                    'fiber' => $telurSarapan['fiber'],
                    'consumed_at' => Carbon::today()->setTime(7, 35),
                ]);
            }

            if ($nasi && $ayam && $bayam) {
                // Makan Siang
                $nasiSiang = $nasi->calculateForPortion(200);
                MealLog::create([
                    'user_id' => $demoUser->id,
                    'food_id' => $nasi->id,
                    'meal_type' => 'makan_siang',
                    'portion_grams' => $nasiSiang['portion_grams'],
                    'calories' => $nasiSiang['calories'],
                    'protein' => $nasiSiang['protein'],
                    'carbohydrates' => $nasiSiang['carbohydrates'],
                    'fat' => $nasiSiang['fat'],
                    'fiber' => $nasiSiang['fiber'],
                    'consumed_at' => Carbon::today()->setTime(12, 45),
                ]);

                $ayamSiang = $ayam->calculateForPortion(120);
                MealLog::create([
                    'user_id' => $demoUser->id,
                    'food_id' => $ayam->id,
                    'meal_type' => 'makan_siang',
                    'portion_grams' => $ayamSiang['portion_grams'],
                    'calories' => $ayamSiang['calories'],
                    'protein' => $ayamSiang['protein'],
                    'carbohydrates' => $ayamSiang['carbohydrates'],
                    'fat' => $ayamSiang['fat'],
                    'fiber' => $ayamSiang['fiber'],
                    'consumed_at' => Carbon::today()->setTime(12, 50),
                ]);

                $bayamSiang = $bayam->calculateForPortion(100);
                MealLog::create([
                    'user_id' => $demoUser->id,
                    'food_id' => $bayam->id,
                    'meal_type' => 'makan_siang',
                    'portion_grams' => $bayamSiang['portion_grams'],
                    'calories' => $bayamSiang['calories'],
                    'protein' => $bayamSiang['protein'],
                    'carbohydrates' => $bayamSiang['carbohydrates'],
                    'fat' => $bayamSiang['fat'],
                    'fiber' => $bayamSiang['fiber'],
                    'consumed_at' => Carbon::today()->setTime(12, 52),
                ]);
            }
        }
    }
}
