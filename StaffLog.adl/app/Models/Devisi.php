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

    /**
     * Satu divisi memiliki banyak pengguna (karyawan)
     * 
     * Asumsi: Di tabel pengguna ada kolom 'devisi_id' sebagai foreign key
     */
    public function pengguna(): HasMany
    {
        // Jika kolom foreign key di tabel pengguna adalah 'devisi_id'
        return $this->hasMany(Pengguna::class, 'devisi_id', 'id_devisi');
    }
    
    /**
     * Alternative: Jika ingin lebih eksplisit
     */
    public function karyawan(): HasMany
    {
        return $this->hasMany(Pengguna::class, 'devisi_id', 'id_devisi');
    }
}