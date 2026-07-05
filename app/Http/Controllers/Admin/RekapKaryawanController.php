<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Devisi;
use Illuminate\Http\Request;

class RekapKaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengguna::where('role', 'karyawan')
            ->leftJoin('devisi', 'pengguna.divisi', '=', 'devisi.id_devisi')
            ->select('pengguna.*', 'devisi.nama_devisi as divisi_nama');

        // Filter status
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('pengguna.status', $request->status);
        }

        // Filter divisi
        if ($request->filled('divisi')) {
            $query->where('pengguna.divisi', $request->divisi);
        }

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pengguna.nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('pengguna.username', 'like', "%{$search}%")
                  ->orWhere('pengguna.id_karyawan', 'like', "%{$search}%")
                  ->orWhere('pengguna.nomor_hp', 'like', "%{$search}%")
                  ->orWhere('devisi.nama_devisi', 'like', "%{$search}%");
            });
        }

        $karyawan = $query->orderBy('pengguna.nama_lengkap', 'asc')
            ->get();

        $divisis = Devisi::select('id_devisi as id', 'nama_devisi')->get();
        $filterStatus = $request->get('status', '');
        $filterDivisi = $request->get('divisi', '');
        $filterSearch = $request->get('search', '');

        return view('admin.RekapKaryawan', compact('karyawan', 'divisis', 'filterStatus', 'filterDivisi', 'filterSearch'));
    }
}
