<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresensiSeeder extends Seeder
{
    public function run(): void
    {
        $karyawan = DB::table('pengguna')->where('role', 'karyawan')->first();
        
        if (!$karyawan) {
            $this->command->error('Tidak ada data karyawan!');
            return;
        }
        
        $presensiData = [];
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        
        for ($i = 1; $i <= 5; $i++) {
            $tanggal = "2024-04-0" . $i;
            $hari = $hariList[$i - 1];
            
            if ($i == 3) {
                $status = 'Izin';
                $check_in = null;
                $check_out = null;
            } elseif ($i == 4) {
                $status = 'Sakit';
                $check_in = null;
                $check_out = null;
            } else {
                $status = 'Hadir';
                $check_in = '08:00:00';
                $check_out = '17:00:00';
            }
            
            $presensiData[] = [
                'id_pengguna' => $karyawan->id_pengguna,
                'id_pengaturan' => 1,
                'id_izin' => null,
                'tanggal' => $tanggal,
                'hari' => $hari,
                'check_in' => $check_in,
                'check_in_lat' => $status == 'Hadir' ? -6.200000 : null,
                'check_in_lng' => $status == 'Hadir' ? 106.816666 : null,
                'check_out' => $check_out,
                'check_out_lat' => $status == 'Hadir' ? -6.200000 : null,
                'check_out_lng' => $status == 'Hadir' ? 106.816666 : null,
                'status' => $status,
                'menit_terlambat' => 0,
                'catatan_keterlambatan' => null,
            ];
        }
        
        DB::table('presensi')->insert($presensiData);
        $this->command->info('✅ Berhasil menambah ' . count($presensiData) . ' data presensi');
    }
}