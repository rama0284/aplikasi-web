<?php

namespace App\Http\Controllers;

use App\Models\FoodScan;
use App\Models\MealLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $range = $request->query('range', '7'); // 7 atau 30 hari
        $daysCount = in_array($range, ['7', '30']) ? (int) $range : 7;

        $labels = [];
        $caloriesData = [];
        $proteinData = [];
        $carbsData = [];
        $fatData = [];

        for ($i = $daysCount - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->locale('id')->isoFormat($daysCount > 7 ? 'D MMM' : 'ddd, D MMM');

            $dayLogs = MealLog::where('user_id', $user->id)
                ->whereDate('consumed_at', $date)
                ->get();

            $caloriesData[] = round($dayLogs->sum('calories'), 1);
            $proteinData[] = round($dayLogs->sum('protein'), 1);
            $carbsData[] = round($dayLogs->sum('carbohydrates'), 1);
            $fatData[] = round($dayLogs->sum('fat'), 1);
        }

        // Statistik rata-rata
        $totalCaloriesPeriod = array_sum($caloriesData);
        $avgCalories = $daysCount > 0 ? round($totalCaloriesPeriod / $daysCount, 1) : 0;
        $totalProteinPeriod = array_sum($proteinData);
        $avgProtein = $daysCount > 0 ? round($totalProteinPeriod / $daysCount, 1) : 0;

        // Riwayat seluruh scan gambar
        $scans = FoodScan::with('food')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('history.index', compact(
            'range',
            'daysCount',
            'labels',
            'caloriesData',
            'proteinData',
            'carbsData',
            'fatData',
            'avgCalories',
            'avgProtein',
            'scans'
        ));
    }
}
