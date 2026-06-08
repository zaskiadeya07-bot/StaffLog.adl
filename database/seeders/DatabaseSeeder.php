<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ─────────────────────────────────────────────
        // 1. DEVISI
        // ─────────────────────────────────────────────
        $devisiIds = [];
        $devisiList = [
            'Barista',
            'Kasir',
            'Kitchen',
            'Service Crew',
            'Admin',
            'Manager',
        ];

        foreach ($devisiList as $nama) {
            $devisiIds[] = DB::table('devisi')->insertGetId([
                'nama_devisi' => $nama,
            ]);
        }

        // ─────────────────────────────────────────────
        // 2. MASTER DATA (konfigurasi kantor)
        // ─────────────────────────────────────────────
        $masterId = DB::table('master_data')->insertGetId([
            'jam_masuk_std'  => '08:00:00',
            'jam_pulang_std' => '17:00:00',
            'lat_kantor'     => -6.20876500,
            'long_kantor'    => 106.84559300,
            'radius'         => 100,   // meter
            'toleransi'      => 15,    // menit
        ]);

        // ─────────────────────────────────────────────
        // 3. PENGGUNA
        // ─────────────────────────────────────────────
        $penggunaData = [
            // Admin
            [
                'nama_lengkap'    => 'Ahmad Fauzi',
                'username'        => 'ahmad.fauzi',
                'password'        => Hash::make('password'),
                'role'            => 'admin',
                'divisi'          => $devisiIds[1], // SDM
                'nomor_hp'        => '081234567890',
                'tgl_mulai_kerja' => '2020-01-15',
            ],
            [
                'nama_lengkap'    => 'Siti Rahayu',
                'username'        => 'siti.rahayu',
                'password'        => Hash::make('password'),
                'role'            => 'admin',
                'divisi'          => $devisiIds[1], // SDM
                'nomor_hp'        => '081298765432',
                'tgl_mulai_kerja' => '2019-03-01',
            ],
            // Karyawan
            [
                'nama_lengkap'    => 'Budi Santoso',
                'username'        => 'budi.santoso',
                'password'        => Hash::make('password'),
                'role'            => 'karyawan',
                'divisi'          => $devisiIds[0], // TI
                'nomor_hp'        => '085611223344',
                'tgl_mulai_kerja' => '2021-06-01',
            ],
            [
                'nama_lengkap'    => 'Dewi Lestari',
                'username'        => 'dewi.lestari',
                'password'        => Hash::make('password'),
                'role'            => 'karyawan',
                'divisi'          => $devisiIds[0], // TI
                'nomor_hp'        => '087722334455',
                'tgl_mulai_kerja' => '2022-02-14',
            ],
            [
                'nama_lengkap'    => 'Riko Pratama',
                'username'        => 'riko.pratama',
                'password'        => Hash::make('password'),
                'role'            => 'karyawan',
                'divisi'          => $devisiIds[2], // Keuangan
                'nomor_hp'        => '089933445566',
                'tgl_mulai_kerja' => '2021-09-01',
            ],
            [
                'nama_lengkap'    => 'Nurul Hidayah',
                'username'        => 'nurul.hidayah',
                'password'        => Hash::make('password'),
                'role'            => 'karyawan',
                'divisi'          => $devisiIds[3], // Operasional
                'nomor_hp'        => '082244556677',
                'tgl_mulai_kerja' => '2023-01-10',
            ],
            [
                'nama_lengkap'    => 'Teguh Wibowo',
                'username'        => 'teguh.wibowo',
                'password'        => Hash::make('password'),
                'role'            => 'karyawan',
                'divisi'          => $devisiIds[4], // Marketing
                'nomor_hp'        => '081355667788',
                'tgl_mulai_kerja' => '2020-07-20',
            ],
            [
                'nama_lengkap'    => 'Rina Kusuma',
                'username'        => 'rina.kusuma',
                'password'        => Hash::make('password'),
                'role'            => 'karyawan',
                'divisi'          => $devisiIds[4], // Marketing
                'nomor_hp'        => '085566778899',
                'tgl_mulai_kerja' => '2022-11-03',
            ],
            [
                'nama_lengkap'    => 'Hendra Gunawan',
                'username'        => 'hendra.gunawan',
                'password'        => Hash::make('password'),
                'role'            => 'karyawan',
                'divisi'          => $devisiIds[2], // Keuangan
                'nomor_hp'        => '087788990011',
                'tgl_mulai_kerja' => '2021-03-15',
            ],
            [
                'nama_lengkap'    => 'Maya Anggraini',
                'username'        => 'maya.anggraini',
                'password'        => Hash::make('password'),
                'role'            => 'karyawan',
                'divisi'          => $devisiIds[3], // Operasional
                'nomor_hp'        => '082211223344',
                'tgl_mulai_kerja' => '2023-04-01',
            ],
        ];

        $penggunaIds = [];
        $counter = 1;
        foreach ($penggunaData as $pengguna) {
            $pengguna['id_karyawan'] = 'EMP-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $pengguna['status'] = 'aktif';
            $counter++;
            $penggunaIds[] = DB::table('pengguna')->insertGetId($pengguna);
        }

        // admin id = penggunaIds[0] dan [1]
        $adminId1 = $penggunaIds[0];
        $adminId2 = $penggunaIds[1];

        // ─────────────────────────────────────────────
        // 4. PERIZINAN
        // ─────────────────────────────────────────────
        $perizinanRecords = [
            [
                'id_pengguna_pengaju' => $penggunaIds[2], // Budi
                'id_admin_validator'  => $adminId1,
                'jenis_izin'          => 'cuti_sakit',
                'tgl_pengajuan'       => '2025-05-05',
                'tgl_mulai'           => '2025-05-06',
                'tgl_selesai'         => '2025-05-07',
                'keterangan'          => 'Sakit demam, sudah ke dokter.',
                'file_surat'          => null,
                'status_approval'     => 'disetujui',
                'catatan_admin'       => 'Izin disetujui, semoga cepat sembuh.',
                'tgl_validasi'        => '2025-05-05 10:30:00',
            ],
            [
                'id_pengguna_pengaju' => $penggunaIds[3], // Dewi
                'id_admin_validator'  => $adminId1,
                'jenis_izin'          => 'cuti_tahunan',
                'tgl_pengajuan'       => '2025-05-10',
                'tgl_mulai'           => '2025-05-15',
                'tgl_selesai'         => '2025-05-17',
                'keterangan'          => 'Cuti tahunan untuk keperluan keluarga.',
                'file_surat'          => null,
                'status_approval'     => 'disetujui',
                'catatan_admin'       => 'Disetujui.',
                'tgl_validasi'        => '2025-05-11 09:00:00',
            ],
            [
                'id_pengguna_pengaju' => $penggunaIds[4], // Riko
                'id_admin_validator'  => $adminId2,
                'jenis_izin'          => 'izin',
                'tgl_pengajuan'       => '2025-06-01',
                'tgl_mulai'           => '2025-06-05',
                'tgl_selesai'         => '2025-06-07',
                'keterangan'          => 'Perjalanan dinas ke Surabaya untuk audit.',
                'file_surat'          => 'surat_tugas_riko.pdf',
                'status_approval'     => 'disetujui',
                'catatan_admin'       => 'Disetujui, lampirkan laporan setelah kembali.',
                'tgl_validasi'        => '2025-06-02 08:45:00',
            ],
            [
                'id_pengguna_pengaju' => $penggunaIds[5], // Nurul
                'id_admin_validator'  => null,
                'jenis_izin'          => 'cuti_sakit',
                'tgl_pengajuan'       => '2025-06-10',
                'tgl_mulai'           => '2025-06-10',
                'tgl_selesai'         => '2025-06-11',
                'keterangan'          => 'Sakit kepala dan pusing.',
                'file_surat'          => null,
                'status_approval'     => 'pending',
                'catatan_admin'       => null,
                'tgl_validasi'        => null,
            ],
            [
                'id_pengguna_pengaju' => $penggunaIds[6], // Teguh
                'id_admin_validator'  => $adminId1,
                'jenis_izin'          => 'cuti_tahunan',
                'tgl_pengajuan'       => '2025-06-08',
                'tgl_mulai'           => '2025-06-12',
                'tgl_selesai'         => '2025-06-13',
                'keterangan'          => 'Keperluan pribadi.',
                'file_surat'          => null,
                'status_approval'     => 'ditolak',
                'catatan_admin'       => 'Tidak bisa disetujui, ada event penting minggu itu.',
                'tgl_validasi'        => '2025-06-09 14:00:00',
            ],
        ];

        $perizinanIds = [];
        foreach ($perizinanRecords as $perizinan) {
            $perizinanIds[] = DB::table('perizinan')->insertGetId($perizinan);
        }

        // ─────────────────────────────────────────────
        // 5. PRESENSI (data beberapa hari terakhir)
        // ─────────────────────────────────────────────
        $presensiData = [];

        // Daftar karyawan (semua pengguna)
        $allPengguna = $penggunaIds;

        // Data presensi per tanggal
        $tanggalList = ['2025-06-09', '2025-06-10', '2025-06-11'];

        foreach ($tanggalList as $tanggal) {
            foreach ($allPengguna as $idx => $penggunaId) {

                // Nurul izin di 10-11 Juni
                if ($idx === 5 && in_array($tanggal, ['2025-06-10', '2025-06-11'])) {
                    $presensiData[] = [
                        'id_pengguna'           => $penggunaId,
                        'id_pengaturan'         => $masterId,
                        'id_izin'               => $perizinanIds[3], // Nurul's izin
                        'tanggal'               => $tanggal,
                        'check_in'             => null,
                        'check_in_lat'             => null,
                        'check_in_lng'            => null,
                        'check_out'            => null,
                        'check_out_lat'            => null,
                        'check_out_lng'           => null,
                        'status'      => 'izin',
                        'menit_terlambat'       => 0,
                        'catatan_keterlambatan' => null,
                    ];
                    continue;
                }

                // Teguh alpha di 12-13 (diluar range ini, tapi contoh di 11)
                if ($idx === 6 && $tanggal === '2025-06-11') {
                    $presensiData[] = [
                        'id_pengguna'           => $penggunaId,
                        'id_pengaturan'         => $masterId,
                        'id_izin'               => null,
                        'tanggal'               => $tanggal,
                        'check_in'             => null,
                        'check_in_lat'             => null,
                        'check_in_lng'            => null,
                        'check_out'            => null,
                        'check_out_lat'            => null,
                        'check_out_lng'           => null,
                        'status'      => 'alpha',
                        'menit_terlambat'       => 0,
                        'catatan_keterlambatan' => null,
                    ];
                    continue;
                }

                // Terlambat untuk beberapa orang
                $terlambat     = ($idx % 3 === 0) ? rand(5, 30) : 0;
                $jamMasukMenit = 8 * 60 + ($terlambat > 0 ? $terlambat + 15 : rand(0, 10));
                $jamMasuk      = sprintf('%02d:%02d:00', intdiv($jamMasukMenit, 60), $jamMasukMenit % 60);
                $jamKeluar     = (rand(0, 1)) ? '17:' . sprintf('%02d', rand(0, 30)) . ':00' : null;

                $presensiData[] = [
                    'id_pengguna'           => $penggunaId,
                    'id_pengaturan'         => $masterId,
                    'id_izin'               => null,
                    'tanggal'               => $tanggal,
                    'check_in'             => $jamMasuk,
                    'check_in_lat'             => -6.20876500 + (rand(-50, 50) / 100000),
                    'check_in_lng'            => 106.84559300 + (rand(-50, 50) / 100000),
                    'check_out'            => $jamKeluar,
                    'check_out_lat'            => $jamKeluar ? -6.20876500 + (rand(-50, 50) / 100000) : null,
                    'check_out_lng'           => $jamKeluar ? 106.84559300 + (rand(-50, 50) / 100000) : null,
                    'status'      => 'hadir',
                    'menit_terlambat'       => max(0, $terlambat - 15), // dikurangi toleransi 15 menit
                    'catatan_keterlambatan' => $terlambat > 0 ? 'Terlambat ' . $terlambat . ' menit dari jam masuk standar.' : null,
                ];
            }
        }

        DB::table('presensi')->insert($presensiData);

        $this->command->info('✅ Seeder selesai! Data dummy berhasil dibuat:');
        $this->command->info('   - ' . count($devisiList) . ' divisi');
        $this->command->info('   - 1 master data (konfigurasi kantor)');
        $this->command->info('   - ' . count($penggunaData) . ' pengguna (2 admin, 8 karyawan) | password: "password"');
        $this->command->info('   - ' . count($perizinanRecords) . ' perizinan');
        $this->command->info('   - ' . count($presensiData) . ' record presensi (3 hari)');
    }
}
