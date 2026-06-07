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
    public    $timestamps = true;

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
        'hari',
        'check_in',
        'check_in_lat',
        'check_in_lng',
        'check_out',
        'check_out_lat',
        'check_out_lng',
        'status',
        'keterangan',
        'menit_terlambat',
        'catatan_keterlambatan',
    ];
    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'tanggal' => 'date',
        'check_in_lat' => 'decimal:8',
        'check_in_lng' => 'decimal:8',
        'check_out_lat' => 'decimal:8',
        'check_out_lng' => 'decimal:8',
        'menit_terlambat' => 'integer',
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
