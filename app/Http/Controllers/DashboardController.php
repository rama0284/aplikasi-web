<?php

namespace App\Http\Controllers;

use App\Models\MealLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $goal = $user->getOrCreateGoal();
        $today = Carbon::today();

        // 1. Makanan hari ini
        $todayLogs = MealLog::with('food')
            ->where('user_id', $user->id)
            ->whereDate('consumed_at', $today)
            ->orderBy('consumed_at', 'desc')
            ->get();

        // 2. Total makronutrien hari ini
        $totalCalories = round($todayLogs->sum('calories'), 1);
        $totalProtein = round($todayLogs->sum('protein'), 1);
        $totalCarbs = round($todayLogs->sum('carbohydrates'), 1);
        $totalFat = round($todayLogs->sum('fat'), 1);
        $totalFiber = round($todayLogs->sum('fiber'), 1);

        // 3. Persentase pencapaian target harian
        $calorieGoal = max(1, $goal->calorie_goal ?: 2000);
        $proteinGoal = max(1, $goal->protein_goal ?: 60);
        $carbsGoal = max(1, $goal->carbohydrates_goal ?: 250);
        $fatGoal = max(1, $goal->fat_goal ?: 65);

        $caloriePercent = min(100, round(($totalCalories / $calorieGoal) * 100));
        $proteinPercent = min(100, round(($totalProtein / $proteinGoal) * 100));
        $carbsPercent = min(100, round(($totalCarbs / $carbsGoal) * 100));
        $fatPercent = min(100, round(($totalFat / $fatGoal) * 100));

        // 4. Data 7 hari terakhir untuk grafik tren konsumsi
        $chartLabels = [];
        $chartCalories = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $chartLabels[] = $day->locale('id')->isoFormat('ddd, D MMM');
            $daySum = MealLog::where('user_id', $user->id)
                ->whereDate('consumed_at', $day)
                ->sum('calories');
            $chartCalories[] = round($daySum, 1);
        }

        // 5. Total scan yang pernah dilakukan
        $scanCount = $user->foodScans()->count();

        return view('dashboard.index', compact(
            'user',
            'goal',
            'todayLogs',
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
            'chartLabels',
            'chartCalories',
            'scanCount'
        ));
    }
}
