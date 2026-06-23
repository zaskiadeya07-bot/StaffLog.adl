<?php

namespace App\Services;

use App\Models\Pengguna;

class KaryawanService
{
    public function generateIdKaryawan(): string
    {
        $last = Pengguna::where('id_karyawan', 'like', 'EMP-%')
            ->orderByRaw('CAST(SUBSTRING(id_karyawan, 5) AS UNSIGNED) DESC')
            ->first();

        $nextNumber = $last ? (int) substr($last->id_karyawan, 4) + 1 : 1;
        return 'EMP-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
