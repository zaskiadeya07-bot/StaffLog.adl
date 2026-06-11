<?php

namespace App\Http\Controllers\Karyawan;

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

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $bulanNama = $namaBulan[(int)$bulan];

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
