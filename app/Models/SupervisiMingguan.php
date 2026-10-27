<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisiMingguan extends Model
{
    protected $table = 'supervisi_mingguan';

    protected $fillable = [
        'mahasiswa_id',
        'supervisi_matkul_id',
        'pengajuan_proyek_id',
        'minggu_ke',
        'dosen_id',
        'matkul_id',
        'pdf_path',
        'status',
        'catatan_dosen',
        'diajukan_at',
        'diverifikasi_at',
    ];

    protected $casts = [
        'diajukan_at'     => 'datetime',
        'diverifikasi_at' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function supervisiMatkul()
    {
        return $this->belongsTo(SupervisiMatkul::class, 'supervisi_matkul_id');
    }

    public function proyek()
    {
        return $this->belongsTo(PengajuanProyek::class, 'pengajuan_proyek_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function matkul()
    {
        return $this->belongsTo(MataKuliah::class, 'matkul_id');
    }
}