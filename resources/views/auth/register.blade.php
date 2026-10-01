@extends('layouts.guest')

@section('title', 'Daftar Akun Baru — NutriScan AI')

@section('content')
<div class="py-12 sm:py-16 flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md bg-cream-100 p-8 rounded-3xl border border-stone-200/90 shadow-xl shadow-stone-900/5">
        <!-- Card Header -->
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-emerald-800 text-white flex items-center justify-center mx-auto mb-3 shadow-md shadow-emerald-900/20">
                <i data-lucide="user-plus" class="w-6 h-6 text-emerald-200"></i>
            </div>
            <h2 class="text-2xl font-bold text-emerald-950">Mulai Perjalanan Sehat</h2>
            <p class="text-xs text-charcoal-muted mt-1">Buat akun untuk memantau asupan gizi piring Anda</p>
        </div>

        <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-semibold text-charcoal mb-1">Nama Lengkap</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-charcoal-light">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </span>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="Contoh: Rama Putra"
                        class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border @error('name') border-red-500 @else border-stone-300 @enderror bg-cream-50 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:border-transparent transition">
                </div>
                @error('name')
                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs font-semibold text-charcoal mb-1">Alamat Email Kampus / Pribadi</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-charcoal-light">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </span>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com"
                        class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border @error('email') border-red-500 @else border-stone-300 @enderror bg-cream-50 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:border-transparent transition">
                </div>
                @error('email')
                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-xs font-semibold text-charcoal mb-1">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-charcoal-light">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </span>
                    <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter"
                        class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border @error('password') border-red-500 @else border-stone-300 @enderror bg-cream-50 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:border-transparent transition">
                </div>
                @error('password')
                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-charcoal mb-1">Konfirmasi Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-charcoal-light">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                    </span>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password"
                        class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-stone-300 bg-cream-50 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:border-transparent transition">
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-3 rounded-xl bg-emerald-800 text-white font-semibold text-sm hover:bg-emerald-900 shadow-md shadow-emerald-900/20 hover:scale-[1.01] transition pt-2">
                Daftar Akun Sekarang
            </button>
        </form>

        <p class="text-center text-xs text-charcoal-muted mt-6">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="font-bold text-emerald-800 hover:underline ml-1">Masuk di sini</a>
        </p>
    </div>
</div>
@endsection
