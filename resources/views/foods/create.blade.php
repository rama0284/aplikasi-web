@extends('layouts.app')

@section('title', 'Tambah Makanan Baru — Database Nutrisi')
@section('page_title', 'Tambah Makanan Baru')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    {{-- Breadcrumb & Back --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('foods.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-charcoal-muted hover:text-emerald-800 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Database Makanan</span>
        </a>
        <span class="text-xs text-charcoal-light font-semibold">Tersimpan di MySQL `foods` table</span>
    </div>

    {{-- 2-Column Responsive Form & Live Preview --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- Left Column: Form (7 cols) --}}
        <div class="lg:col-span-7 bg-white/95 backdrop-blur-md rounded-3xl p-6 sm:p-8 border border-stone-200/90 shadow-sm space-y-6">
            
            <div class="border-b border-stone-200/70 pb-4">
                <h2 class="text-xl font-black text-charcoal">Formulir Data Gizi Makanan</h2>
                <p class="text-xs text-charcoal-muted mt-0.5">
                    Masukkan nilai nutrisi berbasis takaran <strong>per 100 gram</strong> untuk kalkulasi porsi yang presisi.
                </p>
            </div>

            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs space-y-1">
                    <p class="font-bold flex items-center gap-1.5">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
                        <span>Mohon perbaiki kesalahan berikut:</span>
                    </p>
                    <ul class="list-disc pl-5 space-y-0.5 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('foods.store') }}" class="space-y-5">
                @csrf

                {{-- Row 1: Nama & Emoji --}}
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-charcoal mb-1.5">Nama Makanan *</label>
                        <input type="text" id="input-name" name="name" value="{{ old('name') }}" required
                            placeholder="Contoh: Dada Ayam Panggang Rosemary"
                            class="w-full px-4 py-2.5 text-sm rounded-xl bg-cream-100 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-emerald-700/30 text-charcoal font-semibold"
                            oninput="updateLivePreview()">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-charcoal mb-1.5">Emoji Icon</label>
                        <input type="text" id="input-emoji" name="icon_emoji" value="{{ old('icon_emoji', '🥗') }}" maxlength="10"
                            placeholder="🥗"
                            class="w-full text-center px-4 py-2.5 text-lg rounded-xl bg-cream-100 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-emerald-700/30 text-charcoal"
                            oninput="updateLivePreview()">
                    </div>
                </div>

                {{-- Row 2: Kategori & Grade Kesehatan --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-charcoal mb-1.5">Kategori Makanan *</label>
                        <input type="text" id="input-category" name="category" list="category-list" value="{{ old('category') }}" required
                            placeholder="Pilih atau ketik kategori..."
                            class="w-full px-4 py-2.5 text-sm rounded-xl bg-cream-100 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-emerald-700/30 text-charcoal font-semibold"
                            oninput="updateLivePreview()">
                        <datalist id="category-list">
                            @foreach($existingCategories as $cat)
                                <option value="{{ $cat }}"></option>
                            @endforeach
                            <option value="Makanan Pokok"></option>
                            <option value="Lauk Pauk"></option>
                            <option value="Sayuran"></option>
                            <option value="Buah-buahan"></option>
                            <option value="Camilan Sehat"></option>
                            <option value="Minuman Sehat"></option>
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-charcoal mb-1.5">Grade Nutrisi *</label>
                        <select id="input-grade" name="health_grade" required
                            class="w-full px-4 py-2.5 text-sm rounded-xl bg-cream-100 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-emerald-700/30 text-charcoal font-bold"
                            onchange="updateLivePreview()">
                            <option value="A" {{ old('health_grade', 'A') === 'A' ? 'selected' : '' }}>🟢 Grade A — Sangat Bernutrisi</option>
                            <option value="B" {{ old('health_grade') === 'B' ? 'selected' : '' }}>🔵 Grade B — Baik & Seimbang</option>
                            <option value="C" {{ old('health_grade') === 'C' ? 'selected' : '' }}>🟡 Grade C — Konsumsi Terukur</option>
                            <option value="D" {{ old('health_grade') === 'D' ? 'selected' : '' }}>🔴 Grade D — Batasi Konsumsi</option>
                        </select>
                    </div>
                </div>

                {{-- Row 3: Porsi Standar --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-charcoal mb-1.5">Porsi Standar Penyajian *</label>
                    <input type="text" id="input-serving" name="serving_size" value="{{ old('serving_size', '1 porsi (100g)') }}" required
                        placeholder="Contoh: 1 porsi (100g) / 1 mangkuk sedang (150g)"
                        class="w-full px-4 py-2.5 text-sm rounded-xl bg-cream-100 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-emerald-700/30 text-charcoal font-medium"
                        oninput="updateLivePreview()">
                </div>

                {{-- SECTION: KANDUNGAN GIZI UTAMA PER 100 GRAM --}}
                <div class="p-4 rounded-2xl bg-cream-200/60 border border-stone-200/80 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black uppercase tracking-wider text-emerald-900">Makronutrien Inti (Per 100g)</span>
                        <span class="text-[10px] font-bold text-charcoal-light">Standar TKPI Kemenkes</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-charcoal mb-1">Kalori (kkal) *</label>
                            <input type="number" step="0.1" id="input-calories" name="calories_per_100g" value="{{ old('calories_per_100g', 150) }}" required
                                class="w-full px-3 py-2 text-sm rounded-xl bg-white border border-stone-200 focus:ring-2 focus:ring-emerald-700/30 font-bold"
                                oninput="updateLivePreview()">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-blue-900 mb-1">Protein (g) *</label>
                            <input type="number" step="0.1" id="input-protein" name="protein_per_100g" value="{{ old('protein_per_100g', 10) }}" required
                                class="w-full px-3 py-2 text-sm rounded-xl bg-white border border-stone-200 focus:ring-2 focus:ring-blue-700/30 font-bold"
                                oninput="updateLivePreview()">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-amber-900 mb-1">Karbohidrat (g) *</label>
                            <input type="number" step="0.1" id="input-carbs" name="carbohydrates_per_100g" value="{{ old('carbohydrates_per_100g', 20) }}" required
                                class="w-full px-3 py-2 text-sm rounded-xl bg-white border border-stone-200 focus:ring-2 focus:ring-amber-700/30 font-bold"
                                oninput="updateLivePreview()">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-rose-900 mb-1">Lemak (g) *</label>
                            <input type="number" step="0.1" id="input-fat" name="fat_per_100g" value="{{ old('fat_per_100g', 5) }}" required
                                class="w-full px-3 py-2 text-sm rounded-xl bg-white border border-stone-200 focus:ring-2 focus:ring-rose-700/30 font-bold"
                                oninput="updateLivePreview()">
                        </div>
                    </div>

                    {{-- Mikronutrien / Komponen Tambahan --}}
                    <div class="grid grid-cols-3 gap-3 pt-2 border-t border-stone-200/60">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-charcoal mb-1">Serat (g)</label>
                            <input type="number" step="0.1" id="input-fiber" name="fiber_per_100g" value="{{ old('fiber_per_100g', 1.5) }}"
                                class="w-full px-3 py-2 text-sm rounded-xl bg-white border border-stone-200 font-semibold"
                                oninput="updateLivePreview()">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-charcoal mb-1">Gula (g)</label>
                            <input type="number" step="0.1" id="input-sugar" name="sugar_g" value="{{ old('sugar_g', 0.5) }}"
                                class="w-full px-3 py-2 text-sm rounded-xl bg-white border border-stone-200 font-semibold"
                                oninput="updateLivePreview()">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-charcoal mb-1">Natrium (mg)</label>
                            <input type="number" step="1" id="input-sodium" name="sodium_mg" value="{{ old('sodium_mg', 120) }}"
                                class="w-full px-3 py-2 text-sm rounded-xl bg-white border border-stone-200 font-semibold"
                                oninput="updateLivePreview()">
                        </div>
                    </div>
                </div>

                {{-- Deskripsi & Sumber --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-charcoal mb-1.5">Deskripsi / Catatan Manfaat Gizi</label>
                    <textarea id="input-desc" name="description" rows="2"
                        placeholder="Tuliskan keterangan bahan, metode memasak sehat, atau kandungan mikronutrien..."
                        class="w-full px-4 py-2.5 text-sm rounded-xl bg-cream-100 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-emerald-700/30 text-charcoal"
                        oninput="updateLivePreview()">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-charcoal mb-1.5">Sumber Referensi Data</label>
                    <input type="text" name="nutrition_source" value="{{ old('nutrition_source', 'Tabel Komposisi Pangan Indonesia (TKPI) Kemenkes RI') }}"
                        class="w-full px-4 py-2 text-xs rounded-xl bg-cream-100 border border-stone-200 text-charcoal-muted">
                </div>

                <div class="flex gap-3 pt-3">
                    <a href="{{ route('foods.index') }}" class="px-5 py-3 rounded-2xl bg-cream-300 text-charcoal font-bold text-xs hover:bg-stone-300 transition text-center">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 py-3 rounded-2xl bg-emerald-800 text-white font-black text-sm hover:bg-emerald-900 transition shadow-md shadow-emerald-900/20">
                        Simpan ke Database Makanan
                    </button>
                </div>
            </form>
        </div>

        {{-- Right Column: Live Nutritional Preview Card (5 cols) --}}
        <div class="lg:col-span-5 sticky top-28 space-y-4">
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-stone-200/90 shadow-md">
                
                <div class="flex items-center justify-between pb-3 border-b-4 border-black mb-3">
                    <div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-emerald-700">Preview Label Resmi</span>
                        <h3 class="text-xl font-black text-charcoal tracking-tight">Informasi Nilai Gizi</h3>
                    </div>
                    <span id="preview-emoji" class="text-3xl">🥗</span>
                </div>

                <div class="space-y-1 pb-2 border-b-8 border-black">
                    <p id="preview-name" class="font-black text-base text-charcoal leading-tight">Nama Makanan</p>
                    <p class="text-xs text-charcoal-muted flex justify-between">
                        <span>Porsi Penyajian:</span>
                        <strong id="preview-serving" class="text-charcoal font-bold">1 porsi (100g)</strong>
                    </p>
                    <p class="text-xs text-charcoal-muted flex justify-between">
                        <span>Kategori:</span>
                        <strong id="preview-category" class="text-emerald-800 font-bold">Makanan</strong>
                    </p>
                </div>

                {{-- Calories Callout --}}
                <div class="py-3 border-b-4 border-black flex items-baseline justify-between">
                    <div>
                        <span class="text-[11px] font-black uppercase text-charcoal-muted block">Jumlah Per 100g</span>
                        <span class="text-2xl font-black text-charcoal">Energi Total</span>
                    </div>
                    <div class="text-right">
                        <span id="preview-calories" class="text-3xl font-black text-emerald-900">150</span>
                        <span class="text-xs font-bold text-charcoal-muted block -mt-1">kkal</span>
                    </div>
                </div>

                {{-- Macronutrient Lines --}}
                <div class="divide-y divide-stone-200 text-xs py-2">
                    <div class="py-1.5 flex justify-between items-center">
                        <span class="font-bold text-blue-950 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            Protein
                        </span>
                        <span id="preview-protein" class="font-black text-blue-950">10.0 g</span>
                    </div>
                    <div class="py-1.5 flex justify-between items-center">
                        <span class="font-bold text-amber-950 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Karbohidrat Total
                        </span>
                        <span id="preview-carbs" class="font-black text-amber-950">20.0 g</span>
                    </div>
                    <div class="py-1.5 pl-4 flex justify-between items-center text-charcoal-muted">
                        <span>Serat Pangan</span>
                        <span id="preview-fiber" class="font-semibold text-charcoal">1.5 g</span>
                    </div>
                    <div class="py-1.5 pl-4 flex justify-between items-center text-charcoal-muted">
                        <span>Gula</span>
                        <span id="preview-sugar" class="font-semibold text-charcoal">0.5 g</span>
                    </div>
                    <div class="py-1.5 flex justify-between items-center">
                        <span class="font-bold text-rose-950 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Lemak Total
                        </span>
                        <span id="preview-fat" class="font-black text-rose-950">5.0 g</span>
                    </div>
                    <div class="py-1.5 flex justify-between items-center">
                        <span class="font-bold text-charcoal">Natrium (Garam)</span>
                        <span id="preview-sodium" class="font-bold text-charcoal">120 mg</span>
                    </div>
                </div>

                {{-- Grade & Tip --}}
                <div class="mt-4 pt-3 border-t-2 border-dashed border-stone-300 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-charcoal">Nutri-Grade:</span>
                        <span id="preview-grade" class="px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                            Grade A
                        </span>
                    </div>
                    <span class="text-[10px] text-charcoal-light font-medium">*Berdasarkan takaran 100g</span>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-emerald-950 text-emerald-100 text-xs flex items-center gap-3">
                <i data-lucide="sparkles" class="w-5 h-5 text-emerald-300 flex-shrink-0"></i>
                <p class="leading-relaxed">
                    Data yang disimpan akan langsung terhubung ke fitur <strong>Food Diary</strong> dan <strong>AI Food Scanner</strong>.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function updateLivePreview() {
    const name = document.getElementById('input-name').value || 'Nama Makanan';
    const emoji = document.getElementById('input-emoji').value || '🥗';
    const category = document.getElementById('input-category').value || 'Makanan';
    const serving = document.getElementById('input-serving').value || '1 porsi (100g)';
    const grade = document.getElementById('input-grade').value || 'A';
    
    const cal = parseFloat(document.getElementById('input-calories').value) || 0;
    const prot = parseFloat(document.getElementById('input-protein').value) || 0;
    const carbs = parseFloat(document.getElementById('input-carbs').value) || 0;
    const fat = parseFloat(document.getElementById('input-fat').value) || 0;
    const fiber = parseFloat(document.getElementById('input-fiber').value) || 0;
    const sugar = parseFloat(document.getElementById('input-sugar').value) || 0;
    const sodium = parseFloat(document.getElementById('input-sodium').value) || 0;

    document.getElementById('preview-name').textContent = name;
    document.getElementById('preview-emoji').textContent = emoji;
    document.getElementById('preview-category').textContent = category;
    document.getElementById('preview-serving').textContent = serving;
    document.getElementById('preview-calories').textContent = cal.toFixed(0);
    document.getElementById('preview-protein').textContent = prot.toFixed(1) + ' g';
    document.getElementById('preview-carbs').textContent = carbs.toFixed(1) + ' g';
    document.getElementById('preview-fat').textContent = fat.toFixed(1) + ' g';
    document.getElementById('preview-fiber').textContent = fiber.toFixed(1) + ' g';
    document.getElementById('preview-sugar').textContent = sugar.toFixed(1) + ' g';
    document.getElementById('preview-sodium').textContent = sodium.toFixed(0) + ' mg';
    
    const gradePill = document.getElementById('preview-grade');
    gradePill.textContent = 'Grade ' + grade;
}
</script>
@endsection
