<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;

class RekapKaryawanController extends Controller
{
    public function index()
    {
        $karyawan = Pengguna::where('role', 'karyawan')
            ->leftJoin('devisi', 'pengguna.divisi', '=', 'devisi.id_devisi')
            ->select('pengguna.*', 'devisi.nama_devisi as divisi_nama')
            ->orderBy('pengguna.nama_lengkap', 'asc')
            ->get();

        return view('admin.RekapKaryawan', compact('karyawan'));
    }
}
