<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NutriScan AI — Smart Food & Nutrition Vision')</title>

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN for instant universal rendering) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#166534', // Theme Emerald Green
                            900: '#14532d',
                            950: '#052e16',
                        },
                        cream: {
                            50: '#FFFFFF',
                            100: '#FDFDF9',
                            200: '#F8F8F2', // Theme Background
                            300: '#EFEFE5',
                        },
                        charcoal: {
                            DEFAULT: '#202820', // Theme Charcoal Text
                            muted: '#526052',
                            light: '#859685',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            background-color: #F8F8F2;
            color: #202820;
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        }
    </style>
</head>
<body class="min-h-full flex flex-col bg-cream-200 text-charcoal antialiased">

    <!-- Navigation Header -->
    <nav class="sticky top-0 z-50 bg-cream-100/90 backdrop-blur-md border-b border-stone-200/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Brand Logo -->
                <a href="{{ route('landing') }}" class="flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-xl bg-emerald-800 text-white flex items-center justify-center shadow-md shadow-emerald-900/20 group-hover:scale-105 transition-transform">
                        <i data-lucide="scan-line" class="w-5 h-5 text-emerald-200"></i>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-emerald-950">Nutri<span class="text-emerald-800">Scan</span> <span class="text-xs uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-semibold ml-1">AI</span></span>
                </a>

                <!-- Nav Links -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('landing') }}#fitur" class="text-sm font-medium text-charcoal-muted hover:text-emerald-800 transition">Fitur</a>
                    <a href="{{ route('landing') }}#cara-kerja" class="text-sm font-medium text-charcoal-muted hover:text-emerald-800 transition">Cara Kerja</a>
                    <a href="{{ route('landing') }}#teknologi" class="text-sm font-medium text-charcoal-muted hover:text-emerald-800 transition">Teknologi AI</a>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-semibold bg-emerald-800 text-white px-4 py-2 rounded-xl hover:bg-emerald-900 shadow-sm shadow-emerald-900/10 transition">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span>Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-charcoal hover:text-emerald-800 px-3 py-2 transition">Masuk</a>
                        <a href="{{ route('register') }}" class="text-sm font-semibold bg-emerald-800 text-white px-4 py-2 rounded-xl hover:bg-emerald-900 shadow-sm shadow-emerald-900/20 transition">Daftar Gratis</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Notifications -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 shadow-sm animate-fade-in">
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
    <footer class="bg-cream-100 border-t border-stone-200/80 py-10 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-emerald-800 text-white flex items-center justify-center">
                        <i data-lucide="scan-line" class="w-4 h-4"></i>
                    </div>
                    <span class="font-bold text-emerald-950">NutriScan AI</span>
                    <span class="text-xs text-charcoal-muted ml-2">© 2026 Proyek Praktik Aplikasi Web — IT</span>
                </div>
                <div class="text-xs text-charcoal-muted text-center md:text-right">
                    Didukung oleh Laravel 13, 9Router Vision API & Tabel Komposisi Pangan Indonesia.
                </div>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
