<?php

namespace App\Http\Controllers\Karyawan;

use App\Helpers\BulanHelper;
use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Illuminate\Http\Request;

class RekapAbsenController extends Controller
{
    public function index(Request $request)
    {
        $idPengguna = $request->session()->get('pengguna_id');
        if (!$idPengguna) {
            return redirect()->route('login')->with('error', 'Sesi tidak valid.');
        }

        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $bulanNama = BulanHelper::getNamaBulanByAngka((int)$bulan);

        $presensi = Presensi::where('id_pengguna', $idPengguna)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        $statHadir = $presensi->where('status', 'hadir')->count();
        $statTerlambat = $presensi->where('status', 'terlambat')->count();
        $statIzin = $presensi->where('status', 'izin')->count();
        $statAlpha = $presensi->where('status', 'alpha')->count();

        return view('karyawan.RekapAbsen', compact(
            'presensi', 'bulan', 'tahun',
            'bulanNama', 'statHadir', 'statTerlambat',
            'statIzin', 'statAlpha'
        ));
    }
}
