<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table    = 'kelas';
    protected $fillable = ['kode_kelas', 'nama_kelas'];

    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class, 'kelas', 'nama_kelas');
    }
}