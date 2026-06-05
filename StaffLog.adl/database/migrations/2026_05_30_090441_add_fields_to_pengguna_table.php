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
        Schema::table('pengguna', function (Blueprint $table) {
            // Tambah kolom id_karyawan (unique) setelah kolom id_pengguna
            if (!Schema::hasColumn('pengguna', 'id_karyawan')) {
                $table->string('id_karyawan')->unique()->nullable()->after('id_pengguna');
            }
            
            // Tambah kolom alamat (text) setelah kolom nomor_hp
            if (!Schema::hasColumn('pengguna', 'alamat')) {
                $table->text('alamat')->nullable()->after('nomor_hp');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            // Hapus kolom yang sudah ditambahkan
            $table->dropColumn(['id_karyawan', 'alamat']);
        });
    }
};