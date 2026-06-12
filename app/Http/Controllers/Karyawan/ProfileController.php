<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $pengguna = Pengguna::with('devisi')->find($request->session()->get('pengguna_id'));
        return view('karyawan.Profile', compact('pengguna'));
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $pengguna = Pengguna::find($request->session()->get('pengguna_id'));

        $pengguna->update([
            'password' => Hash::make($request->password_baru),
        ]);

        return back()->with('password_success', 'Password berhasil diubah!');
    }

    public function updateProfile(Request $request)
    {
        $pengguna = Pengguna::findOrFail($request->session()->get('pengguna_id'));

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'nomor_hp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
        ]);

        $pengguna->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
