<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NutriScan AI — Smart Nutrition Platform')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

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
                            800: '#166534', // Theme Primary Emerald Green
                            900: '#14532d',
                            950: '#052e16',
                        },
                        cream: {
                            50: '#FFFFFF',
                            100: '#FDFDF9',
                            200: '#F8F8F2', // Theme Background
                            300: '#EFEFE5',
                            400: '#E2E2D6',
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

    <!-- Chart.js for data visualization -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            background-color: #F8F8F2;
            color: #202820;
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #F8F8F2;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
        }
    </style>
</head>
<body class="h-full flex bg-cream-200 text-charcoal antialiased">

    <!-- Mobile Navigation Drawer Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden transition-opacity" onclick="toggleSidebar()"></div>

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-cream-100 border-r border-stone-200/80 flex flex-col transition-transform transform -translate-x-full md:translate-x-0 md:static md:inset-auto">
        <!-- Logo Header -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-stone-200/70">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-800 text-white flex items-center justify-center shadow-md shadow-emerald-900/20">
                    <i data-lucide="scan-line" class="w-5 h-5 text-emerald-200"></i>
                </div>
                <span class="text-lg font-bold tracking-tight text-emerald-950">Nutri<span class="text-emerald-800">Scan</span> <span class="text-[10px] uppercase px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold ml-0.5">AI</span></span>
            </a>
            <button class="md:hidden text-charcoal-muted hover:text-charcoal" onclick="toggleSidebar()">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto custom-scrollbar">
            <div class="text-[11px] font-bold uppercase tracking-wider text-charcoal-light px-3 mb-2">Navigasi Utama</div>

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('dashboard') ? 'bg-emerald-800 text-white shadow-sm shadow-emerald-900/20' : 'text-charcoal-muted hover:bg-cream-300 hover:text-charcoal' }}">
                <i data-lucide="layout-dashboard" class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-emerald-200' : 'text-charcoal-light' }}"></i>
                <span>Dashboard</span>
            </a>

            <!-- Food Scanner -->
            <a href="{{ route('scanner.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('scanner.*') ? 'bg-emerald-800 text-white shadow-sm shadow-emerald-900/20' : 'text-charcoal-muted hover:bg-cream-300 hover:text-charcoal' }}">
                <i data-lucide="camera" class="w-4 h-4 {{ request()->routeIs('scanner.*') ? 'text-emerald-200' : 'text-charcoal-light' }}"></i>
                <span>AI Food Scanner</span>
                <span class="ml-auto text-[10px] px-1.5 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-bold">AI</span>
            </a>

            <!-- Food Diary -->
            <a href="{{ route('diary.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('diary.*') ? 'bg-emerald-800 text-white shadow-sm shadow-emerald-900/20' : 'text-charcoal-muted hover:bg-cream-300 hover:text-charcoal' }}">
                <i data-lucide="book-open" class="w-4 h-4 {{ request()->routeIs('diary.*') ? 'text-emerald-200' : 'text-charcoal-light' }}"></i>
                <span>Food Diary</span>
            </a>

            <!-- Nutrition History -->
            <a href="{{ route('history.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('history.*') ? 'bg-emerald-800 text-white shadow-sm shadow-emerald-900/20' : 'text-charcoal-muted hover:bg-cream-300 hover:text-charcoal' }}">
                <i data-lucide="line-chart" class="w-4 h-4 {{ request()->routeIs('history.*') ? 'text-emerald-200' : 'text-charcoal-light' }}"></i>
                <span>Riwayat & Tren</span>
            </a>

            <div class="text-[11px] font-bold uppercase tracking-wider text-charcoal-light px-3 mt-6 mb-2">Asisten & Profil</div>

            <!-- AI Assistant -->
            <a href="{{ route('assistant.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('assistant.*') ? 'bg-emerald-800 text-white shadow-sm shadow-emerald-900/20' : 'text-charcoal-muted hover:bg-cream-300 hover:text-charcoal' }}">
                <i data-lucide="bot" class="w-4 h-4 {{ request()->routeIs('assistant.*') ? 'text-emerald-200' : 'text-charcoal-light' }}"></i>
                <span>Asisten Gizi AI</span>
            </a>

            <!-- Profile & Settings -->
            <a href="{{ route('profile.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('profile.*') ? 'bg-emerald-800 text-white shadow-sm shadow-emerald-900/20' : 'text-charcoal-muted hover:bg-cream-300 hover:text-charcoal' }}">
                <i data-lucide="user-cog" class="w-4 h-4 {{ request()->routeIs('profile.*') ? 'text-emerald-200' : 'text-charcoal-light' }}"></i>
                <span>Profil & Target</span>
            </a>
        </div>

        <!-- Sidebar Footer / User Info -->
        <div class="p-4 border-t border-stone-200/70 bg-cream-100">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-emerald-700 text-white flex items-center justify-center font-bold text-xs uppercase shadow-sm">
                    {{ substr(Auth::user()->name ?? 'U', 0, 2) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-charcoal truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-charcoal-muted truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50 rounded-lg transition border border-red-200/60">
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                    <span>Keluar Akun</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto custom-scrollbar">

        <!-- Top Header Bar -->
        <header class="h-16 bg-cream-100/90 backdrop-blur-md border-b border-stone-200/70 flex items-center justify-between px-4 sm:px-6 lg:px-8 sticky top-0 z-30">
            <div class="flex items-center gap-3">
                <button class="md:hidden p-2 rounded-lg text-charcoal-muted hover:bg-cream-300" onclick="toggleSidebar()">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div class="hidden sm:block">
                    <h1 class="text-base font-bold text-charcoal">@yield('page_title', 'NutriScan AI Dashboard')</h1>
                    <p class="text-xs text-charcoal-muted">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex items-center gap-3">
                <a href="{{ route('scanner.index') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold bg-emerald-800 text-white px-3.5 py-2 rounded-xl hover:bg-emerald-900 shadow-sm shadow-emerald-900/10 transition">
                    <i data-lucide="scan" class="w-4 h-4 text-emerald-200"></i>
                    <span class="hidden sm:inline">Scan Makanan</span>
                    <span class="sm:hidden">Scan</span>
                </a>

                <a href="{{ route('assistant.index') }}" class="p-2 rounded-xl text-charcoal-muted hover:bg-cream-300 hover:text-emerald-800 transition" title="Tanya Asisten AI">
                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                </a>
            </div>
        </header>

        <!-- Flash Notifications -->
        <div class="px-4 sm:px-6 lg:px-8 mt-4">
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

        <!-- Main Workspace -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
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
