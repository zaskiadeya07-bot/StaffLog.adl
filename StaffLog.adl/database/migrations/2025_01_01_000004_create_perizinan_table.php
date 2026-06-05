<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perizinan', function (Blueprint $table) {
            $table->increments('id_izin');
            $table->unsignedInteger('id_pengguna_pengaju')->notNullable();
            $table->unsignedInteger('id_admin_validator')->nullable();
            $table->enum('jenis_izin', ['sakit', 'cuti', 'dinas'])->notNullable();
            $table->date('tgl_pengajuan')->notNullable();
            $table->date('tgl_mulai')->notNullable();
            $table->date('tgl_selesai')->notNullable();
            $table->text('keterangan')->notNullable();
            $table->string('file_surat', 255)->nullable();
            $table->enum('status_approval', ['pending', 'disetujui', 'ditolak'])
                  ->notNullable()
                  ->default('pending');
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perizinan');
    }
};
