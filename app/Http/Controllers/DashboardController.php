<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\Pengguna;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Tampilan dashboard berdasarkan role (admin/karyawan)
     */
    public function index()
    {
        // Cek apakah user sudah login
        if (!session()->has('pengguna_id')) {
            return redirect()->route('login');
        }

        $penggunaId = session('pengguna_id');
        $pengguna = Pengguna::find($penggunaId);
        
        // Jika role admin, tampilkan admin dashboard
        if ($pengguna && $pengguna->role == 'admin') {
            return redirect()->route('admin.rekap-karyawan');
        }
        
        // Jika role karyawan, tampilkan karyawan dashboard
        if ($pengguna && $pengguna->role == 'karyawan') {
            // Hitung statistik absensi bulan ini
            $bulanIni = date('m');
            $tahunIni = date('Y');
            
            $statHadir = Presensi::where('id_pengguna', $penggunaId)
                ->whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni)
                ->where('status', 'hadir')
                ->count();
                
            $statTerlambat = Presensi::where('id_pengguna', $penggunaId)
                ->whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni)
                ->where('status', 'terlambat')
                ->count();
                
            $statIzin = Presensi::where('id_pengguna', $penggunaId)
                ->whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni)
                ->where('status', 'izin')
                ->count();
                
            $totalAbsensi = $statHadir + $statTerlambat + $statIzin;
            
            return view('karyawan.dashboard', compact(
                'pengguna', 
                'statHadir', 
                'statTerlambat', 
                'statIzin', 
                'totalAbsensi'
            ));
        }
        
        // Jika tidak ada role, logout
        session()->flush();
        return redirect()->route('login')->with('error', 'Role tidak dikenali');
    }
}