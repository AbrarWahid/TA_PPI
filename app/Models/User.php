<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'role_aktif', 'akses_role', 'prodi', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Relasi ──
    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class);
    }

    public function dosen()
    {
        return $this->hasOne(Dosen::class);
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class);
    }

    // ── Role checks (berdasarkan role permanen) ──
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    public function isDosen(): bool
    {
        return $this->role === 'dosen';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager_proyek' || $this->roleAktifManager();
    }

    public function isKps(): bool
    {
        return $this->role_aktif === 'kps';
    }

    // ── Cek apakah user punya akses role tertentu di user_roles ──
    public function hasUserRole(string $role): bool
    {
        return $this->userRoles()->where('role', $role)->exists();
    }

    // ── Ambil prodi KPS (dari tabel user_roles) ──
    public function getProdiKps(): ?string
    {
        $kpsRole = $this->userRoles()->where('role', 'kps')->first();
        return $kpsRole?->prodi;
    }

    // ── Role aktif checks ──
    public function roleAktifManager(): bool
    {
        return $this->role === 'dosen' && $this->role_aktif === 'manager_proyek';
    }

    public function roleAktifDosen(): bool
    {
        return $this->role === 'dosen' && $this->role_aktif === 'dosen_pengampu';
    }

    public function roleAktifKps(): bool
    {
        return $this->role === 'dosen' && $this->role_aktif === 'kps';
    }

    public function getRoleEfektif(): string
    {
        if ($this->role === 'dosen') {
            return $this->role_aktif ?? 'dosen_pengampu';
        }
        return $this->role;
    }

    // ── Daftar role yang bisa dipilih user ini ──
    public function getAvailableRoles(): array
    {
        if ($this->role !== 'dosen') {
            return [];
        }

        // Ambil roles dari user_roles (kps, dll)
        $roles = $this->userRoles->pluck('role')->toArray();

        // Selalu tambahkan roles dari akses_role lama
        $akses = $this->akses_role ?? 'dosen_pengampu';
        if ($akses === 'keduanya') {
            $roles = array_merge($roles, ['dosen_pengampu', 'manager_proyek']);
        } elseif ($akses === 'dosen_pengampu') {
            $roles[] = 'dosen_pengampu';
        } elseif ($akses === 'manager_proyek') {
            $roles[] = 'manager_proyek';
        }

        // Hapus duplikat
        return array_unique($roles);
    }
}