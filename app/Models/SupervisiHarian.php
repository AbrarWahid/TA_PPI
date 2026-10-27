<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisiHarian extends Model
{
    protected $table = 'supervisi_harian';

    protected $fillable = [
        'mahasiswa_id',
        'supervisi_matkul_id',
        'minggu_ke',
        'hari',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'aktivitas',
        'kendala',
        'dokumentasi',
    ];

    protected $casts = [
        'tanggal'     => 'date',
        'jam_mulai'   => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function supervisiMatkul()
    {
        return $this->belongsTo(SupervisiMatkul::class, 'supervisi_matkul_id');
    }
}