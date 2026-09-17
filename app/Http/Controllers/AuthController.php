<?php

namespace App\Http\Controllers;

use App\Models\Kantor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|alpha_dash|max:50|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username gudang wajib diisi.',
            'username.unique' => 'Username tersebut sudah terdaftar.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip, dan garis bawah.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email tersebut sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Pendaftaran mandiri akun baru selalu diberikan peran 'cabang' (Operator Gudang dengan akses terbatas)
        $user = User::create([
            'name' => $validated['name'],
            'username' => strtolower($validated['username']),
            'email' => strtolower($validated['email']),
            'role' => 'cabang',
            'password' => Hash::make($validated['password']),
            'status' => 'aktif',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Akun Operator Gudang berhasil dibuat! Selamat datang di O-Stock Warehouse, ' . $user->name . '.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $fieldType = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $attempt = [
            $fieldType => $credentials['login'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($attempt, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->role === 'cabang' && $user->id_cabang) {
                session(['active_branch_id' => $user->id_cabang]);
            }

            return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        return back()->withErrors([
            'login' => 'Username/email atau password yang Anda masukkan salah.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    public function switchBranch(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Hanya administrator yang dapat mengubah konteks cabang.');
        }

        $branchId = $request->input('branch_id');
        if ($branchId === 'all') {
            session()->forget('active_branch_id');
        } else {
            $branch = Kantor::findOrFail($branchId);
            session(['active_branch_id' => $branch->id]);
        }

        return back()->with('success', 'Konteks cabang berhasil diubah.');
    }
}
