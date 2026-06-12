<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;

class GantiPasswordController extends Controller
{
    public function index()
    {
        $pengguna = Pengguna::find(session('pengguna_id'));
        return view('admin.GantiPassword', compact('pengguna'));
    }

    public function update(UpdatePasswordRequest $request)
    {
        $pengguna = Pengguna::find(session('pengguna_id'));
        $pengguna->update([
            'password' => Hash::make($request->password_baru),
        ]);

        return back()->with('success', 'Password berhasil diubah!');
    }
}
