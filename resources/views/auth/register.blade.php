<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru - O-Stock Warehouse</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="min-h-full flex items-center justify-center p-4 py-8">

    <div class="max-w-md w-full">
        <!-- Logo & Title -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-600 text-white text-3xl shadow-lg shadow-indigo-600/30 mb-3">
                <i class="fa-solid fa-warehouse"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">O-STOCK WAREHOUSE</h1>
            <p class="text-sm text-slate-500 mt-1">Sistem Manajemen Stok & Inventori Gudang</p>
        </div>

        <!-- Registration Card -->
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200 border border-slate-200/80 p-8">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-indigo-600"></i>
                    <span>Buat Akun Baru</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">Daftarkan akun untuk mulai mengelola stok dan barang gudang.</p>
            </div>

            @if($errors->any())
                <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium rounded-lg">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                        <span>Terdapat kesalahan pengisian formulir:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-[11px] pl-1 text-rose-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-regular fa-id-badge"></i>
                        </span>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                               placeholder="contoh: Budi Santoso"
                               class="w-full pl-10 pr-3.5 py-2.5 text-sm bg-slate-50 border @error('name') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 outline-none transition">
                    </div>
                    @error('name')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username & Peran (2 cols) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Username</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                                <i class="fa-regular fa-user"></i>
                            </span>
                            <input type="text" name="username" id="username" value="{{ old('username') }}" required
                                   placeholder="budi_gudang"
                                   class="w-full pl-10 pr-3.5 py-2.5 text-sm bg-slate-50 border @error('username') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 outline-none transition">
                        </div>
                        @error('username')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Peran Gudang</label>
                        <div class="relative flex items-center justify-between px-3 py-2 bg-slate-100 border border-slate-300 rounded-lg text-sm text-slate-700 h-[42px]">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-dolly text-indigo-600 text-xs"></i>
                                <span class="font-bold text-slate-800 text-xs">Operator Gudang</span>
                            </div>
                            <span class="text-[10px] font-bold bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full border border-amber-200">
                                Akses Terbatas
                            </span>
                        </div>
                        <input type="hidden" name="role" value="cabang">
                    </div>
                </div>

                <!-- Info Akses Terbatas Operator Gudang -->
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-2.5 text-xs text-amber-900">
                    <i class="fa-solid fa-shield-halved text-amber-600 mt-0.5 shrink-0 text-sm"></i>
                    <div class="leading-relaxed">
                        <p class="font-bold text-amber-950">Akses Terbatas: Operator Gudang</p>
                        <p class="text-[11px] text-amber-800 mt-0.5">
                            Akun baru difokuskan untuk tugas operasional gudang (scan barcode, terima barang masuk, pengeluaran barang, audit stock opname, dan kartu stok). Hak akses Kepala Gudang/Admin dikelola secara terpisah demi keamanan inventori.
                        </p>
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               placeholder="budi@perusahaan.com"
                               class="w-full pl-10 pr-3.5 py-2.5 text-sm bg-slate-50 border @error('email') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 outline-none transition">
                    </div>
                    @error('email')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password & Konfirmasi -->
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <input type="password" name="password" id="password" required minlength="6"
                               placeholder="Minimal 6 karakter"
                               class="w-full pl-10 pr-3.5 py-2.5 text-sm bg-slate-50 border @error('password') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 outline-none transition">
                    </div>
                    @error('password')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Konfirmasi Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-check-double"></i>
                        </span>
                        <input type="password" name="password_confirmation" id="password_confirmation" required minlength="6"
                               placeholder="Ulangi password di atas"
                               class="w-full pl-10 pr-3.5 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 outline-none transition">
                    </div>
                </div>

                <button type="submit"
                        class="w-full mt-3 py-3 px-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-bold rounded-lg shadow-md shadow-indigo-600/20 transition duration-150 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    <span>Daftar & Masuk ke Sistem</span>
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500 mb-2">Sudah memiliki akun gudang?</p>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Kembali ke Halaman Login</span>
                </a>
            </div>
        </div>
    </div>

</body>
</html>
