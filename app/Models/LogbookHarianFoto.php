<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogbookHarianFoto extends Model
{
    protected $table = 'logbook_harian_fotos';

    protected $fillable = [
        'mahasiswa_id',
        'pengajuan_proyek_id',
        'tanggal',
        'foto_path',
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
}
