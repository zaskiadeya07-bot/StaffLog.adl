<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\Pengguna;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        if ($request->session()->has('pengguna_id')) {
            return $this->redirectByRole($request->session()->get('pengguna_role'));
        }

        return view('auth.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username harus diisi.',
            'password.required' => 'Password harus diisi.',
        ]);

        $pengguna = Pengguna::where('username', $request->username)->first();

        if (!$pengguna || !Hash::check($request->password, $pengguna->password)) {
            return back()
                ->withErrors(['username' => 'Username atau password yang Anda masukkan salah.'])
                ->onlyInput('username');
        }

        $request->session()->regenerate();

        $request->session()->put('pengguna_id',       $pengguna->id_pengguna);
        $request->session()->put('pengguna_nama',     $pengguna->nama_lengkap);
        $request->session()->put('pengguna_username', $pengguna->username);
        $request->session()->put('pengguna_role',     $pengguna->role);
        $request->session()->put('pengguna_divisi',   $pengguna->divisi);

        return $this->redirectByRole($pengguna->role);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->flush();
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
