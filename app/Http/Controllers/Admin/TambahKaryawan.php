<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Devisi;
use App\Models\Pengguna;
use App\Models\Perizinan;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TambahKaryawan extends Controller
{
    public function index()
    {
        $divisis = Devisi::select('id_devisi as id', 'nama_devisi')->get();
        return view('admin.TambahKaryawan', compact('divisis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'id_karyawan' => 'required|string|max:20|unique:pengguna,id_karyawan',
            'username' => 'required|string|max:50|unique:pengguna,username',
            'alamat' => 'nullable|string',
            'nomor_hp' => 'nullable|string|max:15',
            'tgl_mulai_kerja' => 'nullable|date',
            'divisi' => 'required|exists:devisi,id_devisi',
            'password' => 'required|min:6|confirmed',
        ]);

        try {
            Pengguna::create([
                'nama_lengkap' => $validated['nama_lengkap'],
                'id_karyawan' => $validated['id_karyawan'],
                'username' => $validated['username'],
                'alamat' => $validated['alamat'],
                'nomor_hp' => $validated['nomor_hp'],
                'tgl_mulai_kerja' => $validated['tgl_mulai_kerja'],
                'divisi' => $validated['divisi'],
                'password' => Hash::make($validated['password']),
                'role' => 'karyawan',
            ]);

            return redirect()
                ->route('admin.rekap-karyawan')
                ->with('success', 'Karyawan ' . $validated['nama_lengkap'] . ' berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menambahkan karyawan. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function edit($id)
    {
        $karyawan = Pengguna::find($id);

        if (!$karyawan) {
            return redirect()
                ->route('admin.rekap-karyawan')
                ->with('error', 'Karyawan tidak ditemukan');
        }

        $divisis = Devisi::select('id_devisi as id', 'nama_devisi')->get();
        return view('admin.EditKaryawan', compact('karyawan', 'divisis'));
    }

    public function update(Request $request, $id)
    {
        $karyawan = Pengguna::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'id_karyawan' => 'required|string|max:20|unique:pengguna,id_karyawan,' . $id . ',id_pengguna',
            'username' => 'required|string|max:50|unique:pengguna,username,' . $id . ',id_pengguna',
            'alamat' => 'nullable|string',
            'nomor_hp' => 'nullable|string|max:15',
            'tgl_mulai_kerja' => 'nullable|date',
            'divisi' => 'required|exists:devisi,id_devisi',
        ]);

        try {
            $updateData = [
                'nama_lengkap' => $validated['nama_lengkap'],
                'id_karyawan' => $validated['id_karyawan'],
                'username' => $validated['username'],
                'alamat' => $validated['alamat'],
                'nomor_hp' => $validated['nomor_hp'],
                'tgl_mulai_kerja' => $validated['tgl_mulai_kerja'],
                'divisi' => $validated['divisi'],
            ];

            if ($request->filled('password')) {
                $request->validate(['password' => 'min:6|confirmed']);
                $updateData['password'] = Hash::make($request->password);
            }

            $karyawan->update($updateData);

            return redirect()
                ->route('admin.rekap-karyawan')
                ->with('success', 'Data karyawan berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal mengupdate karyawan. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $karyawan = Pengguna::findOrFail($id);

            Perizinan::where('id_pengguna_pengaju', $id)
                ->orWhere('id_admin_validator', $id)
                ->delete();

            Presensi::where('id_pengguna', $id)->delete();

            $karyawan->delete();

            return redirect()
                ->route('admin.rekap-karyawan')
                ->with('success', 'Karyawan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus karyawan. Silakan coba lagi.');
        }
    }
}
