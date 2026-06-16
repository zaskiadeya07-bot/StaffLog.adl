<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('perizinan')
            ->whereIn('jenis_izin', ['cuti', 'dinas', 'sakit'])
            ->update([
                'jenis_izin' => DB::raw("CASE jenis_izin
                    WHEN 'cuti'   THEN 'cuti_tahunan'
                    WHEN 'dinas'  THEN 'izin'
                    WHEN 'sakit'  THEN 'cuti_sakit'
                END"),
            ]);

        DB::statement("ALTER TABLE perizinan MODIFY COLUMN jenis_izin ENUM('cuti_tahunan','cuti_sakit','izin')");
    }

    public function down(): void
    {
        DB::table('perizinan')
            ->whereIn('jenis_izin', ['cuti_tahunan', 'cuti_sakit', 'izin'])
            ->update([
                'jenis_izin' => DB::raw("CASE jenis_izin
                    WHEN 'cuti_tahunan' THEN 'cuti'
                    WHEN 'cuti_sakit'   THEN 'sakit'
                    WHEN 'izin'         THEN 'dinas'
                END"),
            ]);

        DB::statement("ALTER TABLE perizinan MODIFY COLUMN jenis_izin ENUM('sakit','cuti','dinas')");
    }
};
