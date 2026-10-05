@extends('layouts.app')

@section('title', 'AI Food Scanner — NutriScan AI')
@section('page_title', 'AI Food Scanner')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- AI Service Status & Info Banner -->
    @if(!$isAiConfigured)
        <div class="p-4 sm:p-5 rounded-3xl bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border border-amber-300/80 text-amber-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="p-2.5 rounded-2xl bg-amber-100 text-amber-900 flex-shrink-0 shadow-sm">
                    <i data-lucide="sparkles" class="w-5 h-5 text-amber-700"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h4 class="text-xs font-black uppercase tracking-wider text-amber-900">Mode Simulasi AI Heuristik Cerdas Aktif</h4>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-200/70 text-amber-900">[DEMO AKTIF]</span>
                    </div>
                    <p class="text-xs text-amber-900/90 leading-relaxed mt-0.5">
                        AI dapat mendeteksi burger, pizza, kentang, sate, nasi goreng, ayam, bakso, dll. Untuk mengaktifkan Vision AI nyata, pasang API Key 9Router di <code class="font-mono bg-amber-100 px-1 py-0.5 rounded text-[11px]">.env</code>.
                    </p>
                </div>
            </div>
            <a href="{{ route('profile.index') }}" class="text-xs font-bold text-amber-900 underline hover:text-amber-950 whitespace-nowrap pl-11 sm:pl-0">
                Pengaturan
            </a>
        </div>
    @else
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5 text-xs font-bold">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                <span>Vision AI 9Router Aktif &bull; Model: <code class="font-mono text-emerald-800">{{ config('services.nutriscan_ai.model', 'gpt-4o-mini') }}</code></span>
            </div>
            <span class="text-[11px] font-extrabold px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-900">Siap Menganalisis</span>
        </div>
    @endif

    <!-- Upload Box Card -->
    <div class="bg-white/90 backdrop-blur-md p-6 sm:p-8 rounded-3xl border border-stone-200/90 shadow-sm relative">
        <div class="text-center max-w-md mx-auto mb-6">
            <div class="w-14 h-14 rounded-2xl bg-emerald-800 text-emerald-100 flex items-center justify-center mx-auto mb-3 shadow-md shadow-emerald-900/10">
                <i data-lucide="scan" class="w-7 h-7"></i>
            </div>
            <h2 class="text-2xl font-black text-emerald-950 tracking-tight">Analisis Piring Makanan</h2>
            <p class="text-xs text-charcoal-muted mt-1 leading-relaxed">
                Unggah foto makanan Anda. AI akan mengidentifikasi jenis hidangan, porsi visual, dan kandungan gizinya secara instan.
            </p>
        </div>

        <form id="scannerForm" method="POST" action="{{ route('scanner.analyze') }}" enctype="multipart/form-data" onsubmit="handleFormSubmit(event)">
            @csrf

            <!-- Drag & Drop Zone -->
            <div id="dropzone"
                onclick="document.getElementById('imageInput').click()"
                ondragover="handleDragOver(event)"
                ondragleave="handleDragLeave(event)"
                ondrop="handleDrop(event)"
                class="border-2 border-dashed border-stone-300 rounded-3xl p-8 text-center cursor-pointer hover:border-emerald-700/60 hover:bg-emerald-50/20 transition relative group">

                <input type="file" id="imageInput" name="image" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="previewSelectedImage(this)">

                <!-- Empty Upload State -->
                <div id="emptyUploadState" class="space-y-3">
                    <div class="w-16 h-16 rounded-2xl bg-cream-300 text-charcoal-light flex items-center justify-center mx-auto group-hover:scale-105 group-hover:bg-emerald-100 group-hover:text-emerald-800 transition shadow-inner">
                        <i data-lucide="upload-cloud" class="w-8 h-8"></i>
                    </div>
                    <div>
                        <p class="text-sm font-extrabold text-charcoal">Seret & lepas foto di sini, atau <span class="text-emerald-800 underline">pilih dari file</span></p>
                        <p class="text-xs text-charcoal-light mt-1">Mendukung format JPEG, PNG, dan WEBP (Maksimal 5 MB)</p>
                    </div>
                </div>

                <!-- Selected Image Preview State -->
                <div id="previewState" class="hidden space-y-4">
                    <div class="relative inline-block rounded-2xl overflow-hidden shadow-lg max-h-72 border border-stone-200">
                        <img id="imagePreview" src="#" alt="Preview Makanan" class="max-h-72 w-auto object-contain mx-auto">
                        <button type="button" onclick="cancelPreview(event)" class="absolute top-2 right-2 p-1.5 rounded-full bg-black/60 text-white hover:bg-black transition" title="Ganti foto">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <p id="fileNameLabel" class="text-xs font-bold text-emerald-900"></p>
                </div>
            </div>

            @error('image')
                <p class="text-xs text-red-600 mt-2 font-medium">{{ $message }}</p>
            @enderror

            <!-- Action Button -->
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-[11px] text-charcoal-muted flex items-center gap-1.5">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-700"></i>
                    <span>Tersambung ke Basis Data Gizi TKPI Kemenkes & USDA</span>
                </div>

                <button type="submit" id="analyzeBtn" disabled
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-800 to-emerald-900 text-white font-extrabold text-sm shadow-md disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-lg hover:scale-[1.01] transition">
                    <i data-lucide="sparkles" class="w-4 h-4 text-emerald-200"></i>
                    <span>Analyze Food (Analisis AI)</span>
                </button>
            </div>
        </form>

        <!-- Loading Spinner Overlay -->
        <div id="loadingOverlay" class="absolute inset-0 bg-white/95 backdrop-blur-md rounded-3xl z-20 hidden flex-col items-center justify-center p-6 text-center animate-fade-in">
            <div class="w-16 h-16 rounded-2xl bg-emerald-800 text-white flex items-center justify-center mb-4 shadow-xl shadow-emerald-950/20 animate-bounce">
                <i data-lucide="scan" class="w-8 h-8 text-emerald-200"></i>
            </div>
            <h3 class="text-lg font-black text-emerald-950 mb-1" id="loadingTitle">AI Sedang Memindai Piring Anda...</h3>
            <p class="text-xs text-charcoal-muted max-w-sm leading-relaxed" id="loadingDesc">
                Mengidentifikasi jenis hidangan, menaksir berat porsi visual, dan menghitung rincian makronutrien.
            </p>
            <div class="w-48 bg-cream-300 rounded-full h-2 mt-5 overflow-hidden">
                <div class="bg-emerald-700 h-2 rounded-full animate-pulse w-3/4"></div>
            </div>
        </div>
    </div>

    <!-- Quick Nutrition Tips Bento -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl bg-white/80 border border-stone-200/80 text-xs shadow-sm">
            <div class="font-extrabold text-charcoal mb-1 flex items-center gap-1.5">
                <i data-lucide="sun" class="w-4 h-4 text-emerald-700"></i>
                <span>Pencahayaan Cukup</span>
            </div>
            <p class="text-charcoal-muted leading-relaxed">Pastikan makanan tampak jelas agar AI mengenali komponen sayur, lauk, dan karbohidrat.</p>
        </div>
        <div class="p-4 rounded-2xl bg-white/80 border border-stone-200/80 text-xs shadow-sm">
            <div class="font-extrabold text-charcoal mb-1 flex items-center gap-1.5">
                <i data-lucide="camera" class="w-4 h-4 text-emerald-700"></i>
                <span>Tampilan Piring Utuh</span>
            </div>
            <p class="text-charcoal-muted leading-relaxed">Ambil foto dari sudut 45 derajat atau tampak atas untuk estimasi porsi yang paling presisi.</p>
        </div>
        <div class="p-4 rounded-2xl bg-white/80 border border-stone-200/80 text-xs shadow-sm">
            <div class="font-extrabold text-charcoal mb-1 flex items-center gap-1.5">
                <i data-lucide="sliders" class="w-4 h-4 text-emerald-700"></i>
                <span>Koreksi Fleksibel</span>
            </div>
            <p class="text-charcoal-muted leading-relaxed">Anda selalu dapat menyesuaikan takaran gram porsi secara dinamis pada halaman hasil.</p>
        </div>
    </div>

    <!-- Contoh Foto untuk Dicoba -->
    <div class="bg-white/80 backdrop-blur-md p-5 sm:p-6 rounded-3xl border border-stone-200/90 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-black text-emerald-950 flex items-center gap-2">
                    <i data-lucide="images" class="w-4 h-4 text-emerald-700"></i>
                    <span>Coba dengan Foto Contoh</span>
                </h3>
                <p class="text-[11px] text-charcoal-muted mt-0.5">Klik salah satu foto untuk langsung mengisi area unggah.</p>
            </div>
        </div>
        <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
            @foreach(['nasi-goreng.jpg' => 'Nasi Goreng', 'ayam-bakar.jpg' => 'Ayam Bakar', 'sate-ayam.jpg' => 'Sate Ayam', 'bakso.jpg' => 'Bakso', 'salad.jpg' => 'Salad', 'pizza.jpg' => 'Pizza'] as $file => $label)
                <button type="button" onclick="loadSampleImage('{{ asset('images/foods/' . $file) }}', '{{ $file }}', this)"
                    class="group relative rounded-2xl overflow-hidden aspect-square border-2 border-transparent hover:border-emerald-600 focus:border-emerald-600 transition shadow-sm">
                    <img src="{{ asset('images/foods/' . $file) }}" alt="{{ $label }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                    <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-emerald-950/90 to-transparent pt-6 pb-1.5 text-[10px] font-bold text-white text-center">{{ $label }}</span>
                </button>
            @endforeach
        </div>
    </div>
