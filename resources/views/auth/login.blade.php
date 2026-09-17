<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - O-Stock Warehouse Stock Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="h-full flex items-center justify-center p-4">

    <div class="max-w-md w-full">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-600 text-white text-3xl shadow-lg shadow-indigo-600/30 mb-4">
                <i class="fa-solid fa-warehouse"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">O-STOCK WAREHOUSE</h1>
            <p class="text-sm text-slate-500 mt-1">Sistem Manajemen Stok & Inventori Gudang</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200 border border-slate-200/80 p-8">
            <h2 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                <i class="fa-solid fa-lock text-indigo-600"></i>
                <span>Masuk ke Sistem Gudang</span>
            </h2>

            @if(session('success'))
                <div class="mb-4 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium rounded-lg flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->has('login'))
                <div class="mb-4 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium rounded-lg flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                    <span>{{ $errors->first('login') }}</span>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="login" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Username / Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-regular fa-user"></i>
                        </span>
                        <input type="text" name="login" id="login" value="{{ old('login') }}" required autofocus
                               placeholder="contoh: admin atau operator"
                               class="w-full pl-10 pr-3.5 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 outline-none transition">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <input type="password" name="password" id="password" required
                               placeholder="••••••••"
                               class="w-full pl-10 pr-3.5 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 outline-none transition">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <button type="submit"
                        class="w-full mt-2 py-3 px-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-bold rounded-lg shadow-md shadow-indigo-600/20 transition duration-150">
                    Masuk ke Sistem Gudang
                </button>
            </form>
            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500 mb-3">Belum memiliki akun gudang?</p>
                <a href="{{ route('register') }}"
                   class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-300 text-indigo-700 text-xs font-bold rounded-lg transition duration-150">
                    <i class="fa-solid fa-user-plus text-indigo-600"></i>
                    <span>Buat Akun Baru</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
