<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengguna extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Table Configuration
    |--------------------------------------------------------------------------
    */

    protected $table      = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    public    $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Attributes
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'nama_lengkap',
        'username',
        'password',
        'role',
        'divisi',
        'nomor_hp',
        'tgl_mulai_kerja',
    ];

    /*
    |--------------------------------------------------------------------------
    | Hidden Attributes (never exposed in JSON / array output)
    |--------------------------------------------------------------------------
    */

    protected $hidden = [
        'password',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'password'        => 'hashed',
        'tgl_mulai_kerja' => 'date',
        'role'            => 'string',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Pengguna ini milik satu divisi.
     */
    public function devisi(): BelongsTo
    {
        return $this->belongsTo(Devisi::class, 'divisi', 'id_devisi');
    }

    /**
     * Pengguna ini memiliki banyak record presensi.
     */
    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Pengguna ini memiliki banyak pengajuan perizinan (sebagai pengaju).
     */
    public function perizinan(): HasMany
    {
        return $this->hasMany(Perizinan::class, 'id_pengguna_pengaju', 'id_pengguna');
    }
}
