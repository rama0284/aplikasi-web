@extends('layouts.guest')

@section('title', 'NutriScan AI — Kenali Makananmu, Ubah Kebiasaanmu')

@section('content')
<!-- ══════════════ HERO SECTION ══════════════ -->
<section class="relative overflow-hidden pt-12 pb-16 sm:pt-16 sm:pb-24">
    <!-- Ambient Background -->
    <div class="absolute -top-40 -left-40 w-[32rem] h-[32rem] bg-emerald-300/25 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute top-1/3 -right-40 w-[28rem] h-[28rem] bg-lime-300/20 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <!-- Left: Headline -->
            <div class="space-y-7 text-center lg:text-left animate-fade-up">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/80 border border-emerald-200/80 text-emerald-900 text-xs font-bold shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span>Vision AI 9Router & Data Nutrisi TKPI Kemenkes</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-emerald-950 leading-[1.08] text-balance">
                    Kenali Makananmu,
                    <span class="relative inline-block text-emerald-700">
                        Ubah Kebiasaanmu.
                        <svg class="absolute -bottom-2 left-0 w-full h-3 text-lime-400/70" viewBox="0 0 300 12" fill="none" preserveAspectRatio="none">
                            <path d="M2 9C60 3 140 2 298 7" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                        </svg>
                    </span>
                </h1>

                <p class="text-base sm:text-lg text-charcoal-muted max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    Cukup foto sepiring makanan, AI kami mengenali hidangannya, memperkirakan porsi, dan menghitung kalori serta makronutrien secara presisi dari basis data gizi resmi.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-1">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3.5 rounded-2xl bg-emerald-800 text-white font-bold shadow-lg shadow-emerald-900/25 hover:bg-emerald-900 hover:scale-[1.02] transition">
                        <i data-lucide="camera" class="w-5 h-5 text-emerald-200"></i>
                        <span>Mulai Scan Gratis</span>
                    </a>
                    <a href="#cara-kerja" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-2xl bg-white/80 text-charcoal font-bold border border-stone-300 hover:bg-white transition">
                        <span>Lihat Cara Kerja</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 text-charcoal-muted"></i>
                    </a>
                </div>

                <!-- Stats -->
                <div class="pt-6 grid grid-cols-3 gap-4 max-w-lg mx-auto lg:mx-0 border-t border-stone-300/70">
                    <div>
                        <p class="text-2xl sm:text-3xl font-black text-emerald-800">24+</p>
                        <p class="text-[11px] font-semibold text-charcoal-muted uppercase tracking-wide">Menu Gizi</p>
                    </div>
                    <div>
                        <p class="text-2xl sm:text-3xl font-black text-emerald-800">96%</p>
                        <p class="text-[11px] font-semibold text-charcoal-muted uppercase tracking-wide">Akurasi AI</p>
                    </div>
                    <div>
                        <p class="text-2xl sm:text-3xl font-black text-emerald-800">4 Macros</p>
                        <p class="text-[11px] font-semibold text-charcoal-muted uppercase tracking-wide">Terlacak</p>
                    </div>
                </div>
            </div>

            <!-- Right: Photo Collage -->
            <div class="relative">
                <div class="relative rounded-[2rem] overflow-hidden shadow-2xl shadow-emerald-950/20 border-4 border-white ring-1 ring-stone-200">
                    <img src="{{ asset('images/hero/hero-food.jpg') }}" alt="Aneka makanan sehat Indonesia" class="w-full h-[26rem] object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/70 via-transparent to-transparent"></div>

                    <!-- Floating AI result card -->
                    <div class="absolute bottom-4 left-4 right-4 p-4 rounded-2xl bg-white/95 backdrop-blur-md shadow-xl">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-xs font-bold text-charcoal">Item Terdeteksi</span>
                            </div>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Confidence 96%</span>
                        </div>
                        <div class="grid grid-cols-4 gap-2 text-center">
                            <div class="p-2 rounded-xl bg-cream-200">
                                <span class="block text-[9px] text-charcoal-muted font-medium">Kalori</span>
                                <span class="text-xs font-black text-emerald-900">485</span>
                            </div>
                            <div class="p-2 rounded-xl bg-cream-200">
                                <span class="block text-[9px] text-charcoal-muted font-medium">Protein</span>
                                <span class="text-xs font-black text-blue-700">32g</span>
                            </div>
                            <div class="p-2 rounded-xl bg-cream-200">
                                <span class="block text-[9px] text-charcoal-muted font-medium">Karbo</span>
                                <span class="text-xs font-black text-amber-700">58g</span>
                            </div>
                            <div class="p-2 rounded-xl bg-cream-200">
                                <span class="block text-[9px] text-charcoal-muted font-medium">Lemak</span>
                                <span class="text-xs font-black text-rose-700">12g</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Floating badge: scan icon -->
                <div class="absolute -top-5 -right-5 w-24 h-24 rounded-3xl overflow-hidden border-4 border-white shadow-xl animate-float hidden sm:block">
                    <img src="{{ asset('images/foods/salad.jpg') }}" alt="Salad segar" class="w-full h-full object-cover">
                </div>

                <!-- Floating badge: check -->
                <div class="absolute -bottom-6 -left-6 px-4 py-3 rounded-2xl bg-white shadow-xl border border-stone-200 flex items-center gap-2.5 animate-float" style="animation-delay: 1.5s">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center">
                        <i data-lucide="check-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black text-charcoal">Tersimpan!</p>
                        <p class="text-[10px] text-charcoal-muted">ke Food Diary</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════ LOGOS / TRUST BAR ══════════════ -->
