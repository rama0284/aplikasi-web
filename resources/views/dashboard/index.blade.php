@extends('layouts.app')

@section('title', 'NutriScan AI — Smart Nutrition Dashboard')
@section('page_title', 'Dashboard Gizi & Kalori')

@section('content')
<div class="space-y-7">

    {{-- ══════════════ 1. HERO BENTO BANNER ══════════════ --}}
    <div class="relative overflow-hidden p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-emerald-950 via-emerald-900 to-teal-950 text-white shadow-xl border border-emerald-700/40">
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-emerald-400/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-12 -bottom-12 w-64 h-64 bg-teal-400/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-800/60 text-emerald-200 text-xs font-bold backdrop-blur-md border border-emerald-600/40">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>NutriScan AI Active • Target Kalori Personal</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight">
                    Halo, {{ Auth::user()->name }}! 👋
                </h2>
                <p class="text-sm text-emerald-100/80 leading-relaxed font-normal">
                    @if($caloriePercent < 50)
                        Awali hari dengan asupan gizi seimbang. Jangan lewatkan sarapan kaya protein untuk energi optimal.
                    @elseif($caloriePercent <= 90)
                        Bagus sekali! Asupan nutrisi harian Anda mendekati target optimal secara sehat dan teratur.
                    @else
                        Target kalori harian Anda telah tercapai! Tetap jaga hidrasi dan cukupi istirahat hari ini.
                    @endif
                </p>
            </div>

            <div class="flex flex-col sm:flex-row md:flex-col lg:flex-row items-stretch sm:items-center gap-3">
                <a href="{{ route('scanner.index') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-white text-emerald-950 font-black text-sm hover:bg-emerald-50 hover:scale-[1.02] transition shadow-lg shadow-black/15">
                    <i data-lucide="scan" class="w-4 h-4 text-emerald-700"></i>
                    <span>Scan Makanan AI</span>
                </a>
                <a href="{{ route('foods.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-emerald-800/80 hover:bg-emerald-800 text-white font-bold text-sm border border-emerald-600/50 backdrop-blur-md transition">
                    <i data-lucide="database" class="w-4 h-4 text-emerald-300"></i>
                    <span>Katalog {{ $totalFoodsCount }} Makanan</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ══════════════ 2. BENTO ROW: CALORIE GAUGE & MACROS ══════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        
        {{-- Calorie Gauge Card (5 cols) --}}
        <div class="lg:col-span-5 bg-white/95 rounded-3xl p-6 sm:p-7 border border-stone-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-charcoal-light">Konsumsi Harian</span>
                    <h3 class="text-base font-black text-charcoal">Energi & Kalori Hari Ini</h3>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-black {{ $caloriePercent > 100 ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                    {{ $caloriePercent }}% Target
                </span>
            </div>

            {{-- Progress Circle / Big Indicator --}}
            <div class="my-auto py-4 text-center">
                <div class="relative inline-flex items-center justify-center">
                    <svg class="w-44 h-44 -rotate-90 transform" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="50" stroke="#f1f5f9" stroke-width="10" fill="transparent" />
                        <circle cx="60" cy="60" r="50" stroke="#047857" stroke-width="10" stroke-linecap="round" fill="transparent"
                            stroke-dasharray="{{ 2 * 3.14159 * 50 }}"
                            stroke-dashoffset="{{ (2 * 3.14159 * 50) * (1 - ($caloriePercent / 100)) }}"
                            class="transition-all duration-1000 ease-out" />
                    </svg>
                    <div class="absolute flex flex-col items-center">
                        <span class="text-3xl sm:text-4xl font-black text-charcoal tracking-tight">{{ number_format($totalCalories, 0) }}</span>
                        <span class="text-xs font-bold text-charcoal-muted uppercase">dari {{ number_format($calorieGoal, 0) }} kkal</span>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-center gap-6 text-xs">
                    <div>
                        <span class="text-[10px] font-bold text-charcoal-light uppercase block">Sudah Masuk</span>
                        <strong class="text-emerald-800 font-black text-sm">{{ number_format($totalCalories, 0) }} kkal</strong>
                    </div>
                    <div class="h-6 w-px bg-stone-200"></div>
                    <div>
                        <span class="text-[10px] font-bold text-charcoal-light uppercase block">Sisa Alokasi</span>
                        <strong class="text-charcoal font-black text-sm">{{ number_format($remainingCalories, 0) }} kkal</strong>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-stone-200/70 flex items-center justify-between text-xs">
                <span class="text-charcoal-muted">Target diet Anda:</span>
                <a href="{{ route('profile.index') }}" class="font-bold text-emerald-800 hover:underline flex items-center gap-1">
                    <span>Ubah Target</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </div>

        {{-- Macro Nutrients Cards (7 cols) --}}
        <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            {{-- Protein Card --}}
            <div class="p-5 rounded-3xl bg-white/95 border border-stone-200/90 shadow-xs flex flex-col justify-between hover:border-blue-300 transition">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center">
                                <i data-lucide="dumbbell" class="w-4 h-4"></i>
                            </div>
                            <span class="font-extrabold text-xs text-blue-950 uppercase tracking-wider">Protein</span>
                        </div>
                        <span class="text-xs font-black text-blue-800">{{ $proteinPercent }}%</span>
                    </div>

                    <div class="my-3">
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-2xl font-black text-blue-950">{{ $totalProtein }}g</span>
                            <span class="text-xs font-semibold text-charcoal-muted">/ {{ $proteinGoal }}g target</span>
                        </div>
                        <div class="mt-2 h-2 w-full bg-blue-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-600 rounded-full transition-all duration-700" style="width: {{ $proteinPercent }}%"></div>
                        </div>
                    </div>
                </div>
                <p class="text-[11px] text-charcoal-muted font-medium pt-2 border-t border-stone-100">
                    Membangun otot dan regenerasi sel tubuh.
                </p>
            </div>

            {{-- Carbohydrates Card --}}
            <div class="p-5 rounded-3xl bg-white/95 border border-stone-200/90 shadow-xs flex flex-col justify-between hover:border-amber-300 transition">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">
                                <i data-lucide="wheat" class="w-4 h-4"></i>
                            </div>
                            <span class="font-extrabold text-xs text-amber-950 uppercase tracking-wider">Karbohidrat</span>
                        </div>
                        <span class="text-xs font-black text-amber-800">{{ $carbsPercent }}%</span>
                    </div>

                    <div class="my-3">
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-2xl font-black text-amber-950">{{ $totalCarbs }}g</span>
                            <span class="text-xs font-semibold text-charcoal-muted">/ {{ $carbsGoal }}g target</span>
                        </div>
                        <div class="mt-2 h-2 w-full bg-amber-100 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full transition-all duration-700" style="width: {{ $carbsPercent }}%"></div>
                        </div>
                    </div>
                </div>
                <p class="text-[11px] text-charcoal-muted font-medium pt-2 border-t border-stone-100">
                    Sumber bahan bakar utama aktivitas harian.
                </p>
            </div>

            {{-- Fat Card --}}
            <div class="p-5 rounded-3xl bg-white/95 border border-stone-200/90 shadow-xs flex flex-col justify-between hover:border-rose-300 transition">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-800 flex items-center justify-center">
                                <i data-lucide="flame" class="w-4 h-4"></i>
                            </div>
                            <span class="font-extrabold text-xs text-rose-950 uppercase tracking-wider">Lemak Sehat</span>
                        </div>
                        <span class="text-xs font-black text-rose-800">{{ $fatPercent }}%</span>
                    </div>

                    <div class="my-3">
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-2xl font-black text-rose-950">{{ $totalFat }}g</span>
                            <span class="text-xs font-semibold text-charcoal-muted">/ {{ $fatGoal }}g target</span>
                        </div>
                        <div class="mt-2 h-2 w-full bg-rose-100 rounded-full overflow-hidden">
                            <div class="h-full bg-rose-500 rounded-full transition-all duration-700" style="width: {{ $fatPercent }}%"></div>
                        </div>
                    </div>
                </div>
                <p class="text-[11px] text-charcoal-muted font-medium pt-2 border-t border-stone-100">
                    Dibutuhkan untuk penyerapan vitamin A, D, E, K.
                </p>
            </div>

            {{-- Fiber Card --}}
            <div class="p-5 rounded-3xl bg-white/95 border border-stone-200/90 shadow-xs flex flex-col justify-between hover:border-emerald-300 transition">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center">
                                <i data-lucide="apple" class="w-4 h-4"></i>
                            </div>
                            <span class="font-extrabold text-xs text-emerald-950 uppercase tracking-wider">Serat Alami</span>
                        </div>
                        <span class="text-xs font-black text-emerald-800">Sehat</span>
                    </div>

                    <div class="my-3">
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-2xl font-black text-emerald-950">{{ $totalFiber }}g</span>
                            <span class="text-xs font-semibold text-charcoal-muted">konsumsi hari ini</span>
                        </div>
                        <div class="mt-2 h-2 w-full bg-emerald-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-600 rounded-full" style="width: {{ min(100, round(($totalFiber / 25) * 100)) }}%"></div>
                        </div>
                    </div>
                </div>
                <p class="text-[11px] text-charcoal-muted font-medium pt-2 border-t border-stone-100">
                    Menjaga mikrobioma usus & stabilitas gula darah.
                </p>
            </div>
        </div>
    </div>

    {{-- ══════════════ 3. QUICK ACTIONS BAR ══════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('scanner.index') }}" class="group p-4 rounded-3xl bg-white/95 border border-stone-200/90 shadow-xs hover:shadow-md hover:border-emerald-300 transition flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-900 text-white flex items-center justify-center shadow-md group-hover:scale-110 transition duration-300">
                <i data-lucide="camera" class="w-6 h-6"></i>
            </div>
            <div>
                <h4 class="font-black text-sm text-charcoal group-hover:text-emerald-800">Scan AI</h4>
                <p class="text-[11px] text-charcoal-muted">Deteksi foto piring</p>
            </div>
        </a>

        <a href="{{ route('diary.index') }}" class="group p-4 rounded-3xl bg-white/95 border border-stone-200/90 shadow-xs hover:shadow-md hover:border-blue-300 transition flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-800 flex items-center justify-center shadow-xs group-hover:scale-110 transition duration-300">
                <i data-lucide="book-open" class="w-6 h-6"></i>
            </div>
            <div>
                <h4 class="font-black text-sm text-charcoal group-hover:text-blue-800">Jurnal Makan</h4>
                <p class="text-[11px] text-charcoal-muted">Catat konsumsi</p>
            </div>
        </a>

        <a href="{{ route('foods.create') }}" class="group p-4 rounded-3xl bg-white/95 border border-stone-200/90 shadow-xs hover:shadow-md hover:border-amber-300 transition flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center shadow-xs group-hover:scale-110 transition duration-300">
                <i data-lucide="plus-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <h4 class="font-black text-sm text-charcoal group-hover:text-amber-800">Entri Baru</h4>
                <p class="text-[11px] text-charcoal-muted">Tambah ke database</p>
            </div>
        </a>

        <a href="{{ route('assistant.index') }}" class="group p-4 rounded-3xl bg-white/95 border border-stone-200/90 shadow-xs hover:shadow-md hover:border-teal-300 transition flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-800 flex items-center justify-center shadow-xs group-hover:scale-110 transition duration-300">
                <i data-lucide="bot" class="w-6 h-6"></i>
            </div>
            <div>
                <h4 class="font-black text-sm text-charcoal group-hover:text-teal-800">Asisten AI</h4>
                <p class="text-[11px] text-charcoal-muted">Konsultasi gizi</p>
            </div>
        </a>
    </div>

    {{-- ══════════════ 4. BENTO ROW: TODAY'S MEAL TIMELINE & 7-DAY TRENDS ══════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-7">
        
        {{-- Today's Meals Timeline (7 cols) --}}
        <div class="lg:col-span-7 bg-white/95 rounded-3xl p-6 sm:p-7 border border-stone-200/90 shadow-xs space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-stone-200/70">
                <div>
                    <h3 class="font-black text-base text-charcoal flex items-center gap-2">
                        <i data-lucide="clock" class="w-4 h-4 text-emerald-700"></i>
                        <span>Menu & Jurnal Hari Ini</span>
                    </h3>
                    <p class="text-xs text-charcoal-muted">Riwayat sarapan, makan siang, malam, dan camilan terdaftar.</p>
                </div>
                <a href="{{ route('diary.index') }}" class="text-xs font-bold text-emerald-800 hover:underline">
                    Buka Diary Lengkap →
                </a>
            </div>

            {{-- 4 Meal Times Container --}}
            <div class="space-y-4">
                
                {{-- Sarapan --}}
                <div class="p-4 rounded-2xl bg-cream-100 border border-stone-200/70">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-black text-charcoal uppercase flex items-center gap-1.5">
                            <span>🍳 Sarapan Pagi</span>
                            <span class="text-[10px] font-bold text-charcoal-muted">({{ $groupedMeals['sarapan']->count() }} item)</span>
                        </span>
                        <span class="text-xs font-black text-emerald-800">
                            {{ number_format($groupedMeals['sarapan']->sum('calories'), 0) }} kkal
                        </span>
                    </div>

                    @forelse($groupedMeals['sarapan'] as $item)
                        <div class="flex items-center justify-between py-1.5 text-xs border-t border-stone-200/50">
                            <span class="font-semibold text-charcoal">{{ $item->display_name }} ({{ $item->portion_grams }}g)</span>
                            <span class="font-bold text-charcoal-muted">{{ number_format($item->calories, 0) }} kkal</span>
                        </div>
                    @empty
                        <p class="text-xs text-charcoal-light py-1">Belum ada makanan sarapan yang dicatat.</p>
                    @endforelse
                </div>

                {{-- Makan Siang --}}
                <div class="p-4 rounded-2xl bg-cream-100 border border-stone-200/70">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-black text-charcoal uppercase flex items-center gap-1.5">
                            <span>🍛 Makan Siang</span>
                            <span class="text-[10px] font-bold text-charcoal-muted">({{ $groupedMeals['makan_siang']->count() }} item)</span>
                        </span>
                        <span class="text-xs font-black text-emerald-800">
                            {{ number_format($groupedMeals['makan_siang']->sum('calories'), 0) }} kkal
                        </span>
                    </div>

                    @forelse($groupedMeals['makan_siang'] as $item)
                        <div class="flex items-center justify-between py-1.5 text-xs border-t border-stone-200/50">
                            <span class="font-semibold text-charcoal">{{ $item->display_name }} ({{ $item->portion_grams }}g)</span>
                            <span class="font-bold text-charcoal-muted">{{ number_format($item->calories, 0) }} kkal</span>
                        </div>
                    @empty
                        <p class="text-xs text-charcoal-light py-1">Belum ada makanan siang yang dicatat.</p>
                    @endforelse
                </div>

                {{-- Makan Malam --}}
                <div class="p-4 rounded-2xl bg-cream-100 border border-stone-200/70">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-black text-charcoal uppercase flex items-center gap-1.5">
                            <span>🍲 Makan Malam</span>
                            <span class="text-[10px] font-bold text-charcoal-muted">({{ $groupedMeals['makan_malam']->count() }} item)</span>
                        </span>
                        <span class="text-xs font-black text-emerald-800">
                            {{ number_format($groupedMeals['makan_malam']->sum('calories'), 0) }} kkal
                        </span>
                    </div>

                    @forelse($groupedMeals['makan_malam'] as $item)
                        <div class="flex items-center justify-between py-1.5 text-xs border-t border-stone-200/50">
                            <span class="font-semibold text-charcoal">{{ $item->display_name }} ({{ $item->portion_grams }}g)</span>
                            <span class="font-bold text-charcoal-muted">{{ number_format($item->calories, 0) }} kkal</span>
                        </div>
                    @empty
                        <p class="text-xs text-charcoal-light py-1">Belum ada makanan malam yang dicatat.</p>
                    @endforelse
                </div>

                {{-- Camilan --}}
                <div class="p-4 rounded-2xl bg-cream-100 border border-stone-200/70">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-black text-charcoal uppercase flex items-center gap-1.5">
                            <span>🥪 Camilan & Minuman</span>
                            <span class="text-[10px] font-bold text-charcoal-muted">({{ $groupedMeals['camilan']->count() }} item)</span>
                        </span>
                        <span class="text-xs font-black text-emerald-800">
                            {{ number_format($groupedMeals['camilan']->sum('calories'), 0) }} kkal
                        </span>
                    </div>

                    @forelse($groupedMeals['camilan'] as $item)
                        <div class="flex items-center justify-between py-1.5 text-xs border-t border-stone-200/50">
                            <span class="font-semibold text-charcoal">{{ $item->display_name }} ({{ $item->portion_grams }}g)</span>
                            <span class="font-bold text-charcoal-muted">{{ number_format($item->calories, 0) }} kkal</span>
                        </div>
                    @empty
                        <p class="text-xs text-charcoal-light py-1">Belum ada camilan yang dicatat.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- 7-Day Trend Chart & Food DB Recommendations (5 cols) --}}
        <div class="lg:col-span-5 space-y-6">
            
            {{-- Chart Card --}}
            <div class="bg-white/95 rounded-3xl p-6 sm:p-7 border border-stone-200/90 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-stone-200/70">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-charcoal-light">Tren Konsumsi</span>
                        <h3 class="text-base font-black text-charcoal">Kalori 7 Hari Terakhir</h3>
                    </div>
                    <span class="text-xs font-bold text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                        Target: {{ $calorieGoal }} kkal
                    </span>
                </div>

                <div class="h-56">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>

            {{-- Recommended Healthy Foods from DB --}}
            <div class="bg-white/95 rounded-3xl p-6 sm:p-7 border border-stone-200/90 shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-800">Rekomendasi Menu Sehat</span>
                        <h4 class="text-base font-black text-charcoal">Pilihan Bergizi Tinggi</h4>
                    </div>
                    <a href="{{ route('foods.index') }}" class="text-xs font-bold text-emerald-800 hover:underline">
                        Lihat Semua →
                    </a>
                </div>

                <div class="space-y-2.5">
                    @foreach($recommendedFoods as $rec)
                    <div class="p-3 rounded-2xl bg-cream-100 border border-stone-200/70 hover:border-emerald-300 hover:bg-white transition flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">{{ $rec->icon_emoji ?: '🥗' }}</span>
                            <div>
                                <h5 class="text-xs font-black text-charcoal line-clamp-1">
                                    <a href="{{ route('foods.show', $rec->id) }}" class="hover:text-emerald-800">{{ $rec->name }}</a>
                                </h5>
                                <p class="text-[11px] text-charcoal-muted">
                                    {{ number_format($rec->calories_per_100g, 0) }} kkal • {{ $rec->protein_per_100g }}g protein
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('foods.show', $rec->id) }}" class="p-1.5 rounded-xl bg-cream-200 text-charcoal hover:bg-emerald-100 hover:text-emerald-900 transition" title="Lihat Nilai Gizi">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════ CHART.JS INITIALIZATION ══════════════ --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('weeklyChart').getContext('2d');

    // Create gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 220);
    gradient.addColorStop(0, 'rgba(4, 120, 87, 0.45)');
    gradient.addColorStop(1, 'rgba(4, 120, 87, 0.02)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Asupan Kalori (kkal)',
                data: {!! json_encode($chartCalories) !!},
                borderColor: '#047857',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#047857',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    backgroundColor: '#064e3b',
                    titleFont: { size: 12, weight: 'bold' },
                    bodyFont: { size: 12 },
                    padding: 10,
                    cornerRadius: 12,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                    },
                    ticks: {
                        font: { size: 10 },
                        color: '#7e917e',
                    }
                },
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        font: { size: 10 },
                        color: '#7e917e',
                    }
                }
            }
        }
    });
});
</script>
@endsection
