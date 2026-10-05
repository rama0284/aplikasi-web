<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NutriScan AI — Smart Food & Nutrition Vision')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0', 300: '#6ee7b7',
                            400: '#34d399', 500: '#10b981', 600: '#059669', 700: '#047857',
                            800: '#065f46', 900: '#064e3b', 950: '#022c22',
                        },
                        cream: {
                            50: '#FFFFFF', 100: '#FAF9F5', 200: '#F4F2EA', 300: '#E9E6DA',
                        },
                        charcoal: {
                            DEFAULT: '#1a231a', muted: '#4b5a4b', light: '#7e917e',
                        },
                        lime: { 400: '#a3e635', 500: '#84cc16' }
                    },
                    fontFamily: { sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'] },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-12px)' },
                        },
                        'fade-up': {
                            '0%': { opacity: '0', transform: 'translateY(16px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                    },
                    animation: {
                        float: 'float 6s ease-in-out infinite',
                        'fade-up': 'fade-up 0.7s ease-out both',
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            background-color: #F4F2EA;
            color: #1a231a;
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        }
        .text-balance { text-wrap: balance; }
        .glass-nav {
            background: rgba(250, 249, 245, 0.72);
            backdrop-filter: blur(16px) saturate(160%);
            -webkit-backdrop-filter: blur(16px) saturate(160%);
        }
    </style>
</head>
<body class="min-h-full flex flex-col bg-cream-200 text-charcoal antialiased selection:bg-emerald-200 selection:text-emerald-950">

    <!-- Navigation Header -->
    <nav class="sticky top-0 z-50 glass-nav border-b border-stone-300/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Brand Logo -->
                <a href="{{ route('landing') }}" class="flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-900 text-white flex items-center justify-center shadow-md shadow-emerald-900/25 group-hover:scale-105 transition-transform">
                        <i data-lucide="scan-line" class="w-5 h-5 text-emerald-100"></i>
                    </div>
                    <span class="text-xl font-extrabold tracking-tight text-emerald-950">Nutri<span class="text-emerald-700">Scan</span> <span class="text-[10px] uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold ml-0.5 align-middle">AI</span></span>
                </a>

                <!-- Nav Links -->
                <div class="hidden md:flex items-center gap-1 bg-white/50 border border-stone-200/70 rounded-full px-1.5 py-1">
                    <a href="{{ route('landing') }}#fitur" class="px-3.5 py-1.5 text-sm font-semibold text-charcoal-muted hover:text-emerald-800 hover:bg-white rounded-full transition">Fitur</a>
                    <a href="{{ route('landing') }}#cara-kerja" class="px-3.5 py-1.5 text-sm font-semibold text-charcoal-muted hover:text-emerald-800 hover:bg-white rounded-full transition">Cara Kerja</a>
                    <a href="{{ route('landing') }}#galeri" class="px-3.5 py-1.5 text-sm font-semibold text-charcoal-muted hover:text-emerald-800 hover:bg-white rounded-full transition">Galeri</a>
                    <a href="{{ route('landing') }}#teknologi" class="px-3.5 py-1.5 text-sm font-semibold text-charcoal-muted hover:text-emerald-800 hover:bg-white rounded-full transition">Teknologi</a>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-bold bg-emerald-800 text-white px-4 py-2 rounded-xl hover:bg-emerald-900 shadow-sm shadow-emerald-900/20 transition">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span>Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline text-sm font-semibold text-charcoal hover:text-emerald-800 px-3 py-2 transition">Masuk</a>
                        <a href="{{ route('register') }}" class="text-sm font-bold bg-emerald-800 text-white px-4 py-2 rounded-xl hover:bg-emerald-900 shadow-sm shadow-emerald-900/20 transition">Daftar Gratis</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Notifications -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 shadow-sm">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-red-50 border border-red-200 text-red-900 shadow-sm">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 flex-shrink-0"></i>
                <p class="text-sm font-medium">{{ session('error') }}</p>
            </div>
        @endif
        @if(session('info'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 shadow-sm">
                <i data-lucide="info" class="w-5 h-5 text-amber-600 flex-shrink-0"></i>
                <p class="text-sm font-medium">{{ session('info') }}</p>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-emerald-950 text-emerald-100/80 pt-14 pb-8 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-10 border-b border-emerald-900/60">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-9 h-9 rounded-xl bg-emerald-700 text-white flex items-center justify-center">
                            <i data-lucide="scan-line" class="w-4.5 h-4.5"></i>
                        </div>
                        <span class="text-lg font-extrabold text-white">NutriScan AI</span>
                    </div>
                    <p class="text-sm leading-relaxed max-w-md text-emerald-100/70">
                        Platform cerdas untuk mengenali makanan dari foto, menghitung nutrisi presisi, dan membangun kebiasaan makan sehat yang terukur setiap hari.
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-bold text-sm mb-3">Navigasi</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('landing') }}#fitur" class="hover:text-white transition">Fitur</a></li>
                        <li><a href="{{ route('landing') }}#cara-kerja" class="hover:text-white transition">Cara Kerja</a></li>
                        <li><a href="{{ route('landing') }}#galeri" class="hover:text-white transition">Galeri Makanan</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition">Daftar Gratis</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold text-sm mb-3">Teknologi</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-3.5 h-3.5 text-emerald-400"></i> Laravel 12</li>
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-3.5 h-3.5 text-emerald-400"></i> 9Router Vision AI</li>
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-3.5 h-3.5 text-emerald-400"></i> Data TKPI Kemenkes</li>
                    </ul>
                </div>
            </div>
            <div class="pt-6 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-emerald-100/50">
                <span>© 2026 NutriScan AI — Proyek Praktik Aplikasi Web</span>
                <span class="flex items-center gap-1.5">Dibuat dengan <i data-lucide="heart" class="w-3.5 h-3.5 text-rose-400 fill-rose-400"></i> untuk gaya hidup sehat</span>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
