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
        Schema::create('pengguna', function (Blueprint $table) {
            $table->increments('id_pengguna');
            $table->string('nama_lengkap', 100);
            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->enum('role', ['admin', 'karyawan']);
            $table->unsignedInteger('divisi');
            $table->string('nomor_hp', 12)->nullable();
            $table->date('tgl_mulai_kerja')->nullable();

            $table->foreign('divisi')
                  ->references('id_devisi')
                  ->on('devisi')
                  ->onUpdate('CASCADE')
                  ->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
