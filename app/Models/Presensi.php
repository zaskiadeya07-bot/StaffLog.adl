<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    protected $table      = 'presensi';
    protected $primaryKey = 'id_presensi';
    public    $timestamps = true;

    protected $fillable = [
        'id_pengguna',
        'id_pengaturan',
        'id_izin',
        'tanggal',
        'check_in',
        'check_in_lat',
        'check_in_lng',
        'check_out',
        'check_out_lat',
        'check_out_lng',
        'status',
        'menit_terlambat',
        'catatan_keterlambatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'check_in_lat' => 'decimal:8',
        'check_in_lng' => 'decimal:8',
        'check_out_lat' => 'decimal:8',
        'check_out_lng' => 'decimal:8',
        'menit_terlambat' => 'integer',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function perizinan(): BelongsTo
    {
        return $this->belongsTo(Perizinan::class, 'id_izin', 'id_izin');
    }
}
