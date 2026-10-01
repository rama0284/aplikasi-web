@extends('layouts.guest')

@section('title', 'Masuk ke NutriScan AI')

@section('content')
<div class="py-12 sm:py-16 flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md bg-cream-100 p-8 rounded-3xl border border-stone-200/90 shadow-xl shadow-stone-900/5">
        <!-- Card Header -->
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-emerald-800 text-white flex items-center justify-center mx-auto mb-3 shadow-md shadow-emerald-900/20">
                <i data-lucide="log-in" class="w-6 h-6 text-emerald-200"></i>
            </div>
            <h2 class="text-2xl font-bold text-emerald-950">Selamat Datang</h2>
            <p class="text-xs text-charcoal-muted mt-1">Masuk untuk mengakses dashboard & food diary Anda</p>
        </div>

        <!-- Demo Account Banner -->
        <div class="mb-6 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-xs text-emerald-900">
            <div class="flex items-center gap-1.5 font-bold mb-1">
                <i data-lucide="info" class="w-4 h-4 text-emerald-700"></i>
                <span>Akun Demo Praktikum:</span>
            </div>
            <p class="text-[11px] text-emerald-800">Email: <code class="font-mono bg-emerald-100 px-1 py-0.5 rounded">demo@nutriscan.ai</code> &bull; Password: <code class="font-mono bg-emerald-100 px-1 py-0.5 rounded">password123</code></p>
        </div>

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs font-semibold text-charcoal mb-1">Alamat Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-charcoal-light">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </span>
                    <input type="email" id="email" name="email" value="{{ old('email', 'demo@nutriscan.ai') }}" required autofocus
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
                    <input type="password" id="password" name="password" required
                        class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border @error('password') border-red-500 @else border-stone-300 @enderror bg-cream-50 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:border-transparent transition">
                </div>
                @error('password')
                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-stone-300 text-emerald-800 focus:ring-emerald-700">
                    <span class="text-xs text-charcoal-muted">Ingat saya</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-3 rounded-xl bg-emerald-800 text-white font-semibold text-sm hover:bg-emerald-900 shadow-md shadow-emerald-900/20 hover:scale-[1.01] transition">
                Masuk ke Akun
            </button>
        </form>

        <p class="text-center text-xs text-charcoal-muted mt-6">
            Belum memiliki akun?
            <a href="{{ route('register') }}" class="font-bold text-emerald-800 hover:underline ml-1">Daftar sekarang</a>
        </p>
    </div>
</div>
@endsection
