@extends('layouts.guest')

@section('title', 'NutriScan AI — Understand Your Food, Transform Your Habits')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden py-16 sm:py-24">
    <!-- Ambient Background Glow -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-300/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 -right-32 w-96 h-96 bg-emerald-200/30 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Left Headline & CTA -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-100/80 border border-emerald-200 text-emerald-900 text-xs font-semibold">
                    <i data-lucide="sparkles" class="w-4 h-4 text-emerald-700"></i>
                    <span>Didukung AI Vision 9Router & Data Nutrisi TKPI</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-emerald-950 leading-[1.15]">
                    Understand Your Food, <span class="text-emerald-800">Transform Your Habits.</span>
                </h1>

                <p class="text-base sm:text-lg text-charcoal-muted max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                    Identifikasi makanan secara otomatis melalui foto, perkirakan makronutrien berdasarkan basis data resmi, dan bangun kebiasaan makan sehat yang terukur setiap hari.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                    <a href="{{ route('scanner.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-xl bg-emerald-800 text-white font-semibold shadow-lg shadow-emerald-900/20 hover:bg-emerald-900 hover:scale-[1.02] transition">
                        <i data-lucide="camera" class="w-5 h-5 text-emerald-200"></i>
                        <span>Scan Your Food</span>
                    </a>
                    <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-cream-100 text-charcoal font-semibold border border-stone-300 hover:bg-cream-300 transition">
                        <span>Get Started Free</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 text-charcoal-muted"></i>
                    </a>
                </div>

                <!-- Trust indicators -->
                <div class="pt-6 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-xs text-charcoal-muted border-t border-stone-200/60">
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-700"></i>
                        <span>Akurasi Berbasis Standar TKPI</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-700"></i>
                        <span>Vision AI Recognition</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-700"></i>
                        <span>Food Diary Terpadu</span>
                    </div>
                </div>
            </div>

            <!-- Right Visual Interactive Card -->
            <div class="lg:col-span-5">
                <div class="relative bg-cream-100 p-6 rounded-3xl border border-stone-200/90 shadow-xl shadow-stone-900/5">
                    <!-- Scanner Mock Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-stone-200">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-semibold text-charcoal">Hasil Identifikasi AI</span>
                        </div>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Confidence: 96%</span>
                    </div>

                    <!-- Food Sample Card -->
                    <div class="mt-4 space-y-4">
                        <div class="relative rounded-2xl overflow-hidden bg-stone-100 border border-stone-200 h-44 flex items-center justify-center group">
                            <!-- Visual Graphic for sample food -->
                            <div class="w-full h-full bg-gradient-to-tr from-emerald-900/10 to-emerald-600/10 flex flex-col items-center justify-center p-4 text-center">
                                <div class="w-16 h-16 rounded-2xl bg-emerald-800 text-white flex items-center justify-center mb-2 shadow-md">
                                    <i data-lucide="utensils" class="w-8 h-8 text-emerald-200"></i>
                                </div>
                                <span class="text-xs font-bold text-emerald-950">Nasi Ayam Bakar & Lalapan</span>
                                <span class="text-[10px] text-charcoal-muted">Estimasi Porsi: 300 gram</span>
                            </div>
                            <div class="absolute bottom-2 left-2 px-2 py-1 rounded bg-black/60 backdrop-blur-sm text-[10px] text-white font-mono">
                                AI Vision Tag: detected
                            </div>
                        </div>

                        <!-- Macro Grid -->
                        <div class="grid grid-cols-4 gap-2 text-center">
                            <div class="p-2.5 rounded-xl bg-cream-200 border border-stone-200/70">
                                <span class="block text-[10px] text-charcoal-muted font-medium">Kalori</span>
                                <span class="text-sm font-bold text-emerald-900">485 <span class="text-[9px] font-normal text-charcoal-muted">kkal</span></span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-cream-200 border border-stone-200/70">
                                <span class="block text-[10px] text-charcoal-muted font-medium">Protein</span>
                                <span class="text-sm font-bold text-blue-700">32.4 <span class="text-[9px] font-normal text-charcoal-muted">g</span></span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-cream-200 border border-stone-200/70">
                                <span class="block text-[10px] text-charcoal-muted font-medium">Karbo</span>
                                <span class="text-sm font-bold text-amber-700">58.0 <span class="text-[9px] font-normal text-charcoal-muted">g</span></span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-cream-200 border border-stone-200/70">
                                <span class="block text-[10px] text-charcoal-muted font-medium">Lemak</span>
                                <span class="text-sm font-bold text-rose-700">12.5 <span class="text-[9px] font-normal text-charcoal-muted">g</span></span>
                            </div>
                        </div>

                        <!-- Simulated CTA -->
                        <a href="{{ route('scanner.index') }}" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-emerald-800 text-white font-semibold text-xs hover:bg-emerald-900 transition">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            <span>Simpan ke Food Diary Saya</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section id="cara-kerja" class="py-16 bg-cream-100 border-y border-stone-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-xs font-bold uppercase tracking-wider text-emerald-800 mb-2">Alur Cerdas & Praktis</h2>
            <p class="text-3xl font-extrabold text-emerald-950">Cara Kerja NutriScan AI</p>
            <p class="text-sm text-charcoal-muted mt-2">Hanya butuh 4 langkah mudah untuk menganalisis piring makanan Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Step 1 -->
            <div class="p-6 rounded-2xl bg-cream-200 border border-stone-200/70 relative">
                <span class="w-8 h-8 rounded-full bg-emerald-800 text-white font-bold text-sm flex items-center justify-center mb-4">1</span>
                <h3 class="text-base font-bold text-emerald-950 mb-1.5">Unggah Foto Makanan</h3>
                <p class="text-xs text-charcoal-muted leading-relaxed">Jepret atau unggah foto makanan Anda (format JPEG, PNG, atau WEBP).</p>
            </div>

            <!-- Step 2 -->
            <div class="p-6 rounded-2xl bg-cream-200 border border-stone-200/70 relative">
                <span class="w-8 h-8 rounded-full bg-emerald-800 text-white font-bold text-sm flex items-center justify-center mb-4">2</span>
                <h3 class="text-base font-bold text-emerald-950 mb-1.5">Deteksi AI Vision</h3>
                <p class="text-xs text-charcoal-muted leading-relaxed">Model AI mengenali menu makanan dan mendeteksi bahan serta porsi visual.</p>
            </div>

            <!-- Step 3 -->
            <div class="p-6 rounded-2xl bg-cream-200 border border-stone-200/70 relative">
                <span class="w-8 h-8 rounded-full bg-emerald-800 text-white font-bold text-sm flex items-center justify-center mb-4">3</span>
                <h3 class="text-base font-bold text-emerald-950 mb-1.5">Hitung Nutrisi Presisi</h3>
                <p class="text-xs text-charcoal-muted leading-relaxed">Backend mencocokkan data nutrisi resmi per 100g dan mengonversi porsi aktual.</p>
            </div>

            <!-- Step 4 -->
            <div class="p-6 rounded-2xl bg-cream-200 border border-stone-200/70 relative">
                <span class="w-8 h-8 rounded-full bg-emerald-800 text-white font-bold text-sm flex items-center justify-center mb-4">4</span>
                <h3 class="text-base font-bold text-emerald-950 mb-1.5">Food Diary & Analitik</h3>
                <p class="text-xs text-charcoal-muted leading-relaxed">Tercatat rapi di buku harian untuk memantau kalori dan target mingguan.</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="fitur" class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <h2 class="text-xs font-bold uppercase tracking-wider text-emerald-800 mb-2">Fitur Unggulan</h2>
            <p class="text-3xl font-extrabold text-emerald-950">Segala yang Anda Butuhkan untuk Manajemen Gizi</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="p-6 rounded-2xl bg-cream-100 border border-stone-200 shadow-sm hover:border-emerald-700/40 transition">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center mb-4">
                    <i data-lucide="scan" class="w-6 h-6"></i>
                </div>
                <h3 class="text-lg font-bold text-emerald-950 mb-2">AI Food Recognition</h3>
                <p class="text-sm text-charcoal-muted leading-relaxed">
                    Identifikasi otomatis berbagai kuliner khas Nusantara dan makanan internasional dengan toleransi koreksi manual jika dibutuhkan.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="p-6 rounded-2xl bg-cream-100 border border-stone-200 shadow-sm hover:border-emerald-700/40 transition">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center mb-4">
                    <i data-lucide="pie-chart" class="w-6 h-6"></i>
                </div>
                <h3 class="text-lg font-bold text-emerald-950 mb-2">Perhitungan Makro Ilmiah</h3>
                <p class="text-sm text-charcoal-muted leading-relaxed">
                    Data kalori, protein, karbohidrat, lemak, dan serat dihitung langsung dari basis data gizi terpercaya, bukan rekaan acak model bahasa.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="p-6 rounded-2xl bg-cream-100 border border-stone-200 shadow-sm hover:border-emerald-700/40 transition">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center mb-4">
                    <i data-lucide="message-square" class="w-6 h-6"></i>
                </div>
                <h3 class="text-lg font-bold text-emerald-950 mb-2">Asisten Gizi AI 24/7</h3>
                <p class="text-sm text-charcoal-muted leading-relaxed">
                    Konsultasikan ide menu sehat, tips pemenuhan protein, dan pemahaman nutrisi dalam Bahasa Indonesia dengan aman tanpa klaim medis berlebih.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-16 bg-emerald-900 text-white relative overflow-hidden">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h2 class="text-3xl sm:text-4xl font-extrabold mb-4">Siap Memulai Pola Makan Sadar Gizi?</h2>
        <p class="text-emerald-100 text-sm sm:text-base max-w-xl mx-auto mb-8">
            Daftar sekarang secara gratis dan mulai lacak nutrisi setiap hidangan yang Anda nikmati hari ini.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-cream-100 text-emerald-950 font-bold hover:bg-cream-200 transition shadow-lg">
                Buat Akun Sekarang
            </a>
            <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl border border-emerald-400 text-white font-medium hover:bg-emerald-800 transition">
                Masuk ke Akun
            </a>
        </div>
    </div>
</section>
@endsection
