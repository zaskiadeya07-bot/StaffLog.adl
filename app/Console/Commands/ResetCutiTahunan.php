<?php

namespace App\Console\Commands;

use App\Models\MasterData;
use App\Models\Pengguna;
use Illuminate\Console\Command;

class ResetCutiTahunan extends Command
{
    protected $signature = 'cuti:reset-tahunan';
    protected $description = 'Reset jatah cuti tahunan di awal tahun baru';

    public function handle()
    {
        $pengaturan = MasterData::first();
        $jatahCuti = $pengaturan?->jatah_cuti_tahunan ?? 12;

        $totalKaryawan = Pengguna::where('role', 'karyawan')
            ->where('status', 'aktif')
            ->count();

        $tahun = now()->year;

        $this->info("Tahun {$tahun}: Jatah cuti tahunan {$jatahCuti} hari untuk {$totalKaryawan} karyawan aktif.");
        $this->info('Reset jatah cuti tahunan selesai (perhitungan otomatis per tahun).');

        return Command::SUCCESS;
    }
}
