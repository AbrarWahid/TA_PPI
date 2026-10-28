<?php

namespace App\External\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JadwalMonitoring extends Model
{
    use HasFactory;

    protected $fillable = [
        'nidn',
        'perusahaan',
        'tanggal',
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'nidn', 'nidn');
    }
}
