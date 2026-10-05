@extends('layouts.app')

@section('title', $food->name . ' — Informasi Nilai Gizi')
@section('page_title', 'Detail Gizi Makanan')

@section('content')
<div class="space-y-7 max-w-6xl mx-auto">

    {{-- Breadcrumb & Back --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('foods.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-charcoal-muted hover:text-emerald-800 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Database Makanan</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('foods.edit', $food->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 font-bold text-xs hover:bg-blue-100 border border-blue-200/60 transition">
                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                <span>Edit Data</span>
            </a>
            <button type="button" onclick="document.getElementById('delete-food-modal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 text-rose-700 font-bold text-xs hover:bg-rose-100 border border-rose-200/60 transition">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                <span>Hapus</span>
            </button>
        </div>
    </div>

    {{-- Hero Summary Card --}}
    <div class="bg-white/95 rounded-3xl p-6 sm:p-8 border border-stone-200/90 shadow-sm relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            
            <div class="flex items-start gap-4 sm:gap-5">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-cream-200 border border-stone-200/80 flex items-center justify-center text-4xl sm:text-5xl shadow-inner flex-shrink-0">
                    {{ $food->icon_emoji ?: '🥗' }}
                </div>
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-800 bg-emerald-50 px-2.5 py-0.5 rounded-md border border-emerald-200/60">
                            {{ $food->category }}
                        </span>
                        <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full border {{ $food->grade_badge['bg'] }}">
                            Grade {{ $food->health_grade ?: 'A' }}
                        </span>
                        <span class="text-[11px] text-charcoal-light font-medium">
                            Takaran: {{ $food->serving_size }}
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black text-charcoal tracking-tight">{{ $food->name }}</h1>

                    @if($food->description)
                        <p class="text-xs sm:text-sm text-charcoal-muted leading-relaxed max-w-xl font-normal">
                            {{ $food->description }}
                        </p>
                    @endif
                </div>
            </div>

            {{-- Big Calorie Banner --}}
            <div class="p-5 rounded-3xl bg-gradient-to-br from-emerald-950 to-teal-950 text-white text-center md:min-w-[190px] shadow-lg shadow-emerald-950/20 flex-shrink-0">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-300">Kalori Utama</span>
                <p class="text-3xl sm:text-4xl font-black my-0.5">{{ number_format($food->calories_per_100g, 0) }}</p>
                <span class="text-xs text-emerald-200/80 font-bold">kkal / 100 gram</span>
            </div>
        </div>
    </div>

    {{-- 2-Column Nutrition Analysis --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-7">
        
        {{-- Left: Dynamic Portion Calculator & Macro Ratio (7 cols) --}}
        <div class="lg:col-span-7 space-y-6">
            
            {{-- Interactive Portion Calculator Card --}}
            <div class="bg-white/95 rounded-3xl p-6 sm:p-7 border border-stone-200/90 shadow-sm space-y-5">
                <div class="flex items-center justify-between border-b border-stone-200/70 pb-3">
                    <div>
                        <h3 class="font-black text-base text-charcoal flex items-center gap-2">
                            <i data-lucide="calculator" class="w-4 h-4 text-emerald-700"></i>
                            <span>Kalkulator Porsi Dinamis</span>
                        </h3>
                        <p class="text-xs text-charcoal-muted">Geser atau ketik gramasi untuk melihat nutrisi yang disesuaikan.</p>
                    </div>
                    <span id="active-portion-badge" class="px-3 py-1 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-black">
                        100 gram
                    </span>
                </div>

                {{-- Portion Slider --}}
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-xs font-bold text-charcoal-muted">
                        <span>Porsi Kustom:</span>
                        <div class="flex items-center gap-1.5">
                            <input type="number" id="calc-grams-input" value="100" min="10" max="1500" step="10"
                                class="w-20 px-2.5 py-1 text-center text-sm font-black rounded-lg bg-cream-100 border border-stone-200 text-emerald-950 focus:outline-none focus:ring-2 focus:ring-emerald-700"
                                oninput="syncFromInput(this.value)">
                            <span class="text-xs">gram</span>
                        </div>
                    </div>

                    <input type="range" id="calc-slider" min="20" max="500" step="10" value="100"
                        class="w-full h-2 bg-stone-200 rounded-lg appearance-none cursor-pointer accent-emerald-700"
                        oninput="syncFromSlider(this.value)">

                    {{-- Quick preset chips --}}
                    <div class="flex items-center gap-2 pt-1 text-xs">
                        <span class="text-[10px] text-charcoal-light font-bold">Preset:</span>
                        <button type="button" onclick="setPreset(50)" class="px-2.5 py-1 rounded-lg bg-cream-200 hover:bg-emerald-100 font-bold transition">50g</button>
                        <button type="button" onclick="setPreset(100)" class="px-2.5 py-1 rounded-lg bg-emerald-800 text-white font-bold transition">100g (Standar)</button>
                        <button type="button" onclick="setPreset(150)" class="px-2.5 py-1 rounded-lg bg-cream-200 hover:bg-emerald-100 font-bold transition">150g (1 Porsi)</button>
                        <button type="button" onclick="setPreset(200)" class="px-2.5 py-1 rounded-lg bg-cream-200 hover:bg-emerald-100 font-bold transition">200g (Porsi Besar)</button>
                    </div>
                </div>

                {{-- Dynamic Result Cards --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3 border-t border-stone-200/70">
                    <div class="p-3.5 rounded-2xl bg-cream-100 border border-stone-200/70 text-center">
                        <span class="text-[10px] font-bold text-charcoal-muted uppercase block">Kalori</span>
                        <span id="dyn-calories" class="text-xl font-black text-charcoal">--</span>
                        <span class="text-[10px] text-charcoal-light block">kkal</span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-100 text-center">
                        <span class="text-[10px] font-bold text-blue-900 uppercase block">Protein</span>
                        <span id="dyn-protein" class="text-xl font-black text-blue-950">--</span>
                        <span class="text-[10px] text-blue-700 block">gram</span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-100 text-center">
                        <span class="text-[10px] font-bold text-amber-900 uppercase block">Karbo</span>
                        <span id="dyn-carbs" class="text-xl font-black text-amber-950">--</span>
                        <span class="text-[10px] text-amber-700 block">gram</span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-100 text-center">
                        <span class="text-[10px] font-bold text-rose-900 uppercase block">Lemak</span>
                        <span id="dyn-fat" class="text-xl font-black text-rose-950">--</span>
                        <span class="text-[10px] text-rose-700 block">gram</span>
                    </div>
                </div>

                {{-- Direct Log Button using dynamic grams --}}
                <form method="POST" action="{{ route('foods.quick-log', $food->id) }}" class="flex items-center gap-3 pt-2">
                    @csrf
                    <input type="hidden" id="form-portion-grams" name="portion_grams" value="100">
                    <select name="meal_type" class="px-3.5 py-2.5 text-xs rounded-xl bg-cream-100 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-emerald-700/30 text-charcoal font-bold">
                        <option value="sarapan">🍳 Sarapan</option>
                        <option value="makan_siang" selected>🍛 Makan Siang</option>
                        <option value="makan_malam">🍲 Makan Malam</option>
                        <option value="camilan">🥪 Camilan</option>
                    </select>

                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-800 text-white font-extrabold text-xs hover:bg-emerald-900 transition shadow-sm">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Catat Porsi Ini ke Jurnal</span>
                    </button>
                </form>
            </div>

            {{-- Macronutrient Energy Ratio Card --}}
            <div class="bg-white/95 rounded-3xl p-6 sm:p-7 border border-stone-200/90 shadow-sm space-y-4">
                <h3 class="font-black text-base text-charcoal flex items-center gap-2">
                    <i data-lucide="pie-chart" class="w-4 h-4 text-emerald-700"></i>
                    <span>Rasio Sumber Kalori (Aturan 4-4-9)</span>
                </h3>
                <p class="text-xs text-charcoal-muted">
                    Proporsi kontribusi kalori dari Protein (4 kkal/g), Karbohidrat (4 kkal/g), dan Lemak (9 kkal/g).
                </p>

                <div class="h-4 w-full bg-cream-300 rounded-full overflow-hidden flex">
                    <div class="bg-blue-500 h-full" style="width: {{ $macroRatio['protein'] }}%" title="Protein: {{ $macroRatio['protein'] }}%"></div>
                    <div class="bg-amber-400 h-full" style="width: {{ $macroRatio['carbs'] }}%" title="Karbohidrat: {{ $macroRatio['carbs'] }}%"></div>
                    <div class="bg-rose-500 h-full" style="width: {{ $macroRatio['fat'] }}%" title="Lemak: {{ $macroRatio['fat'] }}%"></div>
                </div>

                <div class="grid grid-cols-3 gap-3 text-center text-xs pt-1">
                    <div class="p-2.5 rounded-xl bg-blue-50/60 border border-blue-100">
                        <span class="text-[10px] font-bold text-blue-900 uppercase block">Protein</span>
                        <span class="text-base font-black text-blue-950">{{ $macroRatio['protein'] }}%</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-amber-50/60 border border-amber-100">
                        <span class="text-[10px] font-bold text-amber-900 uppercase block">Karbohidrat</span>
                        <span class="text-base font-black text-amber-950">{{ $macroRatio['carbs'] }}%</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-rose-50/60 border border-rose-100">
                        <span class="text-[10px] font-bold text-rose-900 uppercase block">Lemak</span>
                        <span class="text-base font-black text-rose-950">{{ $macroRatio['fat'] }}%</span>
                    </div>
                </div>
            </div>

            {{-- Similar Foods Card --}}
            @if($similarFoods->count() > 0)
            <div class="bg-white/95 rounded-3xl p-6 border border-stone-200/90 shadow-sm space-y-3">
                <h4 class="font-extrabold text-xs uppercase tracking-wider text-charcoal-muted">Makanan Serupa di Kategori {{ $food->category }}</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach($similarFoods as $sim)
                    <a href="{{ route('foods.show', $sim->id) }}" class="p-3 rounded-2xl bg-cream-100 border border-stone-200/70 hover:border-emerald-300 hover:bg-white transition block group">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xl">{{ $sim->icon_emoji ?: '🥗' }}</span>
                            <span class="font-bold text-xs text-charcoal group-hover:text-emerald-800 truncate">{{ $sim->name }}</span>
                        </div>
                        <p class="text-[11px] text-charcoal-muted">{{ number_format($sim->calories_per_100g, 0) }} kkal • {{ $sim->protein_per_100g }}g prot</p>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Right: Official Nutrition Fact Sheet (5 cols) --}}
        <div class="lg:col-span-5 sticky top-28 space-y-4">
            <div class="bg-white rounded-3xl p-6 sm:p-7 border-2 border-black shadow-lg">
                
                <div class="border-b-4 border-black pb-2 mb-2">
                    <span class="text-[9px] font-black uppercase tracking-widest text-emerald-800">Standar Resmi BPOM / TKPI</span>
                    <h3 class="text-2xl font-black text-black tracking-tight leading-tight">Informasi Nilai Gizi</h3>
                    <p class="text-xs font-semibold text-charcoal-muted">Nutrition Facts</p>
                </div>

                <div class="border-b-8 border-black pb-2 text-xs space-y-1">
                    <div class="flex justify-between">
                        <span class="font-bold text-charcoal">Ukuran Porsi Standar</span>
                        <strong class="text-black">{{ $food->serving_size }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-charcoal-muted">Takaran Basis</span>
                        <span class="font-bold text-black">100 gram</span>
                    </div>
                </div>

                {{-- Calories Header --}}
                <div class="py-3 border-b-4 border-black flex items-baseline justify-between">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-charcoal-muted block">Jumlah Per 100g</span>
                        <span class="text-xl font-black text-black">Energi Total</span>
                    </div>
                    <div class="text-right">
                        <span class="text-3xl font-black text-black">{{ number_format($food->calories_per_100g, 0) }}</span>
                        <span class="text-xs font-bold text-charcoal-muted block -mt-1">kkal</span>
                    </div>
                </div>

                {{-- Detail Table --}}
                <div class="divide-y divide-stone-300 text-xs py-1">
                    <div class="py-1.5 flex justify-between items-center font-black">
                        <span>Protein</span>
                        <span>{{ number_format($food->protein_per_100g, 1) }} g</span>
                    </div>

                    <div class="py-1.5 flex justify-between items-center font-black">
                        <span>Karbohidrat Total</span>
                        <span>{{ number_format($food->carbohydrates_per_100g, 1) }} g</span>
                    </div>

                    <div class="py-1 pl-4 flex justify-between items-center text-charcoal-muted">
                        <span>Serat Pangan</span>
                        <span class="font-semibold text-black">{{ number_format($food->fiber_per_100g, 1) }} g</span>
                    </div>

                    <div class="py-1 pl-4 flex justify-between items-center text-charcoal-muted">
                        <span>Gula</span>
                        <span class="font-semibold text-black">{{ number_format($food->sugar_g, 1) }} g</span>
                    </div>

                    <div class="py-1.5 flex justify-between items-center font-black">
                        <span>Lemak Total</span>
                        <span>{{ number_format($food->fat_per_100g, 1) }} g</span>
                    </div>

                    <div class="py-1.5 flex justify-between items-center font-black">
                        <span>Natrium (Garam)</span>
                        <span>{{ number_format($food->sodium_mg, 0) }} mg</span>
                    </div>
                </div>

                {{-- Footnote Source --}}
                <div class="pt-3 border-t-2 border-dashed border-stone-300 text-[10px] text-charcoal-muted space-y-1">
                    <p class="font-bold text-charcoal">Sumber Data:</p>
                    <p class="leading-relaxed">{{ $food->nutrition_source ?: 'Tabel Komposisi Pangan Indonesia (TKPI) Kemenkes RI' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="delete-food-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('delete-food-modal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 border border-stone-200">
        <div class="w-14 h-14 rounded-2xl bg-rose-100 flex items-center justify-center mx-auto mb-4">
            <i data-lucide="alert-triangle" class="w-7 h-7 text-rose-600"></i>
        </div>
        <h3 class="text-center font-black text-lg text-charcoal mb-1">Hapus Data Makanan?</h3>
        <p class="text-center text-xs text-charcoal-muted mb-2">Data ini akan dihapus secara permanen dari basis data MySQL:</p>
        <p class="text-center font-black text-emerald-900 mb-4 bg-emerald-50 py-2 rounded-xl border border-emerald-100">{{ $food->name }}</p>
        
        <form method="POST" action="{{ route('foods.destroy', $food->id) }}">
            @csrf
            @method('DELETE')
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('delete-food-modal').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl bg-cream-300 text-charcoal font-bold text-xs hover:bg-stone-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-rose-600 text-white font-extrabold text-xs hover:bg-rose-700 transition shadow-sm">
                    Hapus
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const baseCal = {{ $food->calories_per_100g }};
const baseProt = {{ $food->protein_per_100g }};
const baseCarb = {{ $food->carbohydrates_per_100g }};
const baseFat = {{ $food->fat_per_100g }};

function calculate(grams) {
    const factor = grams / 100;
    document.getElementById('active-portion-badge').textContent = grams + ' gram';
    document.getElementById('form-portion-grams').value = grams;
    document.getElementById('dyn-calories').textContent = (baseCal * factor).toFixed(1);
    document.getElementById('dyn-protein').textContent = (baseProt * factor).toFixed(1);
    document.getElementById('dyn-carbs').textContent = (baseCarb * factor).toFixed(1);
    document.getElementById('dyn-fat').textContent = (baseFat * factor).toFixed(1);
}

function syncFromSlider(val) {
    document.getElementById('calc-grams-input').value = val;
    calculate(val);
}

function syncFromInput(val) {
    const num = Math.max(10, Math.min(1500, parseFloat(val) || 100));
    document.getElementById('calc-slider').value = Math.min(500, num);
    calculate(num);
}

function setPreset(val) {
    document.getElementById('calc-slider').value = val;
    document.getElementById('calc-grams-input').value = val;
    calculate(val);
}

// Initial calculation on load
document.addEventListener('DOMContentLoaded', () => {
    calculate(100);
});
</script>
@endsection
