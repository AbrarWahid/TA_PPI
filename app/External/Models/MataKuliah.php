<?php

namespace App\External\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    protected $table = 'external_mata_kuliahs';
    protected $primaryKey = 'kd_mat';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id_prodi', 'kd_mat', 'nama_komp', 'jam_min'];

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'id_prodi', 'id_prodi');
    }

    public function logbooks()
    {
        return $this->hasMany(Logbook::class, 'kd_mat', 'kd_mat');
    }
}
