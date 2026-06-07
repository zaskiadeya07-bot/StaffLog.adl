<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Presensi;
use Illuminate\Http\Request;

class DetailRekapKehadiranController extends Controller
{
    public function index(Request $request, $id)
    {
        $karyawan = Pengguna::with('devisi')->find($id);

        if (!$karyawan) {
            return redirect()
                ->route('admin.rekap-karyawan')
                ->with('error', 'Karyawan tidak ditemukan');
        }

        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $bulanNama = $namaBulan[(int)$bulan];

        $presensi = Presensi::where('id_pengguna', $id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        $statHadir = $presensi->where('status', 'hadir')->count();
        $statTerlambat = $presensi->where('status', 'terlambat')->count();
        $statIzin = $presensi->where('status', 'izin')->count();
        $statAlpha = $presensi->where('status', 'alpha')->count();

        return view('admin.DetailRekapKehadiran', compact(
            'karyawan', 'presensi', 'bulan', 'tahun',
            'bulanNama', 'statHadir', 'statTerlambat',
            'statIzin', 'statAlpha'
        ));
    }
}
