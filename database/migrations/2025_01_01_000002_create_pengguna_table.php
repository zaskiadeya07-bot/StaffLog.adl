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
            $table->string('nama_lengkap', 100)->notNullable();
            $table->string('username', 50)->unique()->notNullable();
            $table->string('password', 255)->notNullable();
            $table->enum('role', ['admin', 'karyawan'])->notNullable();
            $table->unsignedInteger('divisi')->notNullable();
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
