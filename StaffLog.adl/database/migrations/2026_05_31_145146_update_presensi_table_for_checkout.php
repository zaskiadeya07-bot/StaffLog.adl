<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            // ========== RENAME COLUMN (kode Anda sudah ada) ==========
            // Rename kolom lama ke baru
            if (Schema::hasColumn('presensi', 'jam_masuk')) {
                $table->renameColumn('jam_masuk', 'check_in');
            }
            if (Schema::hasColumn('presensi', 'jam_keluar')) {
                $table->renameColumn('jam_keluar', 'check_out');
            }
            if (Schema::hasColumn('presensi', 'lat_masuk')) {
                $table->renameColumn('lat_masuk', 'check_in_lat');
            }
            if (Schema::hasColumn('presensi', 'long_masuk')) {
                $table->renameColumn('long_masuk', 'check_in_lng');
            }
            if (Schema::hasColumn('presensi', 'lat_keluar')) {
                $table->renameColumn('lat_keluar', 'check_out_lat');
            }
            if (Schema::hasColumn('presensi', 'long_keluar')) {
                $table->renameColumn('long_keluar', 'check_out_lng');
            }
            if (Schema::hasColumn('presensi', 'status_kehadiran')) {
                $table->renameColumn('status_kehadiran', 'status');
            }
            
            // ========== TAMBAHKAN KOLOM HARI (UNTUK REKAP KEHADIRAN) ==========
            if (!Schema::hasColumn('presensi', 'hari')) {
                $table->string('hari', 20)->nullable()->after('tanggal');
            }
            
            // ========== TAMBAHKAN KOLOM KETERANGAN (JIKA BELUM ADA) ==========
            if (!Schema::hasColumn('presensi', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('status');
            }
            
            // ========== KODE YANG SUDAH ADA (timestamps dll) ==========
            // Tambah kolom timestamps jika belum ada
            if (!Schema::hasColumn('presensi', 'created_at')) {
                $table->timestamps();
            }
            
            // Ubah id_pengaturan jadi nullable
            try {
                $table->unsignedInteger('id_pengaturan')->nullable()->change();
            } catch (\Exception $e) {
                // Abaikan error jika tidak bisa change
            }
        });
    }

    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            // Rollback rename
            $table->renameColumn('check_in', 'jam_masuk');
            $table->renameColumn('check_out', 'jam_keluar');
            $table->renameColumn('check_in_lat', 'lat_masuk');
            $table->renameColumn('check_in_lng', 'long_masuk');
            $table->renameColumn('check_out_lat', 'lat_keluar');
            $table->renameColumn('check_out_lng', 'long_keluar');
            $table->renameColumn('status', 'status_kehadiran');
            
            // Hapus kolom yang ditambahkan
            if (Schema::hasColumn('presensi', 'hari')) {
                $table->dropColumn('hari');
            }
            if (Schema::hasColumn('presensi', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
            
            $table->dropTimestamps();
            
            try {
                $table->unsignedInteger('id_pengaturan')->nullable(false)->change();
            } catch (\Exception $e) {
                // Abaikan error
            }
        });
    }
};