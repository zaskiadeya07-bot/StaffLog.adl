<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hanya set default jika belum ada data
        $exists = DB::table('master_data')->where('id_pengaturan', 1)->exists();
        if (!$exists) {
            DB::table('master_data')->insert([
                'id_pengaturan'  => 1,
                'jam_masuk_std'  => '08:00:00',
                'jam_pulang_std' => '17:00:00',
                'lat_kantor'     => 1.12542180,
                'long_kantor'    => 104.03015790,
                'radius'         => 200,
                'toleransi'      => 15,
            ]);
        }
    }

    public function down(): void
    {
        // Tidak perlu melakukan apa-apa
    }
};