<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Perizinan extends Model
{
    protected $table = 'perizinan';
    protected $primaryKey = 'id_izin';
    public $timestamps = false;

    protected $fillable = [
        'id_pengguna_pengaju',   // ← perhatikan: ini yang benar
        'id_admin_validator',
        'jenis_izin',
        'tgl_pengajuan',
        'tgl_mulai',              // ← perhatikan: tgl_mulai (bukan tgt_mulai)
        'tgl_selesai',           // ← perhatikan: tgl_selesai (bukan tgt_selesai)
        'keterangan',
        'file_surat',
        'status_approval',
        'catatan_admin',
        'tgl_validasi',
    ];

    public function pengaju(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna_pengaju', 'id_pengguna');
    }

    public function adminValidator(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_admin_validator', 'id_pengguna');
    }

}