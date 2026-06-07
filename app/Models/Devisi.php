<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Devisi extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Table Configuration
    |--------------------------------------------------------------------------
    */

    protected $table      = 'devisi';
    protected $primaryKey = 'id_devisi';
    public    $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Attributes
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'nama_devisi',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function pengguna(): HasMany
    {
        return $this->hasMany(Pengguna::class, 'divisi', 'id_devisi');
    }

    public function karyawan(): HasMany
    {
        return $this->hasMany(Pengguna::class, 'divisi', 'id_devisi');
    }
}