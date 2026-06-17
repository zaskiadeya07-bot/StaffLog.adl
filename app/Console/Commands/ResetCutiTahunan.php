<?php

namespace App\Console\Commands;

use App\Models\MasterData;
use App\Models\Pengguna;
use Illuminate\Console\Command;

class ResetCutiTahunan extends Command
{
    protected $signature = 'cuti:reset-bulanan';
    protected $description = 'Catat jatah cuti bulanan di awal bulan baru';

    public function handle()
    {
        $pengaturan = MasterData::first();
        $jatahCuti = $pengaturan?->jatah_cuti_bulanan ?? 1;

        $totalKaryawan = Pengguna::where('role', 'karyawan')
            ->where('status', 'aktif')
            ->count();

        $bulan = now()->translatedFormat('F Y');

        $this->info("{$bulan}: Jatah cuti {$jatahCuti} hari untuk {$totalKaryawan} karyawan aktif.");
        $this->info('Reset period cuti bulanan selesai.');

        return Command::SUCCESS;
    }
}
