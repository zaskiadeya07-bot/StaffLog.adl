<?php

namespace App\Services;

use App\Models\MasterData;
use App\Models\Presensi;

class PresensiService
{
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function checkRadius(float $lat, float $lng, MasterData $setting): array
    {
        $jarak = $this->calculateDistance(
            $lat, $lng,
            (float)$setting->lat_kantor,
            (float)$setting->long_kantor
        );

        return [
            'jarak' => $jarak,
            'di_dalam_radius' => $jarak <= $setting->radius,
        ];
    }

    public function hitungMenitTerlambat(string $jamMasuk, MasterData $setting): float
    {
        $jamStandar = strtotime($setting->jam_masuk_std);
        $jamMasukTime = strtotime($jamMasuk);
        $menitTerlambat = max(0, ($jamMasukTime - $jamStandar) / 60);
        return max(0, $menitTerlambat - ($setting->toleransi ?? 0));
    }

    public function cekSudahCheckIn(int $penggunaId): ?Presensi
    {
        return Presensi::where('id_pengguna', $penggunaId)
            ->whereDate('tanggal', today())
            ->first();
    }

    public function statusCheckIn(int $penggunaId): array
    {
        $todayCheckIn = Presensi::where('id_pengguna', $penggunaId)
            ->whereDate('tanggal', today())
            ->first();

        return [
            'sudah_check_in' => !is_null($todayCheckIn),
            'jam_masuk' => $todayCheckIn ? $todayCheckIn->check_in : null,
            'tanggal' => $todayCheckIn ? $todayCheckIn->tanggal : null,
        ];
    }

    public function statusCheckOut(int $penggunaId): array
    {
        $todayPresensi = Presensi::where('id_pengguna', $penggunaId)
            ->whereDate('tanggal', today())
            ->first();

        $hasCheckedOut = $todayPresensi && !is_null($todayPresensi->check_out);

        return [
            'hasCheckedOut' => $hasCheckedOut,
            'data' => $todayPresensi ? [
                'check_in_time' => $todayPresensi->check_in,
                'check_out_time' => $todayPresensi->check_out,
                'tanggal' => $todayPresensi->tanggal,
            ] : null,
        ];
    }
}
