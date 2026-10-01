<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\MealLog;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FoodDiaryController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $dateParam = $request->query('date', Carbon::today()->format('Y-m-d'));

        try {
            $selectedDate = Carbon::parse($dateParam);
        } catch (\Throwable $e) {
            $selectedDate = Carbon::today();
        }

        $logs = MealLog::with(['food', 'foodScan'])
            ->where('user_id', $user->id)
            ->whereDate('consumed_at', $selectedDate)
            ->orderBy('consumed_at', 'asc')
            ->get();

        $groupedLogs = [
            'sarapan' => $logs->where('meal_type', 'sarapan'),
            'makan_siang' => $logs->where('meal_type', 'makan_siang'),
            'makan_malam' => $logs->where('meal_type', 'makan_malam'),
            'camilan' => $logs->where('meal_type', 'camilan'),
        ];

        $totalCalories = round($logs->sum('calories'), 1);
        $totalProtein = round($logs->sum('protein'), 1);
        $totalCarbs = round($logs->sum('carbohydrates'), 1);
        $totalFat = round($logs->sum('fat'), 1);
        $totalFiber = round($logs->sum('fiber'), 1);

        $goal = $user->getOrCreateGoal();
        $allFoods = Food::orderBy('name')->get();

        return view('diary.index', compact(
            'logs',
            'groupedLogs',
            'selectedDate',
            'totalCalories',
            'totalProtein',
            'totalCarbs',
            'totalFat',
            'totalFiber',
            'goal',
            'allFoods'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'meal_type' => ['required', 'in:sarapan,makan_siang,makan_malam,camilan'],
            'portion_grams' => ['required', 'numeric', 'min:5', 'max:2500'],
            'consumed_at' => ['required', 'date'],
            'food_id' => ['nullable', 'exists:foods,id'],
            'custom_food_name' => ['nullable', 'string', 'max:255'],
            'custom_calories' => ['nullable', 'numeric', 'min:0'],
            'custom_protein' => ['nullable', 'numeric', 'min:0'],
            'custom_carbs' => ['nullable', 'numeric', 'min:0'],
            'custom_fat' => ['nullable', 'numeric', 'min:0'],
        ], [
            'meal_type.required' => 'Waktu makan wajib dipilih.',
            'portion_grams.required' => 'Ukuran porsi wajib diisi.',
            'consumed_at.required' => 'Waktu konsumsi wajib diisi.',
        ]);

        $portion = (float) $validated['portion_grams'];
        $consumedAt = Carbon::parse($validated['consumed_at']);

        if (!empty($validated['food_id'])) {
            $food = Food::findOrFail($validated['food_id']);
            $calc = $food->calculateForPortion($portion);

            MealLog::create([
                'user_id' => Auth::id(),
                'food_id' => $food->id,
                'custom_food_name' => null,
                'meal_type' => $validated['meal_type'],
                'portion_grams' => $portion,
                'calories' => $calc['calories'],
                'protein' => $calc['protein'],
                'carbohydrates' => $calc['carbohydrates'],
                'fat' => $calc['fat'],
                'fiber' => $calc['fiber'],
                'consumed_at' => $consumedAt,
            ]);
        } else {
            $name = $validated['custom_food_name'] ?: 'Makanan Kustom';
            $factor = $portion / 100.0;
            $calories = round(($validated['custom_calories'] ?? 150) * $factor, 1);
            $protein = round(($validated['custom_protein'] ?? 5) * $factor, 1);
            $carbs = round(($validated['custom_carbs'] ?? 20) * $factor, 1);
            $fat = round(($validated['custom_fat'] ?? 4) * $factor, 1);

            MealLog::create([
                'user_id' => Auth::id(),
                'food_id' => null,
                'custom_food_name' => $name,
                'meal_type' => $validated['meal_type'],
                'portion_grams' => $portion,
                'calories' => $calories,
                'protein' => $protein,
                'carbohydrates' => $carbs,
                'fat' => $fat,
                'fiber' => null,
                'consumed_at' => $consumedAt,
            ]);
        }

        return redirect()->route('diary.index', ['date' => $consumedAt->format('Y-m-d')])
            ->with('success', 'Catatan makanan berhasil ditambahkan ke buku harian.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $log = MealLog::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $date = $log->consumed_at->format('Y-m-d');
        $log->delete();

        return redirect()->route('diary.index', ['date' => $date])
            ->with('info', 'Catatan makanan telah dihapus.');
    }
}