</div>

<script>
    function previewSelectedImage(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('fileNameLabel').textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
                document.getElementById('emptyUploadState').classList.add('hidden');
                document.getElementById('previewState').classList.remove('hidden');
                document.getElementById('analyzeBtn').disabled = false;
                lucide.createIcons();
            }

            reader.readAsDataURL(file);
        }
    }

    function cancelPreview(e) {
        e.stopPropagation();
        document.getElementById('imageInput').value = '';
        document.getElementById('imagePreview').src = '#';
        document.getElementById('emptyUploadState').classList.remove('hidden');
        document.getElementById('previewState').classList.add('hidden');
        document.getElementById('analyzeBtn').disabled = true;
    }

    function handleDragOver(e) {
        e.preventDefault();
        document.getElementById('dropzone').classList.add('border-emerald-600', 'bg-emerald-50/50');
    }

    function handleDragLeave(e) {
        e.preventDefault();
        document.getElementById('dropzone').classList.remove('border-emerald-600', 'bg-emerald-50/50');
    }

    function handleDrop(e) {
        e.preventDefault();
        document.getElementById('dropzone').classList.remove('border-emerald-600', 'bg-emerald-50/50');
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            document.getElementById('imageInput').files = e.dataTransfer.files;
            previewSelectedImage(document.getElementById('imageInput'));
        }
    }

    function handleFormSubmit(e) {
        const overlay = document.getElementById('loadingOverlay');
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
    }

    // Muat foto contoh dari server menjadi File agar bisa langsung dianalisis
    async function loadSampleImage(url, filename, btn) {
        try {
            if (btn) btn.disabled = true;
            const res = await fetch(url);
            const blob = await res.blob();
            const file = new File([blob], filename, { type: blob.type || 'image/jpeg' });

            const dt = new DataTransfer();
            dt.items.add(file);
            const input = document.getElementById('imageInput');
            input.files = dt.files;

            previewSelectedImage(input);
            document.getElementById('dropzone').scrollIntoView({ behavior: 'smooth', block: 'center' });
        } catch (err) {
            alert('Gagal memuat foto contoh. Silakan unggah foto Anda sendiri.');
        } finally {
            if (btn) btn.disabled = false;
        }
    }
</script>
@endsection