<section class="py-8 border-y border-stone-300/70 bg-white/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-center gap-x-10 gap-y-4 text-xs font-bold text-charcoal-muted">
            <div class="flex items-center gap-2"><i data-lucide="shield-check" class="w-4 h-4 text-emerald-700"></i> Standar Gizi TKPI</div>
            <div class="flex items-center gap-2"><i data-lucide="scan-eye" class="w-4 h-4 text-emerald-700"></i> Vision AI Recognition</div>
            <div class="flex items-center gap-2"><i data-lucide="book-marked" class="w-4 h-4 text-emerald-700"></i> Food Diary Terpadu</div>
            <div class="flex items-center gap-2"><i data-lucide="brain-circuit" class="w-4 h-4 text-emerald-700"></i> Asisten Gizi AI 24/7</div>
        </div>
    </div>
</section>

<!-- ══════════════ HOW IT WORKS ══════════════ -->
<section id="cara-kerja" class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <h2 class="text-xs font-black uppercase tracking-widest text-emerald-700 mb-2">Alur Cerdas & Praktis</h2>
            <p class="text-3xl sm:text-4xl font-black text-emerald-950 text-balance">Cara Kerja NutriScan AI</p>
            <p class="text-sm text-charcoal-muted mt-3">Hanya butuh 4 langkah mudah untuk menganalisis piring makanan Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $steps = [
                    ['icon' => 'camera', 'img' => 'nasi-goreng.jpg', 'title' => 'Unggah Foto Makanan', 'desc' => 'Jepret atau unggah foto makanan Anda (JPEG, PNG, atau WEBP maks 5 MB).'],
                    ['icon' => 'scan-search', 'img' => 'ayam-bakar.jpg', 'title' => 'Deteksi AI Vision', 'desc' => 'Model AI mengenali menu, mendeteksi bahan, dan memperkirakan porsi visual.'],
                    ['icon' => 'calculator', 'img' => 'sate-ayam.jpg', 'title' => 'Hitung Nutrisi Presisi', 'desc' => 'Backend mencocokkan data gizi resmi per 100g dan mengonversi ke porsi aktual.'],
                    ['icon' => 'line-chart', 'img' => 'salad.jpg', 'title' => 'Food Diary & Analitik', 'desc' => 'Tercatat rapi untuk memantau kalori dan target gizi harian Anda.'],
                ];
            @endphp

            @foreach($steps as $i => $step)
                <div class="group relative rounded-3xl overflow-hidden bg-white border border-stone-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300">
                    <div class="relative h-40 overflow-hidden">
                        <img src="{{ asset('images/foods/' . $step['img']) }}" alt="{{ $step['title'] }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/80 to-emerald-950/10"></div>
                        <span class="absolute top-3 left-3 w-9 h-9 rounded-xl bg-white/95 text-emerald-900 font-black text-sm flex items-center justify-center shadow-md">{{ $i + 1 }}</span>
                        <i data-lucide="{{ $step['icon'] }}" class="absolute bottom-3 right-3 w-6 h-6 text-white/90"></i>
                    </div>
                    <div class="p-5">
                        <h3 class="text-base font-black text-emerald-950 mb-1.5">{{ $step['title'] }}</h3>
                        <p class="text-xs text-charcoal-muted leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ══════════════ FOOD GALLERY ══════════════ -->
