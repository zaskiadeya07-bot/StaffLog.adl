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
            $table->unsignedInteger('id_pengguna');
            $table->unsignedInteger('id_pengaturan')->nullable();  // Diubah jadi nullable
            $table->unsignedInteger('id_izin')->nullable();
            $table->date('tanggal');
            $table->time('check_in')->nullable();      // Ganti dari jam_masuk
            $table->time('check_out')->nullable();     // Ganti dari jam_keluar
            $table->decimal('check_in_lat', 10, 8)->nullable();   // Ganti dari lat_masuk
            $table->decimal('check_in_lng', 11, 8)->nullable();   // Ganti dari long_masuk
            $table->decimal('check_out_lat', 10, 8)->nullable();  // Ganti dari lat_keluar
            $table->decimal('check_out_lng', 11, 8)->nullable();  // Ganti dari long_keluar
            $table->enum('status', ['hadir', 'izin', 'alpha', 'terlambat'])->default('hadir');  // Ganti dari status_kehadiran
            $table->integer('menit_terlambat')->default(0);
            $table->text('catatan_keterlambatan')->nullable();
            $table->timestamps();  // Tambahkan created_at & updated_at

            // Foreign Keys
            $table->foreign('id_pengguna')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');  // Ubah dari RESTRICT ke cascade

            $table->foreign('id_pengaturan')
                  ->references('id_pengaturan')
                  ->on('master_data')
                  ->onUpdate('cascade')
                  ->onDelete('set null');  // Ubah dari RESTRICT ke set null

            $table->foreign('id_izin')
                  ->references('id_izin')
                  ->on('perizinan')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
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