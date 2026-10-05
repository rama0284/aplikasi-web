@extends('layouts.app')

@section('title', 'Database Makanan & Nutrisi — NutriScan AI')
@section('page_title', 'Database Makanan & Gizi')

@section('content')
<div class="space-y-7">

    {{-- ══════════════ HERO BANNER ══════════════ --}}
    <div class="relative overflow-hidden p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-emerald-950 via-emerald-900 to-teal-950 text-white shadow-xl border border-emerald-700/40">
        <div class="absolute -right-16 -top-16 w-72 h-72 bg-emerald-400/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 -bottom-10 w-64 h-64 bg-teal-400/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2.5 max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-800/60 text-emerald-200 text-xs font-bold backdrop-blur-md border border-emerald-600/40">
                    <i data-lucide="database" class="w-3.5 h-3.5 text-emerald-300"></i>
                    <span>Master Data Nutrisi Indonesia (TKPI & USDA)</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight">Katalog & Database Gizi Makanan</h2>
                <p class="text-sm text-emerald-100/80 leading-relaxed font-normal">
                    Basis data gizi terpadu dengan perhitungan makronutrien akurat. Anda dapat mencari, menambahkan resep baru, mengubah porsi, dan mencatat langsung ke jurnal harian.
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row md:flex-col lg:flex-row items-stretch sm:items-center gap-3">
                <a href="{{ route('foods.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-white text-emerald-950 font-black text-sm hover:bg-emerald-50 hover:scale-[1.02] transition shadow-lg shadow-black/15">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-emerald-700"></i>
                    <span>Tambah Makanan Baru</span>
                </a>
                <a href="{{ route('scanner.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-emerald-800/80 hover:bg-emerald-800 text-white font-bold text-sm border border-emerald-600/50 backdrop-blur-md transition">
                    <i data-lucide="scan" class="w-4 h-4 text-emerald-300"></i>
                    <span>Scan via AI</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ══════════════ STATS ROW ══════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        {{-- Total Food --}}
        <div class="p-5 rounded-3xl bg-white/90 backdrop-blur-md border border-stone-200/90 shadow-xs hover:border-emerald-300 transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-extrabold uppercase text-charcoal-muted tracking-wider">Total Makanan</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <i data-lucide="utensils" class="w-4 h-4 text-emerald-800"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-black text-charcoal">{{ $stats['total'] }}</p>
            <p class="text-[11px] text-charcoal-muted mt-1 font-medium">Tersimpan di MySQL</p>
        </div>

        {{-- High Protein --}}
        <div class="p-5 rounded-3xl bg-white/90 backdrop-blur-md border border-stone-200/90 shadow-xs hover:border-blue-300 transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-extrabold uppercase text-blue-900 tracking-wider">Tinggi Protein</span>
                <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center">
                    <i data-lucide="dumbbell" class="w-4 h-4 text-blue-700"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-black text-blue-950">{{ $stats['high_protein'] }}</p>
            <p class="text-[11px] text-blue-700 mt-1 font-medium">≥ 15g protein/100g</p>
        </div>

        {{-- Low Calorie --}}
        <div class="p-5 rounded-3xl bg-white/90 backdrop-blur-md border border-stone-200/90 shadow-xs hover:border-teal-300 transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-extrabold uppercase text-teal-900 tracking-wider">Rendah Kalori</span>
                <div class="w-9 h-9 rounded-xl bg-teal-100 flex items-center justify-center">
                    <i data-lucide="feather" class="w-4 h-4 text-teal-700"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-black text-teal-950">{{ $stats['low_cal'] }}</p>
            <p class="text-[11px] text-teal-700 mt-1 font-medium">≤ 100 kkal/100g</p>
        </div>

        {{-- High Fiber --}}
        <div class="p-5 rounded-3xl bg-white/90 backdrop-blur-md border border-stone-200/90 shadow-xs hover:border-amber-300 transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-extrabold uppercase text-amber-900 tracking-wider">Kaya Serat</span>
                <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center">
                    <i data-lucide="apple" class="w-4 h-4 text-amber-700"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-black text-amber-950">{{ $stats['high_fiber'] }}</p>
            <p class="text-[11px] text-amber-700 mt-1 font-medium">≥ 2.5g serat/100g</p>
        </div>
    </div>

    {{-- ══════════════ FILTER & SEARCH TOOLBAR ══════════════ --}}
    <div class="p-4 sm:p-5 rounded-3xl bg-white/90 backdrop-blur-md border border-stone-200/90 shadow-xs space-y-3">
        <form method="GET" action="{{ route('foods.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3">
            
            {{-- Search Bar --}}
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-charcoal-light pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama makanan, bahan, atau deskripsi gizi..."
                    class="w-full pl-10 pr-4 py-2.5 text-sm rounded-2xl bg-cream-100 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-emerald-700/30 text-charcoal placeholder:text-charcoal-light transition">
            </div>

            {{-- Category Dropdown --}}
            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                <select name="category" class="px-3.5 py-2.5 text-sm rounded-2xl bg-cream-100 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-emerald-700/30 text-charcoal transition min-w-[150px]">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>

                {{-- Sort Dropdown --}}
                <select name="sort" class="px-3.5 py-2.5 text-sm rounded-2xl bg-cream-100 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-emerald-700/30 text-charcoal transition min-w-[160px]">
                    <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Nama (A - Z)</option>
                    <option value="calories_desc" {{ request('sort') === 'calories_desc' ? 'selected' : '' }}>Kalori Tertinggi</option>
                    <option value="calories_asc" {{ request('sort') === 'calories_asc' ? 'selected' : '' }}>Kalori Terendah</option>
                    <option value="protein_desc" {{ request('sort') === 'protein_desc' ? 'selected' : '' }}>Protein Tertinggi</option>
                    <option value="fiber_desc" {{ request('sort') === 'fiber_desc' ? 'selected' : '' }}>Serat Tertinggi</option>
                </select>

                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-emerald-800 text-white text-sm font-bold hover:bg-emerald-900 transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    <span>Terapkan</span>
                </button>

                @if(request()->hasAny(['search', 'category', 'sort']))
                    <a href="{{ route('foods.index') }}" class="p-2.5 rounded-2xl bg-cream-300 text-charcoal-muted hover:bg-stone-300 transition flex items-center justify-center" title="Reset Filter">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Category Pills Shortcut --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1 custom-scrollbar text-xs">
            <span class="text-[11px] font-bold text-charcoal-light uppercase mr-1">Kategori:</span>
            <a href="{{ route('foods.index') }}" class="px-3 py-1 rounded-xl font-bold transition flex-shrink-0 {{ !request('category') ? 'bg-emerald-800 text-white' : 'bg-cream-200 text-charcoal-muted hover:bg-cream-300' }}">Semua</a>
            @foreach($categories as $cat)
                <a href="{{ route('foods.index', ['category' => $cat]) }}" class="px-3 py-1 rounded-xl font-bold transition flex-shrink-0 {{ request('category') === $cat ? 'bg-emerald-800 text-white' : 'bg-cream-200 text-charcoal-muted hover:bg-cream-300' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- ══════════════ FOOD CARDS GRID ══════════════ --}}
    @if($foods->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($foods as $food)
        <div class="group relative bg-white/95 rounded-3xl border border-stone-200/90 shadow-xs hover:shadow-xl hover:shadow-emerald-950/8 hover:-translate-y-1 transition duration-300 flex flex-col justify-between overflow-hidden">
            
            {{-- Top Accent Line based on Health Grade --}}
            <div class="h-1.5 w-full 
                @if($food->health_grade === 'A') bg-gradient-to-r from-emerald-500 to-teal-400
                @elseif($food->health_grade === 'B') bg-gradient-to-r from-teal-500 to-blue-400
                @elseif($food->health_grade === 'C') bg-gradient-to-r from-amber-400 to-orange-400
                @else bg-gradient-to-r from-rose-400 to-red-500
                @endif">
            </div>

            <div class="p-6 flex-1 flex flex-col justify-between">
                <div>
                    {{-- Header: Foto, Category, Health Grade Badge --}}
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $food->imageUrl() }}" alt="{{ $food->name }}"
                                 class="w-14 h-14 rounded-2xl object-cover border border-stone-200/70 shadow-sm group-hover:scale-110 transition duration-300">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60">
                                    {{ $food->category }}
                                </span>
                                <h3 class="text-base font-black text-charcoal mt-1 group-hover:text-emerald-800 transition line-clamp-1">
                                    <a href="{{ route('foods.show', $food->id) }}">{{ $food->name }}</a>
                                </h3>
                            </div>
                        </div>

                        {{-- Grade Pill --}}
                        <span class="text-[10px] font-black px-2.5 py-1 rounded-full border {{ $food->grade_badge['bg'] }} flex-shrink-0">
                            Grade {{ $food->health_grade ?: 'A' }}
                        </span>
                    </div>

                    {{-- Description --}}
                    @if($food->description)
                        <p class="text-xs text-charcoal-muted line-clamp-2 mb-4 leading-relaxed font-normal">
                            {{ $food->description }}
                        </p>
                    @endif

                    {{-- Primary Calorie Metric --}}
                    <div class="p-3.5 rounded-2xl bg-cream-100 border border-stone-200/70 mb-4 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-charcoal-light">Energi Total</span>
                            <p class="text-xl font-black text-charcoal leading-tight">
                                {{ number_format($food->calories_per_100g, 0) }} <span class="text-xs font-bold text-charcoal-muted">kkal</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-charcoal-light">Porsi Standar</span>
                            <p class="text-xs font-bold text-emerald-900 truncate max-w-[150px]">{{ $food->serving_size }}</p>
                        </div>
                    </div>

                    {{-- Macro Nutrients Bars (per 100g) --}}
                    <div class="grid grid-cols-3 gap-2 mb-4 text-center">
                        <div class="p-2 rounded-xl bg-blue-50/70 border border-blue-100">
                            <span class="text-[10px] font-bold text-blue-800 uppercase block">Protein</span>
                            <span class="text-sm font-black text-blue-950">{{ $food->protein_per_100g }}g</span>
                        </div>
                        <div class="p-2 rounded-xl bg-amber-50/70 border border-amber-100">
                            <span class="text-[10px] font-bold text-amber-800 uppercase block">Karbo</span>
                            <span class="text-sm font-black text-amber-950">{{ $food->carbohydrates_per_100g }}g</span>
                        </div>
                        <div class="p-2 rounded-xl bg-rose-50/70 border border-rose-100">
                            <span class="text-[10px] font-bold text-rose-800 uppercase block">Lemak</span>
                            <span class="text-sm font-black text-rose-950">{{ $food->fat_per_100g }}g</span>
                        </div>
                    </div>
                </div>

                {{-- Action Toolbar --}}
                <div class="pt-3 border-t border-stone-200/80 flex items-center gap-2">
                    {{-- Quick Log Button --}}
                    <button type="button" 
                        onclick="openQuickLogModal('{{ $food->id }}', '{{ addslashes($food->name) }}', '{{ $food->calories_per_100g }}', '{{ $food->protein_per_100g }}', '{{ $food->carbohydrates_per_100g }}', '{{ $food->fat_per_100g }}')"
                        class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-800 text-white font-bold text-xs hover:bg-emerald-900 transition shadow-xs">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Catat</span>
                    </button>

                    {{-- Detail Button --}}
                    <a href="{{ route('foods.show', $food->id) }}" class="p-2 rounded-xl bg-cream-200 text-charcoal hover:bg-emerald-100 hover:text-emerald-900 border border-stone-200/80 transition" title="Lihat Detail Nilai Gizi">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </a>

                    {{-- Edit Button --}}
                    <a href="{{ route('foods.edit', $food->id) }}" class="p-2 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200/60 transition" title="Edit Data Nutrisi">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </a>

                    {{-- Delete Button --}}
                    <button type="button" onclick="confirmDeleteFood('{{ $food->id }}', '{{ addslashes($food->name) }}')" class="p-2 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200/60 transition" title="Hapus dari Database">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($foods->hasPages())
    <div class="flex justify-center mt-4">
        {{ $foods->links() }}
    </div>
    @endif

    @else
    {{-- Empty State --}}
    <div class="py-20 text-center bg-white/90 backdrop-blur-md rounded-3xl border border-stone-200/90 shadow-xs">
        <div class="w-20 h-20 rounded-3xl bg-cream-300 flex items-center justify-center mx-auto mb-4 shadow-inner">
            <i data-lucide="search-x" class="w-10 h-10 text-charcoal-muted"></i>
        </div>
        <h4 class="text-xl font-black text-charcoal mb-1">Makanan Tidak Ditemukan</h4>
        <p class="text-sm text-charcoal-muted max-w-md mx-auto mb-6">
            Tidak ada makanan yang cocok dengan kata kunci pencarian atau filter kategori yang dipilih.
        </p>
        <div class="flex items-center justify-center gap-3">
            <a href="{{ route('foods.index') }}" class="px-5 py-2.5 rounded-2xl bg-cream-300 text-charcoal font-bold text-xs hover:bg-stone-300 transition">
                Reset Pencarian
            </a>
            <a href="{{ route('foods.create') }}" class="px-5 py-2.5 rounded-2xl bg-emerald-800 text-white font-bold text-xs hover:bg-emerald-900 transition shadow-sm">
                + Tambah Makanan Ini
            </a>
        </div>
    </div>
    @endif
