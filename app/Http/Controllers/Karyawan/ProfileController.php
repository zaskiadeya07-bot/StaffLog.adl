<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Karyawan\UpdateProfileRequest;
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

    public function updateProfile(UpdateProfileRequest $request)
    {
        $pengguna = Pengguna::findOrFail($request->session()->get('pengguna_id'));

        $data = $request->validated();

        if ($request->filled('password_baru')) {
            $data['password'] = Hash::make($request->password_baru);
        }

        unset($data['password_lama'], $data['password_baru'], $data['password_baru_confirmation']);

        $pengguna->update($data);

        $message = $request->filled('password_baru') ? 'Profil dan password berhasil diperbarui!' : 'Profil berhasil diperbarui!';
        return back()->with('success', $message);
    }
}
