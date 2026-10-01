<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\FoodScan;
use App\Models\MealLog;
use App\Services\AIServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ScannerController extends Controller
{
    protected AIServiceInterface $aiService;

    public function __construct(AIServiceInterface $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Tampilan form pengunggah foto makanan.
     */
    public function index(): View
    {
        $isAiConfigured = $this->aiService->isConfigured();
        $isDemoMode = (bool) config('services.nutriscan_ai.demo_mode', true);

        return view('scanner.index', compact('isAiConfigured', 'isDemoMode'));
    }

    /**
     * Proses analisis foto makanan melalui AI Vision.
     */
    public function analyze(Request $request): RedirectResponse
    {
        $request->validate([
            'image' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,png,webp',
                'max:5120', // Maksimal 5 MB
            ],
        ], [
            'image.required' => 'Silakan pilih foto makanan yang ingin dianalisis.',
            'image.image' => 'File yang diunggah harus berupa gambar.',
            'image.mimes' => 'Format gambar yang didukung hanya JPEG, PNG, dan WEBP.',
            'image.max' => 'Ukuran gambar tidak boleh melebihi 5 MB.',
        ]);

        $file = $request->file('image');
        $storedPath = $file->store('food_scans', 'public');
        $absolutePath = Storage::disk('public')->path($storedPath);

        // Panggil AI Vision Service dengan menyertakan nama file asli
        $aiResult = $this->aiService->identifyFood($absolutePath, $file->getMimeType(), $file->getClientOriginalName());

        if (!$aiResult['success'] && empty($aiResult['food_name'])) {
            return back()->with('error', $aiResult['error_message'] ?: 'AI tidak dapat mengidentifikasi makanan pada foto.');
        }

        $identifiedName = $aiResult['food_name'] ?: 'Makanan Terdeteksi';

        // 1. Pencocokan tepat (Exact match)
        $matchedFood = Food::whereRaw('LOWER(name) = ?', [strtolower($identifiedName)])->first();

        // 2. Pencocokan parsial dua arah
        if (!$matchedFood) {
            $matchedFood = Food::where('name', 'LIKE', '%' . $identifiedName . '%')
                ->orWhereRaw('? LIKE CONCAT("%", name, "%")', [$identifiedName])
                ->first();
        }

        // 3. Pencocokan token kesamaan kata kunci (Token overlap)
        if (!$matchedFood) {
            $targetTokens = array_filter(explode(' ', strtolower(preg_replace('/[^a-z0-9 ]/i', '', $identifiedName))));
            $allFoods = Food::all();
            $bestScore = 0;
            $bestCandidate = null;

            foreach ($allFoods as $candidate) {
                $foodTokens = array_filter(explode(' ', strtolower(preg_replace('/[^a-z0-9 ]/i', '', $candidate->name))));
                $overlap = count(array_intersect($targetTokens, $foodTokens));
                if ($overlap > $bestScore) {
                    $bestScore = $overlap;
                    $bestCandidate = $candidate;
                }
            }

            if ($bestScore > 0) {
                $matchedFood = $bestCandidate;
            }
        }

        $scan = FoodScan::create([
            'user_id' => Auth::id(),
            'image_path' => $storedPath,
            'identified_food_name' => $identifiedName,
            'food_id' => $matchedFood?->id,
            'estimated_portion_grams' => $aiResult['estimated_portion_grams'] ?? 200,
            'confidence' => $aiResult['confidence'] ?? 0.85,
            'ai_response' => $aiResult,
            'status' => $aiResult['success'] ? 'completed' : 'failed',
        ]);

        $statusMsg = $aiResult['is_demo']
            ? 'Analisis berhasil disimulasikan dalam [MODE DEMO].'
            : 'Foto makanan berhasil dianalisis oleh AI!';

        return redirect()->route('scanner.result', $scan->id)->with('success', $statusMsg);
    }

    /**
     * Tampilan hasil scan nutrisi beserta estimasi makronutrien dan opsi porsi.
     */
    public function show(int $id): View
    {
        $scan = FoodScan::with('food')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $allFoods = Food::orderBy('name')->get();
        $selectedFood = $scan->food;

        // Jika belum terhubung ke makanan di database, cari padanan terbaik
        if (!$selectedFood) {
            $matched = Food::whereRaw('LOWER(name) = ?', [strtolower($scan->identified_food_name)])->first();
            if (!$matched) {
                $matched = Food::where('name', 'LIKE', '%' . $scan->identified_food_name . '%')
                    ->orWhereRaw('? LIKE CONCAT("%", name, "%")', [$scan->identified_food_name])
                    ->first();
            }
            if ($matched) {
                $selectedFood = $matched;
                $scan->update(['food_id' => $matched->id]);
            }
        }

        // Porsi default dalam gram
        $portionGrams = $scan->estimated_portion_grams ?: 200;

        // Hitung nutrisi awal
        if ($selectedFood) {
            $nutrition = $selectedFood->calculateForPortion($portionGrams);
        } else {
            // Estimasi generik jika belum ada di database
            $nutrition = [
                'portion_grams' => $portionGrams,
                'calories' => round($portionGrams * 1.5, 1),
                'protein' => round($portionGrams * 0.08, 1),
                'carbohydrates' => round($portionGrams * 0.20, 1),
                'fat' => round($portionGrams * 0.05, 1),
                'fiber' => null,
            ];
        }

        return view('scanner.result', compact('scan', 'selectedFood', 'allFoods', 'portionGrams', 'nutrition'));
    }

    /**
     * Simpan hasil scan ke Food Diary pengguna.
     */
    public function saveToDiary(Request $request, int $id): RedirectResponse
    {
        $scan = FoodScan::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'meal_type' => ['required', 'in:sarapan,makan_siang,makan_malam,camilan'],
            'portion_grams' => ['required', 'numeric', 'min:10', 'max:2000'],
            'food_id' => ['nullable', 'exists:foods,id'],
            'custom_food_name' => ['nullable', 'string', 'max:255'],
        ], [
            'meal_type.required' => 'Silakan pilih waktu makan.',
            'portion_grams.required' => 'Porsi makanan harus diisi.',
            'portion_grams.min' => 'Porsi minimal adalah 10 gram.',
        ]);

        $portion = (float) $validated['portion_grams'];
        $food = null;

        if (!empty($validated['food_id'])) {
            $food = Food::find($validated['food_id']);
        }

        if ($food) {
            $calc = $food->calculateForPortion($portion);
            $calories = $calc['calories'];
            $protein = $calc['protein'];
            $carbs = $calc['carbohydrates'];
            $fat = $calc['fat'];
            $fiber = $calc['fiber'];
            $customName = null;
        } else {
            // Makanan kustom / estimasi belum terdaftar
            $factor = $portion / 100.0;
            $calories = round(($request->input('custom_calories', 150)) * $factor, 1);
            $protein = round(($request->input('custom_protein', 8)) * $factor, 1);
            $carbs = round(($request->input('custom_carbs', 20)) * $factor, 1);
            $fat = round(($request->input('custom_fat', 5)) * $factor, 1);
            $fiber = $request->filled('custom_fiber') ? round($request->input('custom_fiber') * $factor, 1) : null;
            $customName = $validated['custom_food_name'] ?: $scan->identified_food_name;
        }

        $mealLog = MealLog::create([
            'user_id' => Auth::id(),
            'food_id' => $food?->id,
            'food_scan_id' => $scan->id,
            'custom_food_name' => $customName,
            'meal_type' => $validated['meal_type'],
            'portion_grams' => $portion,
            'calories' => $calories,
            'protein' => $protein,
            'carbohydrates' => $carbs,
            'fat' => $fat,
            'fiber' => $fiber,
            'consumed_at' => Carbon::now(),
        ]);

        // Perbarui relasi food_id pada scan jika sebelumnya belum terhubung
        if ($food && !$scan->food_id) {
            $scan->update(['food_id' => $food->id]);
        }

        return redirect()->route('diary.index')->with('success', 'Makanan berhasil dicatat ke dalam Food Diary hari ini!');
    }
}
