<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Rules\CurrentPassword;
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
            'password_lama' => ['required', new CurrentPassword(session('pengguna_id'))],
            'password_baru' => ['required', 'min:6', 'confirmed'],
        ], [
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
            'password_baru.min'       => 'Password baru minimal 6 karakter.',
            'password_lama.required'  => 'Password saat ini wajib diisi.',
            'password_baru.required'  => 'Password baru wajib diisi.',
        ]);

        $pengguna = Pengguna::find(session('pengguna_id'));

        $pengguna->update([
            'password' => Hash::make($request->password_baru),
        ]);

        return back()->with('password_success', 'Password berhasil diubah!');
    }

    public function updateProfile(Request $request)
    {
        $pengguna = Pengguna::findOrFail(session('pengguna_id'));

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'nomor_hp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
        ]);

        $pengguna->update($validated);

        return back()->with('success', 'Profil berhasil diupdate!');
    }
}
