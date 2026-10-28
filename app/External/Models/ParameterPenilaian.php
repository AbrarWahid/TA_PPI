<?php

namespace App\External\Models;

use Illuminate\Database\Eloquent\Model;

class ParameterPenilaian extends Model
{
    protected $fillable = ['jenis', 'sub_kategori', 'indikator'];

    protected $casts = [
        'indikator' => 'array',
    ];
}
