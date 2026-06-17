<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'jatah_cuti_bulanan',
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

}
