@extends('layouts.app')

@section('title', 'Riwayat & Tren Nutrisi — NutriScan AI')
@section('page_title', 'Riwayat & Analisis Tren')

@section('content')
<div class="space-y-8">

    <!-- Range Filter & Summary Stats -->
    <div class="bg-cream-100 p-6 rounded-3xl border border-stone-200/90 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h3 class="text-lg font-bold text-emerald-950">Analisis Tren Konsumsi Gizi</h3>
            <p class="text-xs text-charcoal-muted mt-0.5">Pantau konsistensi pemenuhan target kalori dan makronutrien Anda</p>
        </div>

        <!-- Range Buttons -->
        <div class="flex items-center gap-2 bg-cream-200 p-1.5 rounded-2xl border border-stone-300/80">
            <a href="{{ route('history.index', ['range' => '7']) }}"
               class="px-4 py-1.5 rounded-xl text-xs font-bold transition {{ $range == '7' ? 'bg-emerald-800 text-white shadow-sm' : 'text-charcoal-muted hover:text-charcoal' }}">
                7 Hari Terakhir
            </a>
            <a href="{{ route('history.index', ['range' => '30']) }}"
               class="px-4 py-1.5 rounded-xl text-xs font-bold transition {{ $range == '30' ? 'bg-emerald-800 text-white shadow-sm' : 'text-charcoal-muted hover:text-charcoal' }}">
                30 Hari Terakhir
            </a>
        </div>
    </div>

    <!-- Average Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="p-5 rounded-2xl bg-cream-100 border border-stone-200/90 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-charcoal-muted block">Rata-rata Kalori Harian</span>
            <div class="flex items-baseline gap-1 mt-2">
                <span class="text-2xl font-black text-emerald-950">{{ $avgCalories }}</span>
                <span class="text-xs text-charcoal-muted">kkal / hari</span>
            </div>
            <p class="text-[11px] text-charcoal-light mt-1">Dihitung dari total hari aktif pada periode ini</p>
        </div>

        <div class="p-5 rounded-2xl bg-cream-100 border border-stone-200/90 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-charcoal-muted block">Rata-rata Protein Harian</span>
            <div class="flex items-baseline gap-1 mt-2">
                <span class="text-2xl font-black text-blue-900">{{ $avgProtein }}</span>
                <span class="text-xs text-charcoal-muted">gram / hari</span>
            </div>
            <p class="text-[11px] text-charcoal-light mt-1">Mendukung metabolisme dan pemulihan sel tubuh</p>
        </div>

        <div class="p-5 rounded-2xl bg-cream-100 border border-stone-200/90 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-charcoal-muted block">Total Foto Scan AI</span>
            <div class="flex items-baseline gap-1 mt-2">
                <span class="text-2xl font-black text-emerald-800">{{ $scans->total() }}</span>
                <span class="text-xs text-charcoal-muted">kali scan</span>
            </div>
            <p class="text-[11px] text-charcoal-light mt-1">Foto makanan yang diidentifikasi melalui Vision AI</p>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Calorie Chart -->
        <div class="bg-cream-100 p-6 rounded-3xl border border-stone-200/90 shadow-sm">
            <h4 class="text-sm font-bold text-charcoal mb-1">Grafik Asupan Kalori (kkal)</h4>
            <p class="text-xs text-charcoal-muted mb-4">Konsumsi kalori per hari selama {{ $daysCount }} hari</p>
            <div class="h-64 relative">
                <canvas id="caloriesHistoryChart"></canvas>
            </div>
        </div>

        <!-- Macronutrients Chart -->
        <div class="bg-cream-100 p-6 rounded-3xl border border-stone-200/90 shadow-sm">
            <h4 class="text-sm font-bold text-charcoal mb-1">Tren Makronutrien (Gram)</h4>
            <p class="text-xs text-charcoal-muted mb-4">Keseimbangan Protein, Karbohidrat, dan Lemak</p>
            <div class="h-64 relative">
                <canvas id="macrosHistoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Food Scans Gallery / Table -->
    <div class="bg-cream-100 p-6 rounded-3xl border border-stone-200/90 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h4 class="text-base font-bold text-charcoal">Riwayat Hasil Scan AI</h4>
                <p class="text-xs text-charcoal-muted">Foto hidangan yang pernah Anda pindai melalui sistem</p>
            </div>
            <a href="{{ route('scanner.index') }}" class="text-xs font-semibold text-emerald-800 hover:underline flex items-center gap-1">
                <span>Scan Baru</span>
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        @if($scans->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($scans as $scan)
                    <div class="p-4 rounded-2xl bg-cream-200 border border-stone-200/80 hover:border-emerald-600/50 transition flex flex-col justify-between">
                        <div>
                            <div class="rounded-xl overflow-hidden bg-stone-200 aspect-video mb-3 relative">
                                <img src="{{ $scan->image_url }}" alt="{{ $scan->identified_food_name }}" class="w-full h-full object-cover">
                                <span class="absolute bottom-1.5 right-1.5 px-2 py-0.5 rounded-md bg-black/60 text-white text-[10px] font-mono">
                                    {{ $scan->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                            <h5 class="text-xs font-bold text-charcoal truncate">{{ $scan->identified_food_name }}</h5>
                            <p class="text-[11px] text-charcoal-muted">
                                Porsi: {{ $scan->estimated_portion_grams ?: '-' }}g &bull; Status:
                                <span class="font-semibold text-emerald-800">{{ ucfirst($scan->status) }}</span>
                            </p>
                        </div>

                        <div class="pt-3 mt-3 border-t border-stone-300/60 flex items-center justify-between">
                            <a href="{{ route('scanner.result', $scan->id) }}" class="text-[11px] font-bold text-emerald-800 hover:underline">
                                Buka Rincian Nutrisi &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $scans->links() }}
            </div>
        @else
            <div class="text-center py-10">
                <div class="w-12 h-12 rounded-2xl bg-cream-300 text-charcoal-light flex items-center justify-center mx-auto mb-2">
                    <i data-lucide="image" class="w-6 h-6"></i>
                </div>
                <p class="text-xs font-semibold text-charcoal">Belum ada foto makanan yang dipindai.</p>
                <p class="text-[11px] text-charcoal-muted mt-0.5">Unggah foto piring Anda untuk memulai riwayat visual.</p>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const labels = {!! json_encode($labels) !!};

        // 1. Calories Chart
        const calCtx = document.getElementById('caloriesHistoryChart');
        if (calCtx) {
            new Chart(calCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Kalori (kkal)',
                        data: {!! json_encode($caloriesData) !!},
                        borderColor: '#166534',
                        backgroundColor: 'rgba(22, 101, 52, 0.1)',
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#166534',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#EFEFE5' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // 2. Macros Chart
        const macroCtx = document.getElementById('macrosHistoryChart');
        if (macroCtx) {
            new Chart(macroCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Protein (g)',
                            data: {!! json_encode($proteinData) !!},
                            borderColor: '#2563EB',
                            tension: 0.3,
                        },
                        {
                            label: 'Karbohidrat (g)',
                            data: {!! json_encode($carbsData) !!},
                            borderColor: '#D97706',
                            tension: 0.3,
                        },
                        {
                            label: 'Lemak (g)',
                            data: {!! json_encode($fatData) !!},
                            borderColor: '#E11D48',
                            tension: 0.3,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, font: { family: 'Plus Jakarta Sans', size: 11 } }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#EFEFE5' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    });
</script>
@endsection
