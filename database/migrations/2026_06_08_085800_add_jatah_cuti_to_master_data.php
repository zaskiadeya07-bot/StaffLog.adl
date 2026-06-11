<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_data', function (Blueprint $table) {
            $table->integer('jatah_cuti_tahunan')->default(12)->after('toleransi');
        });
    }

    public function down(): void
    {
        Schema::table('master_data', function (Blueprint $table) {
            $table->dropColumn('jatah_cuti_tahunan');
        });
    }
};
