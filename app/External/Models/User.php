<?php

namespace App\External\Models;

class User extends \App\Models\User
{
    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class, 'user_id');
    }

    public function dosen()
    {
        return $this->hasOne(Dosen::class, 'user_id');
    }

    public function pembimbingIndustri()
    {
        return $this->hasOne(PembimbingIndustri::class, 'user_id');
    }
}
