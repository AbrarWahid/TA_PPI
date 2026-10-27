<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\UserRole;
use Illuminate\Http\Request;

class KpsController extends Controller
{
    public function index()
    {
        $kpsList = UserRole::with('user.dosen')
            ->where('role', 'kps')
            ->get();

        $dosen = Dosen::with('user')->orderBy('nama_dosen')->get();

        $prodiMap = [
            'informatika' => 'D4 TRIN – Teknologi Rekayasa Informatika Industri',
            'otomasi'     => 'D4 TRO – Teknologi Rekayasa Otomasi',
            'mekatronika' => 'D4 TRMO – Teknologi Rekayasa Mekatronika',
        ];

        return view('admin.kps.index', compact('kpsList', 'dosen', 'prodiMap'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'prodi'   => 'required|in:informatika,otomasi,mekatronika',
        ]);

        // Cek apakah prodi sudah punya KPS
        $sudahAda = UserRole::where('role', 'kps')
            ->where('prodi', $request->prodi)
            ->exists();

        if ($sudahAda) {
            return back()->with('error', 'Prodi ini sudah memiliki KPS. Hapus KPS lama terlebih dahulu.');
        }

        // Cek apakah dosen ini sudah jadi KPS di prodi lain
        $sudahKps = UserRole::where('role', 'kps')
            ->where('user_id', $request->user_id)
            ->exists();

        if ($sudahKps) {
            return back()->with('error', 'Dosen ini sudah menjadi KPS untuk prodi lain.');
        }

        UserRole::create([
            'user_id' => $request->user_id,
            'role'    => 'kps',
            'prodi'   => $request->prodi,
        ]);

        return back()->with('success', 'KPS berhasil ditetapkan!');
    }

    public function destroy(UserRole $userRole)
    {
        // Reset role_aktif jika sedang aktif sebagai KPS
        if ($userRole->user->role_aktif === 'kps') {
            $userRole->user->update(['role_aktif' => 'dosen_pengampu']);
        }

        $userRole->delete();

        return back()->with('success', 'KPS berhasil dihapus.');
    }
}