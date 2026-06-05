<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perizinan extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Table Configuration
    |--------------------------------------------------------------------------
    */

    protected $table      = 'perizinan';
    protected $primaryKey = 'id_izin';
    public    $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Attributes
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'id_pengguna_pengaju',
        'id_admin_validator',
        'jenis_izin',
        'tgl_pengajuan',
        'tgl_mulai',
        'tgl_selesai',
        'keterangan',
        'file_surat',
        'status_approval',
        'catatan_admin',
        'tgl_validasi',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    | Catatan: tgl_validasi adalah datetime operasional (bukan timestamps
    | Laravel), sehingga $timestamps = false tetap berlaku.
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'tgl_pengajuan'  => 'date',
        'tgl_mulai'      => 'date',
        'tgl_selesai'    => 'date',
        'tgl_validasi'   => 'datetime',
        'jenis_izin'     => 'string',
        'status_approval' => 'string',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Perizinan ini diajukan oleh seorang pengguna (karyawan).
     */
    public function pengaju(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna_pengaju', 'id_pengguna');
    }

    /**
     * Perizinan ini divalidasi oleh seorang pengguna (admin).
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_admin_validator', 'id_pengguna');
    }

    /**
     * Perizinan ini terhubung ke banyak record presensi.
     */
    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'id_izin', 'id_izin');
    }
}