<section id="galeri" class="py-20 bg-white/50 border-y border-stone-300/70">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <h2 class="text-xs font-black uppercase tracking-widest text-emerald-700 mb-2">Basis Data Visual</h2>
            <p class="text-3xl sm:text-4xl font-black text-emerald-950 text-balance">Mengenali Aneka Kuliner Nyata</p>
            <p class="text-sm text-charcoal-muted mt-3">Dari masakan Nusantara hingga menu internasional, semua dapat difoto dan dianalisis.</p>
        </div>

        @php
            $gallery = [
                ['img' => 'nasi-goreng.jpg', 'name' => 'Nasi Goreng'],
                ['img' => 'ayam-bakar.jpg', 'name' => 'Ayam Bakar'],
                ['img' => 'sate-ayam.jpg', 'name' => 'Sate Ayam'],
                ['img' => 'bakso.jpg', 'name' => 'Bakso Sapi'],
                ['img' => 'soto-ayam.jpg', 'name' => 'Soto Ayam'],
                ['img' => 'rendang.jpg', 'name' => 'Rendang'],
                ['img' => 'mie-goreng.jpg', 'name' => 'Mie Goreng'],
                ['img' => 'salad.jpg', 'name' => 'Salad Segar'],
                ['img' => 'burger.jpg', 'name' => 'Burger'],
                ['img' => 'pizza.jpg', 'name' => 'Pizza'],
                ['img' => 'tempe-tahu.jpg', 'name' => 'Tempe & Tahu'],
                ['img' => 'telur-dadar.jpg', 'name' => 'Telur Dadar'],
            ];
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($gallery as $item)
                <div class="group relative rounded-2xl overflow-hidden aspect-square shadow-sm hover:shadow-lg transition">
                    <img src="{{ asset('images/foods/' . $item['img']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/85 via-emerald-950/10 to-transparent opacity-90"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-3">
                        <p class="text-white font-bold text-sm">{{ $item['name'] }}</p>
                    </div>
                    <span class="absolute top-2 right-2 text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/90 text-emerald-800 opacity-0 group-hover:opacity-100 transition">Dianalisis AI</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ══════════════ FEATURES ══════════════ -->
<section id="fitur" class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <h2 class="text-xs font-black uppercase tracking-widest text-emerald-700 mb-2">Fitur Unggulan</h2>
            <p class="text-3xl sm:text-4xl font-black text-emerald-950 text-balance">Segala yang Anda Butuhkan untuk Manajemen Gizi</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-7 rounded-3xl bg-white border border-stone-200/80 shadow-sm hover:shadow-xl hover:border-emerald-300 hover:-translate-y-1 transition duration-300">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-900 text-white flex items-center justify-center mb-5 shadow-lg shadow-emerald-900/20">
                    <i data-lucide="scan" class="w-7 h-7"></i>
                </div>
                <h3 class="text-lg font-black text-emerald-950 mb-2">AI Food Recognition</h3>
                <p class="text-sm text-charcoal-muted leading-relaxed">
                    Identifikasi otomatis berbagai kuliner khas Nusantara dan internasional, dengan koreksi porsi manual jika diperlukan.
                </p>
            </div>

            <div class="p-7 rounded-3xl bg-white border border-stone-200/80 shadow-sm hover:shadow-xl hover:border-emerald-300 hover:-translate-y-1 transition duration-300">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-700 text-white flex items-center justify-center mb-5 shadow-lg shadow-teal-900/20">
                    <i data-lucide="pie-chart" class="w-7 h-7"></i>
                </div>
                <h3 class="text-lg font-black text-emerald-950 mb-2">Perhitungan Makro Ilmiah</h3>
                <p class="text-sm text-charcoal-muted leading-relaxed">
                    Kalori, protein, karbohidrat, lemak, dan serat dihitung dari basis data gizi terpercaya, bukan rekaan acak model bahasa.
                </p>
            </div>

            <div class="p-7 rounded-3xl bg-white border border-stone-200/80 shadow-sm hover:shadow-xl hover:border-emerald-300 hover:-translate-y-1 transition duration-300">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-lime-500 to-emerald-600 text-white flex items-center justify-center mb-5 shadow-lg shadow-lime-900/20">
                    <i data-lucide="message-square" class="w-7 h-7"></i>
                </div>
                <h3 class="text-lg font-black text-emerald-950 mb-2">Asisten Gizi AI 24/7</h3>
                <p class="text-sm text-charcoal-muted leading-relaxed">
                    Konsultasikan ide menu sehat, tips pemenuhan protein, dan pemahaman nutrisi dalam Bahasa Indonesia yang aman.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════ TECHNOLOGY ══════════════ -->
