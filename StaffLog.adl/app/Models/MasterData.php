<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterData extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Table Configuration
    |--------------------------------------------------------------------------
    */

    protected $table      = 'master_data';
    protected $primaryKey = 'id_pengaturan';
    public    $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Attributes
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'jam_masuk_std',
        'jam_pulang_std',
        'lat_kantor',
        'long_kantor',
        'radius',
        'toleransi',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'lat_kantor'  => 'decimal:8',
        'long_kantor' => 'decimal:8',
        'radius'      => 'integer',
        'toleransi'   => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Satu pengaturan master data digunakan oleh banyak record presensi.
     */
    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'id_pengaturan', 'id_pengaturan');
    }
}
