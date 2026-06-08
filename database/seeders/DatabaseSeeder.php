<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Pengguna;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $devisiList = [
            'Barista', 'Kasir', 'Kitchen',
            'Service Crew', 'Admin', 'Manager',
        ];

        foreach ($devisiList as $nama) {
            DB::table('devisi')->insert(['nama_devisi' => $nama]);
        }

        DB::table('master_data')->insert([
            'jam_masuk_std'  => '08:00:00',
            'jam_pulang_std' => '17:00:00',
            'lat_kantor'     => -6.20876500,
            'long_kantor'    => 106.84559300,
            'radius'         => 100,
            'toleransi'      => 15,
        ]);

        Pengguna::create([
            'nama_lengkap'   => 'Admin',
            'username'       => 'admin',
            'password'       => Hash::make('admin123'),
            'role'           => 'admin',
            'divisi'         => 5,
            'id_karyawan'    => 'EMP-001',
            'status'         => 'aktif',
        ]);
    }
}
