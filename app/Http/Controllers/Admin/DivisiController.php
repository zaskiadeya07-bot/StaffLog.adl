<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDivisiRequest;
use App\Http\Requests\Admin\UpdateDivisiRequest;
use App\Models\Devisi;

class DivisiController extends Controller
{
    public function index()
    {
        $divisis = Devisi::orderBy('id_devisi')->get();
        return view('admin.Divisi', compact('divisis'));
    }

    public function store(StoreDivisiRequest $request)
    {
        Devisi::create($request->validated());

        return redirect()->route('admin.divisi')->with('success', 'Divisi berhasil ditambahkan!');
    }

    public function update(UpdateDivisiRequest $request, $id)
    {
        $divisi = Devisi::findOrFail($id);
        $divisi->update($request->validated());

        return redirect()->route('admin.divisi')->with('success', 'Divisi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $divisi = Devisi::findOrFail($id);

        if ($divisi->pengguna()->exists()) {
            return redirect()->route('admin.divisi')
                ->with('error', 'Divisi "' . $divisi->nama_devisi . '" masih memiliki karyawan. Tidak bisa dihapus.');
        }

        $divisi->delete();

        return redirect()->route('admin.divisi')->with('success', 'Divisi berhasil dihapus!');
    }
}
