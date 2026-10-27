<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengajuanProyek extends Model
{
    protected $table = 'pengajuan_proyek';

    protected $fillable = [
        'kode_pengajuan',
        'periode_id',  
        'manager_id',
        'judul_proyek',
        'deskripsi',
        'tujuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'anggaran',
        'status',
        'catatan_admin',
        'diproses_oleh',
        'diproses_at',
        'dosen_pengampu_id',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'diproses_at'     => 'datetime',
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function diprosesOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function kebutuhan(): HasMany
    {
        return $this->hasMany(PengajuanProyekKebutuhan::class, 'pengajuan_proyek_id');
    }

    public function mahasiswa(): BelongsToMany
    {
        return $this->belongsToMany(Mahasiswa::class, 'proyek_mahasiswa', 'pengajuan_proyek_id', 'mahasiswa_id')
                    ->withPivot('prodi')
                    ->withTimestamps();
    }

    // Helper methods
    public function getStatusBadgeColor(): string
    {
        return match($this->status) {
            'approved' => 'bg-green-100 text-green-700',
            'rejected' => 'bg-red-100 text-red-700',
            default    => 'bg-yellow-100 text-yellow-700',
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default    => 'Menunggu',
        };
    }

    public function getStatusIcon(): string
    {
        return match($this->status) {
            'approved' => 'check_circle',
            'rejected' => 'cancel',
            default    => 'pending',
        };
    }

    public function getTotalMahasiswa(): int
    {
        return $this->kebutuhan->sum('jumlah_mahasiswa');
    }
    public function logbookHarian(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(LogbookHarian::class, 'pengajuan_proyek_id');
}
public function dosenPengampu()
{
    return $this->belongsTo(\App\Models\Dosen::class, 'dosen_pengampu_id');
}
public function periode()
    {
        return $this->belongsTo(PeriodePbl::class, 'periode_id');
    }
}