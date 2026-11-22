{{-- resources/views/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | PBL &amp; PPI Portal — Politeknik Manufaktur Bandung</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    @include('layouts.partials.design-system')
    <style>
        .akasia-left-panel {
            background: linear-gradient(135deg, #02002e 0%, #013981 52%, #142c6e 100%);
            position: relative;
            overflow: hidden;
        }
        .akasia-left-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: url("{{ asset('images/akasia/sapolman.png') }}");
            background-repeat: no-repeat;
            background-position: left bottom;
            background-size: 100%;
            z-index: 1;
            pointer-events: none;
        }
        .input-field, .input-field-sm {
            width: 100%;
            height: 42px;
            padding: 0.5rem 0.875rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            color: #0b0b0c;
            font-size: 0.8125rem;
            font-weight: 500;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        }
        .input-field::placeholder, .input-field-sm::placeholder { color: #94a3b8; font-weight: 400; }
        .input-field:focus, .input-field-sm:focus {
            border-color: #013981;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(1, 57, 129, 0.10);
        }

        .role-btn, .prodi-btn {
            transition: all 150ms ease;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 0.75rem;
            padding: 0.625rem 0.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            color: #64748b;
            cursor: pointer;
            width: 100%;
        }
        .role-btn:hover, .prodi-btn:hover { background: #f1f5f9; border-color: #cbd5e1; color: #0b0b0c; }
        .role-btn.active, .prodi-btn.active { border-color: #013981; background: #013981; color: #ffffff; }

        .kelas-btn {
            transition: all 150ms ease;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 0.75rem;
            padding: 0.5rem 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-weight: 600;
            font-size: 0.75rem;
            cursor: pointer;
        }
        .kelas-btn:hover { background: #f1f5f9; color: #0b0b0c; }
        .kelas-btn.active { border-color: #013981; background: #013981; color: #ffffff; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 font-sans text-[#0b0b0c] antialiased">

<div class="min-h-screen flex flex-col lg:flex-row w-full">

    {{-- ============================================================
         LEFT PANEL — DUAL SCREEN POLMAN BANDUNG BRANDING
    ============================================================ --}}
    <div class="hidden lg:flex lg:w-1/2 akasia-left-panel flex-col justify-center p-12 xl:p-16 text-white">
        <div class="relative z-10 space-y-5 max-w-lg">
            <div class="inline-flex items-center gap-2 text-xs font-bold text-emerald-300 bg-emerald-400/15 border border-emerald-400/30 px-3.5 py-1.5 rounded-full uppercase tracking-wide">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Jurusan Automation Engineering</span>
            </div>
            <h1 class="text-4xl font-extrabold text-white leading-tight tracking-tight drop-shadow-sm">
                Registrasi Sivitas Akademika
            </h1>
            <p class="text-base text-white/85 leading-relaxed font-medium">
                Daftarkan akun mahasiswa atau dosen untuk mengakses pencatatan logbook, supervisi mata kuliah, dan penilaian terintegrasi.
            </p>
        </div>
    </div>

    {{-- ============================================================
         RIGHT PANEL — REGISTER CARD
    ============================================================ --}}
    <div class="flex-1 flex flex-col justify-center p-6 sm:p-10 lg:p-12 relative min-h-screen overflow-y-auto">

        <div class="flex items-center justify-between lg:justify-end gap-3.5 mb-6">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3.5">
                <img src="{{ asset('images/logo-polman-teks.webp') }}" alt="Polman Bandung" class="h-8 w-auto object-contain">
                <img src="{{ asset('images/logo-blu-speed.png') }}" alt="BLU Speed" class="h-9 w-auto object-contain">
            </a>
        </div>

        <div class="w-full max-w-md mx-auto">
            <div class="w-full border border-slate-200/80 shadow-2xl shadow-slate-200/50 bg-white/95 backdrop-blur-xl rounded-3xl p-7 sm:p-8">
                <div class="space-y-1 pb-5">
                    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Buat Akun Baru</h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Lengkapi data kredensial PBL &amp; PPI Anda.</p>
                </div>

                @if($errors->any())
                <div class="mb-4 flex items-start gap-2.5 p-3.5 bg-red-50 border border-red-200 rounded-xl">
                    <span class="material-symbols-outlined text-[#b91c1c] shrink-0 mt-0.5 text-[18px]">error</span>
                    <div>
                        @foreach($errors->all() as $error)
                        <p class="text-[#b91c1c] text-xs font-semibold">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('register') }}" id="registerForm" class="space-y-3.5">
                    @csrf

                    {{-- ROLE --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Daftar Sebagai</label>
                        <div class="grid grid-cols-2 gap-2.5">
                            <button type="button" onclick="setRole('mahasiswa')" id="role-mahasiswa" class="role-btn active">
                                <span class="material-symbols-outlined text-[18px]">school</span>
                                <span class="font-bold text-xs">Mahasiswa</span>
                            </button>
                            <button type="button" onclick="setRole('dosen')" id="role-dosen" class="role-btn">
                                <span class="material-symbols-outlined text-[18px]">co_present</span>
                                <span class="font-bold text-xs">Dosen</span>
                            </button>
                        </div>
                        <input type="hidden" name="role" id="roleInput" value="mahasiswa">
                    </div>

                    {{-- INFO DOSEN --}}
                    <div id="dosenInfo" class="hidden flex items-start gap-2.5 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                        <span class="material-symbols-outlined text-[#b45309] shrink-0 text-[16px]">info</span>
                        <p class="text-[#b45309] text-xs font-medium">Akun dosen memerlukan <strong>persetujuan Admin</strong> sebelum dapat digunakan.</p>
                    </div>

                    {{-- NAMA --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="Masukkan nama lengkap"
                               class="input-field">
                    </div>

                    {{-- EMAIL & NIM/NIDN --}}
                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   placeholder="nama@polman.ac.id"
                                   class="input-field-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5" id="nimLabel">NIM</label>
                            <input type="text" name="nim" value="{{ old('nim') }}" required
                                   placeholder="23010xxx" id="nimInput"
                                   class="input-field-sm">
                        </div>
                    </div>

                    {{-- PRODI --}}
                    <div id="prodiSection">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Program Studi</label>
                        <div class="grid grid-cols-3 gap-2">
                            @php
                            $prodis = [
                                ['id' => 'informatika', 'label' => 'Informatika', 'icon' => 'terminal'],
                                ['id' => 'otomasi',     'label' => 'Otomasi',     'icon' => 'settings_suggest'],
                                ['id' => 'mekatronika', 'label' => 'Mekatronika', 'icon' => 'precision_manufacturing'],
                            ];
                            @endphp
                            @foreach($prodis as $p)
                            <button type="button" onclick="setProdi('{{ $p['id'] }}')"
                                    id="prodi-{{ $p['id'] }}" class="prodi-btn">
                                <span class="material-symbols-outlined text-[18px]">{{ $p['icon'] }}</span>
                                <span class="font-bold text-[11px]">{{ $p['label'] }}</span>
                            </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="prodi" id="prodiInput" value="{{ old('prodi') }}">
                    </div>

                    {{-- KELAS --}}
                    <div id="kelasSection" class="hidden">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Kelas</label>
                        <div class="grid grid-cols-4 gap-2" id="kelasGrid"></div>
                        <input type="hidden" name="kelas_register" id="kelasInput" value="{{ old('kelas_register') }}">
                    </div>

                    {{-- PASSWORD --}}
                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kata Sandi</label>
                            <input type="password" name="password" required autocomplete="new-password"
                                   placeholder="••••••••" id="pw1"
                                   class="input-field-sm"
                                   oninput="checkStrength(this.value)">
                            <div id="strength-bar" class="hidden mt-1.5">
                                <div class="flex gap-1">
                                    <div id="s1" class="h-1 flex-1 rounded-full bg-slate-200 transition-all duration-300"></div>
                                    <div id="s2" class="h-1 flex-1 rounded-full bg-slate-200 transition-all duration-300"></div>
                                    <div id="s3" class="h-1 flex-1 rounded-full bg-slate-200 transition-all duration-300"></div>
                                    <div id="s4" class="h-1 flex-1 rounded-full bg-slate-200 transition-all duration-300"></div>
                                </div>
                                <p id="strength-label" class="text-[10px] font-semibold mt-0.5"></p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Konfirmasi</label>
                            <input type="password" name="password_confirmation" required autocomplete="new-password"
                                   placeholder="••••••••" id="pw2"
                                   class="input-field-sm"
                                   oninput="checkMatch()">
                            <p id="match-label" class="text-[10px] font-semibold mt-1.5 hidden"></p>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" id="submit-btn"
                                class="w-full h-11 flex items-center justify-center gap-2 bg-[#013981] hover:bg-[#012b63] text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition-all active:scale-[0.98]">
                            <span>Daftar Akun</span>
                        </button>
                    </div>
                </form>

                <div class="text-center text-sm text-slate-500 font-medium mt-5 pt-4 border-t border-slate-100">
                    Sudah memiliki akun?
                    <a href="{{ route('login') }}" class="font-bold text-[#013981] hover:underline ml-1">
                        Masuk Sekarang
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center text-xs text-slate-400 font-medium">
            &copy; {{ date('Y') }} Politeknik Manufaktur Bandung. All rights reserved.
        </div>
    </div>
</div>

<script>
const kelasMap = {
    informatika: ['AEC 1','AEC 2','AEC 3','AEC 4'],
    otomasi:     ['AEB 1','AEB 2','AEB 3','AEB 4'],
    mekatronika: ['AEA 1','AEA 2','AEA 3','AEA 4'],
};

function setRole(role) {
    document.getElementById('roleInput').value = role;
    ['mahasiswa','dosen'].forEach(r => document.getElementById('role-' + r).classList.remove('active'));
    document.getElementById('role-' + role).classList.add('active');
    const isMhs = role === 'mahasiswa';
    document.getElementById('prodiSection').classList.toggle('hidden', !isMhs);
    document.getElementById('kelasSection').classList.add('hidden');
    document.getElementById('dosenInfo').classList.toggle('hidden', isMhs);
    document.getElementById('nimLabel').textContent = isMhs ? 'NIM' : 'NIDN';
    document.getElementById('nimInput').placeholder = isMhs ? '23010xxx' : '19880xxx';
    document.getElementById('prodiInput').value = '';
    document.getElementById('kelasInput').value = '';
    document.querySelectorAll('.prodi-btn').forEach(b => b.classList.remove('active'));
}

function setProdi(prodi) {
    document.getElementById('prodiInput').value = prodi;
    document.getElementById('kelasInput').value = '';
    document.querySelectorAll('.prodi-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('prodi-' + prodi).classList.add('active');
    const grid = document.getElementById('kelasGrid');
    grid.innerHTML = '';
    kelasMap[prodi].forEach(kls => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = kls;
        btn.className = 'kelas-btn';
        btn.onclick = () => setKelas(kls);
        btn.id = 'kelas-' + kls.replace(' ','-');
        grid.appendChild(btn);
    });
    document.getElementById('kelasSection').classList.remove('hidden');
}

function setKelas(kls) {
    document.getElementById('kelasInput').value = kls;
    document.querySelectorAll('.kelas-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('kelas-' + kls.replace(' ','-')).classList.add('active');
}

function checkStrength(val) {
    const bar   = document.getElementById('strength-bar');
    const label = document.getElementById('strength-label');
    if (!val.length) { bar.classList.add('hidden'); return; }
    bar.classList.remove('hidden');
    let score = 0;
    if (val.length >= 8)          score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const colors = ['bg-red-500','bg-amber-500','bg-amber-600','bg-emerald-600'];
    const labels = ['Sangat lemah','Lemah','Cukup','Kuat'];
    ['s1','s2','s3','s4'].forEach((id, i) => {
        document.getElementById(id).className = 'h-1 flex-1 rounded-full transition-all duration-300 ' + (i < score ? colors[score-1] : 'bg-slate-200');
    });
    label.textContent = labels[score-1] ?? '';
    label.className = 'text-[10px] font-semibold mt-0.5 ' +
        (score <= 1 ? 'text-[#b91c1c]' : score <= 3 ? 'text-[#b45309]' : 'text-[#15803d]');
}

function checkMatch() {
    const pw1   = document.getElementById('pw1').value;
    const pw2   = document.getElementById('pw2').value;
    const label = document.getElementById('match-label');
    if (!pw2.length) { label.classList.add('hidden'); return; }
    label.classList.remove('hidden');
    if (pw1 === pw2) {
        label.textContent = '✓ Kata sandi cocok';
        label.className = 'text-[10px] font-semibold mt-1.5 text-[#15803d]';
    } else {
        label.textContent = '✗ Tidak cocok';
        label.className = 'text-[10px] font-semibold mt-1.5 text-[#b91c1c]';
    }
}

document.getElementById('registerForm').addEventListener('submit', function() {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<span>Mendaftarkan...</span>';
    btn.style.opacity = '0.75';
});
</script>

</body>
</html>