<section id="teknologi" class="py-20 bg-emerald-950 text-white relative overflow-hidden">
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-xs font-black uppercase tracking-widest text-emerald-400 mb-2">Didukung Teknologi Modern</h2>
                <p class="text-3xl sm:text-4xl font-black mb-4 text-balance">AI Vision Bertemu Data Gizi Resmi</p>
                <p class="text-emerald-100/70 text-sm leading-relaxed mb-8 max-w-lg">
                    NutriScan AI menggabungkan kecerdasan visual 9Router dengan Tabel Komposisi Pangan Indonesia (TKPI) untuk hasil yang cerdas sekaligus akurat.
                </p>

                <div class="space-y-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-800/80 flex items-center justify-center flex-shrink-0"><i data-lucide="eye" class="w-5 h-5 text-emerald-300"></i></div>
                        <div><h4 class="font-bold text-sm">Vision AI 9Router</h4><p class="text-xs text-emerald-100/60">Mengenali hidangan langsung dari foto dengan akurasi tinggi.</p></div>
                    </div>
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-800/80 flex items-center justify-center flex-shrink-0"><i data-lucide="database" class="w-5 h-5 text-emerald-300"></i></div>
                        <div><h4 class="font-bold text-sm">Basis Data Gizi TKPI</h4><p class="text-xs text-emerald-100/60">Nilai nutrisi per 100 gram diambil dari sumber resmi terpercaya.</p></div>
                    </div>
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-800/80 flex items-center justify-center flex-shrink-0"><i data-lucide="sparkles" class="w-5 h-5 text-emerald-300"></i></div>
                        <div><h4 class="font-bold text-sm">Asisten Gizi Interaktif</h4><p class="text-xs text-emerald-100/60">Tanya jawab gizi kontekstual sesuai target harian Anda.</p></div>
                    </div>
                </div>
            </div>

            <!-- Photo mock -->
            <div class="relative">
                <div class="rounded-[2rem] overflow-hidden border-4 border-emerald-800/60 shadow-2xl">
                    <img src="{{ asset('images/hero/hero-scan.jpg') }}" alt="Proses memindai makanan" class="w-full h-80 object-cover">
                </div>
                <div class="absolute -bottom-5 -left-5 px-5 py-4 rounded-2xl bg-white text-charcoal shadow-xl flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center"><i data-lucide="zap" class="w-6 h-6"></i></div>
                    <div><p class="text-sm font-black">Analisis Instan</p><p class="text-[11px] text-charcoal-muted">Hasil dalam hitungan detik</p></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════ CTA ══════════════ -->
<section class="py-20">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative rounded-[2.5rem] overflow-hidden shadow-2xl">
            <img src="{{ asset('images/hero/hero-food.jpg') }}" alt="Makanan sehat" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/95 via-emerald-950/85 to-emerald-900/70"></div>
            <div class="relative z-10 px-8 sm:px-14 py-16 text-center text-white">
                <h2 class="text-3xl sm:text-4xl font-black mb-4 text-balance">Siap Memulai Pola Makan Sadar Gizi?</h2>
                <p class="text-emerald-100/80 text-sm sm:text-base max-w-xl mx-auto mb-8">
                    Daftar sekarang secara gratis dan mulai lacak nutrisi setiap hidangan yang Anda nikmati hari ini.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-white text-emerald-950 font-black hover:bg-emerald-50 hover:scale-105 transition shadow-lg">
                        Buat Akun Sekarang
                    </a>
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-2xl border border-emerald-400/70 text-white font-semibold hover:bg-white/10 transition">
                        Masuk ke Akun
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
