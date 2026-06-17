<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_data', function (Blueprint $table) {
            $table->renameColumn('jatah_cuti_tahunan', 'jatah_cuti_bulanan');
        });
    }

    public function down(): void
    {
        Schema::table('master_data', function (Blueprint $table) {
            $table->renameColumn('jatah_cuti_bulanan', 'jatah_cuti_tahunan');
        });
    }
};
