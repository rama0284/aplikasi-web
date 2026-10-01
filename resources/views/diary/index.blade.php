@extends('layouts.app')

@section('title', 'Food Diary — NutriScan AI')
@section('page_title', 'Buku Harian Makanan')

@section('content')
<div class="space-y-8">

    <!-- Date Navigation Header -->
    <div class="bg-cream-100 p-5 rounded-3xl border border-stone-200/90 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <!-- Date Switcher -->
        <div class="flex items-center gap-2">
            <a href="{{ route('diary.index', ['date' => $selectedDate->copy()->subDay()->format('Y-m-d')]) }}"
               class="p-2 rounded-xl bg-cream-200 text-charcoal-muted hover:bg-cream-300 transition" title="Hari Sebelumnya">
                <i data-lucide="chevron-left" class="w-5 h-5"></i>
            </a>

            <form method="GET" action="{{ route('diary.index') }}" class="flex items-center gap-2">
                <input type="date" name="date" value="{{ $selectedDate->format('Y-m-d') }}" onchange="this.form.submit()"
                       class="px-3.5 py-2 rounded-xl border border-stone-300 bg-cream-50 text-sm font-bold text-charcoal focus:ring-2 focus:ring-emerald-700">
            </form>

            <a href="{{ route('diary.index', ['date' => $selectedDate->copy()->addDay()->format('Y-m-d')]) }}"
               class="p-2 rounded-xl bg-cream-200 text-charcoal-muted hover:bg-cream-300 transition" title="Hari Berikutnya">
                <i data-lucide="chevron-right" class="w-5 h-5"></i>
            </a>

            @if(!$selectedDate->isToday())
                <a href="{{ route('diary.index') }}" class="text-xs font-semibold text-emerald-800 bg-emerald-50 px-3 py-1.5 rounded-lg hover:bg-emerald-100 transition ml-1">
                    Kembali ke Hari Ini
                </a>
            @endif
        </div>

        <!-- Add Manual Food Action -->
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <button onclick="openManualModal()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-800 text-white font-semibold text-xs shadow-sm hover:bg-emerald-900 transition">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Makanan Manual</span>
            </button>
            <a href="{{ route('scanner.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-cream-200 border border-stone-300 text-charcoal font-semibold text-xs hover:bg-cream-300 transition">
                <i data-lucide="camera" class="w-4 h-4 text-emerald-800"></i>
                <span>Scan Foto</span>
            </a>
        </div>
    </div>

    <!-- Day's Nutrition Summary Bar -->
    <div class="p-6 rounded-3xl bg-cream-100 border border-stone-200/90 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-stone-200/70">
            <div>
                <h3 class="text-base font-bold text-charcoal">
                    Asupan Nutrisi — {{ $selectedDate->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </h3>
                <p class="text-xs text-charcoal-muted">Total makronutrien yang dikonsumsi pada tanggal ini</p>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-black text-emerald-950">{{ $totalCalories }}</span>
                <span class="text-xs text-charcoal-muted">/ {{ $goal->calorie_goal ?: 2000 }} kkal</span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 text-center">
            <div class="p-3 rounded-2xl bg-cream-200">
                <span class="text-[10px] uppercase font-bold text-charcoal-muted block">Kalori</span>
                <span class="text-lg font-black text-emerald-900">{{ $totalCalories }} kkal</span>
            </div>
            <div class="p-3 rounded-2xl bg-cream-200">
                <span class="text-[10px] uppercase font-bold text-blue-700 block">Protein</span>
                <span class="text-lg font-black text-blue-900">{{ $totalProtein }} g</span>
            </div>
            <div class="p-3 rounded-2xl bg-cream-200">
                <span class="text-[10px] uppercase font-bold text-amber-700 block">Karbohidrat</span>
                <span class="text-lg font-black text-amber-900">{{ $totalCarbs }} g</span>
            </div>
            <div class="p-3 rounded-2xl bg-cream-200">
                <span class="text-[10px] uppercase font-bold text-rose-700 block">Lemak</span>
                <span class="text-lg font-black text-rose-900">{{ $totalFat }} g</span>
            </div>
        </div>
    </div>

    <!-- Meal Sections (Sarapan, Makan Siang, Makan Malam, Camilan) -->
    <div class="space-y-6">

        @php
            $sections = [
                ['key' => 'sarapan', 'title' => 'Sarapan (Pagi)', 'icon' => 'sunrise', 'color' => 'amber'],
                ['key' => 'makan_siang', 'title' => 'Makan Siang', 'icon' => 'sun', 'color' => 'emerald'],
                ['key' => 'makan_malam', 'title' => 'Makan Malam', 'icon' => 'moon', 'color' => 'indigo'],
                ['key' => 'camilan', 'title' => 'Camilan & Snack', 'icon' => 'cookie', 'color' => 'purple'],
            ];
        @endphp

        @foreach($sections as $sec)
            @php
                $secLogs = $groupedLogs[$sec['key']];
                $secCals = round($secLogs->sum('calories'), 1);
            @endphp
            <div class="bg-cream-100 rounded-3xl border border-stone-200/90 shadow-sm overflow-hidden">
                <!-- Section Header -->
                <div class="px-6 py-4 bg-cream-200/70 border-b border-stone-200/70 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-white shadow-sm flex items-center justify-center text-emerald-800">
                            <i data-lucide="{{ $sec['icon'] }}" class="w-4 h-4"></i>
                        </div>
                        <h4 class="text-sm font-bold text-charcoal">{{ $sec['title'] }}</h4>
                    </div>
                    <span class="text-xs font-bold text-charcoal-muted">
                        Subtotal: <strong class="text-emerald-950">{{ $secCals }} kkal</strong>
                    </span>
                </div>

                <!-- Section Items List -->
                <div class="p-4 sm:p-6">
                    @if($secLogs->count() > 0)
                        <div class="space-y-3">
                            @foreach($secLogs as $log)
                                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-cream-50 border border-stone-200/70 hover:border-emerald-600/40 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-100/60 text-emerald-800 flex items-center justify-center font-bold text-xs">
                                            @if($log->food_scan_id)
                                                <i data-lucide="scan" class="w-4 h-4"></i>
                                            @else
                                                <i data-lucide="utensils" class="w-4 h-4"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h5 class="text-xs font-bold text-charcoal">{{ $log->display_name }}</h5>
                                            <p class="text-[11px] text-charcoal-muted">
                                                {{ $log->portion_grams }} gram &bull; {{ $log->consumed_at->format('H:i') }} WIB
                                                @if($log->food_scan_id)
                                                    <span class="ml-1 text-[10px] px-1.5 py-0.2 bg-emerald-100 text-emerald-800 rounded">dari Scan AI</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <div class="text-right">
                                            <span class="text-xs font-bold text-emerald-950">{{ $log->calories }} kkal</span>
                                            <p class="text-[10px] text-charcoal-light">P: {{ $log->protein }}g | K: {{ $log->carbohydrates }}g | L: {{ $log->fat }}g</p>
                                        </div>

                                        <!-- Delete Action -->
                                        <form method="POST" action="{{ route('diary.destroy', $log->id) }}" onsubmit="return confirm('Hapus catatan makanan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-stone-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-charcoal-light italic py-2 text-center">Belum ada makanan dicatat untuk waktu ini.</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Modal: Tambah Makanan Manual -->
<div id="manualModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-cream-100 rounded-3xl max-w-lg w-full p-6 sm:p-7 border border-stone-200 shadow-2xl space-y-5 animate-fade-in">
        <div class="flex items-center justify-between pb-3 border-b border-stone-200">
            <h3 class="text-base font-bold text-emerald-950 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5 text-emerald-800"></i>
                <span>Catat Makanan Manual</span>
            </h3>
            <button onclick="closeManualModal()" class="text-stone-400 hover:text-charcoal">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('diary.store') }}" class="space-y-4">
            @csrf

            <!-- Waktu Makan -->
            <div>
                <label class="block text-xs font-semibold text-charcoal mb-1">Waktu Makan</label>
                <select name="meal_type" required class="w-full px-3.5 py-2 rounded-xl border border-stone-300 bg-cream-50 text-xs focus:ring-2 focus:ring-emerald-700">
                    <option value="sarapan">Sarapan</option>
                    <option value="makan_siang" selected>Makan Siang</option>
                    <option value="makan_malam">Makan Malam</option>
                    <option value="camilan">Camilan</option>
                </select>
            </div>

            <!-- Tanggal & Jam Konsumsi -->
            <div>
                <label class="block text-xs font-semibold text-charcoal mb-1">Waktu Konsumsi</label>
                <input type="datetime-local" name="consumed_at" value="{{ $selectedDate->format('Y-m-d\TH:i') }}" required
                       class="w-full px-3.5 py-2 rounded-xl border border-stone-300 bg-cream-50 text-xs focus:ring-2 focus:ring-emerald-700">
            </div>

            <!-- Pilih dari Database atau Kustom -->
            <div>
                <label class="block text-xs font-semibold text-charcoal mb-1">Pilih Makanan dari Database</label>
                <select name="food_id" id="modalFoodSelect" onchange="toggleCustomFields(this)"
                        class="w-full px-3.5 py-2 rounded-xl border border-stone-300 bg-cream-50 text-xs focus:ring-2 focus:ring-emerald-700">
                    <option value="">-- Makanan Kustom (Input Manual) --</option>
                    @foreach($allFoods as $f)
                        <option value="{{ $f->id }}">{{ $f->name }} ({{ $f->calories_per_100g }} kkal / 100g)</option>
                    @endforeach
                </select>
            </div>

            <!-- Custom Input Fields -->
            <div id="customFoodGroup" class="space-y-3 p-3.5 rounded-2xl bg-cream-200/80 border border-stone-200">
                <div>
                    <label class="block text-[11px] font-semibold text-charcoal mb-1">Nama Makanan Kustom</label>
                    <input type="text" name="custom_food_name" placeholder="Misal: Kue Lapis Legit"
                           class="w-full px-3 py-1.5 rounded-xl border border-stone-300 bg-cream-50 text-xs">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] text-charcoal-muted mb-0.5">Kalori per 100g (kkal)</label>
                        <input type="number" step="0.1" name="custom_calories" value="180" class="w-full px-2.5 py-1.5 rounded-lg border border-stone-300 bg-cream-50 text-xs">
                    </div>
                    <div>
                        <label class="block text-[10px] text-charcoal-muted mb-0.5">Protein per 100g (g)</label>
                        <input type="number" step="0.1" name="custom_protein" value="5" class="w-full px-2.5 py-1.5 rounded-lg border border-stone-300 bg-cream-50 text-xs">
                    </div>
                </div>
            </div>

            <!-- Porsi Gram -->
            <div>
                <label class="block text-xs font-semibold text-charcoal mb-1">Ukuran Porsi (Gram)</label>
                <input type="number" name="portion_grams" min="5" max="2500" value="150" required
                       class="w-full px-3.5 py-2 rounded-xl border border-stone-300 bg-cream-50 text-xs focus:ring-2 focus:ring-emerald-700">
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-200">
                <button type="button" onclick="closeManualModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-charcoal-muted hover:bg-cream-300">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-800 text-white text-xs font-bold hover:bg-emerald-900 transition shadow-sm">
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
