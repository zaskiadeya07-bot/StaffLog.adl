<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Pengguna;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function index()
    {
        // Jika sudah login, redirect langsung sesuai role
        if (Session::has('pengguna_id')) {
            return $this->redirectByRole(Session::get('pengguna_role'));
        }

        return view('auth.login');
    }

    /**
     * Menangani upaya autentikasi.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username harus diisi.',
            'password.required' => 'Password harus diisi.',
        ]);

        // Cari pengguna berdasarkan username
        $pengguna = Pengguna::where('username', $request->username)->first();

        // Cek pengguna ada dan password cocok
        if (!$pengguna || !Hash::check($request->password, $pengguna->password)) {
            return back()
                ->withErrors(['username' => 'Username atau password yang Anda masukkan salah.'])
                ->onlyInput('username');
        }

        // Regenerasi session untuk cegah session fixation
        $request->session()->regenerate();

        // Simpan data sesi
        Session::put('pengguna_id',       $pengguna->id_pengguna);
        Session::put('pengguna_nama',     $pengguna->nama_lengkap);
        Session::put('pengguna_username', $pengguna->username);
        Session::put('pengguna_role',     $pengguna->role);
        Session::put('pengguna_divisi',   $pengguna->divisi);

        // Redirect otomatis sesuai role
        return $this->redirectByRole($pengguna->role);
    }

    /**
     * Menangani Logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Session::flush();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil keluar.');
    }

    /**
     * Redirect ke halaman sesuai role.
     */
    private function redirectByRole(string $role): RedirectResponse
    {
        return match ($role) {
            'admin'    => redirect()->route('admin.rekap-karyawan'),
            'karyawan' => redirect()->route('karyawan.dashboard'),
            default    => redirect()->route('login'),
        };
    }
}
