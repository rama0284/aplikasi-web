<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NutriScan AI — Smart Nutrition Platform')</title>

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN) -->
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
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46', // Deep Forest Primary
                            900: '#064e3b',
                            950: '#022c22',
                        },
                        cream: {
                            50: '#FFFFFF',
                            100: '#FAF9F5',
                            200: '#F4F2EA', // Warm Soft Background
                            300: '#E9E6DA',
                            400: '#DDD9CB',
                        },
                        lime: { 400: '#a3e635', 500: '#84cc16' },
                        charcoal: {
                            DEFAULT: '#1a231a',
                            muted: '#4b5a4b',
                            light: '#7e917e',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Chart.js for data visualization -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            background-color: #F4F2EA;
            color: #1a231a;
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
        /* Smooth transitions */
        .glass-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(233, 230, 218, 0.9);
        }
        .glass-dark {
            background: linear-gradient(135deg, #065f46 0%, #022c22 100%);
        }
        .nav-active {
            background: linear-gradient(135deg, #065f46 0%, #047857 100%);
            box-shadow: 0 8px 20px -6px rgba(2, 44, 34, 0.5);
        }
    </style>
</head>
<body class="h-full flex bg-cream-200 text-charcoal antialiased selection:bg-emerald-200 selection:text-emerald-950">

    <!-- Mobile Navigation Drawer Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity backdrop-blur-sm" onclick="toggleSidebar()"></div>

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-cream-100 border-r border-stone-200/90 flex flex-col transition-transform duration-300 transform -translate-x-full md:translate-x-0 md:static md:inset-auto shadow-sm">
        
        <!-- Logo Header -->
        <div class="h-20 flex items-center justify-between px-6 border-b border-stone-200/80">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-900 text-white flex items-center justify-center shadow-lg shadow-emerald-900/25 group-hover:scale-105 transition duration-300">
                    <i data-lucide="scan-line" class="w-5 h-5 text-emerald-100"></i>
                </div>
                <div>
                    <span class="text-xl font-black tracking-tight text-emerald-950 flex items-center gap-1.5">
                        Nutri<span class="text-emerald-600">Scan</span>
                        <span class="text-[9px] uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold tracking-widest border border-emerald-200">AI</span>
                    </span>
                    <p class="text-[10px] font-semibold text-charcoal-light tracking-wide">Smart Nutrition Platform</p>
                </div>
            </a>
            <button class="md:hidden text-charcoal-muted hover:text-charcoal p-1.5 rounded-xl hover:bg-cream-300 transition" onclick="toggleSidebar()">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto custom-scrollbar">
            
            <div class="text-[10px] font-extrabold uppercase tracking-wider text-charcoal-light px-3 mb-2 flex items-center justify-between">
                <span>Menu Utama</span>
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            </div>

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-2xl font-bold text-sm transition {{ request()->routeIs('dashboard') ? 'bg-emerald-800 text-white shadow-md shadow-emerald-950/20' : 'text-charcoal-muted hover:bg-cream-300 hover:text-charcoal' }}">
                <i data-lucide="layout-grid" class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-emerald-200' : 'text-charcoal-light' }}"></i>
                <span>Dashboard Gizi</span>
            </a>

            <!-- Database Makanan (CRUD Master Nutrisi) -->
            <a href="{{ route('foods.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-2xl font-bold text-sm transition {{ request()->routeIs('foods.*') ? 'bg-emerald-800 text-white shadow-md shadow-emerald-950/20' : 'text-charcoal-muted hover:bg-cream-300 hover:text-charcoal' }}">
                <i data-lucide="utensils-crossed" class="w-4 h-4 {{ request()->routeIs('foods.*') ? 'text-emerald-200' : 'text-charcoal-light' }}"></i>
                <span>Database Makanan</span>
                <span class="ml-auto text-[10px] px-2 py-0.5 rounded-full {{ request()->routeIs('foods.*') ? 'bg-emerald-700 text-emerald-100' : 'bg-emerald-100 text-emerald-800' }} font-extrabold">CRUD</span>
            </a>

            <!-- AI Food Scanner -->
            <a href="{{ route('scanner.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-2xl font-bold text-sm transition {{ request()->routeIs('scanner.*') ? 'bg-emerald-800 text-white shadow-md shadow-emerald-950/20' : 'text-charcoal-muted hover:bg-cream-300 hover:text-charcoal' }}">
                <i data-lucide="scan" class="w-4 h-4 {{ request()->routeIs('scanner.*') ? 'text-emerald-200' : 'text-charcoal-light' }}"></i>
                <span>AI Food Scanner</span>
                <span class="ml-auto text-[10px] px-2 py-0.5 rounded-full bg-gradient-to-r from-amber-400 to-amber-500 text-emerald-950 font-black shadow-xs">AI</span>
            </a>

            <!-- Food Diary -->
            <a href="{{ route('diary.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-2xl font-bold text-sm transition {{ request()->routeIs('diary.*') ? 'bg-emerald-800 text-white shadow-md shadow-emerald-950/20' : 'text-charcoal-muted hover:bg-cream-300 hover:text-charcoal' }}">
                <i data-lucide="book-marked" class="w-4 h-4 {{ request()->routeIs('diary.*') ? 'text-emerald-200' : 'text-charcoal-light' }}"></i>
                <span>Jurnal Makan Harian</span>
            </a>

            <div class="text-[10px] font-extrabold uppercase tracking-wider text-charcoal-light px-3 mt-7 mb-2 flex items-center justify-between">
                <span>Analisis & AI</span>
                <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
            </div>

            <!-- Nutrition History & Trends -->
            <a href="{{ route('history.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-2xl font-bold text-sm transition {{ request()->routeIs('history.*') ? 'bg-emerald-800 text-white shadow-md shadow-emerald-950/20' : 'text-charcoal-muted hover:bg-cream-300 hover:text-charcoal' }}">
                <i data-lucide="trending-up" class="w-4 h-4 {{ request()->routeIs('history.*') ? 'text-emerald-200' : 'text-charcoal-light' }}"></i>
                <span>Riwayat & Tren Gizi</span>
            </a>

            <!-- AI Assistant -->
            <a href="{{ route('assistant.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-2xl font-bold text-sm transition {{ request()->routeIs('assistant.*') ? 'bg-emerald-800 text-white shadow-md shadow-emerald-950/20' : 'text-charcoal-muted hover:bg-cream-300 hover:text-charcoal' }}">
                <i data-lucide="bot" class="w-4 h-4 {{ request()->routeIs('assistant.*') ? 'text-emerald-200' : 'text-charcoal-light' }}"></i>
                <span>Asisten Gizi AI</span>
            </a>

            <!-- Profile & Nutrition Goals -->
            <a href="{{ route('profile.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-2xl font-bold text-sm transition {{ request()->routeIs('profile.*') ? 'bg-emerald-800 text-white shadow-md shadow-emerald-950/20' : 'text-charcoal-muted hover:bg-cream-300 hover:text-charcoal' }}">
                <i data-lucide="sliders" class="w-4 h-4 {{ request()->routeIs('profile.*') ? 'text-emerald-200' : 'text-charcoal-light' }}"></i>
                <span>Target & Profil</span>
            </a>
        </div>

        <!-- Sidebar Footer / User Info -->
        <div class="p-4 border-t border-stone-200/80 bg-cream-100/90">
            <div class="flex items-center gap-3 mb-3 p-2 rounded-2xl bg-cream-200/80 border border-stone-200/60">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-700 to-teal-800 text-white flex items-center justify-center font-black text-sm uppercase shadow-sm">
                    {{ substr(Auth::user()->name ?? 'U', 0, 2) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-black text-charcoal truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-charcoal-muted truncate font-medium">{{ Auth::user()->email }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-50 rounded-xl transition border border-rose-200/70">
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                    <span>Keluar Akun</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto custom-scrollbar">

        <!-- Top Header Bar -->
        <header class="h-20 bg-cream-100/80 backdrop-blur-xl border-b border-stone-200/80 flex items-center justify-between px-4 sm:px-8 sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button class="md:hidden p-2 rounded-xl text-charcoal-muted hover:bg-cream-300 transition" onclick="toggleSidebar()">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <div>
                    <h1 class="text-lg sm:text-xl font-black text-charcoal tracking-tight">@yield('page_title', 'NutriScan AI Dashboard')</h1>
                    <p class="text-xs text-charcoal-muted font-medium flex items-center gap-1.5 mt-0.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-emerald-700"></i>
                        <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                    </p>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex items-center gap-2.5 sm:gap-3">
                <a href="{{ route('foods.create') }}" class="hidden sm:inline-flex items-center gap-2 text-xs font-bold bg-white text-emerald-950 px-3.5 py-2.5 rounded-xl hover:bg-emerald-50 border border-stone-200/90 shadow-sm transition">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-emerald-700"></i>
                    <span>Tambah Makanan</span>
                </a>

                <a href="{{ route('scanner.index') }}" class="inline-flex items-center gap-2 text-xs font-extrabold bg-gradient-to-r from-emerald-800 to-teal-800 text-white px-4 py-2.5 rounded-xl hover:shadow-lg hover:shadow-emerald-900/20 hover:scale-[1.02] transition">
                    <i data-lucide="scan" class="w-4 h-4 text-emerald-200"></i>
                    <span>Scan AI</span>
                </a>

                <a href="{{ route('assistant.index') }}" class="p-2.5 rounded-xl bg-white text-emerald-800 hover:bg-emerald-50 border border-stone-200/80 shadow-sm transition" title="Tanya Asisten Gizi AI">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                </a>
            </div>
        </header>

        <!-- Flash Notifications -->
        <div class="px-4 sm:px-8 mt-5">
            @if(session('success'))
                <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-50/90 border border-emerald-200/80 text-emerald-950 shadow-sm backdrop-blur-md">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-700"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center gap-3 p-4 rounded-2xl bg-rose-50/90 border border-rose-200/80 text-rose-950 shadow-sm backdrop-blur-md">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-700"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if(session('info'))
                <div class="flex items-center gap-3 p-4 rounded-2xl bg-amber-50/90 border border-amber-200/80 text-amber-950 shadow-sm backdrop-blur-md">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="info" class="w-5 h-5 text-amber-700"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold">{{ session('info') }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Main Workspace -->
        <main class="flex-1 p-4 sm:p-8">
            @yield('content')
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>
