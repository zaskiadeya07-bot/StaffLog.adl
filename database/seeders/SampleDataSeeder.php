<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use App\Models\Presensi;
use App\Models\Perizinan;
use App\Models\MasterData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $pengaturan = MasterData::first();
        if (!$pengaturan) {
            $this->command->error('Master data belum ada. Jalankan DatabaseSeeder dulu.');
            return;
        }

        $karyawan = [
            ['nama_lengkap' => 'Budi Santoso',  'username' => 'budi',    'divisi' => 1, 'id_karyawan' => 'EMP-002', 'nomor_hp' => '081234567890', 'tgl_mulai_kerja' => '2025-01-15'],
            ['nama_lengkap' => 'Siti Rahmawati', 'username' => 'siti',    'divisi' => 1, 'id_karyawan' => 'EMP-003', 'nomor_hp' => '081234567891', 'tgl_mulai_kerja' => '2025-03-01'],
            ['nama_lengkap' => 'Ahmad Fauzi',    'username' => 'ahmad',   'divisi' => 2, 'id_karyawan' => 'EMP-004', 'nomor_hp' => '081234567892', 'tgl_mulai_kerja' => '2025-06-10'],
            ['nama_lengkap' => 'Dewi Lestari',   'username' => 'dewi',    'divisi' => 3, 'id_karyawan' => 'EMP-005', 'nomor_hp' => '081234567893', 'tgl_mulai_kerja' => '2025-02-20'],
            ['nama_lengkap' => 'Rudi Hartono',   'username' => 'rudi',    'divisi' => 4, 'id_karyawan' => 'EMP-006', 'nomor_hp' => '081234567894', 'tgl_mulai_kerja' => '2025-09-01'],
            ['nama_lengkap' => 'Maya Sari',      'username' => 'maya',    'divisi' => 2, 'id_karyawan' => 'EMP-007', 'nomor_hp' => '081234567895', 'tgl_mulai_kerja' => '2026-01-05'],
        ];

        $createdIds = [];

        foreach ($karyawan as $data) {
            $user = Pengguna::create([
                'nama_lengkap'    => $data['nama_lengkap'],
                'username'        => $data['username'],
                'password'        => Hash::make('123456'),
                'role'            => 'karyawan',
                'divisi'          => $data['divisi'],
                'nomor_hp'        => $data['nomor_hp'],
                'tgl_mulai_kerja' => $data['tgl_mulai_kerja'],
                'alamat'          => 'Jl. Contoh No. ' . substr($data['id_karyawan'], -1),
                'id_karyawan'     => $data['id_karyawan'],
                'status'          => 'aktif',
            ]);
            $createdIds[] = $user->id_pengguna;
        }

        $this->command->info('Sample karyawan: ' . count($karyawan) . ' created.');

        $now = now();
        $bulan = $now->month;
        $tahun = $now->year;
        $jmlHari = $now->daysInMonth;
        $jamMasuk = $pengaturan->jam_masuk_std ?? '08:00';
        $toleransi = $pengaturan->toleransi ?? 15;

        $totalPresensi = 0;

        foreach ($createdIds as $idPengguna) {
            for ($hari = 1; $hari <= $jmlHari; $hari++) {
                $tgl = \Carbon\Carbon::create($tahun, $bulan, $hari);
                if ($tgl->isWeekend() || $tgl->gt($now)) {
                    continue;
                }

                $checkIn = null;
                $checkOut = null;
                $status = 'alpha';
                $menitTerlambat = 0;

                // Random: 85% hadir, 10% izin, 5% alpha
                $rand = mt_rand(1, 100);
                if ($rand <= 80) {
                    $status = 'hadir';
                    $checkIn  = $jamMasuk;
                    $checkOut = $pengaturan->jam_pulang_std ?? '17:00';
                } elseif ($rand <= 95) {
                    $status = 'terlambat';
                    $menitTerlambat = mt_rand(1, $toleransi + 10);
                    $checkIn = \Carbon\Carbon::parse($jamMasuk)->addMinutes($menitTerlambat)->format('H:i:s');
                    $checkOut = $pengaturan->jam_pulang_std ?? '17:00';
                } else {
                    $status = 'alpha';
                }

                Presensi::create([
                    'id_pengguna'    => $idPengguna,
                    'id_pengaturan'  => $pengaturan->id_pengaturan,
                    'tanggal'        => $tgl->toDateString(),
                    'check_in'       => $checkIn,
                    'check_out'      => $checkOut,
                    'check_in_lat'   => -6.208765 + (mt_rand(-10, 10) / 10000),
                    'check_in_lng'   => 106.845593 + (mt_rand(-10, 10) / 10000),
                    'check_out_lat'  => -6.208765 + (mt_rand(-10, 10) / 10000),
                    'check_out_lng'  => 106.845593 + (mt_rand(-10, 10) / 10000),
                    'status'         => $status,
                    'menit_terlambat' => $menitTerlambat,
                ]);
                $totalPresensi++;
            }
        }

        $this->command->info("Sample presensi: {$totalPresensi} records created.");

        // Sample perizinan
        $sampleIzin = [
            [
                'id_pengguna' => $createdIds[0],
                'jenis_izin' => 'cuti_sakit',
                'tgl_mulai' => now()->subDays(5)->toDateString(),
                'tgl_selesai' => now()->subDays(3)->toDateString(),
                'keterangan' => 'Demam dan tidak bisa masuk kerja',
                'status' => 'disetujui',
            ],
            [
                'id_pengguna' => $createdIds[2],
                'jenis_izin' => 'izin',
                'tgl_mulai' => now()->subDays(2)->toDateString(),
                'tgl_selesai' => now()->subDays(2)->toDateString(),
                'keterangan' => 'Ada urusan keluarga mendadak',
                'status' => 'pending',
            ],
            [
                'id_pengguna' => $createdIds[4],
                'jenis_izin' => 'cuti_tahunan',
                'tgl_mulai' => now()->addDays(10)->toDateString(),
                'tgl_selesai' => now()->addDays(12)->toDateString(),
                'keterangan' => 'Cuti tahunan',
                'status' => 'pending',
            ],
        ];

        foreach ($sampleIzin as $izin) {
            Perizinan::create([
                'id_pengguna_pengaju' => $izin['id_pengguna'],
                'id_admin_validator'  => $izin['status'] === 'disetujui' ? 1 : null,
                'jenis_izin'          => $izin['jenis_izin'],
                'tgl_pengajuan'       => now()->subDays(7)->toDateString(),
                'tgl_mulai'           => $izin['tgl_mulai'],
                'tgl_selesai'         => $izin['tgl_selesai'],
                'keterangan'          => $izin['keterangan'],
                'file_surat'          => null,
                'status_approval'     => $izin['status'],
                'catatan_admin'       => $izin['status'] === 'disetujui' ? 'Disetujui.' : null,
                'tgl_validasi'        => $izin['status'] === 'disetujui' ? now()->subDays(6)->toDateString() : null,
            ]);
        }

        $this->command->info('Sample perizinan: ' . count($sampleIzin) . ' created.');
    }
}
