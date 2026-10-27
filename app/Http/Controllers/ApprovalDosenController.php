<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ApprovalDosenController extends Controller
{
    public function index()
    {
        $pendingDosen = User::where('role', 'dosen')
            ->where('status', 'pending')
            ->with('dosen')
            ->latest()
            ->get();

        $approvedDosen = User::where('role', 'dosen')
            ->where('status', 'active')
            ->with('dosen')
            ->latest()
            ->get();

        return view('admin.approval_akun', compact('pendingDosen', 'approvedDosen'));
    }

    public function approve(User $user)
    {
        $user->update(['status' => 'active']);
        return back()->with('success', 'Akun ' . $user->name . ' berhasil disetujui.');
    }

    public function approveWithRole(Request $request, User $user)
    {
        $request->validate([
            'akses_role' => 'required|in:dosen_pengampu,manager_proyek,keduanya',
        ]);

        $aksesRole = $request->akses_role;

        // Set role_aktif default berdasarkan akses
        $roleAktif = match($aksesRole) {
            'manager_proyek' => 'manager_proyek',
            'keduanya'       => 'dosen_pengampu', // default ke dosen, bisa switch
            default          => 'dosen_pengampu',
        };

        $user->update([
            'status'     => 'active',
            'akses_role' => $aksesRole,
            'role_aktif' => $roleAktif,
        ]);

        return back()->with('success', 'Akun ' . $user->name . ' disetujui dengan akses: ' . $aksesRole);
    }

    public function reject(User $user)
    {
        $user->dosen()->delete();
        $user->delete();
        return back()->with('success', 'Akun dosen berhasil ditolak dan dihapus.');
    }
}