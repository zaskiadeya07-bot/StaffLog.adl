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
        Schema::create('master_data', function (Blueprint $table) {
            $table->increments('id_pengaturan');
            $table->time('jam_masuk_std')->notNullable();
            $table->time('jam_pulang_std')->notNullable();
            $table->decimal('lat_kantor', 10, 8)->notNullable();
            $table->decimal('long_kantor', 11, 8)->notNullable();
            $table->integer('radius')->notNullable()->comment('dalam meter');
            $table->integer('toleransi')->notNullable()->comment('toleransi keterlambatan dalam menit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_data');
    }
};
