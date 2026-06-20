<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Presensi;
use App\Models\Perizinan;
use App\Models\MasterData;
use App\Services\PerizinanService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct(
        protected PerizinanService $perizinanService
    ) {}

    protected function isWeekend($date): bool
    {
        return in_array(Carbon::parse($date)->format('l'), ['Saturday', 'Sunday']);
    }

    protected function autoAlphaHariIni($idPengguna): void
    {
        $now = Carbon::now();
        $today = $now->toDateString();

        if ($this->isWeekend($today)) return;

        $pengaturan = MasterData::first();
        if (!$pengaturan || !$pengaturan->jam_pulang_std) return;

        $jamPulang = Carbon::parse($pengaturan->jam_pulang_std);

        if ($now->lessThan($jamPulang)) return;

        $sudahAda = Presensi::where('id_pengguna', $idPengguna)
            ->whereDate('tanggal', $today)
            ->exists();

        if ($sudahAda) {
            $alphaCheckout = Presensi::where('id_pengguna', $idPengguna)
                ->whereDate('tanggal', $today)
                ->whereNotNull('check_in')
                ->whereNull('check_out')
                ->where('status', '!=', 'alpha')
                ->first();

            if ($alphaCheckout) {
                $alphaCheckout->update([
                    'status' => 'alpha',
                    'catatan_keterlambatan' => 'Tidak melakukan check out',
                ]);
            }
            return;
        }

        $adaIzin = Perizinan::where('id_pengguna_pengaju', $idPengguna)
            ->where('status_approval', 'disetujui')
            ->whereDate('tgl_mulai', '<=', $today)
            ->whereDate('tgl_selesai', '>=', $today)
            ->exists();

        if ($adaIzin) return;

        Presensi::create([
            'id_pengguna' => $idPengguna,
            'id_pengaturan' => $pengaturan->id_pengaturan,
            'tanggal' => $today,
            'status' => 'alpha',
            'catatan_keterlambatan' => 'Tidak hadir tanpa keterangan',
        ]);
    }

    public function index(Request $request)
    {
        $idPengguna = $request->session()->get('pengguna_id');

        $pengguna = Pengguna::with('devisi')->find($idPengguna);
        if (!$pengguna) {
            return redirect()->route('login')->with('error', 'Sesi tidak valid.');
        }

        $this->autoAlphaHariIni($idPengguna);

        $today = Carbon::today();
        $bulanIni = $today->month;
        $tahunIni = $today->year;

        $presensiHariIni = Presensi::where('id_pengguna', $idPengguna)
            ->whereDate('tanggal', $today)
            ->first();

        $sisaCuti = $this->perizinanService->hitungSisaCuti($idPengguna, $bulanIni, $tahunIni);

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
