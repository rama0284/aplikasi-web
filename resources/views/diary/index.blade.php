@extends('layouts.app')

@section('title', 'Food Diary — NutriScan AI')
@section('page_title', 'Jurnal Makan Harian')

@section('content')
<div class="space-y-7">

    <!-- Date Navigation Header Banner -->
    <div class="bg-white/95 p-5 sm:p-6 rounded-3xl border border-stone-200/90 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <!-- Date Switcher -->
        <div class="flex items-center gap-2">
            <a href="{{ route('diary.index', ['date' => $selectedDate->copy()->subDay()->format('Y-m-d')]) }}"
               class="p-2.5 rounded-2xl bg-cream-200 text-charcoal-muted hover:bg-emerald-100 hover:text-emerald-900 transition" title="Hari Sebelumnya">
                <i data-lucide="chevron-left" class="w-5 h-5"></i>
            </a>

            <form method="GET" action="{{ route('diary.index') }}" class="flex items-center gap-2">
                <input type="date" name="date" value="{{ $selectedDate->format('Y-m-d') }}" onchange="this.form.submit()"
                       class="px-4 py-2 rounded-2xl border border-stone-200 bg-cream-100 text-sm font-black text-charcoal focus:outline-none focus:ring-2 focus:ring-emerald-700/30">
            </form>

            <a href="{{ route('diary.index', ['date' => $selectedDate->copy()->addDay()->format('Y-m-d')]) }}"
               class="p-2.5 rounded-2xl bg-cream-200 text-charcoal-muted hover:bg-emerald-100 hover:text-emerald-900 transition" title="Hari Berikutnya">
                <i data-lucide="chevron-right" class="w-5 h-5"></i>
            </a>

            @if(!$selectedDate->isToday())
                <a href="{{ route('diary.index') }}" class="text-xs font-black text-emerald-800 bg-emerald-50 px-3 py-2 rounded-xl hover:bg-emerald-100 transition border border-emerald-200/60 ml-1">
                    Hari Ini
                </a>
            @endif
        </div>

        <!-- Add Food Action -->
        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <button onclick="openManualModal()" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-2xl bg-emerald-800 text-white font-black text-xs shadow-xs hover:bg-emerald-900 transition">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Catat Makanan</span>
            </button>
            <a href="{{ route('foods.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-cream-200 border border-stone-200 text-charcoal font-bold text-xs hover:bg-cream-300 transition">
                <i data-lucide="database" class="w-4 h-4 text-emerald-800"></i>
                <span>Katalog Makanan</span>
            </a>
            <a href="{{ route('scanner.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-cream-200 border border-stone-200 text-charcoal font-bold text-xs hover:bg-cream-300 transition">
                <i data-lucide="scan" class="w-4 h-4 text-emerald-800"></i>
                <span>Scan AI</span>
            </a>
        </div>
    </div>

    <!-- Day's Nutrition Summary Bar -->
    <div class="p-6 rounded-3xl bg-white/95 border border-stone-200/90 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-stone-200/70">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-charcoal-light">Laporan Harian</span>
                <h3 class="text-base font-black text-charcoal">
                    Asupan Nutrisi — {{ $selectedDate->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </h3>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-black text-emerald-950">{{ number_format($totalCalories, 0) }}</span>
                <span class="text-xs font-bold text-charcoal-muted">/ {{ number_format($goal->calorie_goal ?: 2100, 0) }} kkal target</span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
            <div class="p-3.5 rounded-2xl bg-cream-100 border border-stone-200/60">
                <span class="text-[10px] uppercase font-bold text-charcoal-muted block">Kalori Masuk</span>
                <span class="text-xl font-black text-emerald-950">{{ number_format($totalCalories, 0) }} kkal</span>
            </div>
            <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-100">
                <span class="text-[10px] uppercase font-bold text-blue-900 block">Protein</span>
                <span class="text-xl font-black text-blue-950">{{ $totalProtein }} g</span>
            </div>
            <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-100">
                <span class="text-[10px] uppercase font-bold text-amber-900 block">Karbohidrat</span>
                <span class="text-xl font-black text-amber-950">{{ $totalCarbs }} g</span>
            </div>
            <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-100">
                <span class="text-[10px] uppercase font-bold text-rose-900 block">Lemak</span>
                <span class="text-xl font-black text-rose-950">{{ $totalFat }} g</span>
            </div>
        </div>
    </div>

    <!-- Meal Sections (Sarapan, Makan Siang, Makan Malam, Camilan) -->
    <div class="space-y-6">

        @php
            $sections = [
                ['key' => 'sarapan', 'title' => 'Sarapan Pagi', 'icon' => 'sunrise', 'desc' => '06:00 - 10:00 WIB'],
                ['key' => 'makan_siang', 'title' => 'Makan Siang', 'icon' => 'sun', 'desc' => '11:30 - 14:30 WIB'],
                ['key' => 'makan_malam', 'title' => 'Makan Malam', 'icon' => 'moon', 'desc' => '18:00 - 20:30 WIB'],
                ['key' => 'camilan', 'title' => 'Camilan & Snack Sehat', 'icon' => 'apple', 'desc' => 'Antara waktu makan utama'],
            ];
        @endphp

        @foreach($sections as $sec)
            @php
                $secLogs = $groupedLogs[$sec['key']];
                $secCals = round($secLogs->sum('calories'), 1);
            @endphp
            <div class="bg-white/95 rounded-3xl border border-stone-200/90 shadow-xs overflow-hidden">
                <!-- Section Header -->
                <div class="px-6 py-4 bg-cream-100/80 border-b border-stone-200/70 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-2xl bg-white shadow-xs border border-stone-200/80 flex items-center justify-center text-emerald-800">
                            <i data-lucide="{{ $sec['icon'] }}" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-charcoal">{{ $sec['title'] }}</h4>
                            <span class="text-[10px] text-charcoal-muted">{{ $sec['desc'] }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-charcoal-muted">
                            Subtotal: <strong class="text-emerald-950 font-black">{{ $secCals }} kkal</strong>
                        </span>
                        <button onclick="openManualModalForType('{{ $sec['key'] }}')" class="p-1.5 rounded-xl bg-emerald-50 text-emerald-800 hover:bg-emerald-100 transition" title="Tambah ke waktu ini">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Section Items List -->
                <div class="p-4 sm:p-6">
                    @if($secLogs->count() > 0)
                        <div class="space-y-3">
                            @foreach($secLogs as $log)
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-2xl bg-cream-50 border border-stone-200/70 hover:border-emerald-500/40 hover:bg-white transition gap-3">
                                    <div class="flex items-center gap-3.5">
                                        <img src="{{ $log->image_url }}" alt="{{ $log->display_name }}"
                                             class="w-12 h-12 rounded-2xl object-cover border border-stone-200 shadow-sm flex-shrink-0">
                                        <div>
                                            <h5 class="text-xs sm:text-sm font-black text-charcoal">{{ $log->display_name }}</h5>
                                            <p class="text-[11px] text-charcoal-muted flex items-center gap-1.5 mt-0.5">
                                                <span>{{ $log->portion_grams }} gram</span>
                                                <span>•</span>
                                                <span>{{ $log->consumed_at->format('H:i') }} WIB</span>
                                                @if($log->food_scan_id)
                                                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-amber-100 text-amber-900 font-bold">Hasil AI</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between sm:justify-end gap-4 border-t sm:border-t-0 pt-2 sm:pt-0 border-stone-200/60">
                                        <div class="text-left sm:text-right">
                                            <span class="text-sm font-black text-emerald-950">{{ $log->calories }} kkal</span>
                                            <p class="text-[10px] text-charcoal-light font-medium">P: {{ $log->protein }}g | K: {{ $log->carbohydrates }}g | L: {{ $log->fat }}g</p>
                                        </div>

                                        <div class="flex items-center gap-1.5">
                                            <!-- Edit Action -->
                                            <a href="{{ route('diary.edit', $log->id) }}" class="p-2 text-stone-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition" title="Edit Porsi">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </a>

                                            <!-- Delete Action -->
                                            <form method="POST" action="{{ route('diary.destroy', $log->id) }}" onsubmit="return confirm('Hapus catatan makanan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition" title="Hapus">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-6 text-center">
                            <p class="text-xs text-charcoal-light italic mb-2">Belum ada makanan dicatat untuk waktu {{ strtolower($sec['title']) }}.</p>
                            <button onclick="openManualModalForType('{{ $sec['key'] }}')" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-800 hover:underline">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                <span>Catat {{ $sec['title'] }}</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Modal: Tambah Makanan Manual -->
<div id="manualModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 border border-stone-200 shadow-2xl space-y-5 animate-fade-in">
        <div class="flex items-center justify-between pb-3 border-b border-stone-200">
            <h3 class="text-base font-black text-emerald-950 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5 text-emerald-800"></i>
                <span>Catat Makanan Baru</span>
            </h3>
            <button onclick="closeManualModal()" class="text-stone-400 hover:text-charcoal p-1 rounded-xl hover:bg-cream-200 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('diary.store') }}" class="space-y-4">
            @csrf

            <!-- Waktu Makan -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-charcoal mb-1">Waktu Makan *</label>
                <select id="modalMealTypeSelect" name="meal_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-stone-200 bg-cream-100 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-700/30">
                    <option value="sarapan">🍳 Sarapan Pagi</option>
                    <option value="makan_siang" selected>🍛 Makan Siang</option>
                    <option value="makan_malam">🍲 Makan Malam</option>
                    <option value="camilan">🥪 Camilan Sehat</option>
                </select>
            </div>

            <!-- Tanggal & Jam Konsumsi -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-charcoal mb-1">Waktu Konsumsi *</label>
                <input type="datetime-local" name="consumed_at" value="{{ $selectedDate->format('Y-m-d\TH:i') }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-stone-200 bg-cream-100 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-700/30">
            </div>

            <!-- Pilih dari Database atau Kustom -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-charcoal mb-1">Pilih Makanan dari Database</label>
                <select name="food_id" id="modalFoodSelect" onchange="toggleCustomFields(this)"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-stone-200 bg-cream-100 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-700/30">
                    <option value="">-- Makanan Kustom (Input Manual) --</option>
                    @foreach($allFoods as $f)
                        <option value="{{ $f->id }}">{{ $f->name }} ({{ number_format($f->calories_per_100g, 0) }} kkal / 100g)</option>
                    @endforeach
                </select>
            </div>

            <!-- Custom Input Fields -->
            <div id="customFoodGroup" class="space-y-3 p-4 rounded-2xl bg-cream-200/80 border border-stone-200">
                <div>
                    <label class="block text-[11px] font-bold text-charcoal mb-1">Nama Makanan Kustom</label>
                    <input type="text" name="custom_food_name" placeholder="Misal: Kue Lapis Legit"
                           class="w-full px-3 py-2 rounded-xl border border-stone-200 bg-white text-xs font-medium">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] font-bold text-charcoal-muted mb-0.5">Kalori per 100g (kkal)</label>
                        <input type="number" step="0.1" name="custom_calories" value="180" class="w-full px-3 py-1.5 rounded-xl border border-stone-200 bg-white text-xs font-bold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-blue-900 mb-0.5">Protein per 100g (g)</label>
                        <input type="number" step="0.1" name="custom_protein" value="5" class="w-full px-3 py-1.5 rounded-xl border border-stone-200 bg-white text-xs font-bold">
                    </div>
                </div>
            </div>

            <!-- Porsi Gram -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-charcoal mb-1">Ukuran Porsi (Gram) *</label>
                <input type="number" name="portion_grams" min="5" max="2500" value="150" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-stone-200 bg-cream-100 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-emerald-700/30">
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-200">
                <button type="button" onclick="closeManualModal()" class="px-4 py-2.5 rounded-xl text-xs font-bold text-charcoal-muted hover:bg-cream-200 transition">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-800 text-white text-xs font-black hover:bg-emerald-900 transition shadow-sm">
                    Simpan ke Diary
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openManualModal() {
        document.getElementById('manualModal').classList.remove('hidden');
    }

    function openManualModalForType(type) {
        document.getElementById('modalMealTypeSelect').value = type;
        document.getElementById('manualModal').classList.remove('hidden');
    }

    function closeManualModal() {
        document.getElementById('manualModal').classList.add('hidden');
    }

    function toggleCustomFields(select) {
        const group = document.getElementById('customFoodGroup');
        if (select.value === '') {
            group.classList.remove('hidden');
        } else {
            group.classList.add('hidden');
        }
    }
</script>
@endsection
