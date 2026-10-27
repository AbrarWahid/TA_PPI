<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table    = 'mahasiswa';
    
    // 'kelas' ditambahkan ke sini agar diizinkan masuk ke database
    protected $fillable = ['user_id', 'nim', 'nama', 'kelas'];

    public function user()          { return $this->belongsTo(User::class); }
    public function logbook()       { return $this->hasMany(LogbookMingguan::class); }
    public function laporan()       { return $this->hasMany(LaporanMkPpi::class); }
    public function logbookHarian() { return $this->hasMany(LogbookHarian::class, 'mahasiswa_id'); }

    public function penilaianManager()
    {
        return $this->hasMany(PenilaianManager::class, 'mahasiswa_id');
    }

    public function penilaianDosen()
    {
        return $this->hasMany(PenilaianDosen::class, 'mahasiswa_id');
    }

    public function proyek()
    {
        return $this->belongsToMany(PengajuanProyek::class, 'proyek_mahasiswa', 'mahasiswa_id', 'pengajuan_proyek_id')
                    ->withPivot('prodi');
    }

    public function proyekAktif()
    {
        return $this->proyek()->where('status', 'approved')->first();
    }
}