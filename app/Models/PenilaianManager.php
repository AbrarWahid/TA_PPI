<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianManager extends Model
{
    protected $table = 'penilaian_manager';

    protected $fillable = [
        'mahasiswa_id', 'pengajuan_proyek_id', 'manager_id',
        'ls_critical_thinking', 'ls_kolaborasi', 'ls_kreativitas', 'ls_komunikasi',
        'lf_fleksibilitas', 'lf_kepemimpinan', 'lf_produktivitas', 'lf_social_skill',
        'lp_rpp', 'lp_logbook', 'lp_dokumen_projek',
        'nilai_manager', 'catatan_manager',
    ];

    // ── Accessor ──────────────────────────────────────────────
    public function getLearningSkillsAttribute(): ?float
    {
        if (is_null($this->ls_critical_thinking)) return null;
        return round(($this->ls_critical_thinking + $this->ls_kolaborasi + $this->ls_kreativitas + $this->ls_komunikasi) / 4, 1);
    }

    public function getLifeSkillsAttribute(): ?float
    {
        if (is_null($this->lf_fleksibilitas)) return null;
        return round(($this->lf_fleksibilitas + $this->lf_kepemimpinan + $this->lf_produktivitas + $this->lf_social_skill) / 4, 1);
    }

    public function getLaporanProjectAttribute(): ?float
    {
        if (is_null($this->lp_rpp)) return null;
        return round(($this->lp_rpp + $this->lp_logbook + $this->lp_dokumen_projek) / 3, 1);
    }

    // ── Relasi ────────────────────────────────────────────────
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function pengajuanProyek()
    {
        return $this->belongsTo(PengajuanProyek::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}