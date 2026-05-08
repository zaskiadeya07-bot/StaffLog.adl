<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Table Configuration
    |--------------------------------------------------------------------------
    */

    protected $table      = 'presensi';
    protected $primaryKey = 'id_presensi';
    public    $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Attributes
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'id_pengguna',
        'id_pengaturan',
        'id_izin',
        'tanggal',
        'jam_masuk',
        'lat_masuk',
        'long_masuk',
        'jam_keluar',
        'lat_keluar',
        'long_keluar',
        'status_kehadiran',
        'menit_terlambat',
        'catatan_keterlambatan',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'tanggal'          => 'date',
        'lat_masuk'        => 'decimal:8',
        'long_masuk'       => 'decimal:8',
        'lat_keluar'       => 'decimal:8',
        'long_keluar'      => 'decimal:8',
        'menit_terlambat'  => 'integer',
        'status_kehadiran' => 'string',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Presensi ini milik seorang pengguna.
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Presensi ini mengacu pada satu konfigurasi master data (jam standar, lokasi, dll).
     */
    public function masterData(): BelongsTo
    {
        return $this->belongsTo(MasterData::class, 'id_pengaturan', 'id_pengaturan');
    }

    /**
     * Presensi ini (opsional) terhubung ke satu data perizinan.
     */
    public function perizinan(): BelongsTo
    {
        return $this->belongsTo(Perizinan::class, 'id_izin', 'id_izin');
    }
}
