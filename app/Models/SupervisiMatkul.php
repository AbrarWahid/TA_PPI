<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisiMatkul extends Model
{
    protected $table = 'supervisi_matkul';

    protected $fillable = [
        'mahasiswa_id',
        'mata_kuliah_id',
        'dosen_id',
        'tahun_ajaran',
        'semester',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }
}