<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = auth()->user();

        if (!$user) {
            return redirect('/login');
        }

        // Blok user yang masih pending
        if ($user->status === 'pending') {
            auth()->logout();
            return redirect('/login')->withErrors([
                'email' => 'Akun Anda belum disetujui Admin.',
            ]);
        }

        $roleEfektif = $user->getRoleEfektif();
        $diizinkan   = false;

        foreach ($roles as $role) {
            // Match langsung role efektif (termasuk 'kps')
            if ($roleEfektif === $role) {
                $diizinkan = true;
                break;
            }

            // Match role permanen (admin, mahasiswa, manager_proyek)
            if ($user->role === $role) {
                $diizinkan = true;
                break;
            }

            // Dosen: cek berdasarkan role_aktif
            if ($user->role === 'dosen') {
                if ($role === 'manager_proyek' && $roleEfektif === 'manager_proyek') {
                    $diizinkan = true;
                    break;
                }
                if ($role === 'dosen' && $roleEfektif === 'dosen_pengampu') {
                    $diizinkan = true;
                    break;
                }
                if ($role === 'kps' && $roleEfektif === 'kps') {
                    $diizinkan = true;
                    break;
                }
            }
        }

        if (!$diizinkan) {
            if ($user->role === 'dosen' || $user->role === 'admin') {
                return redirect()->route('dashboard')
                    ->with('error', 'Akses ditolak. Role Anda saat ini tidak memiliki izin.');
            }
            abort(403);
        }

        return $next($request);
    }
}