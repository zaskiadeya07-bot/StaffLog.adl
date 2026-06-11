<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Presensi;
use App\Models\Perizinan;
use App\Models\MasterData;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $idPengguna = $request->session()->get('pengguna_id');

        $pengguna = Pengguna::with('devisi')->find($idPengguna);
        if (!$pengguna) {
            return redirect()->route('login')->with('error', 'Sesi tidak valid.');
        }

        $today = Carbon::today();
        $bulanIni = $today->month;
        $tahunIni = $today->year;

        $presensiHariIni = Presensi::where('id_pengguna', $idPengguna)
            ->whereDate('tanggal', $today)
            ->first();

        $masterData = MasterData::first();

        $sisaCuti = 0;
        if ($masterData && $pengguna->tgl_mulai_kerja) {
            $cutiTahunanTotal = $masterData->jatah_cuti_tahunan ?? 0;

            $cutiTerpakai = Perizinan::where('id_pengguna_pengaju', $idPengguna)
                ->where('jenis_izin', 'cuti_tahunan')
                ->where('status_approval', 'disetujui')
                ->whereYear('tgl_mulai', $tahunIni)
                ->count();

            $sisaCuti = max(0, $cutiTahunanTotal - $cutiTerpakai);
        }

        $stats = Presensi::where('id_pengguna', $idPengguna)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->selectRaw("
                SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = 'terlambat' THEN 1 ELSE 0 END) as terlambat,
                SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status = 'alpha' THEN 1 ELSE 0 END) as alpha
            ")
            ->first();

        $riwayatIzin = Perizinan::where('id_pengguna_pengaju', $idPengguna)
            ->with('adminValidator')
            ->orderBy('tgl_pengajuan', 'desc')
            ->take(5)
            ->get();

        return view('karyawan.Dashboard', compact(
            'pengguna',
            'presensiHariIni',
            'sisaCuti',
            'stats',
            'riwayatIzin',
            'today'
        ));
    }
}
