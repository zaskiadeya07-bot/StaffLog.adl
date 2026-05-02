<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function index()
    {
        return view('login'); // Pastikan file blade Anda ada di resources/views/auth/login.blade.php
    }

    /**
     * Menangani upaya autentikasi.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'name' => ['required'],
            'password' => ['required'],
        ]);

        // 2. Coba login (Attempt)
        // 'remember' diambil dari checkbox di form
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            // 3. Regenerasi session untuk keamanan (mencegah session fixation)
            $request->session()->regenerate();

            // 4. Redirect ke halaman yang dituju sebelumnya atau ke dashboard
            return redirect()->intended('dashboard');
        }

        // 5. Jika gagal, kembalikan ke login dengan pesan error
        return back()->withErrors([
            'name' => 'Name atau password yang Anda masukkan salah.',
        ])->onlyInput('name');
    }

    /**
     * Menangani Logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}