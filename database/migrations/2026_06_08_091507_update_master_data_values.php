<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Memastikan nilai master_data tetap sesuai
        DB::table('master_data')
            ->where('id_pengaturan', 1)
            ->update([
                'lat_kantor'  => 1.12542180,
                'long_kantor' => 104.03015790,
                'radius'      => 200,
            ]);
    }

    public function down(): void
    {
        // Kembalikan ke nilai sebelumnya (sama saja)
        DB::table('master_data')
            ->where('id_pengaturan', 1)
            ->update([
                'lat_kantor'  => 1.12542180,
                'long_kantor' => 104.03015790,
                'radius'      => 200,
            ]);
    }
};