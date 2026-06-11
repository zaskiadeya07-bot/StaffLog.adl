<?php

namespace App\Console\Commands;

use App\Models\Pengguna;
use App\Models\Presensi;
use App\Models\Perizinan;
use App\Models\MasterData;
use Illuminate\Console\Command;

class MarkAbsentEmployees extends Command
{
    protected $signature = 'presensi:mark-absent';
    protected $description = 'Tandai karyawan yang tidak hadir tanpa izin sebagai alpha';

    public function handle()
    {
        $tanggal = now()->subDay()->toDateString();
        $hari = now()->subDay()->format('l');

        // Lewati weekend
        if (in_array($hari, ['Saturday', 'Sunday'])) {
            $this->info('Hari ini akhir pekan, tidak ada penandaan alpha.');
            return Command::SUCCESS;
        }

        $pengaturan = MasterData::first();

        // Ambil semua karyawan aktif
        $karyawan = Pengguna::where('role', 'karyawan')
            ->where('status', 'aktif')
            ->get();

        $count = 0;

        foreach ($karyawan as $k) {
            // Cek apakah sudah ada presensi di tanggal tersebut
            $presensi = Presensi::where('id_pengguna', $k->id_pengguna)
                ->whereDate('tanggal', $tanggal)
                ->first();

            if ($presensi) {
                continue; // Sudah ada presensi, skip
            }

            // Cek apakah ada izin disetujui untuk tanggal ini
            $adaIzin = Perizinan::where('id_pengguna_pengaju', $k->id_pengguna)
                ->where('status_approval', 'disetujui')
                ->whereDate('tgl_mulai', '<=', $tanggal)
                ->whereDate('tgl_selesai', '>=', $tanggal)
                ->exists();

            if ($adaIzin) {
                continue; // Ada izin, skip
            }

            // Buat presensi alpha
            Presensi::create([
                'id_pengguna' => $k->id_pengguna,
                'id_pengaturan' => $pengaturan?->id_pengaturan,
                'tanggal' => $tanggal,
                'status' => 'alpha',
                'catatan_keterlambatan' => 'Tidak hadir tanpa keterangan',
            ]);

            $count++;
        }

        $this->info("Berhasil menandai {$count} karyawan sebagai alpha.");
        return Command::SUCCESS;
    }
}
