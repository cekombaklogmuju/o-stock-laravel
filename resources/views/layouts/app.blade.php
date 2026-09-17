<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'O-Stock') }} - @yield('title', 'Warehouse Stock Management')</title>

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
                    <i class="fa-solid fa-warehouse"></i>
                </div>
                <div class="flex flex-col">
                    <span class="font-black text-white text-base tracking-wider uppercase">O-STOCK</span>
                    <span class="text-[10px] text-indigo-400 font-medium tracking-tight">Warehouse Management</span>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="text-slate-400 hover:text-white lg:hidden">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <!-- Navigation Menu -->
        <div class="flex-1 overflow-y-auto px-4 py-6 space-y-6">
            <!-- Utama -->
            <div>
                <p class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Utama</p>
                <nav class="space-y-1">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                        <span>Dashboard Gudang</span>
                    </a>
                </nav>
            </div>

            <!-- Operasional Gudang -->
            <div>
                <p class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Operasional Gudang</p>
                <nav class="space-y-1">
                    <a href="{{ route('barang-masuk.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('barang-masuk.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-truck-ramp-box w-5 text-center text-emerald-400"></i>
                        <span>Barang Masuk (Receiving)</span>
                    </a>
                    <a href="{{ route('barang-keluar.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('barang-keluar.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-dolly w-5 text-center text-amber-400"></i>
                        <span>Barang Keluar (Dispatch)</span>
                    </a>
                    <a href="{{ route('stock-opname.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('stock-opname.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-clipboard-check w-5 text-center text-cyan-400"></i>
                        <span>Stock Opname (Audit)</span>
                    </a>
                    <a href="{{ route('kartu_stok.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('kartu_stok.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-book-open-reader w-5 text-center text-indigo-400"></i>
                        <span>Buku Kartu Stok</span>
                    </a>
                </nav>
            </div>

            <!-- Master Data Gudang & Rak -->
            <div>
                <p class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Master & Lokasi</p>
                <nav class="space-y-1">
                    <a href="{{ route('produk.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('produk.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-boxes-stacked w-5 text-center"></i>
                        <span>Data Barang & Rak</span>
                    </a>
                    <a href="{{ route('kategori.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('kategori.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-tags w-5 text-center"></i>
                        <span>Kategori Barang</span>
                    </a>
                    <a href="{{ route('supplier.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('supplier.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-truck-field w-5 text-center"></i>
                        <span>Supplier</span>
                    </a>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('kantor.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('kantor.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-building-circle-check w-5 text-center"></i>
                        <span>Lokasi Gudang</span>
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
                        {{ auth()->user()->role === 'admin' ? 'Kepala Gudang (Admin)' : 'Operator Gudang (Terbatas)' }}
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

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                        <i class="fa-solid fa-warehouse text-indigo-500"></i>
                        Gudang Utama (Central Warehouse)
                    </span>
                </div>
            </div>

            <!-- Topbar Quick Action -->
            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('barang-masuk.create') }}"
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-semibold px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-lg shadow-sm transition">
                    <i class="fa-solid fa-plus"></i>
                    <span>Barang Masuk</span>
                </a>
                <a href="{{ route('barang-keluar.create') }}"
                   class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white text-xs sm:text-sm font-semibold px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-lg shadow-sm transition">
                    <i class="fa-solid fa-minus"></i>
                    <span>Barang Keluar</span>
                </a>
                <a href="{{ route('stock-opname.create') }}"
                   class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white text-xs sm:text-sm font-semibold px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-lg shadow-sm transition hidden md:inline-flex">
                    <i class="fa-solid fa-clipboard-check"></i>
                    <span>Opname</span>
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
            &copy; {{ date('Y') }} O-Stock Warehouse Management System.
        </footer>
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>