</div>

{{-- ══════════════ QUICK LOG MODAL ══════════════ --}}
<div id="quick-log-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeQuickLogModal()"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 border border-stone-200">
        <div class="flex items-center justify-between pb-4 border-b border-stone-200/80 mb-4">
            <div>
                <h3 class="font-black text-lg text-charcoal">Catat ke Jurnal Makan</h3>
                <p id="modal-food-name" class="text-xs font-bold text-emerald-700"></p>
            </div>
            <button onclick="closeQuickLogModal()" class="p-1.5 rounded-xl text-charcoal-muted hover:bg-cream-200 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="quick-log-form" method="POST" action="">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-charcoal mb-1.5">Waktu Makan</label>
                    <select name="meal_type" class="w-full px-3.5 py-2.5 text-sm rounded-xl bg-cream-100 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-emerald-700/30 text-charcoal font-semibold">
                        <option value="sarapan">🍳 Sarapan Pagi</option>
                        <option value="makan_siang" selected>🍛 Makan Siang</option>
                        <option value="makan_malam">🍲 Makan Malam</option>
                        <option value="camilan">🥪 Camilan Sehat</option>
                    </select>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-bold uppercase tracking-wider text-charcoal">Berat Porsi (gram)</label>
                        <span id="calc-calories" class="text-xs font-black text-emerald-800">-- kkal</span>
                    </div>
                    <input type="number" id="quick-portion" name="portion_grams" value="100" min="5" max="2500" step="5"
                        class="w-full px-3.5 py-2.5 text-sm rounded-xl bg-cream-100 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-emerald-700/30 text-charcoal font-bold"
                        oninput="updateQuickCalculation()">
                </div>

                {{-- Live macro preview badges --}}
                <div class="grid grid-cols-3 gap-2 p-3 rounded-2xl bg-cream-100 border border-stone-200/80 text-center text-xs">
                    <div>
                        <span class="text-[10px] text-charcoal-muted font-bold block">Protein</span>
                        <span id="calc-protein" class="font-black text-blue-900">0g</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-charcoal-muted font-bold block">Karbo</span>
                        <span id="calc-carbs" class="font-black text-amber-900">0g</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-charcoal-muted font-bold block">Lemak</span>
                        <span id="calc-fat" class="font-black text-rose-900">0g</span>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeQuickLogModal()" class="flex-1 py-2.5 rounded-xl bg-cream-300 text-charcoal font-bold text-xs hover:bg-stone-300 transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-emerald-800 text-white font-extrabold text-xs hover:bg-emerald-900 transition shadow-sm">
                        Simpan ke Jurnal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════ DELETE CONFIRMATION MODAL ══════════════ --}}
