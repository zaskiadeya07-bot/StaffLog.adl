<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengguna extends Model
{
    protected $table      = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    public    $timestamps = false;

    protected $fillable = [
        'nama_lengkap',
        'username',
        'password',
        'role',
        'divisi',
        'nomor_hp',
        'tgl_mulai_kerja',
        'alamat',
        'id_karyawan',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'tgl_mulai_kerja' => 'date',
        'role'            => 'string',
    ];

    public function devisi(): BelongsTo
    {
        return $this->belongsTo(Devisi::class, 'divisi', 'id_devisi');
    }

}