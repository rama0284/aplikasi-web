@extends('layouts.app')

@section('title', 'Edit Catatan Makanan — Food Diary')
@section('page_title', 'Edit Catatan Makanan')

@section('content')
<div class="space-y-6 max-w-2xl mx-auto">

    {{-- Breadcrumb & Back --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('diary.index', ['date' => $log->consumed_at->format('Y-m-d')]) }}" class="inline-flex items-center gap-2 text-xs font-bold text-charcoal-muted hover:text-emerald-800 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Food Diary</span>
        </a>
        <span class="text-xs text-charcoal-light font-medium">Log #{{ $log->id }}</span>
    </div>

    {{-- Edit Card --}}
    <div class="bg-white/95 rounded-3xl p-6 sm:p-8 border border-stone-200/90 shadow-sm space-y-6">
        
        <div class="border-b border-stone-200/70 pb-4 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-800">Edit Entri Jurnal</span>
                <h2 class="text-xl font-black text-charcoal mt-0.5">{{ $log->display_name }}</h2>
            </div>
            <img src="{{ $log->image_url }}" alt="{{ $log->display_name }}"
                 class="w-12 h-12 rounded-2xl object-cover border border-stone-200 shadow-sm">
        </div>

        <form method="POST" action="{{ route('diary.update', $log->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Waktu Makan --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-charcoal mb-1.5">Waktu Makan *</label>
                <select name="meal_type" required class="w-full px-4 py-2.5 rounded-xl border border-stone-200 bg-cream-100 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-700/30">
                    <option value="sarapan" {{ $log->meal_type === 'sarapan' ? 'selected' : '' }}>🍳 Sarapan Pagi</option>
                    <option value="makan_siang" {{ $log->meal_type === 'makan_siang' ? 'selected' : '' }}>🍛 Makan Siang</option>
                    <option value="makan_malam" {{ $log->meal_type === 'makan_malam' ? 'selected' : '' }}>🍲 Makan Malam</option>
                    <option value="camilan" {{ $log->meal_type === 'camilan' ? 'selected' : '' }}>🥪 Camilan Sehat</option>
                </select>
            </div>

            {{-- Waktu Konsumsi --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-charcoal mb-1.5">Tanggal & Jam Konsumsi *</label>
                <input type="datetime-local" name="consumed_at" value="{{ $log->consumed_at->format('Y-m-d\TH:i') }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-stone-200 bg-cream-100 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-700/30">
            </div>

            {{-- Ukuran Porsi (Gram) --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-xs font-bold uppercase tracking-wider text-charcoal">Ukuran Porsi (Gram) *</label>
                    <span id="calorie-preview" class="text-xs font-black text-emerald-800">{{ $log->calories }} kkal</span>
                </div>
                <input type="number" id="edit-portion-input" name="portion_grams" min="5" max="2500" value="{{ $log->portion_grams }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-stone-200 bg-cream-100 text-sm font-black focus:outline-none focus:ring-2 focus:ring-emerald-700/30"
                       oninput="recalculateCalories(this.value)">
            </div>

            {{-- Nutrients Summary Pill --}}
            <div class="p-4 rounded-2xl bg-cream-100 border border-stone-200/70">
                <span class="text-[10px] font-bold uppercase tracking-wider text-charcoal-muted block mb-2">Estimasi Nilai Gizi Saat Ini</span>
                <div class="grid grid-cols-4 gap-2 text-center text-xs">
                    <div>
                        <span class="text-[10px] text-charcoal-muted block">Kalori</span>
                        <strong id="disp-cal" class="text-charcoal font-black text-sm">{{ $log->calories }}</strong>
                    </div>
                    <div>
                        <span class="text-[10px] text-blue-900 block">Protein</span>
                        <strong id="disp-prot" class="text-blue-950 font-black text-sm">{{ $log->protein }}g</strong>
                    </div>
                    <div>
                        <span class="text-[10px] text-amber-900 block">Karbo</span>
                        <strong id="disp-carb" class="text-amber-950 font-black text-sm">{{ $log->carbohydrates }}g</strong>
                    </div>
                    <div>
                        <span class="text-[10px] text-rose-900 block">Lemak</span>
                        <strong id="disp-fat" class="text-rose-950 font-black text-sm">{{ $log->fat }}g</strong>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-3">
                <a href="{{ route('diary.index', ['date' => $log->consumed_at->format('Y-m-d')]) }}" class="px-5 py-3 rounded-2xl bg-cream-300 text-charcoal font-bold text-xs hover:bg-stone-300 transition text-center">
                    Batal
                </a>
                <button type="submit" class="flex-1 py-3 rounded-2xl bg-emerald-800 text-white font-black text-sm hover:bg-emerald-900 transition shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const originalPortion = {{ $log->portion_grams ?: 100 }};
const originalCal = {{ $log->calories }};
const originalProt = {{ $log->protein }};
const originalCarb = {{ $log->carbohydrates }};
const originalFat = {{ $log->fat }};

function recalculateCalories(grams) {
    const g = parseFloat(grams) || originalPortion;
    const ratio = g / originalPortion;
    const newCal = (originalCal * ratio).toFixed(1);
    const newProt = (originalProt * ratio).toFixed(1);
    const newCarb = (originalCarb * ratio).toFixed(1);
    const newFat = (originalFat * ratio).toFixed(1);

    document.getElementById('calorie-preview').textContent = newCal + ' kkal';
    document.getElementById('disp-cal').textContent = newCal;
    document.getElementById('disp-prot').textContent = newProt + 'g';
    document.getElementById('disp-carb').textContent = newCarb + 'g';
    document.getElementById('disp-fat').textContent = newFat + 'g';
}
</script>
@endsection
