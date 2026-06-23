<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKaryawanRequest;
use App\Http\Requests\Admin\UpdateKaryawanRequest;
use App\Models\Devisi;
use App\Models\Pengguna;
use App\Services\KaryawanService;
use Illuminate\Support\Facades\Hash;

class TambahKaryawan extends Controller
{
    public function __construct(
        protected KaryawanService $karyawanService
    ) {}

    public function index()
    {
        $divisis = Devisi::select('id_devisi as id', 'nama_devisi')->get();
        $newId = $this->karyawanService->generateIdKaryawan();

        return view('admin.TambahKaryawan', compact('divisis', 'newId'));
    }

    public function store(StoreKaryawanRequest $request)
    {
        $validated = $request->validated();
        $newId = $this->karyawanService->generateIdKaryawan();

        try {
            Pengguna::create([
                'nama_lengkap' => $validated['nama_lengkap'],
                'id_karyawan' => $newId,
                'username' => $validated['username'],
                'alamat' => $validated['alamat'],
                'nomor_hp' => $validated['nomor_hp'],
                'tgl_mulai_kerja' => $validated['tgl_mulai_kerja'],
                'divisi' => $validated['divisi'],
                'password' => Hash::make($validated['password']),
                'role' => 'karyawan',
                'status' => 'aktif',
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
        $karyawan = Pengguna::findOrFail($id);
        $divisis = Devisi::select('id_devisi as id', 'nama_devisi')->get();
        return view('admin.EditKaryawan', compact('karyawan', 'divisis'));
    }

    public function update(UpdateKaryawanRequest $request, $id)
    {
        $karyawan = Pengguna::findOrFail($id);

        $validated = $request->validated();

        $updateData = $validated;

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $karyawan->update($updateData);

        return redirect()
            ->route('admin.rekap-karyawan')
            ->with('success', 'Data karyawan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        try {
            $karyawan = Pengguna::findOrFail($id);
            $karyawan->update(['status' => 'nonaktif']);

            return redirect()
                ->route('admin.rekap-karyawan')
                ->with('success', "Karyawan {$karyawan->nama_lengkap} berhasil dinonaktifkan!");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menonaktifkan karyawan. Silakan coba lagi.');
        }
    }

    public function activate($id)
    {
        try {
            $karyawan = Pengguna::findOrFail($id);
            $karyawan->update(['status' => 'aktif']);

            return redirect()
                ->route('admin.rekap-karyawan')
                ->with('success', "Karyawan {$karyawan->nama_lengkap} berhasil diaktifkan kembali!");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal mengaktifkan karyawan. Silakan coba lagi.');
        }
    }
}
