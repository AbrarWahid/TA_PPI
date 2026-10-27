{{-- resources/views/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | PBL Portal AE Polman Bandung</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        aqua: {
                            50:  '#f0fffe',
                            100: '#ccfff7',
                            200: '#7fffd4',
                            300: '#40e0d0',
                            400: '#00c4b4',
                            500: '#00a896',
                            600: '#008080',
                            700: '#006666',
                            800: '#004d4d',
                            900: '#003333',
                        }
                    },
                    keyframes: {
                        fadeUp: {
                            '0%':   { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    },
                    animation: {
                        fadeUp: 'fadeUp 0.5s ease forwards',
                    }
                }
            }
        }
    </script>
    <style>
        ::selection { background: #7fffd4; color: #004d4d; }

        /* ---- Input field lebih kontras ---- */
        .input-field {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            background: #f8fffe;
            border: 1.5px solid #b2e8e0;
            border-radius: 0.875rem;
            color: #003333;
            font-size: 0.875rem;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: all 0.2s ease;
        }
        .input-field::placeholder { color: #7fb8b0; font-weight: 400; }
        .input-field:focus {
            border-color: #008080;
            background: #f0fffe;
            box-shadow: 0 0 0 4px rgba(0,128,128,0.10);
        }
        .input-field-sm {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            background: #f8fffe;
            border: 1.5px solid #b2e8e0;
            border-radius: 0.875rem;
            color: #003333;
            font-size: 0.8125rem;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: all 0.2s ease;
        }
        .input-field-sm::placeholder { color: #7fb8b0; font-weight: 400; }
        .input-field-sm:focus {
            border-color: #008080;
            background: #f0fffe;
            box-shadow: 0 0 0 4px rgba(0,128,128,0.10);
        }

        .input-icon {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            color: #60a8a0;
            font-size: 18px !important;
            pointer-events: none;
            transition: color 0.2s;
        }
        .input-group:focus-within .input-icon { color: #008080; }

        /* ---- Role / Prodi / Kelas buttons ---- */
        .role-btn {
            transition: all 180ms cubic-bezier(0.34,1.56,0.64,1);
            border: 1.5px solid #b2e8e0;
            background: #f8fffe;
            border-radius: 0.875rem;
            padding: 0.875rem 0.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.375rem;
            color: #60a8a0;
            cursor: pointer;
            width: 100%;
        }
        .role-btn:hover { transform: scale(1.02); background: #edfdf8; }
        .role-btn.active { border-color: #008080; background: #e6faf5; color: #004d4d; }

        .prodi-btn {
            transition: all 180ms cubic-bezier(0.34,1.56,0.64,1);
            border: 1.5px solid #b2e8e0;
            background: #f8fffe;
            border-radius: 0.875rem;
            padding: 0.75rem 0.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.375rem;
            color: #60a8a0;
            cursor: pointer;
        }
        .prodi-btn:hover { transform: scale(1.02); background: #edfdf8; }
        .prodi-btn.active { border-color: #008080; background: #e6faf5; color: #004d4d; }

        .kelas-btn {
            transition: all 180ms cubic-bezier(0.34,1.56,0.64,1);
            border: 1.5px solid #b2e8e0;
            background: #f8fffe;
            border-radius: 0.875rem;
            padding: 0.625rem 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #60a8a0;
            font-weight: 800;
            font-size: 0.7rem;
            cursor: pointer;
        }
        .kelas-btn:hover { transform: scale(1.03); background: #edfdf8; }
        .kelas-btn.active { border-color: #008080; background: #008080; color: white; }

        /* ---- Panel kiri ---- */
        .dot-grid-left {
            background-image: radial-gradient(circle at 2px 2px, #7fffd4 1px, transparent 0);
            background-size: 32px 32px;
        }
    </style>
</head>
<body class="min-h-screen font-sans antialiased bg-white">

<div class="flex h-screen w-full">

    {{-- ============================================================
         KIRI — BRANDING PANEL (versi lama dipertahankan)
    ============================================================ --}}
    <div class="w-1/2 relative hidden lg:flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 bg-[#004d4d]"></div>
        <div class="absolute -top-[10%] -right-[10%] w-[80%] h-[80%] bg-[#7fffd4]/20 rounded-full blur-[120px]"></div>
        <div class="absolute top-[20%] left-[10%] w-[60%] h-[60%] bg-[#008080]/40 rounded-full blur-[100px]"></div>
        <div class="absolute -bottom-[20%] -left-[10%] w-[70%] h-[70%] bg-[#40e0d0]/20 rounded-full blur-[110px]"></div>
        <div class="absolute inset-0 opacity-[0.07] dot-grid-left"></div>

        <div class="relative z-10 w-full max-w-lg px-12">
            <div class="relative mb-10 inline-block">
    <div class="absolute -inset-1 bg-gradient-to-r from-[#7fffd4] to-[#008080] rounded-[2rem] blur opacity-30"></div>
    <!-- Ubah p-8 menjadi p-4 atau p-5 agar kotaknya tidak terlalu lebar -->
    <div class="relative bg-white/5 backdrop-blur-3xl p-4 rounded-[2rem] border border-white/10 shadow-2xl">
        <!-- Sesuaikan ukuran logo (misal: w-32 h-32 atau w-40 h-40) -->
        <img src="{{ asset('images/logo.png') }}" alt="AE Polman" class="w-32 h-32 object-contain"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
        <span class="text-[#7fffd4] font-black text-2xl hidden">AE</span>
    </div>
</div>

            <div class="space-y-6">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-px w-8 bg-[#7fffd4]"></div>
                        <span class="text-[#7fffd4] font-mono text-[10px] tracking-[0.4em] uppercase">Next-Gen Automation</span>
                    </div>
                    <h1 class="text-6xl font-black text-white leading-none tracking-tighter">
                        Assessment <br>
                        <span class="text-transparent bg-clip-text"
                              style="background: linear-gradient(135deg, #7fffd4, #40e0d0, #008080); -webkit-background-clip: text;">
                            System.
                        </span>
                    </h1>
                </div>
                <p class="text-[#7fffd4]/60 text-lg leading-relaxed max-w-sm font-light italic">
                    Streamlining <span class="text-white not-italic font-medium">industrial excellence</span>
                    through smart project-based evaluation.
                </p>
                <div class="flex flex-wrap gap-2 pt-4">
                    @foreach(['Smart','Efficient','Integrated'] as $tag)
                    <div class="px-5 py-1.5 rounded-full border border-[#7fffd4]/20 bg-[#7fffd4]/5 text-[#7fffd4] text-[10px] uppercase tracking-widest font-bold">
                        {{ $tag }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="absolute bottom-12 left-12 right-12 h-px bg-gradient-to-r from-transparent via-[#7fffd4]/20 to-transparent"></div>
    </div>

    {{-- ============================================================
         KANAN — FORM REGISTER
    ============================================================ --}}
    <div class="w-full lg:w-1/2 bg-gray-50 flex items-start justify-center overflow-y-auto">
        <div class="w-full max-w-md px-10 py-10 animate-fadeUp">

            {{-- Mobile logo --}}
            <div class="lg:hidden flex items-center gap-3 mb-8">
                <div class="h-10 w-10 rounded-xl bg-aqua-800 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-7 w-7 object-contain"
                         onerror="this.style.display='none';">
                </div>
                <p class="font-black text-aqua-800 text-sm">PBL Portal</p>
            </div>

            {{-- Header --}}
            <div class="mb-7">
                <span class="inline-block text-[10px] font-bold tracking-[0.3em] uppercase text-aqua-600 bg-aqua-100 border border-aqua-200 px-3.5 py-1.5 rounded-full mb-4">
                    Buat Akun Baru
                </span>
                <h2 class="text-3xl font-black text-aqua-900 tracking-tight">Daftar Akun</h2>
                <p class="text-gray-500 mt-1 text-sm font-medium">Lengkapi data kredensial PBL Anda.</p>
            </div>

            {{-- Error --}}
            @if($errors->any())
            <div class="mb-5 flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-2xl">
                <span class="material-symbols-outlined text-red-500 shrink-0 mt-0.5" style="font-size:18px;">error</span>
                <div>
                    @foreach($errors->all() as $error)
                    <p class="text-red-600 text-sm font-semibold">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="registerForm" class="space-y-5">
                @csrf

                {{-- ROLE --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-2.5">Daftar Sebagai</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" onclick="setRole('mahasiswa')" id="role-mahasiswa" class="role-btn active">
                            <span class="material-symbols-outlined" style="font-size:22px;">school</span>
                            <span class="font-black text-xs">Mahasiswa</span>
                        </button>
                        <button type="button" onclick="setRole('dosen')" id="role-dosen" class="role-btn">
                            <span class="material-symbols-outlined" style="font-size:22px;">co_present</span>
                            <span class="font-black text-xs">Dosen</span>
                        </button>
                    </div>
                    <input type="hidden" name="role" id="roleInput" value="mahasiswa">
                </div>

                {{-- INFO DOSEN --}}
                <div id="dosenInfo" class="hidden flex items-start gap-3 p-3.5 bg-amber-50 border border-amber-200 rounded-2xl">
                    <span class="material-symbols-outlined text-amber-500 shrink-0" style="font-size:16px;">info</span>
                    <p class="text-amber-700 text-xs font-semibold">Akun dosen memerlukan <strong>persetujuan Admin</strong> sebelum bisa digunakan.</p>
                </div>

                {{-- NAMA --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-2.5">Nama Lengkap</label>
                    <div class="input-group relative">
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="Masukkan nama lengkap"
                               class="input-field pr-4">
                        <span class="material-symbols-outlined input-icon">badge</span>
                    </div>
                </div>

                {{-- EMAIL & NIM/NIDN --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-2.5">Email</label>
                        <div class="input-group relative">
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   placeholder="email@example.com"
                                   class="input-field-sm pr-2">
                            <span class="material-symbols-outlined input-icon">mail</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-2.5" id="nimLabel">NIM</label>
                        <div class="input-group relative">
                            <input type="text" name="nim" value="{{ old('nim') }}" required
                                   placeholder="23010xxx" id="nimInput"
                                   class="input-field-sm pr-2" style="font-family: monospace;">
                            <span class="material-symbols-outlined input-icon">tag</span>
                        </div>
                    </div>
                </div>

                {{-- PRODI --}}
                <div id="prodiSection">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-2.5">Program Studi</label>
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
                            <span class="material-symbols-outlined" style="font-size:20px;">{{ $p['icon'] }}</span>
                            <span class="font-black uppercase text-[10px]">{{ $p['label'] }}</span>
                        </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="prodi" id="prodiInput" value="{{ old('prodi') }}">
                </div>

                {{-- KELAS --}}
                <div id="kelasSection" class="hidden">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-2.5">Pilih Kelas</label>
                    <div class="grid grid-cols-4 gap-2" id="kelasGrid"></div>
                    <input type="hidden" name="kelas_register" id="kelasInput" value="{{ old('kelas_register') }}">
                </div>

                {{-- PASSWORD --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-2.5">Kata Sandi</label>
                        <div class="input-group relative">
                            <input type="password" name="password" required autocomplete="new-password"
                                   placeholder="••••••••" id="pw1"
                                   class="input-field-sm pr-2"
                                   oninput="checkStrength(this.value)">
                            <span class="material-symbols-outlined input-icon">lock</span>
                        </div>
                        <div id="strength-bar" class="hidden mt-1.5">
                            <div class="flex gap-1">
                                <div id="s1" class="h-1 flex-1 rounded-full bg-gray-200 transition-all duration-300"></div>
                                <div id="s2" class="h-1 flex-1 rounded-full bg-gray-200 transition-all duration-300"></div>
                                <div id="s3" class="h-1 flex-1 rounded-full bg-gray-200 transition-all duration-300"></div>
                                <div id="s4" class="h-1 flex-1 rounded-full bg-gray-200 transition-all duration-300"></div>
                            </div>
                            <p id="strength-label" class="text-[10px] font-bold mt-0.5"></p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-2.5">Konfirmasi</label>
                        <div class="input-group relative">
                            <input type="password" name="password_confirmation" required autocomplete="new-password"
                                   placeholder="••••••••" id="pw2"
                                   class="input-field-sm pr-2"
                                   oninput="checkMatch()">
                            <span class="material-symbols-outlined input-icon">lock</span>
                        </div>
                        <p id="match-label" class="text-[10px] font-bold mt-1.5 hidden"></p>
                    </div>
                </div>

                {{-- SUBMIT --}}
                <div class="pt-2 pb-6">
                    <button type="submit" id="submit-btn"
                            class="w-full flex items-center justify-center gap-2.5 font-black text-sm py-4 rounded-2xl transition-all duration-200 active:scale-[0.98] text-white"
                            style="background: linear-gradient(135deg, #004d4d 0%, #008080 50%, #40e0d0 100%); box-shadow: 0 8px 32px -8px rgba(0,128,128,0.5);">
                        <span class="material-symbols-outlined" style="font-size:16px;">how_to_reg</span>
                        Daftar Sekarang
                    </button>

                    <p class="text-center mt-5 text-sm text-gray-500 font-medium">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="font-black text-aqua-700 hover:text-aqua-800 hover:underline underline-offset-4 decoration-2 transition-colors ml-1">
                            Masuk sekarang
                        </a>
                    </p>
                </div>
            </form>
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
    document.getElementById('nimLabel').textContent    = isMhs ? 'NIM' : 'NIDN';
    document.getElementById('nimInput').placeholder    = isMhs ? '23010xxx' : '19880xxx';
    document.getElementById('prodiInput').value = '';
    document.getElementById('kelasInput').value  = '';
    document.querySelectorAll('.prodi-btn').forEach(b => b.classList.remove('active'));
}

function setProdi(prodi) {
    document.getElementById('prodiInput').value = prodi;
    document.getElementById('kelasInput').value  = '';
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
    const colors = ['bg-red-400','bg-orange-400','bg-yellow-400','bg-teal-500'];
    const labels = ['Sangat lemah','Lemah','Cukup','Kuat'];
    ['s1','s2','s3','s4'].forEach((id, i) => {
        document.getElementById(id).className = 'h-1 flex-1 rounded-full transition-all duration-300 ' + (i < score ? colors[score-1] : 'bg-gray-200');
    });
    label.textContent = labels[score-1] ?? '';
    label.className = 'text-[10px] font-bold mt-0.5 ' +
        (score <= 1 ? 'text-red-400' : score === 2 ? 'text-orange-400' : score === 3 ? 'text-yellow-500' : 'text-teal-600');
}

function checkMatch() {
    const pw1   = document.getElementById('pw1').value;
    const pw2   = document.getElementById('pw2').value;
    const label = document.getElementById('match-label');
    if (!pw2.length) { label.classList.add('hidden'); return; }
    label.classList.remove('hidden');
    if (pw1 === pw2) {
        label.textContent = '✓ Kata sandi cocok';
        label.className = 'text-[10px] font-bold mt-1.5 text-teal-600';
    } else {
        label.textContent = '✗ Tidak cocok';
        label.className = 'text-[10px] font-bold mt-1.5 text-red-500';
    }
}

document.getElementById('registerForm').addEventListener('submit', function() {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="animate-spin w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
        Mendaftarkan...
    `;
    btn.style.opacity = '0.8';
    btn.style.cursor  = 'not-allowed';
});
</script>

</body>
</html>