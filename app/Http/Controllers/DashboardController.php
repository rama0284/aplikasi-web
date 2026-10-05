<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\FoodScan;
use App\Models\MealLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard Utama NutriScan AI
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $goal = $user->getOrCreateGoal();
        $today = Carbon::today();

        // 1. Log Makanan hari ini
        $todayLogs = MealLog::with('food')
            ->where('user_id', $user->id)
            ->whereDate('consumed_at', $today)
            ->orderBy('consumed_at', 'desc')
            ->get();

        // 2. Total konsumsi harian
        $totalCalories = round($todayLogs->sum('calories'), 1);
        $totalProtein = round($todayLogs->sum('protein'), 1);
        $totalCarbs = round($todayLogs->sum('carbohydrates'), 1);
        $totalFat = round($todayLogs->sum('fat'), 1);
        $totalFiber = round($todayLogs->sum('fiber'), 1);

        // 3. Target harian & persentase pencapaian
        $calorieGoal = max(1, $goal->calorie_goal ?: 2100);
        $proteinGoal = max(1, $goal->protein_goal ?: 70);
        $carbsGoal = max(1, $goal->carbohydrates_goal ?: 260);
        $fatGoal = max(1, $goal->fat_goal ?: 60);

        $caloriePercent = min(100, round(($totalCalories / $calorieGoal) * 100));
        $proteinPercent = min(100, round(($totalProtein / $proteinGoal) * 100));
        $carbsPercent = min(100, round(($totalCarbs / $carbsGoal) * 100));
        $fatPercent = min(100, round(($totalFat / $fatGoal) * 100));

        $remainingCalories = max(0, round($calorieGoal - $totalCalories, 1));

        // 4. Pengelompokan makan hari ini
        $groupedMeals = [
            'sarapan' => $todayLogs->where('meal_type', 'sarapan'),
            'makan_siang' => $todayLogs->where('meal_type', 'makan_siang'),
            'makan_malam' => $todayLogs->where('meal_type', 'makan_malam'),
            'camilan' => $todayLogs->where('meal_type', 'camilan'),
        ];

        // 5. Data tren 7 hari terakhir (Kalori & Protein)
        $chartLabels = [];
        $chartCalories = [];
        $chartProtein = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $chartLabels[] = $day->locale('id')->isoFormat('ddd, D MMM');
            
            $dayLogs = MealLog::where('user_id', $user->id)
                ->whereDate('consumed_at', $day)
                ->get();

            $chartCalories[] = round($dayLogs->sum('calories'), 1);
            $chartProtein[] = round($dayLogs->sum('protein'), 1);
        }

        // 6. Statistik AI Food Scanner
        $scanCount = $user->foodScans()->count();
        $recentScans = $user->foodScans()->latest()->take(3)->get();

        // 7. Rekomendasi Pilihan Makanan Sehat dari Database
        $recommendedFoods = Food::whereIn('health_grade', ['A', 'B'])
            ->inRandomOrder()
            ->take(4)
            ->get();

        // 8. Total master makanan di database
        $totalFoodsCount = Food::count();

        return view('dashboard.index', compact(
            'user',
            'goal',
            'todayLogs',
            'groupedMeals',
            'totalCalories',
            'totalProtein',
            'totalCarbs',
            'totalFat',
            'totalFiber',
            'calorieGoal',
            'proteinGoal',
            'carbsGoal',
            'fatGoal',
            'caloriePercent',
            'proteinPercent',
            'carbsPercent',
            'fatPercent',
            'remainingCalories',
            'chartLabels',
            'chartCalories',
            'chartProtein',
            'scanCount',
            'recentScans',
            'recommendedFoods',
            'totalFoodsCount'
        ));
    }
}
