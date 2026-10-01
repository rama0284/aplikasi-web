@extends('layouts.app')

@section('title', 'Profil & Pengaturan Target — NutriScan AI')
@section('page_title', 'Profil & Target Nutrisi')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    <!-- Profile Header Card -->
    <div class="bg-cream-100 p-6 sm:p-7 rounded-3xl border border-stone-200/90 shadow-sm flex items-center gap-5">
        <div class="w-16 h-16 rounded-2xl bg-emerald-800 text-white flex items-center justify-center font-extrabold text-xl uppercase shadow-md shadow-emerald-950/10">
            {{ substr($user->name, 0, 2) }}
        </div>
        <div>
            <h2 class="text-xl font-bold text-emerald-950">{{ $user->name }}</h2>
            <p class="text-xs text-charcoal-muted">{{ $user->email }} &bull; Bergabung sejak {{ $user->created_at->format('M Y') }}</p>
            <span class="inline-block mt-1.5 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                Akun Mahasiswa Terverifikasi
            </span>
        </div>
    </div>

    <!-- Target Nutrisi & Preferensi Form -->
    <div class="bg-cream-100 p-6 sm:p-8 rounded-3xl border border-stone-200/90 shadow-sm space-y-6">
        <div>
            <h3 class="text-base font-bold text-emerald-950 flex items-center gap-2">
                <i data-lucide="target" class="w-5 h-5 text-emerald-800"></i>
                <span>Pengaturan Target Gizi Harian & Profil</span>
            </h3>
            <p class="text-xs text-charcoal-muted mt-1">
                Target ini menjadi acuan persentase dan grafik kesehatan di dashboard NutriScan AI Anda.
            </p>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Data Diri -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-6 border-b border-stone-200/80">
                <div>
                    <label class="block text-xs font-semibold text-charcoal mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 bg-cream-50 text-sm focus:ring-2 focus:ring-emerald-700">
                    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-charcoal mb-1">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 bg-cream-50 text-sm focus:ring-2 focus:ring-emerald-700">
                    @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Target Nutrisi Makro -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-charcoal">Sasaran Makronutrien Harian</h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Kalori -->
                    <div class="p-4 rounded-2xl bg-cream-200 border border-stone-200">
                        <label class="block text-[11px] font-bold text-emerald-950 uppercase mb-1 flex items-center justify-between">
                            <span>Target Energi</span>
                            <i data-lucide="flame" class="w-3.5 h-3.5 text-emerald-700"></i>
                        </label>
                        <div class="flex items-center gap-1.5">
                            <input type="number" name="calorie_goal" value="{{ old('calorie_goal', $goal->calorie_goal ?: 2000) }}" required min="800" max="8000"
                                   class="w-full font-bold text-base px-3 py-1.5 rounded-xl border border-stone-300 bg-cream-50 text-emerald-950 focus:ring-2 focus:ring-emerald-700">
                            <span class="text-xs text-charcoal-muted">kkal</span>
                        </div>
                    </div>

                    <!-- Protein -->
                    <div class="p-4 rounded-2xl bg-cream-200 border border-stone-200">
                        <label class="block text-[11px] font-bold text-blue-950 uppercase mb-1 flex items-center justify-between">
                            <span>Target Protein</span>
                            <i data-lucide="egg" class="w-3.5 h-3.5 text-blue-700"></i>
                        </label>
                        <div class="flex items-center gap-1.5">
                            <input type="number" name="protein_goal" value="{{ old('protein_goal', $goal->protein_goal ?: 60) }}" required min="10" max="400"
                                   class="w-full font-bold text-base px-3 py-1.5 rounded-xl border border-stone-300 bg-cream-50 text-blue-950 focus:ring-2 focus:ring-blue-700">
                            <span class="text-xs text-charcoal-muted">gram</span>
                        </div>
                    </div>

                    <!-- Karbohidrat -->
                    <div class="p-4 rounded-2xl bg-cream-200 border border-stone-200">
                        <label class="block text-[11px] font-bold text-amber-950 uppercase mb-1 flex items-center justify-between">
                            <span>Target Karbo</span>
                            <i data-lucide="wheat" class="w-3.5 h-3.5 text-amber-700"></i>
                        </label>
                        <div class="flex items-center gap-1.5">
                            <input type="number" name="carbohydrates_goal" value="{{ old('carbohydrates_goal', $goal->carbohydrates_goal ?: 250) }}" required min="10" max="800"
                                   class="w-full font-bold text-base px-3 py-1.5 rounded-xl border border-stone-300 bg-cream-50 text-amber-950 focus:ring-2 focus:ring-amber-700">
                            <span class="text-xs text-charcoal-muted">gram</span>
                        </div>
                    </div>

                    <!-- Lemak -->
                    <div class="p-4 rounded-2xl bg-cream-200 border border-stone-200">
                        <label class="block text-[11px] font-bold text-rose-950 uppercase mb-1 flex items-center justify-between">
                            <span>Batas Lemak</span>
                            <i data-lucide="droplet" class="w-3.5 h-3.5 text-rose-700"></i>
                        </label>
                        <div class="flex items-center gap-1.5">
                            <input type="number" name="fat_goal" value="{{ old('fat_goal', $goal->fat_goal ?: 65) }}" required min="5" max="300"
                                   class="w-full font-bold text-base px-3 py-1.5 rounded-xl border border-stone-300 bg-cream-50 text-rose-950 focus:ring-2 focus:ring-rose-700">
                            <span class="text-xs text-charcoal-muted">gram</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preferensi Makanan -->
            <div class="space-y-1.5 pt-2">
                <label class="block text-xs font-semibold text-charcoal">Preferensi Pola Makan / Alergi Makanan</label>
                <textarea name="dietary_preferences" rows="3" placeholder="Contoh: Kurangi gorengan minyak jenuh, tinggi protein nabati (tempe/tahu), alergi seafood."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 bg-cream-50 text-xs sm:text-sm focus:ring-2 focus:ring-emerald-700">{{ old('dietary_preferences', $goal->dietary_preferences) }}</textarea>
                <p class="text-[11px] text-charcoal-light">Catatan ini akan dibaca oleh Asisten AI untuk menyesuaikan rekomendasi menu Anda.</p>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-800 text-white font-bold text-xs sm:text-sm hover:bg-emerald-900 shadow-md transition">
                    Simpan Perubahan Profil & Target
                </button>
            </div>
        </form>
    </div>

    <!-- Password Change Form -->
    <div class="bg-cream-100 p-6 sm:p-8 rounded-3xl border border-stone-200/90 shadow-sm space-y-4">
        <div>
            <h3 class="text-base font-bold text-emerald-950 flex items-center gap-2">
                <i data-lucide="lock" class="w-5 h-5 text-emerald-800"></i>
                <span>Ganti Password Akun</span>
            </h3>
            <p class="text-xs text-charcoal-muted mt-1">Pastikan password baru Anda kuat dan minimal terdiri dari 8 karakter.</p>
        </div>

        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-charcoal mb-1">Password Saat Ini</label>
                    <input type="password" name="current_password" required
                           class="w-full px-3.5 py-2 rounded-xl border border-stone-300 bg-cream-50 text-xs focus:ring-2 focus:ring-emerald-700">
                    @error('current_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-charcoal mb-1">Password Baru</label>
                    <input type="password" name="password" required
                           class="w-full px-3.5 py-2 rounded-xl border border-stone-300 bg-cream-50 text-xs focus:ring-2 focus:ring-emerald-700">
                    @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-charcoal mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-3.5 py-2 rounded-xl border border-stone-300 bg-cream-50 text-xs focus:ring-2 focus:ring-emerald-700">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-5 py-2 rounded-xl bg-stone-800 text-white font-semibold text-xs hover:bg-stone-900 transition">
                    Perbarui Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
