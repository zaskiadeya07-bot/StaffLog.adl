<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Devisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DivisiController extends Controller
{
    public function index()
    {
        $divisis = Devisi::orderBy('id_devisi')->get();
        return view('admin.Divisi', compact('divisis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_devisi' => 'required|string|max:50|unique:devisi,nama_devisi',
        ], [
            'nama_devisi.required' => 'Nama divisi wajib diisi.',
            'nama_devisi.max'      => 'Nama divisi maksimal 50 karakter.',
            'nama_devisi.unique'   => 'Divisi "' . $request->nama_devisi . '" sudah ada.',
        ]);

        try {
            Devisi::create($validated);
            return redirect()->route('admin.divisi')->with('success', 'Divisi berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Gagal tambah divisi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan divisi.')->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $divisi = Devisi::findOrFail($id);

        $validated = $request->validate([
            'nama_devisi' => 'required|string|max:50|unique:devisi,nama_devisi,' . $id . ',id_devisi',
        ], [
            'nama_devisi.required' => 'Nama divisi wajib diisi.',
            'nama_devisi.max'      => 'Nama divisi maksimal 50 karakter.',
            'nama_devisi.unique'   => 'Divisi "' . $request->nama_devisi . '" sudah ada.',
        ]);

        try {
            $divisi->update($validated);
            return redirect()->route('admin.divisi')->with('success', 'Divisi berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Gagal update divisi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui divisi.')->withInput();
        }
    }

    public function destroy($id)
    {
        $divisi = Devisi::findOrFail($id);

        if ($divisi->pengguna()->exists()) {
            return redirect()->route('admin.divisi')
                ->with('error', 'Divisi "' . $divisi->nama_devisi . '" masih memiliki karyawan. Tidak bisa dihapus.');
        }

        try {
            $divisi->delete();
            return redirect()->route('admin.divisi')->with('success', 'Divisi berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Gagal hapus divisi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus divisi.');
        }
    }
}
