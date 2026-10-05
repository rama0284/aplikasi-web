<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\MealLog;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FoodController extends Controller
{
    /**
     * Tampilkan katalog & database makanan dengan pencarian dan filter kategori.
     */
    public function index(Request $request): View
    {
        $query = Food::query();

        // Pencarian nama makanan atau deskripsi
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan kategori
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Sorting
        $sort = $request->input('sort', 'name_asc');
        match ($sort) {
            'calories_desc' => $query->orderBy('calories_per_100g', 'desc'),
            'calories_asc' => $query->orderBy('calories_per_100g', 'asc'),
            'protein_desc' => $query->orderBy('protein_per_100g', 'desc'),
            'fiber_desc' => $query->orderBy('fiber_per_100g', 'desc'),
            default => $query->orderBy('name', 'asc'),
        };

        $foods = $query->paginate(12)->withQueryString();

        // Kategori unik untuk filter dropdown
        $categories = Food::select('category')->distinct()->pluck('category');

        // Statistik database nutrisi
        $stats = [
            'total' => Food::count(),
            'high_protein' => Food::where('protein_per_100g', '>=', 15)->count(),
            'low_cal' => Food::where('calories_per_100g', '<=', 100)->count(),
            'high_fiber' => Food::where('fiber_per_100g', '>=', 2.5)->count(),
        ];

        return view('foods.index', compact('foods', 'categories', 'stats'));
    }

    /**
     * Tampilkan form penambahan makanan baru ke database.
     */
    public function create(): View
    {
        $existingCategories = Food::select('category')->distinct()->pluck('category');

        return view('foods.create', compact('existingCategories'));
    }

    /**
     * Simpan data makanan baru ke basis data MySQL.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:foods,name'],
            'category' => ['required', 'string', 'max:100'],
            'calories_per_100g' => ['required', 'numeric', 'min:0', 'max:1500'],
            'protein_per_100g' => ['required', 'numeric', 'min:0', 'max:100'],
            'carbohydrates_per_100g' => ['required', 'numeric', 'min:0', 'max:100'],
            'fat_per_100g' => ['required', 'numeric', 'min:0', 'max:100'],
            'fiber_per_100g' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'sugar_g' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'sodium_mg' => ['nullable', 'numeric', 'min:0', 'max:5000'],
            'serving_size' => ['required', 'string', 'max:255'],
            'health_grade' => ['required', 'in:A,B,C,D'],
            'icon_emoji' => ['nullable', 'string', 'max:10'],
            'description' => ['nullable', 'string', 'max:1000'],
            'nutrition_source' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama makanan wajib diisi.',
            'name.unique' => 'Makanan dengan nama ini sudah terdaftar dalam database.',
            'category.required' => 'Kategori makanan wajib dipilih atau diisi.',
            'calories_per_100g.required' => 'Nilai kalori per 100g wajib diisi.',
            'protein_per_100g.required' => 'Nilai protein per 100g wajib diisi.',
            'carbohydrates_per_100g.required' => 'Nilai karbohidrat per 100g wajib diisi.',
            'fat_per_100g.required' => 'Nilai lemak per 100g wajib diisi.',
            'serving_size.required' => 'Porsi standar penyajian wajib diisi.',
            'health_grade.required' => 'Grade kesehatan wajib dipilih.',
        ]);

        $validated['icon_emoji'] = $validated['icon_emoji'] ?: '🥗';
        $validated['nutrition_source'] = $validated['nutrition_source'] ?: 'Database Mandiri NutriScan AI';

        $food = Food::create($validated);

        return redirect()->route('foods.show', $food->id)
            ->with('success', "Makanan '{$food->name}' berhasil ditambahkan ke Database Nutrisi!");
    }

    /**
     * Tampilkan detail informasi gizi makanan lengkap dengan kalkulator porsi.
     */
    public function show(Food $food): View
    {
        // Hitung persentase kalori makronutrien (4-4-9 rule)
        // Protein: 4 kkal/g, Karbo: 4 kkal/g, Lemak: 9 kkal/g
        $protCal = $food->protein_per_100g * 4;
        $carbCal = $food->carbohydrates_per_100g * 4;
        $fatCal = $food->fat_per_100g * 9;
        $totalMacroCal = max(1, $protCal + $carbCal + $fatCal);

        $macroRatio = [
            'protein' => round(($protCal / $totalMacroCal) * 100),
            'carbs' => round(($carbCal / $totalMacroCal) * 100),
            'fat' => round(($fatCal / $totalMacroCal) * 100),
        ];

        // Makanan serupa dalam kategori yang sama
        $similarFoods = Food::where('category', $food->category)
            ->where('id', '!=', $food->id)
            ->take(3)
            ->get();

        return view('foods.show', compact('food', 'macroRatio', 'similarFoods'));
    }

    /**
     * Tampilkan form edit data makanan.
     */
    public function edit(Food $food): View
    {
        $existingCategories = Food::select('category')->distinct()->pluck('category');

        return view('foods.edit', compact('food', 'existingCategories'));
    }

    /**
     * Perbarui data makanan di database MySQL.
     */
    public function update(Request $request, Food $food): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:foods,name,' . $food->id],
            'category' => ['required', 'string', 'max:100'],
            'calories_per_100g' => ['required', 'numeric', 'min:0', 'max:1500'],
            'protein_per_100g' => ['required', 'numeric', 'min:0', 'max:100'],
            'carbohydrates_per_100g' => ['required', 'numeric', 'min:0', 'max:100'],
            'fat_per_100g' => ['required', 'numeric', 'min:0', 'max:100'],
            'fiber_per_100g' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'sugar_g' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'sodium_mg' => ['nullable', 'numeric', 'min:0', 'max:5000'],
            'serving_size' => ['required', 'string', 'max:255'],
            'health_grade' => ['required', 'in:A,B,C,D'],
            'icon_emoji' => ['nullable', 'string', 'max:10'],
            'description' => ['nullable', 'string', 'max:1000'],
            'nutrition_source' => ['nullable', 'string', 'max:255'],
        ]);

        $food->update($validated);

        return redirect()->route('foods.show', $food->id)
            ->with('success', "Data nutrisi '{$food->name}' berhasil diperbarui.");
    }

    /**
     * Hapus makanan dari database MySQL.
     */
    public function destroy(Food $food): RedirectResponse
    {
        $name = $food->name;
        
        // Hapus log makan yang merujuk ke makanan ini atau pertahankan nama
        MealLog::where('food_id', $food->id)->update([
            'food_id' => null,
            'custom_food_name' => $name . ' (Arsip)',
        ]);

        $food->delete();

        return redirect()->route('foods.index')
            ->with('info', "Makanan '{$name}' telah dihapus dari database.");
    }

    /**
     * Tambah makanan langsung dari katalog ke Food Diary hari ini (Quick Log).
     */
    public function quickLog(Request $request, Food $food): RedirectResponse
    {
        $portion = (float) $request->input('portion_grams', 100);
        $mealType = $request->input('meal_type', 'makan_siang');

        $calc = $food->calculateForPortion($portion);

        MealLog::create([
            'user_id' => Auth::id(),
            'food_id' => $food->id,
            'meal_type' => $mealType,
            'portion_grams' => $portion,
            'calories' => $calc['calories'],
            'protein' => $calc['protein'],
            'carbohydrates' => $calc['carbohydrates'],
            'fat' => $calc['fat'],
            'fiber' => $calc['fiber'],
            'consumed_at' => Carbon::now(),
        ]);

        return redirect()->route('diary.index')
            ->with('success', "{$food->name} ({$portion}g) berhasil dicatat ke menu {$mealType} hari ini!");
    }
}