<div id="delete-food-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteFoodModal()"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 border border-stone-200">
        <div class="w-14 h-14 rounded-2xl bg-rose-100 flex items-center justify-center mx-auto mb-4">
            <i data-lucide="alert-triangle" class="w-7 h-7 text-rose-600"></i>
        </div>
        <h3 class="text-center font-black text-lg text-charcoal mb-1">Konfirmasi Hapus Makanan</h3>
        <p class="text-center text-xs text-charcoal-muted mb-2">Anda yakin ingin menghapus makanan ini dari basis data?</p>
        <p id="delete-food-name-display" class="text-center font-black text-emerald-900 mb-4 bg-emerald-50 py-2 rounded-xl border border-emerald-100"></p>
        
        <form id="delete-food-form" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteFoodModal()" class="flex-1 py-2.5 rounded-xl bg-cream-300 text-charcoal font-bold text-xs hover:bg-stone-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-rose-600 text-white font-extrabold text-xs hover:bg-rose-700 transition shadow-sm">
                    Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentFoodMacros = { cal: 0, prot: 0, carb: 0, fat: 0 };

function openQuickLogModal(id, name, cal, prot, carb, fat) {
    document.getElementById('modal-food-name').textContent = name;
    document.getElementById('quick-log-form').action = '/foods/' + id + '/quick-log';
    currentFoodMacros = { cal: parseFloat(cal), prot: parseFloat(prot), carb: parseFloat(carb), fat: parseFloat(fat) };
    document.getElementById('quick-portion').value = 100;
    updateQuickCalculation();
    document.getElementById('quick-log-modal').classList.remove('hidden');
}

function closeQuickLogModal() {
    document.getElementById('quick-log-modal').classList.add('hidden');
}

function updateQuickCalculation() {
    const grams = parseFloat(document.getElementById('quick-portion').value) || 0;
    const factor = grams / 100;
    document.getElementById('calc-calories').textContent = (currentFoodMacros.cal * factor).toFixed(1) + ' kkal';
    document.getElementById('calc-protein').textContent = (currentFoodMacros.prot * factor).toFixed(1) + 'g';
    document.getElementById('calc-carbs').textContent = (currentFoodMacros.carb * factor).toFixed(1) + 'g';
    document.getElementById('calc-fat').textContent = (currentFoodMacros.fat * factor).toFixed(1) + 'g';
}

function confirmDeleteFood(id, name) {
    document.getElementById('delete-food-name-display').textContent = name;
    document.getElementById('delete-food-form').action = '/foods/' + id;
    document.getElementById('delete-food-modal').classList.remove('hidden');
}

function closeDeleteFoodModal() {
    document.getElementById('delete-food-modal').classList.add('hidden');
}
</script>
@endsection
