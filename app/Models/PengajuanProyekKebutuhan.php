<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanProyekKebutuhan extends Model
{
    protected $table = 'pengajuan_proyek_kebutuhan';

    protected $fillable = [
        'pengajuan_proyek_id',
        'prodi',
        'jumlah_mahasiswa',
    ];

    public function pengajuanProyek(): BelongsTo
    {
        return $this->belongsTo(PengajuanProyek::class, 'pengajuan_proyek_id');
    }

    public function getProdiLabel(): string
    {
        return match($this->prodi) {
            'mekatronika'          => 'D4 Teknologi Rekayasa Mekatronika',
            'otomasi'              => 'D4 Teknologi Rekayasa Otomasi',
            'informatika_industri' => 'D4 Teknologi Rekayasa Informatika Industri',
            default                => $this->prodi,
        };
    }

    public function getProdiShort(): string
    {
        return match($this->prodi) {
            'mekatronika'          => 'Mekatronika',
            'otomasi'              => 'Otomasi',
            'informatika_industri' => 'Informatika Industri',
            default                => $this->prodi,
        };
    }

    public function getProdiColor(): string
    {
        return match($this->prodi) {
            'mekatronika'          => 'bg-blue-100 text-blue-700',
            'otomasi'              => 'bg-purple-100 text-purple-700',
            'informatika_industri' => 'bg-orange-100 text-orange-700',
            default                => 'bg-gray-100 text-gray-700',
        };
    }
}