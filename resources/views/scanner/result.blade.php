@extends('layouts.app')

@section('title', 'Hasil Analisis Nutrisi — NutriScan AI')
@section('page_title', 'Hasil Analisis AI')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Header Action Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('scanner.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-emerald-800 hover:text-emerald-950 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-xl border border-emerald-200 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Scan Makanan Lain</span>
        </a>
        <div class="flex items-center gap-2">
            <span class="text-xs font-mono font-medium px-2.5 py-1 rounded-lg bg-cream-100 border border-stone-200 text-charcoal-muted">
                ID Scan: #SCN-{{ str_pad($scan->id, 5, '0', STR_PAD_LEFT) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left Column: Photo & AI Intelligence Tagging (5 cols) -->
        <div class="lg:col-span-5 space-y-5">
            <div class="bg-white/80 backdrop-blur-md p-5 rounded-3xl border border-stone-200/90 shadow-sm space-y-4">
                
                <!-- Food Photo with Modern Overlay -->
                <div class="rounded-2xl overflow-hidden bg-stone-900 border border-stone-200 aspect-square flex items-center justify-center relative shadow-inner group">
                    <img src="{{ $scan->image_url }}" alt="{{ $scan->identified_food_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    
                    <div class="absolute top-3 left-3 px-3 py-1 rounded-full bg-black/60 backdrop-blur-md text-[11px] text-white font-semibold flex items-center gap-1.5 border border-white/10 shadow-lg">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-emerald-400 animate-pulse"></i>
                        <span>AI Vision Verified</span>
                    </div>

                    @if($scan->confidence)
                        <div class="absolute bottom-3 right-3 px-3 py-1 rounded-full bg-emerald-950/80 backdrop-blur-md text-[11px] text-emerald-300 font-bold border border-emerald-500/30 shadow-lg">
                            Confidence: {{ round($scan->confidence * 100) }}%
                        </div>
                    @endif
                </div>

                <!-- AI Identification Header -->
                <div class="pt-1">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60">
                            Deteksi AI Vision
                        </span>
                        <span class="text-xs text-charcoal-light">{{ $scan->created_at->diffForHumans() }}</span>
                    </div>

                    <h2 class="text-2xl font-black text-emerald-950 tracking-tight">{{ $scan->identified_food_name }}</h2>

                    @if(!empty($scan->ai_response['notes']))
                        <p class="text-xs text-charcoal-muted mt-2.5 p-3 rounded-2xl bg-cream-200/70 border border-stone-200/70 leading-relaxed">
                            {{ $scan->ai_response['notes'] }}
                        </p>
                    @endif

                    <!-- Detected Ingredients Chips -->
                    @if(!empty($scan->ai_response['possible_ingredients']) && is_array($scan->ai_response['possible_ingredients']))
                        <div class="mt-4 pt-3 border-t border-stone-200/70">
                            <span class="text-[10px] font-bold text-charcoal-light uppercase tracking-wider block mb-2">Komponen Teridentifikasi:</span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($scan->ai_response['possible_ingredients'] as $ing)
                                    <span class="inline-flex items-center gap-1 text-[11px] px-2.5 py-1 rounded-full bg-cream-200 text-charcoal font-medium border border-stone-200/80">
                                        <i data-lucide="check" class="w-3 h-3 text-emerald-600"></i>
                                        <span>{{ $ing }}</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Database Matching Status -->
                <div class="pt-3 border-t border-stone-200/70 text-xs">
                    @if($selectedFood)
                        <div class="flex items-center gap-2 text-emerald-900 font-semibold bg-emerald-50/80 p-2.5 rounded-xl border border-emerald-200">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                            <span class="truncate">Tercocokkan ke basis data: <strong>{{ $selectedFood->name }}</strong></span>
                        </div>
                        <p class="text-[11px] text-charcoal-light mt-1.5 pl-1">Sumber: {{ $selectedFood->nutrition_source ?: 'Tabel Komposisi Pangan Indonesia (TKPI)' }}</p>
                    @else
                        <div class="flex items-center gap-2 text-amber-900 font-semibold bg-amber-50 p-2.5 rounded-xl border border-amber-200">
                            <i data-lucide="info" class="w-4 h-4 text-amber-600 flex-shrink-0"></i>
                            <span>Estimasi nutrisi standar digunakan.</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Medical Disclaimer Card -->
            <div class="p-4 rounded-2xl bg-cream-100/90 border border-stone-200/80 text-xs text-charcoal-muted flex items-start gap-3">
                <i data-lucide="shield-check" class="w-5 h-5 text-emerald-700 flex-shrink-0 mt-0.5"></i>
                <div class="space-y-1">
                    <strong class="text-charcoal block">Catatan Medis & Batasan Akurasi:</strong>
                    <p class="text-[11px] leading-relaxed">
                        Nilai nutrisi merupakan estimasi berbasis visual piring dan data acuan komposisi pangan. Bukan pengganti uji klinis laboratorium atau nasihat dokter spesialis.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Column: Portion Adjustment, Nutrition Chart, & Save to Diary (7 cols) -->
        <div class="lg:col-span-7 space-y-6">

            <div class="bg-white/90 backdrop-blur-md p-6 sm:p-7 rounded-3xl border border-stone-200/90 shadow-sm space-y-6">

                <form method="POST" action="{{ route('scanner.save', $scan->id) }}" id="saveDiaryForm">
                    @csrf

                    <!-- 1. Koreksi / Pilihan Makanan Database -->
                    <div class="space-y-2 pb-5 border-b border-stone-200/70">
                        <div class="flex items-center justify-between">
                            <label for="food_id" class="block text-xs font-bold uppercase tracking-wider text-charcoal">
                                Padanan Menu di Basis Data
                            </label>
                            <span class="text-[11px] text-emerald-800 font-semibold">Dapat disesuaikan</span>
                        </div>
                        <select id="food_id" name="food_id" onchange="onFoodSelectionChanged(this)"
                            class="w-full px-3.5 py-3 rounded-2xl border border-stone-300 bg-cream-50 text-sm font-semibold text-charcoal focus:outline-none focus:ring-2 focus:ring-emerald-700 transition">
                            <option value="">-- Gunakan Hasil Taksiran AI ({{ $scan->identified_food_name }}) --</option>
                            @foreach($allFoods as $f)
                                <option value="{{ $f->id }}"
                                    data-cals="{{ $f->calories_per_100g }}"
                                    data-prot="{{ $f->protein_per_100g }}"
                                    data-carbs="{{ $f->carbohydrates_per_100g }}"
                                    data-fat="{{ $f->fat_per_100g }}"
                                    data-fiber="{{ $f->fiber_per_100g ?? 0 }}"
                                    data-source="{{ $f->nutrition_source }}"
                                    {{ ($selectedFood && $selectedFood->id === $f->id) || (!$selectedFood && stripos($f->name, $scan->identified_food_name) !== false) ? 'selected' : '' }}>
                                    {{ $f->name }} ({{ $f->category }}) — {{ $f->calories_per_100g }} kkal/100g
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-charcoal-light">Jika hidangan berbeda, pilih menu yang paling mewakili dari daftar di atas.</p>
                    </div>

                    <!-- 2. Porsi Slider & Input Dinamis -->
                    <div class="space-y-4 py-4 border-b border-stone-200/70">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="portion_grams" class="text-xs font-bold uppercase tracking-wider text-charcoal block">
                                    Takaran Porsi Hidangan
                                </label>
                                <span class="text-[11px] text-charcoal-muted">Geser atau ketik gramasi aktual yang Anda konsumsi</span>
                            </div>
                            <div class="flex items-center gap-1.5 bg-cream-200 px-3 py-1.5 rounded-xl border border-stone-300">
                                <input type="number" id="portionInput" min="10" max="2000" step="5" value="{{ $portionGrams }}"
                                    oninput="syncPortionSlider(this.value)"
                                    class="w-16 text-center font-black text-sm bg-transparent text-emerald-950 focus:outline-none">
                                <span class="text-xs font-bold text-emerald-800">gram</span>
                            </div>
                        </div>

                        <!-- Slider Bar -->
                        <div class="pt-1">
                            <input type="range" id="portionSlider" min="20" max="800" step="5" value="{{ $portionGrams }}"
                                oninput="syncPortionInput(this.value)"
                                class="w-full accent-emerald-800 cursor-pointer h-2 bg-cream-300 rounded-lg">
                        </div>

                        <!-- Preset Portions Buttons -->
                        <div class="flex flex-wrap gap-2 pt-1">
                            <button type="button" onclick="setPortion(100)" class="text-xs px-3 py-1.5 rounded-xl bg-cream-200 hover:bg-emerald-100 hover:text-emerald-900 border border-stone-300/80 transition font-medium">100g (Porsi Kecil)</button>
                            <button type="button" onclick="setPortion(180)" class="text-xs px-3 py-1.5 rounded-xl bg-cream-200 hover:bg-emerald-100 hover:text-emerald-900 border border-stone-300/80 transition font-medium">180g (Sedang)</button>
                            <button type="button" onclick="setPortion(250)" class="text-xs px-3 py-1.5 rounded-xl bg-cream-200 hover:bg-emerald-100 hover:text-emerald-900 border border-stone-300/80 transition font-medium">250g (Standar)</button>
                            <button type="button" onclick="setPortion(350)" class="text-xs px-3 py-1.5 rounded-xl bg-cream-200 hover:bg-emerald-100 hover:text-emerald-900 border border-stone-300/80 transition font-medium">350g (Kenyang)</button>
                        </div>

                        <!-- Hidden input for form submission -->
                        <input type="hidden" name="portion_grams" id="portion_grams_field" value="{{ $portionGrams }}">
                    </div>

                    <!-- 3. Dynamic Nutrition Breakdown Cards & Donut Chart -->
                    <div class="space-y-4 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-charcoal">Rincian Kandungan Nutrisi Porsi</h3>
                            <span class="text-[11px] text-emerald-800 font-semibold">Kalkulasi Otomatis</span>
                        </div>

                        <!-- 4 Bento Cards for Macros -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <!-- Kalori -->
                            <div class="p-4 rounded-2xl bg-gradient-to-br from-emerald-50 to-cream-100 border border-emerald-200 text-center shadow-sm">
                                <span class="block text-[10px] text-emerald-800 uppercase font-black tracking-wider">Total Energi</span>
                                <span class="text-2xl font-black text-emerald-950 mt-1 block" id="displayCals">{{ $nutrition['calories'] }}</span>
                                <span class="text-[10px] text-charcoal-muted font-medium">kkal</span>
                            </div>

                            <!-- Protein -->
                            <div class="p-4 rounded-2xl bg-gradient-to-br from-blue-50 to-cream-100 border border-blue-200 text-center shadow-sm">
                                <span class="block text-[10px] text-blue-800 uppercase font-black tracking-wider">Protein</span>
                                <span class="text-2xl font-black text-blue-950 mt-1 block" id="displayProt">{{ $nutrition['protein'] }}</span>
                                <span class="text-[10px] text-charcoal-muted font-medium">gram</span>
                            </div>

                            <!-- Karbohidrat -->
                            <div class="p-4 rounded-2xl bg-gradient-to-br from-amber-50 to-cream-100 border border-amber-200 text-center shadow-sm">
                                <span class="block text-[10px] text-amber-800 uppercase font-black tracking-wider">Karbohidrat</span>
                                <span class="text-2xl font-black text-amber-950 mt-1 block" id="displayCarbs">{{ $nutrition['carbohydrates'] }}</span>
                                <span class="text-[10px] text-charcoal-muted font-medium">gram</span>
                            </div>

                            <!-- Lemak -->
                            <div class="p-4 rounded-2xl bg-gradient-to-br from-rose-50 to-cream-100 border border-rose-200 text-center shadow-sm">
                                <span class="block text-[10px] text-rose-800 uppercase font-black tracking-wider">Lemak</span>
                                <span class="text-2xl font-black text-rose-950 mt-1 block" id="displayFat">{{ $nutrition['fat'] }}</span>
                                <span class="text-[10px] text-charcoal-muted font-medium">gram</span>
                            </div>
                        </div>

                        <!-- Macro Donut Chart & Distribution -->
                        <div class="p-5 rounded-3xl bg-cream-100/90 border border-stone-200 flex flex-col sm:flex-row items-center justify-around gap-6">
                            <div class="w-32 h-32 relative">
                                <canvas id="macroDonutChart"></canvas>
                            </div>
                            <div class="space-y-2 text-xs flex-1 max-w-xs">
                                <div class="flex items-center justify-between p-2 rounded-xl bg-blue-50/80 border border-blue-100">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                                        <span class="font-medium text-charcoal">Protein</span>
                                    </div>
                                    <strong class="text-blue-900" id="legendProt">{{ $nutrition['protein'] }}g</strong>
                                </div>
                                <div class="flex items-center justify-between p-2 rounded-xl bg-amber-50/80 border border-amber-100">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                        <span class="font-medium text-charcoal">Karbohidrat</span>
                                    </div>
                                    <strong class="text-amber-900" id="legendCarbs">{{ $nutrition['carbohydrates'] }}g</strong>
                                </div>
                                <div class="flex items-center justify-between p-2 rounded-xl bg-rose-50/80 border border-rose-100">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                                        <span class="font-medium text-charcoal">Lemak</span>
                                    </div>
                                    <strong class="text-rose-900" id="legendFat">{{ $nutrition['fat'] }}g</strong>
                                </div>
                                <div class="text-[11px] text-charcoal-light text-right pt-0.5">
                                    Serat Pangan: <span id="displayFiber" class="font-bold text-charcoal">{{ $nutrition['fiber'] ?? '0' }}</span> g
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Pilihan Waktu Makan & Tombol Simpan -->
                    <div class="space-y-4 pt-4 border-t border-stone-200/70">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-charcoal mb-2.5">
                                Waktu Makan Konsumsi:
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                <label class="flex flex-col items-center justify-center p-3 rounded-2xl border border-stone-300 bg-cream-50 cursor-pointer hover:border-emerald-700 transition group has-[:checked]:border-emerald-800 has-[:checked]:bg-emerald-50 has-[:checked]:ring-2 has-[:checked]:ring-emerald-700/20">
                                    <input type="radio" name="meal_type" value="sarapan" class="sr-only">
                                    <i data-lucide="sunrise" class="w-4 h-4 text-amber-600 mb-1"></i>
                                    <span class="text-xs font-bold text-charcoal">Sarapan</span>
                                </label>
                                <label class="flex flex-col items-center justify-center p-3 rounded-2xl border border-stone-300 bg-cream-50 cursor-pointer hover:border-emerald-700 transition group has-[:checked]:border-emerald-800 has-[:checked]:bg-emerald-50 has-[:checked]:ring-2 has-[:checked]:ring-emerald-700/20">
                                    <input type="radio" name="meal_type" value="makan_siang" checked class="sr-only">
                                    <i data-lucide="sun" class="w-4 h-4 text-emerald-700 mb-1"></i>
                                    <span class="text-xs font-bold text-charcoal">Makan Siang</span>
                                </label>
                                <label class="flex flex-col items-center justify-center p-3 rounded-2xl border border-stone-300 bg-cream-50 cursor-pointer hover:border-emerald-700 transition group has-[:checked]:border-emerald-800 has-[:checked]:bg-emerald-50 has-[:checked]:ring-2 has-[:checked]:ring-emerald-700/20">
                                    <input type="radio" name="meal_type" value="makan_malam" class="sr-only">
                                    <i data-lucide="moon" class="w-4 h-4 text-indigo-600 mb-1"></i>
                                    <span class="text-xs font-bold text-charcoal">Makan Malam</span>
                                </label>
                                <label class="flex flex-col items-center justify-center p-3 rounded-2xl border border-stone-300 bg-cream-50 cursor-pointer hover:border-emerald-700 transition group has-[:checked]:border-emerald-800 has-[:checked]:bg-emerald-50 has-[:checked]:ring-2 has-[:checked]:ring-emerald-700/20">
                                    <input type="radio" name="meal_type" value="camilan" class="sr-only">
                                    <i data-lucide="cookie" class="w-4 h-4 text-purple-600 mb-1"></i>
                                    <span class="text-xs font-bold text-charcoal">Camilan</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="w-full flex items-center justify-center gap-2.5 py-4 rounded-2xl bg-gradient-to-r from-emerald-800 to-emerald-900 text-white font-extrabold text-sm shadow-lg shadow-emerald-950/20 hover:scale-[1.01] hover:shadow-xl transition">
                            <i data-lucide="bookmark-plus" class="w-5 h-5 text-emerald-200"></i>
                            <span>Simpan ke Food Diary Hari Ini</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let macroChart = null;

    // Nilai dasar per 100g
    let baseCals = {{ $selectedFood ? $selectedFood->calories_per_100g : ($nutrition['calories'] / max(0.1, ($portionGrams / 100))) }};
    let baseProt = {{ $selectedFood ? $selectedFood->protein_per_100g : ($nutrition['protein'] / max(0.1, ($portionGrams / 100))) }};
    let baseCarbs = {{ $selectedFood ? $selectedFood->carbohydrates_per_100g : ($nutrition['carbohydrates'] / max(0.1, ($portionGrams / 100))) }};
    let baseFat = {{ $selectedFood ? $selectedFood->fat_per_100g : ($nutrition['fat'] / max(0.1, ($portionGrams / 100))) }};
    let baseFiber = {{ $selectedFood && $selectedFood->fiber_per_100g ? $selectedFood->fiber_per_100g : 0 }};

    function recalculateNutrition(grams) {
        const factor = grams / 100.0;
        const cals = (baseCals * factor).toFixed(1);
        const prot = (baseProt * factor).toFixed(1);
        const carbs = (baseCarbs * factor).toFixed(1);
        const fat = (baseFat * factor).toFixed(1);
        const fiber = (baseFiber * factor).toFixed(1);

        document.getElementById('displayCals').textContent = cals;
        document.getElementById('displayProt').textContent = prot;
        document.getElementById('displayCarbs').textContent = carbs;
        document.getElementById('displayFat').textContent = fat;
        document.getElementById('displayFiber').textContent = fiber;

        document.getElementById('legendProt').textContent = prot + 'g';
        document.getElementById('legendCarbs').textContent = carbs + 'g';
        document.getElementById('legendFat').textContent = fat + 'g';

        document.getElementById('portion_grams_field').value = grams;

        // Update Chart
        if (macroChart) {
            macroChart.data.datasets[0].data = [parseFloat(prot), parseFloat(carbs), parseFloat(fat)];
            macroChart.update();
        }
    }

    function syncPortionSlider(val) {
        val = Math.max(10, Math.min(2000, parseFloat(val) || 10));
        document.getElementById('portionSlider').value = val;
        recalculateNutrition(val);
    }

    function syncPortionInput(val) {
        document.getElementById('portionInput').value = val;
        recalculateNutrition(val);
    }

    function setPortion(val) {
        document.getElementById('portionInput').value = val;
        document.getElementById('portionSlider').value = val;
        recalculateNutrition(val);
    }

    function onFoodSelectionChanged(select) {
        const opt = select.options[select.selectedIndex];
        if (opt && opt.value) {
            baseCals = parseFloat(opt.getAttribute('data-cals')) || 0;
            baseProt = parseFloat(opt.getAttribute('data-prot')) || 0;
            baseCarbs = parseFloat(opt.getAttribute('data-carbs')) || 0;
            baseFat = parseFloat(opt.getAttribute('data-fat')) || 0;
            baseFiber = parseFloat(opt.getAttribute('data-fiber')) || 0;
        }
        const currentGrams = parseFloat(document.getElementById('portionInput').value) || 200;
        recalculateNutrition(currentGrams);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('macroDonutChart');
        if (ctx) {
            macroChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Protein', 'Karbohidrat', 'Lemak'],
                    datasets: [{
                        data: [
                            parseFloat(document.getElementById('displayProt').textContent),
                            parseFloat(document.getElementById('displayCarbs').textContent),
                            parseFloat(document.getElementById('displayFat').textContent)
                        ],
                        backgroundColor: ['#2563EB', '#D97706', '#E11D48'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }
    });
</script>
@endsection
