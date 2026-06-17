<?php

namespace App\Services;

use App\Models\MasterData;
use App\Models\Perizinan;
use App\Models\Presensi;
use Carbon\Carbon;

class PerizinanService
{
    public function hitungSisaCuti(int $penggunaId, ?int $bulan = null, ?int $tahun = null): int
    {
        $bulan = $bulan ?: Carbon::now()->month;
        $tahun = $tahun ?: Carbon::now()->year;
        $setting = MasterData::first();
        $jatahCuti = $setting ? $setting->jatah_cuti_bulanan : 1;

        $cutiTerpakai = Perizinan::where('id_pengguna_pengaju', $penggunaId)
            ->where('jenis_izin', 'cuti_tahunan')
            ->where('status_approval', 'disetujui')
            ->whereMonth('tgl_mulai', $bulan)
            ->whereYear('tgl_mulai', $tahun)
            ->get()
            ->sum(function ($item) {
                return $this->hitungDurasi($item->tgl_mulai, $item->tgl_selesai);
            });

        return max(0, $jatahCuti - $cutiTerpakai);
    }

    public function cekOverlap(int $penggunaId, string $tglMulai, string $tglSelesai, ?int $kecualikanId = null): bool
    {
        $query = Perizinan::where('id_pengguna_pengaju', $penggunaId)
            ->whereIn('status_approval', ['pending', 'disetujui']);

        if ($kecualikanId) {
            $query->where('id_izin', '!=', $kecualikanId);
        }

        return $query->where(function ($q) use ($tglMulai, $tglSelesai) {
            $q->whereBetween('tgl_mulai', [$tglMulai, $tglSelesai])
              ->orWhereBetween('tgl_selesai', [$tglMulai, $tglSelesai])
              ->orWhere(function ($q2) use ($tglMulai, $tglSelesai) {
                  $q2->where('tgl_mulai', '<=', $tglMulai)
                     ->where('tgl_selesai', '>=', $tglSelesai);
              });
        })->exists();
    }

    public function konversiJenisIzin(string $jenis): string
    {
        return match ($jenis) {
            'Cuti Tahunan' => 'cuti_tahunan',
            'Cuti Sakit'   => 'cuti_sakit',
            'Izin'         => 'izin',
            default        => 'izin',
        };
    }

    public function getJenisDisplay(string $jenisDb): string
    {
        return match ($jenisDb) {
            'cuti_tahunan' => 'Cuti Tahunan',
            'cuti_sakit'   => 'Cuti Sakit',
            'izin'         => 'Izin',
            default        => $jenisDb,
        };
    }

    public function getStatusDisplay(string $status): string
    {
        return match ($status) {
            'pending'   => 'Menunggu',
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
            'dibatalkan'=> 'Dibatalkan',
            default     => $status,
        };
    }

    public function hitungDurasi(string $tglMulai, string $tglSelesai): int
    {
        $start = new \DateTime($tglMulai);
        $end = new \DateTime($tglSelesai);
        return $start->diff($end)->days + 1;
    }

    public function formatDurasi(string $tglMulai, string $tglSelesai): string
    {
        if (!$tglMulai || !$tglSelesai) return '1 hari';
        return $this->hitungDurasi($tglMulai, $tglSelesai) . ' hari';
    }

    public function buatPresensiIzin(Perizinan $perizinan): void
    {
        $pengaturan = MasterData::first();

        $start = new \DateTime($perizinan->tgl_mulai);
        $end = new \DateTime($perizinan->tgl_selesai);
        $end->modify('+1 day');

        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($start, $interval, $end);

        foreach ($period as $date) {
            $tanggal = $date->format('Y-m-d');

            $existing = Presensi::where('id_pengguna', $perizinan->id_pengguna_pengaju)
                ->where('tanggal', $tanggal)
                ->first();

            if (!$existing) {
                Presensi::create([
                    'id_pengguna'   => $perizinan->id_pengguna_pengaju,
                    'id_pengaturan' => $pengaturan ? $pengaturan->id_pengaturan : null,
                    'id_izin'       => $perizinan->id_izin,
                    'tanggal'       => $tanggal,
                    'status'        => 'izin',
                ]);
            }
        }
    }
}
