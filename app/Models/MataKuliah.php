<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    use HasFactory;

    // Sesuaikan nama tabel jika berbeda
    protected $table = 'mata_kuliah';

    // Wajib didaftarkan agar bisa di-insert
    protected $fillable = [
        'kode_matkul',
        'program_studi',
        'nama_matkul',
        'sks',
        'semester'
    ];
}
