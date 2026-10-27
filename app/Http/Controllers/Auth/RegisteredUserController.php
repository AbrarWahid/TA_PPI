<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name'           => ['required', 'string', 'max:255'],
        'email'          => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'nim'            => ['required', 'string', 'max:20'],
        'role'           => ['required', 'in:mahasiswa,dosen'],
        'prodi'          => ['nullable', 'required_if:role,mahasiswa', 'string'],
        'kelas_register' => ['nullable', 'required_if:role,mahasiswa', 'string'],
        'password'       => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    // Validasi NIM/NIDN unik
    if ($request->role === 'mahasiswa') {
        if (Mahasiswa::where('nim', $request->nim)->exists()) {
            return back()->withErrors(['nim' => 'NIM sudah terdaftar di sistem.'])->withInput();
        }
    } else {
        if (Dosen::where('nidn', $request->nim)->exists()) {
            return back()->withErrors(['nim' => 'NIDN sudah terdaftar di sistem.'])->withInput();
        }
    }

    // Mahasiswa langsung active, dosen selalu pending (menunggu approval admin)
    $status = $request->role === 'mahasiswa' ? 'active' : 'pending';

    try {
        DB::transaction(function () use ($request, $status) {
            $user = User::create([
                'name'           => $request->name,
                'email'          => $request->email,
                'password'       => Hash::make($request->password),
                'role'           => $request->role,
                'prodi'          => $request->prodi,
                'kelas_register' => $request->kelas_register,
                'status'         => $status,
            ]);

            if ($request->role === 'mahasiswa') {
                Mahasiswa::create([
                    'user_id' => $user->id,
                    'nim'     => $request->nim,
                    'nama'    => $request->name,
                ]);
                event(new Registered($user));
                Auth::login($user);
            } else {
                // role = 'dosen', role_aktif akan dipilih setelah admin approve & login pertama
                Dosen::create([
                    'user_id'    => $user->id,
                    'nidn'       => $request->nim,
                    'nama_dosen' => $request->name,
                ]);
            }
        });
    } catch (\Exception $e) {
        return back()->withErrors(['email' => 'Terjadi kesalahan saat mendaftar, coba lagi.'])->withInput();
    }

    if ($request->role === 'mahasiswa') {
        return redirect(route('dashboard'));
    }

    return redirect(route('login'))
        ->with('success', 'Pendaftaran berhasil! Akun Anda sedang menunggu persetujuan Admin.');
}
}