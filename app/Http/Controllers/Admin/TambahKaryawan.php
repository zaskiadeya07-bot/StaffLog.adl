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
        return view('admin.TambahKaryawan', compact('divisis'));
    }

    public function store(StoreKaryawanRequest $request)
    {
        $idKaryawan = $this->karyawanService->generateIdKaryawan();

        Pengguna::create([
            'nama_lengkap'    => $request->nama_lengkap,
            'id_karyawan'     => $idKaryawan,
            'username'        => $request->username,
            'alamat'          => $request->alamat,
            'nomor_hp'        => $request->nomor_hp,
            'tgl_mulai_kerja' => $request->tgl_mulai_kerja ?? now()->toDateString(),
            'divisi'          => $request->divisi,
            'password'        => Hash::make($request->password),
            'role'            => 'karyawan',
        ]);

        return redirect()
            ->route('admin.rekap-karyawan')
            ->with('success', 'Karyawan ' . $request->nama_lengkap . ' berhasil ditambahkan!');
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

        $updateData = $request->only(['nama_lengkap', 'username', 'alamat', 'nomor_hp', 'divisi']);

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
        $karyawan = Pengguna::findOrFail($id);
        $karyawan->update(['status' => 'nonaktif']);

        return redirect()
            ->route('admin.rekap-karyawan')
            ->with('success', 'Karyawan ' . $karyawan->nama_lengkap . ' berhasil dinonaktifkan.');
    }

    public function activate($id)
    {
        $karyawan = Pengguna::findOrFail($id);
        $karyawan->update(['status' => 'aktif']);

        return redirect()
            ->route('admin.rekap-karyawan')
            ->with('success', 'Karyawan ' . $karyawan->nama_lengkap . ' berhasil diaktifkan kembali.');
    }
}
