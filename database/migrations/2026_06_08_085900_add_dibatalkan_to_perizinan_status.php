<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE perizinan MODIFY COLUMN status_approval ENUM('pending','disetujui','ditolak','dibatalkan') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE perizinan MODIFY COLUMN status_approval ENUM('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending'");
    }
};
