<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class, 'id_pengguna', 'id_pengguna');
    }

    public function perizinans(): HasMany
    {
        return $this->hasMany(Perizinan::class, 'id_pengguna_pengaju', 'id_pengguna');
    }

}