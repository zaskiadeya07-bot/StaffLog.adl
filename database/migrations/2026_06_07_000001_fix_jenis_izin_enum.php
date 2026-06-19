<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::drop('perizinan');
        }

        Schema::create('perizinan', function ($table) {
            $table->increments('id_izin');
            $table->unsignedInteger('id_pengguna_pengaju');
            $table->unsignedInteger('id_admin_validator')->nullable();
            $table->enum('jenis_izin', ['cuti_tahunan', 'cuti_sakit', 'izin']);
            $table->date('tgl_pengajuan');
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->text('keterangan');
            $table->string('file_surat', 255)->nullable();
            $table->enum('status_approval', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->dateTime('tgl_validasi')->nullable();

            $table->foreign('id_pengguna_pengaju')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onUpdate('CASCADE')
                  ->onDelete('RESTRICT');

            $table->foreign('id_admin_validator')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onUpdate('CASCADE')
                  ->onDelete('SET NULL');
        });
    }

    public function down(): void
    {
        Schema::drop('perizinan');

        Schema::create('perizinan', function ($table) {
            $table->increments('id_izin');
            $table->unsignedInteger('id_pengguna_pengaju');
            $table->unsignedInteger('id_admin_validator')->nullable();
            $table->enum('jenis_izin', ['sakit', 'cuti', 'dinas']);
            $table->date('tgl_pengajuan');
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->text('keterangan');
            $table->string('file_surat', 255)->nullable();
            $table->enum('status_approval', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->dateTime('tgl_validasi')->nullable();

            $table->foreign('id_pengguna_pengaju')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onUpdate('CASCADE')
                  ->onDelete('RESTRICT');

            $table->foreign('id_admin_validator')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onUpdate('CASCADE')
                  ->onDelete('SET NULL');
        });
    }
};
