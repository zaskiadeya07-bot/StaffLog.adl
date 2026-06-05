<?php

namespace App\Interfaces;

interface PresensiInterface
{
    /**
     * Rekam Check In
     */
    public function rekamCheckIn($penggunaId, $latitude, $longitude);
    
    /**
     * Rekam Check Out
     */
    public function rekamCheckOut($penggunaId, $latitude, $longitude);
    
    /**
     * Cek apakah sudah check in hari ini
     */
    public function cekStatusHariIni($penggunaId);
    
    /**
     * Hitung keterlambatan
     */
    public function hitungKeterlambatan($jamMasuk);
    
    /**
     * Ambil riwayat presensi
     */
    public function getRiwayat($penggunaId, $bulan, $tahun);
}