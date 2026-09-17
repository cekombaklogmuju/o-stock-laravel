<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'O-Stock') }} - @yield('title', 'Sistem Manajemen Stok')</title>

    <!-- Tailwind CSS & Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Alpine.js for lightweight UI interaction -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: #fff !important; margin: 0; padding: 0; }
        }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-800 flex" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden"
         @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-200 transition-transform duration-300 ease-in-out flex flex-col no-print">
        
        <!-- Brand Header -->
        <div class="h-16 flex items-center justify-between px-6 bg-slate-950 border-b border-slate-800">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md shadow-indigo-600/30">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <div class="flex flex-col">
                    <span class="font-black text-white text-base tracking-wider uppercase">O-STOCK</span>
                    <span class="text-[10px] text-slate-400 font-medium tracking-tight">Multi-Branch System</span>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="text-slate-400 hover:text-white lg:hidden">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <!-- Navigation Menu -->
        <div class="flex-1 overflow-y-auto px-4 py-6 space-y-6">
            <div>
                <p class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Utama</p>
                <nav class="space-y-1">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                        <span>Dashboard</span>
                    </a>
                </nav>
            </div>

            <!-- Penjualan & Kasir (Barcode Scanner) -->
            <div>
                <p class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Penjualan</p>
                <nav class="space-y-1">
                    <a href="{{ route('penjualan.create') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('penjualan.create') ? 'bg-emerald-600 text-white shadow-sm' : 'text-emerald-400 hover:bg-slate-800 hover:text-emerald-300' }}">
                        <i class="fa-solid fa-barcode w-5 text-center"></i>
                        <span>Kasir & Scan Barcode</span>
                    </a>
                    <a href="{{ route('penjualan.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('penjualan.index') || request()->routeIs('penjualan.show') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-receipt w-5 text-center"></i>
                        <span>Daftar Invoice</span>
                    </a>
                </nav>
            </div>

            <!-- Inventori & Mutasi -->
            <div>
                <p class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Inventori</p>
                <nav class="space-y-1">
                    <a href="{{ route('alokasi.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('alokasi.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-truck-ramp-box w-5 text-center"></i>
                        <span>Alokasi Stok</span>
                    </a>
                    <a href="{{ route('branch_request.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('branch_request.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-code-pull-request w-5 text-center"></i>
                        <span>Permintaan Cabang</span>
                    </a>
                    <a href="{{ route('kartu_stok.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('kartu_stok.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-clipboard-list w-5 text-center"></i>
                        <span>Kartu Stok</span>
                    </a>
                </nav>
            </div>

            <!-- Master Data -->
            <div>
                <p class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Master Data</p>
                <nav class="space-y-1">
                    <a href="{{ route('produk.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('produk.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-box-open w-5 text-center"></i>
                        <span>Produk & Barcode</span>
                    </a>
                    <a href="{{ route('kategori.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('kategori.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-tags w-5 text-center"></i>
                        <span>Kategori Produk</span>
                    </a>
                    <a href="{{ route('supplier.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('supplier.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-truck-field w-5 text-center"></i>
                        <span>Supplier</span>
                    </a>
                    <a href="{{ route('konsumen.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('konsumen.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-users w-5 text-center"></i>
                        <span>Konsumen / Pelanggan</span>
                    </a>
                    <a href="{{ route('salesman.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('salesman.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-user-tie w-5 text-center"></i>
                        <span>Salesman</span>
                    </a>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('kantor.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('kantor.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-building-circle-check w-5 text-center"></i>
                        <span>Cabang / Kantor</span>
                    </a>
                    @endif
                </nav>
            </div>
        </div>

        <!-- User Profile & Logout -->
        <div class="p-4 border-t border-slate-800 bg-slate-950 flex items-center justify-between">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center font-bold text-white text-sm shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="truncate">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider {{ auth()->user()->isAdmin() ? 'bg-purple-900 text-purple-200' : 'bg-blue-900 text-blue-200' }}">
                        {{ auth()->user()->role }}
                    </span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-400 transition-colors">
                    <i class="fa-solid fa-arrow-right-from-bracket text-lg"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 lg:pl-64 flex flex-col min-h-screen">
        
        <!-- Topbar -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-30 shadow-xs no-print">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="p-2 text-slate-600 hover:text-slate-900 lg:hidden">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>

                <!-- Branch Context Indicator / Switcher -->
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-slate-400 uppercase hidden sm:inline">Cabang Aktif:</span>
                    @if(auth()->user()->isAdmin())
                        <form action="{{ route('switch.branch') }}" method="POST" class="inline">
                            @csrf
                            <select name="branch_id" onchange="this.form.submit()"
                                    class="text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-300 rounded-md px-2.5 py-1.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                <option value="all" {{ !session('active_branch_id') ? 'selected' : '' }}>Semua Cabang (Global)</option>
                                @foreach($allBranches ?? [] as $b)
                                    <option value="{{ $b->id }}" {{ session('active_branch_id') == $b->id ? 'selected' : '' }}>
                                        {{ $b->nama }} ({{ $b->kode_cabang }})
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                            <i class="fa-solid fa-location-dot"></i>
                            {{ $currentBranch ? $currentBranch->nama : 'Cabang Utama' }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Topbar Quick Action -->
            <div class="flex items-center gap-3">
                <a href="{{ route('penjualan.create') }}"
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-semibold px-3.5 py-2 rounded-lg shadow-sm shadow-emerald-600/20 transition">
                    <i class="fa-solid fa-barcode"></i>
                    <span class="hidden sm:inline">Kasir / Scan</span>
                </a>
            </div>
        </header>

        <!-- Flash Message Alerts -->
        <main class="flex-1 p-4 sm:p-6 max-w-7xl w-full mx-auto">
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl shadow-xs no-print">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg shrink-0"></i>
                    <div class="flex-1 text-sm font-medium">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl shadow-xs no-print">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg shrink-0"></i>
                    <div class="flex-1 text-sm font-medium">{{ session('error') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl shadow-xs no-print">
                    <div class="flex items-center gap-2 font-semibold text-sm mb-1 text-rose-900">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                        <span>Mohon periksa kesalahan input berikut:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-700">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="py-4 px-6 border-t border-slate-200 text-center text-xs text-slate-500 bg-white no-print">
            &copy; {{ date('Y') }} O-Stock Laravel Serverless Multi-Branch System.
        </footer>
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>
