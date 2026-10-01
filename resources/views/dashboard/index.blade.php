@extends('layouts.app')

@section('title', 'Dashboard — NutriScan AI')
@section('page_title', 'Ringkasan Nutrisi Harian')

@section('content')
<div class="space-y-8">

    <!-- Hero Bento Card with Dynamic Glassmorphic Glow -->
    <div class="relative p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-emerald-950 via-emerald-900 to-emerald-800 text-white overflow-hidden shadow-xl shadow-emerald-950/20 border border-emerald-700/40">
        <!-- Ambient Glow Circles -->
        <div class="absolute -right-12 -top-12 w-80 h-80 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-12 -bottom-12 w-64 h-64 bg-teal-400/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-3 max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-800/60 text-emerald-200 text-xs font-semibold backdrop-blur-md border border-emerald-600/40 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Monitoring Nutrisi Pintar Hari Ini</span>
                </div>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight leading-tight">
                    Semangat Sehat, {{ $user->name }}!
                </h2>
                <p class="text-xs sm:text-sm text-emerald-100/90 leading-relaxed">
                    Asupan energi Anda telah mencapai <span class="font-bold text-white underline decoration-emerald-400 decoration-2">{{ $totalCalories }} kkal</span> dari sasaran harian <span class="font-bold text-white">{{ $calorieGoal }} kkal</span>.
                </p>

                <!-- Quick Macro Status Pills -->
                <div class="flex flex-wrap gap-2 pt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white/10 backdrop-blur-md text-[11px] font-semibold border border-white/10 text-emerald-100">
                        <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                        <span>Protein: {{ $totalProtein }}g / {{ $proteinGoal }}g</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white/10 backdrop-blur-md text-[11px] font-semibold border border-white/10 text-emerald-100">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        <span>Karbo: {{ $totalCarbs }}g / {{ $carbsGoal }}g</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white/10 backdrop-blur-md text-[11px] font-semibold border border-white/10 text-emerald-100">
                        <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                        <span>Lemak: {{ $totalFat }}g / {{ $fatGoal }}g</span>
                    </span>
                </div>
            </div>

            <!-- Right Circular Meter & Quick Action -->
            <div class="flex flex-col sm:flex-row lg:flex-col items-center gap-4 bg-emerald-900/60 p-5 rounded-2xl border border-emerald-700/40 backdrop-blur-md">
                <div class="flex items-center gap-4">
                    <div class="relative w-16 h-16 flex items-center justify-center">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-emerald-800" stroke-width="3.5" stroke="currentColor" fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-emerald-400 transition-all duration-1000 ease-out" stroke-width="3.5"
                                stroke-dasharray="{{ $caloriePercent }}, 100" stroke-linecap="round" stroke="currentColor" fill="none"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <span class="absolute text-xs font-black text-white">{{ $caloriePercent }}%</span>
                    </div>
                    <div>
                        <span class="text-[11px] text-emerald-200 block font-medium">Sisa Kuota Kalori</span>
                        <span class="text-lg font-black text-white">{{ max(0, round($calorieGoal - $totalCalories, 1)) }} kkal</span>
                    </div>
                </div>

                <a href="{{ route('scanner.index') }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white text-emerald-950 font-extrabold text-xs shadow-lg shadow-black/10 hover:bg-emerald-50 hover:scale-[1.02] transition">
                    <i data-lucide="scan" class="w-4 h-4 text-emerald-800"></i>
                    <span>Scan Piring Makanan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Macronutrient Bento Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Kalori -->
        <div class="p-5 rounded-3xl bg-white/90 backdrop-blur-md border border-stone-200/90 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-extrabold uppercase tracking-wider text-charcoal-muted">Energi Total</span>
                <div class="w-9 h-9 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center shadow-sm">
                    <i data-lucide="flame" class="w-5 h-5 text-emerald-700"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-emerald-950 tracking-tight">{{ $totalCalories }}</span>
                <span class="text-xs text-charcoal-muted font-bold">/ {{ $calorieGoal }} kkal</span>
            </div>
            <!-- Progress Bar -->
            <div class="mt-3.5 w-full bg-cream-300 rounded-full h-2.5 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-600 to-emerald-800 h-2.5 rounded-full transition-all duration-700" style="width: {{ $caloriePercent }}%"></div>
            </div>
            <div class="flex justify-between items-center text-[11px] text-charcoal-light mt-2 font-medium">
                <span class="text-emerald-800 font-bold">{{ $caloriePercent }}% tercapai</span>
                <span>Sisa: {{ max(0, round($calorieGoal - $totalCalories, 1)) }} kkal</span>
            </div>
        </div>

        <!-- Protein -->
        <div class="p-5 rounded-3xl bg-white/90 backdrop-blur-md border border-stone-200/90 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-extrabold uppercase tracking-wider text-blue-900">Protein</span>
                <div class="w-9 h-9 rounded-2xl bg-blue-100 text-blue-800 flex items-center justify-center shadow-sm">
                    <i data-lucide="egg" class="w-5 h-5 text-blue-700"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-blue-950 tracking-tight">{{ $totalProtein }}</span>
                <span class="text-xs text-charcoal-muted font-bold">/ {{ $proteinGoal }} g</span>
            </div>
            <div class="mt-3.5 w-full bg-cream-300 rounded-full h-2.5 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-700 h-2.5 rounded-full transition-all duration-700" style="width: {{ $proteinPercent }}%"></div>
            </div>
            <div class="flex justify-between items-center text-[11px] text-charcoal-light mt-2 font-medium">
                <span class="text-blue-800 font-bold">{{ $proteinPercent }}% tercapai</span>
                <span>Sisa: {{ max(0, round($proteinGoal - $totalProtein, 1)) }} g</span>
            </div>
        </div>

        <!-- Karbohidrat -->
        <div class="p-5 rounded-3xl bg-white/90 backdrop-blur-md border border-stone-200/90 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-extrabold uppercase tracking-wider text-amber-900">Karbohidrat</span>
                <div class="w-9 h-9 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center shadow-sm">
                    <i data-lucide="wheat" class="w-5 h-5 text-amber-700"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-amber-950 tracking-tight">{{ $totalCarbs }}</span>
                <span class="text-xs text-charcoal-muted font-bold">/ {{ $carbsGoal }} g</span>
            </div>
            <div class="mt-3.5 w-full bg-cream-300 rounded-full h-2.5 overflow-hidden">
                <div class="bg-gradient-to-r from-amber-500 to-amber-700 h-2.5 rounded-full transition-all duration-700" style="width: {{ $carbsPercent }}%"></div>
            </div>
            <div class="flex justify-between items-center text-[11px] text-charcoal-light mt-2 font-medium">
                <span class="text-amber-800 font-bold">{{ $carbsPercent }}% tercapai</span>
                <span>Sisa: {{ max(0, round($carbsGoal - $totalCarbs, 1)) }} g</span>
            </div>
        </div>

        <!-- Lemak -->
        <div class="p-5 rounded-3xl bg-white/90 backdrop-blur-md border border-stone-200/90 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-extrabold uppercase tracking-wider text-rose-900">Lemak</span>
                <div class="w-9 h-9 rounded-2xl bg-rose-100 text-rose-800 flex items-center justify-center shadow-sm">
                    <i data-lucide="droplet" class="w-5 h-5 text-rose-700"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-rose-950 tracking-tight">{{ $totalFat }}</span>
                <span class="text-xs text-charcoal-muted font-bold">/ {{ $fatGoal }} g</span>
            </div>
            <div class="mt-3.5 w-full bg-cream-300 rounded-full h-2.5 overflow-hidden">
                <div class="bg-gradient-to-r from-rose-500 to-rose-700 h-2.5 rounded-full transition-all duration-700" style="width: {{ $fatPercent }}%"></div>
            </div>
            <div class="flex justify-between items-center text-[11px] text-charcoal-light mt-2 font-medium">
                <span class="text-rose-800 font-bold">{{ $fatPercent }}% batas</span>
                <span>Sisa: {{ max(0, round($fatGoal - $totalFat, 1)) }} g</span>
            </div>
        </div>
    </div>

    <!-- Charts & Today's Meals Timeline -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- 7-Day Calorie Trend Chart (7 cols) -->
        <div class="lg:col-span-7 bg-white/90 backdrop-blur-md p-6 sm:p-7 rounded-3xl border border-stone-200/90 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-black text-charcoal tracking-tight">Tren Konsumsi 7 Hari Terakhir</h3>
                    <p class="text-xs text-charcoal-muted mt-0.5">Asupan kalori harian dibandingkan konsistensi sasaran</p>
                </div>
                <a href="{{ route('history.index') }}" class="text-xs font-extrabold text-emerald-800 hover:text-emerald-950 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200 transition flex items-center gap-1">
                    <span>Detail Tren</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="h-64 relative">
                <canvas id="weeklyChart"></canvas>
            </div>

            <!-- Smart AI Health Insight Micro-Widget -->
            <div class="mt-6 pt-4 border-t border-stone-200/70 flex items-start gap-3 bg-cream-100 p-3.5 rounded-2xl border border-stone-200/60 text-xs">
                <div class="w-7 h-7 rounded-xl bg-emerald-800 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                    <i data-lucide="sparkles" class="w-4 h-4 text-emerald-200"></i>
                </div>
                <div class="flex-1">
                    <strong class="text-emerald-950 font-bold block mb-0.5">NutriScan AI Insight:</strong>
                    <p class="text-charcoal-muted leading-relaxed">
                        @if($totalCalories == 0)
                            Anda belum mencatat makanan hari ini. Awali hari dengan sarapan berprotein tinggi seperti telur atau yogurt untuk energi stabil!
                        @elseif($proteinPercent >= 80)
                            Hebat! Asupan protein Anda hari ini sudah {{ $proteinPercent }}% tercapai. Ini sangat optimal untuk metabolisme dan kebugaran tubuh.
                        @else
                            Konsumsi energi Anda hari ini {{ $caloriePercent }}%. Pastikan mencukupi kebutuhan serat dan cairan untuk menjaga fokus harian.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Today's Recent Meals Timeline (5 cols) -->
        <div class="lg:col-span-5 bg-white/90 backdrop-blur-md p-6 sm:p-7 rounded-3xl border border-stone-200/90 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-black text-charcoal tracking-tight">Makanan Hari Ini</h3>
                    <p class="text-xs text-charcoal-muted mt-0.5">{{ count($todayLogs) }} hidangan telah tercatat</p>
                </div>
                <a href="{{ route('diary.index') }}" class="text-xs font-bold text-emerald-800 hover:underline">
                    Buku Harian
                </a>
            </div>

            <!-- Meals List or Clean Empty State -->
            @if(count($todayLogs) > 0)
                <div class="space-y-3 overflow-y-auto max-h-80 custom-scrollbar flex-1 pr-1">
                    @foreach($todayLogs as $log)
                        <div class="flex items-center justify-between p-3.5 rounded-2xl bg-cream-100 border border-stone-200/80 hover:border-emerald-600/40 hover:bg-cream-200 transition group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-800/10 text-emerald-800 flex items-center justify-center font-bold text-xs group-hover:scale-105 transition-transform">
                                    @if($log->food_scan_id)
                                        <i data-lucide="scan" class="w-4 h-4"></i>
                                    @else
                                        <i data-lucide="utensils" class="w-4 h-4"></i>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-charcoal">{{ $log->display_name }}</h4>
                                    <p class="text-[11px] text-charcoal-muted">
                                        <span class="capitalize font-bold text-emerald-800">{{ $log->meal_type_label }}</span> &bull; {{ $log->portion_grams }}g
                                        @if($log->food_scan_id)
                                            <span class="ml-1 text-[9px] font-bold px-1.5 py-0.2 bg-emerald-100 text-emerald-800 rounded">AI Scan</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-black text-emerald-950">{{ $log->calories }} <span class="text-[10px] font-normal text-charcoal-muted">kkal</span></span>
                                <p class="text-[10px] text-charcoal-light font-medium">P: {{ $log->protein }}g &bull; K: {{ $log->carbohydrates }}g</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Clean Empty State -->
                <div class="flex-1 flex flex-col items-center justify-center py-8 text-center">
                    <div class="w-14 h-14 rounded-3xl bg-cream-300 text-charcoal-light flex items-center justify-center mb-3 shadow-inner">
                        <i data-lucide="salad" class="w-7 h-7 text-emerald-700"></i>
                    </div>
                    <h4 class="text-sm font-bold text-charcoal">Belum Ada Makanan Hari Ini</h4>
                    <p class="text-xs text-charcoal-muted max-w-xs mt-1 mb-4 leading-relaxed">
                        Unggah foto menu sarapan, makan siang, atau camilan Anda untuk kalkulasi nutrisi instan.
                    </p>
                    <a href="{{ route('scanner.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-emerald-800 text-white text-xs font-bold hover:bg-emerald-900 transition shadow-md shadow-emerald-950/10">
                        <i data-lucide="camera" class="w-3.5 h-3.5"></i>
                        <span>Scan Makanan Sekarang</span>
                    </a>
                </div>
            @endif

            <div class="pt-4 mt-auto border-t border-stone-200/70 flex items-center justify-between text-xs text-charcoal-muted font-medium">
                <span>Total Serat Harian: <strong class="text-charcoal font-black">{{ $totalFiber }} g</strong></span>
                <span class="text-[11px] text-emerald-800 font-semibold">Tabel Komposisi Pangan RI</span>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('weeklyChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Asupan Kalori (kkal)',
                        data: {!! json_encode($chartCalories) !!},
                        backgroundColor: '#166534',
                        hoverBackgroundColor: '#047857',
                        borderRadius: 10,
                        barThickness: 28,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#14532d',
                            padding: 10,
                            titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
                            bodyFont: { family: 'Plus Jakarta Sans', size: 11 },
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' kkal';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#EFEFE5' },
                            ticks: {
                                font: { family: 'Plus Jakarta Sans', size: 11 },
                                color: '#859685'
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' },
                                color: '#526052'
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
