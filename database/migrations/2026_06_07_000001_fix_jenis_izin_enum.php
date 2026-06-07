<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE perizinan MODIFY COLUMN jenis_izin ENUM('cuti_tahunan','cuti_sakit','izin') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE perizinan MODIFY COLUMN jenis_izin ENUM('sakit','cuti','dinas') NOT NULL");
    }
};
