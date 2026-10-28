<?php

namespace App\External\Models;

use Illuminate\Database\Eloquent\Model;

class LogbookFoto extends Model
{
    protected $fillable = ['nim', 'tanggal', 'foto_path'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }
}
