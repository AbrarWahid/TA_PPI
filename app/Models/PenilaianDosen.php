<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianDosen extends Model
{
    protected $table = 'penilaian_dosen';

    protected $fillable = [
        'mahasiswa_id', 'supervisi_matkul_id', 'dosen_id',
        'lit_informasi', 'lit_media', 'lit_teknologi',
        'pr_konten', 'pr_visual', 'pr_kosakata', 'pr_tanya_jawab', 'pr_mata_gerak',
        'la_penulisan', 'la_pilihan_kata', 'la_konten',
        'nilai_dosen', 'catatan_dosen',
    ];

    // ── Accessor ──────────────────────────────────────────────
    public function getLiteracySkillsAttribute(): ?float
    {
        if (is_null($this->lit_informasi)) return null;
        return round(($this->lit_informasi + $this->lit_media + $this->lit_teknologi) / 3, 1);
    }

    public function getPresentasiAttribute(): ?float
    {
        if (is_null($this->pr_konten)) return null;
        return round(($this->pr_konten + $this->pr_visual + $this->pr_kosakata + $this->pr_tanya_jawab + $this->pr_mata_gerak) / 5, 1);
    }

    public function getLaporanAkhirAttribute(): ?float
    {
        if (is_null($this->la_penulisan)) return null;
        return round(($this->la_penulisan + $this->la_pilihan_kata + $this->la_konten) / 3, 1);
    }

    // ── Relasi ────────────────────────────────────────────────
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function supervisiMatkul()
    {
        return $this->belongsTo(SupervisiMatkul::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }
}