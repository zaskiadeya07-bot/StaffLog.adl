<?php

namespace App\Console\Commands;

use App\Models\Presensi;
use Illuminate\Console\Command;

class MarkMissingCheckOut extends Command
{
    protected $signature = 'presensi:mark-alpha';
    protected $description = 'Tandai karyawan yang lupa check out kemarin sebagai alpha';

    public function handle()
    {
        $affected = Presensi::whereDate('tanggal', today()->subDay())
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->update([
                'status' => 'alpha',
                'catatan_keterlambatan' => 'Tidak melakukan check out',
            ]);

        $this->info("Berhasil menandai {$affected} karyawan sebagai alpha.");

        return Command::SUCCESS;
    }
}
