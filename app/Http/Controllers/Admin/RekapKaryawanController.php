<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Devisi;

class RekapKaryawanController extends Controller
{
    public function index()
    {
        $filter = request('filter', 'aktif');
        $divisiId = request('divisi');

        $karyawan = Pengguna::where('role', 'karyawan')
            ->leftJoin('devisi', 'pengguna.divisi', '=', 'devisi.id_devisi')
            ->select('pengguna.*', 'devisi.nama_devisi as divisi_nama')
            ->when($filter === 'aktif', fn($q) => $q->where('pengguna.status', 'aktif'))
            ->when($filter === 'nonaktif', fn($q) => $q->where('pengguna.status', 'nonaktif'))
            ->when($divisiId, fn($q) => $q->where('pengguna.divisi', $divisiId))
            ->orderBy('pengguna.nama_lengkap', 'asc')
            ->paginate(50);

        $divisis = Devisi::orderBy('nama_devisi')->get();

        return view('admin.RekapKaryawan', compact('karyawan', 'filter', 'divisis', 'divisiId'));
    }
}
