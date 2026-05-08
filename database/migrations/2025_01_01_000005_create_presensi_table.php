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
        Schema::create('presensi', function (Blueprint $table) {
            $table->increments('id_presensi');
            $table->unsignedInteger('id_pengguna')->notNullable();
            $table->unsignedInteger('id_pengaturan')->notNullable();
            $table->unsignedInteger('id_izin')->nullable();
            $table->date('tanggal')->notNullable();
            $table->time('jam_masuk')->nullable();
            $table->decimal('lat_masuk', 10, 8)->nullable();
            $table->decimal('long_masuk', 11, 8)->nullable();
            $table->time('jam_keluar')->nullable();
            $table->decimal('lat_keluar', 10, 8)->nullable();
            $table->decimal('long_keluar', 11, 8)->nullable();
            $table->enum('status_kehadiran', ['hadir', 'izin', 'alpha'])->notNullable();
            $table->integer('menit_terlambat')->default(0);
            $table->text('catatan_keterlambatan')->nullable();

            $table->foreign('id_pengguna')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onUpdate('CASCADE')
                  ->onDelete('RESTRICT');

            $table->foreign('id_pengaturan')
                  ->references('id_pengaturan')
                  ->on('master_data')
                  ->onUpdate('CASCADE')
                  ->onDelete('RESTRICT');

            $table->foreign('id_izin')
                  ->references('id_izin')
                  ->on('perizinan')
                  ->onUpdate('CASCADE')
                  ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
