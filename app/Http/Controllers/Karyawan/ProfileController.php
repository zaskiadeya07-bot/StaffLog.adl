<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $pengguna = Pengguna::with('devisi')->find(session('pengguna_id'));
        return view('karyawan.Profile', compact('pengguna'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_lama'              => ['required'],
            'password_baru'              => ['required', 'min:6', 'confirmed'],
        ], [
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
            'password_baru.min'       => 'Password baru minimal 6 karakter.',
            'password_lama.required'  => 'Password saat ini wajib diisi.',
            'password_baru.required'  => 'Password baru wajib diisi.',
        ]);

        $pengguna = Pengguna::find(session('pengguna_id'));

        if (!Hash::check($request->password_lama, $pengguna->password)) {
            return back()->with('password_error', 'Password saat ini tidak sesuai.');
        }

        $pengguna->update([
            'password' => Hash::make($request->password_baru),
        ]);

        return back()->with('password_success', 'Password berhasil diubah!');
    }
}
