<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogbookHarian extends Model
{
    protected $table = 'logbook_harian';

    protected $fillable = [
    'mahasiswa_id',
    'pengajuan_proyek_id',
    'minggu_ke',
    'hari',
    'tanggal',
    'jam_masuk',
    'jam_keluar',
    'aktivitas',
    'kendala',
    'dokumentasi',
    'status_verifikasi',
    'catatan_dosen',
];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(PengajuanProyek::class, 'pengajuan_proyek_id');
    }

    public function getStatusBadgeColor(): string
    {
        return match($this->status_verifikasi) {
            'disetujui' => 'bg-teal-50 text-teal-700',
            'ditolak'   => 'bg-red-50 text-red-700',
            default     => 'bg-amber-50 text-amber-700',
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status_verifikasi) {
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
            default     => 'Menunggu',
        };
    }
}