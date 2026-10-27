<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    protected $table    = 'dosen';
    protected $fillable = ['user_id','nidn','nama_dosen'];

    public function user()  { return $this->belongsTo(User::class); }
    public function kelas() { return $this->hasMany(Kelas::class); }
}