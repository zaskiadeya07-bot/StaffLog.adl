<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;

class RekapKaryawanController extends Controller
{
    public function index()
    {
        $filter = request('filter', 'aktif');

        $karyawan = Pengguna::where('role', 'karyawan')
            ->leftJoin('devisi', 'pengguna.divisi', '=', 'devisi.id_devisi')
            ->select('pengguna.*', 'devisi.nama_devisi as divisi_nama')
            ->when($filter === 'aktif', fn($q) => $q->where('pengguna.status', 'aktif'))
            ->when($filter === 'nonaktif', fn($q) => $q->where('pengguna.status', 'nonaktif'))
            ->orderBy('pengguna.nama_lengkap', 'asc')
            ->get();

        return view('admin.RekapKaryawan', compact('karyawan', 'filter'));
    }
}
