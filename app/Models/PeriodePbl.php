<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodePbl extends Model
{
    protected $table = 'periode_pbl';

    protected $fillable = [
        'nama_periode',
        'tahun_ajaran',
        'semester',
        'koordinator_id',
        'mekanisme_proyek',
        'tanggal_mulai',
        'tanggal_selesai',
        'frekuensi_logbook',
        'bobot_manager',
        'bobot_dosen',
        'is_aktif',
    ];

    protected $casts = [
        'tanggal_mulai'  => 'date',
        'tanggal_selesai' => 'date',
        'is_aktif'       => 'boolean',
    ];

    // Relasi ke koordinator (users)
    public function koordinator()
    {
        return $this->belongsTo(User::class, 'koordinator_id');
    }

    // Relasi ke pengajuan proyek
    public function pengajuanProyek()
    {
        return $this->hasMany(PengajuanProyek::class, 'periode_id');
    }

    // Scope: ambil periode yang aktif
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    // Static helper: ambil periode aktif
    public static function getAktif(): ?self
    {
        return self::where('is_aktif', true)->first();
    }

    // Label semester
    public function getLabelSemesterAttribute(): string
    {
        return ucfirst($this->semester);
    }

    // Validasi bobot harus 100
    public function bobotValid(): bool
    {
        return ($this->bobot_manager + $this->bobot_dosen) === 100;
    }
}