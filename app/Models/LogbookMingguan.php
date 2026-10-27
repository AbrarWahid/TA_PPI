<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogbookMingguan extends Model
{
    protected $table = 'logbook_mingguan';

    protected $fillable = [
        'mahasiswa_id',
        'pengajuan_proyek_id',
        'minggu_ke',
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
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function proyek()
    {
        return $this->belongsTo(PengajuanProyek::class, 'pengajuan_proyek_id');
    }

    public function logbookHarian()
    {
        return $this->hasMany(LogbookHarian::class, 'mahasiswa_id', 'mahasiswa_id')
                    ->where('pengajuan_proyek_id', $this->pengajuan_proyek_id)
                    ->where('minggu_ke', $this->minggu_ke);
    }
}