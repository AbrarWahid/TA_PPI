<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    protected $table = 'penilaian';

    protected $fillable = [
        'mahasiswa_id', 'dosen_id', 'pengajuan_proyek_id',

        // Manager Proyek (55%)
        'ls_critical_thinking', 'ls_kolaborasi', 'ls_kreativitas', 'ls_komunikasi',
        'lf_fleksibilitas', 'lf_kepemimpinan', 'lf_produktivitas', 'lf_social_skill',
        'lp_rpp', 'lp_logbook', 'lp_dokumen_projek',
        'nilai_manager', 'catatan_manager',

        // Dosen Pengampu (45%)
        'lit_informasi', 'lit_media', 'lit_teknologi',
        'pr_konten', 'pr_visual', 'pr_kosakata', 'pr_tanya_jawab', 'pr_mata_gerak',
        'la_penulisan', 'la_pilihan_kata', 'la_konten',
        'nilai_dosen', 'catatan_dosen',

        'nilai_akhir',
    ];

    // RELASI
    public function mahasiswa() { return $this->belongsTo(Mahasiswa::class); }
    public function dosen()     { return $this->belongsTo(Dosen::class); }
    public function proyek()    { return $this->belongsTo(PengajuanProyek::class, 'pengajuan_proyek_id'); }

    // HITUNG NILAI MANAGER (55%)
    public function hitungNilaiManager(): float
    {
        $learningSkills =
            (($this->ls_critical_thinking ?? 0) * 0.05) +
            (($this->ls_kolaborasi        ?? 0) * 0.05) +
            (($this->ls_kreativitas       ?? 0) * 0.05) +
            (($this->ls_komunikasi        ?? 0) * 0.05);

        $lifeSkills =
            (($this->lf_fleksibilitas  ?? 0) * 0.05) +
            (($this->lf_kepemimpinan   ?? 0) * 0.05) +
            (($this->lf_produktivitas  ?? 0) * 0.05) +
            (($this->lf_social_skill   ?? 0) * 0.05);

        $laporanProject =
            (($this->lp_rpp            ?? 0) * 0.05) +
            (($this->lp_logbook        ?? 0) * 0.05) +
            (($this->lp_dokumen_projek ?? 0) * 0.05);

        return round($learningSkills + $lifeSkills + $laporanProject, 2);
    }

    // HITUNG NILAI DOSEN (45%)
    public function hitungNilaiDosen(): float
    {
        $literacy =
            (($this->lit_informasi  ?? 0) * 0.05) +
            (($this->lit_media      ?? 0) * 0.05) +
            (($this->lit_teknologi  ?? 0) * 0.05);

        $presentasi =
            (($this->pr_konten      ?? 0) * 0.03) +
            (($this->pr_visual      ?? 0) * 0.03) +
            (($this->pr_kosakata    ?? 0) * 0.03) +
            (($this->pr_tanya_jawab ?? 0) * 0.03) +
            (($this->pr_mata_gerak  ?? 0) * 0.03);

        $laporanAkhir =
            (($this->la_penulisan    ?? 0) * 0.05) +
            (($this->la_pilihan_kata ?? 0) * 0.05) +
            (($this->la_konten       ?? 0) * 0.05);

        return round($literacy + $presentasi + $laporanAkhir, 2);
    }

    // HITUNG NILAI AKHIR
    public function hitungNilaiAkhir(): float
    {
        return round(($this->nilai_manager ?? 0) + ($this->nilai_dosen ?? 0), 2);
    }

    public function getGrade(): string
    {
        $nilai = $this->nilai_akhir ?? 0;
        if ($nilai >= 86) return 'A';
        if ($nilai >= 71) return 'B';
        if ($nilai >= 51) return 'C';
        return 'D';
    }

    public function getGradeColor(): string
    {
        $nilai = $this->nilai_akhir ?? 0;
        if ($nilai >= 86) return '#004d40';
        if ($nilai >= 71) return '#00695c';
        if ($nilai >= 51) return '#f59e0b';
        return '#ef4444';
    }
}