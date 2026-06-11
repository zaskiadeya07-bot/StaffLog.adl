<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Rules\CurrentPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GantiPasswordController extends Controller
{
    public function index(Request $request)
    {
        $pengguna = Pengguna::find($request->session()->get('pengguna_id'));
        return view('admin.GantiPassword', compact('pengguna'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'password_lama' => ['required', new CurrentPassword($request->session()->get('pengguna_id'))],
            'password_baru' => ['required', 'min:6', 'confirmed'],
        ], [
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
            'password_baru.min'       => 'Password baru minimal 6 karakter.',
            'password_lama.required'  => 'Password saat ini wajib diisi.',
            'password_baru.required'  => 'Password baru wajib diisi.',
        ]);

        $pengguna = Pengguna::find($request->session()->get('pengguna_id'));

        $pengguna->update([
            'password' => Hash::make($request->password_baru),
        ]);

        return back()->with('success', 'Password berhasil diubah!');
    }
}
