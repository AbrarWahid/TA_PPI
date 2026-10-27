{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | PBL Portal AE Polman Bandung</title>
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
                        float: {
                            '0%,100%': { transform: 'translateY(0px)' },
                            '50%':     { transform: 'translateY(-12px)' },
                        },
                        fadeDown: {
                            '0%':   { opacity: '0', transform: 'translateY(-16px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeUp: {
                            '0%':   { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    },
                    animation: {
                        float:    'float 7s ease-in-out infinite',
                        fadeDown: 'fadeDown 0.4s ease forwards',
                        fadeUp:   'fadeUp 0.5s ease forwards',
                    }
                }
            }
        }
    </script>
    <style>
        ::selection { background: #7fffd4; color: #004d4d; }

        .dot-grid {
            background-image: radial-gradient(#008080 1px, transparent 0);
            background-size: 28px 28px;
        }

        .blob {
            filter: blur(80px);
            border-radius: 9999px;
            position: absolute;
            pointer-events: none;
        }

        .input-field {
            width: 100%;
            padding: 1rem 1rem 1rem 3.25rem;
            background: rgba(127,255,212,0.06);
            border: 1.5px solid rgba(127,255,212,0.25);
            border-radius: 1rem;
            color: #003333;
            font-size: 0.9375rem;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: all 0.2s ease;
        }
        .input-field::placeholder { color: rgba(0,128,128,0.35); font-weight: 400; }
        .input-field:focus {
            border-color: rgba(0,128,128,0.55);
            background: rgba(127,255,212,0.11);
            box-shadow: 0 0 0 4px rgba(127,255,212,0.18);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(0,102,102,0.4);
            font-size: 20px !important;
            transition: color 0.2s;
            pointer-events: none;
        }
        .input-group:focus-within .input-icon { color: #008080; }

        @keyframes pulse-ring {
            0%        { transform: scale(1); opacity: 0.8; }
            80%, 100% { transform: scale(2.2); opacity: 0; }
        }
        .pulse-dot { position: relative; }
        .pulse-dot::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: #40e0d0;
            animation: pulse-ring 2.5s cubic-bezier(0.215,0.61,0.355,1) infinite;
        }

        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="min-h-screen bg-aqua-50 font-sans text-aqua-900 antialiased overflow-hidden">

{{-- ============================================================
     TOAST NOTIFIKASI
============================================================ --}}
<div id="toast" class="fixed top-6 left-1/2 -translate-x-1/2 z-[999] hidden animate-fadeDown">
    <div id="toast-inner" class="flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl border text-sm font-semibold min-w-[260px]">
        <span class="material-symbols-outlined shrink-0" id="toast-icon" style="font-size:18px;"></span>
        <p id="toast-msg"></p>
        <button onclick="hideToast()" class="ml-auto opacity-50 hover:opacity-100 transition-opacity">
            <span class="material-symbols-outlined" style="font-size:16px;">close</span>
        </button>
    </div>
</div>

<div class="flex h-screen w-full">

    {{-- ============================================================
         KIRI — BRANDING PANEL
    ============================================================ --}}
    <div class="hidden lg:flex relative w-[48%] bg-aqua-800 flex-col items-center justify-center p-16 overflow-hidden">

        <div class="absolute inset-0 dot-grid opacity-[0.07] pointer-events-none"></div>
        <div class="blob w-[65%] h-[65%] bg-aqua-300/10 -top-[15%] -right-[10%]"></div>
        <div class="blob w-[55%] h-[55%] bg-aqua-200/8  bottom-[5%] -left-[10%]"></div>

        <div class="relative z-10 flex flex-col items-center text-center gap-7 w-full max-w-sm">

    {{-- Logo --}}
    <div class="w-24 h-24 rounded-[2rem] bg-aqua-200/10 border border-aqua-200/20 flex items-center justify-center shadow-2xl overflow-hidden">
        <img src="{{ asset('images/logo.png') }}" alt="AE Polman" class="w-18 h-18 object-contain"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <span class="text-aqua-200 font-black text-3xl hidden items-center justify-center w-full h-full">AE</span>
    </div>

    {{-- Badge aktif --}}
    <div class="flex items-center justify-center gap-2">
        <span class="pulse-dot w-2.5 h-2.5 rounded-[2rem] bg-aqua-400 shrink-0"></span>
        <span class="text-aqua-200/50 font-bold text-[11px] tracking-[0.3em] uppercase">Sistem Aktif</span>
    </div>

    {{-- Teks utama --}}
    <div class="space-y-2 -mt-2">
        <h1 class="text-5xl font-black text-white leading-tight tracking-tighter">PBL Portal</h1>
        <p class="text-aqua-200/50 text-sm font-bold tracking-widest uppercase">AE Polman Bandung</p>
    </div>

    {{-- Divider --}}
    <div class="flex items-center gap-4 w-full">
        <div class="flex-1 h-px bg-aqua-200/10"></div>
        <span class="text-aqua-200/25 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap">Automation Engineering</span>
        <div class="flex-1 h-px bg-aqua-200/10"></div>
    </div>

    {{-- Deskripsi --}}
    <p class="text-aqua-200/40 text-sm leading-relaxed font-medium">
        Integrated Project-Based Learning Management System untuk seluruh sivitas akademika Jurusan AE.
    </p>

    {{-- Stats --}}
    <div class="flex items-center gap-8">
        @foreach([['4','Role'],['3','Prodi'],['6','Fitur']] as $s)
        <div class="text-center">
            <p class="text-3xl font-black text-aqua-300">{{ $s[0] }}</p>
            <p class="text-[10px] text-aqua-200/30 font-bold uppercase tracking-widest mt-0.5">{{ $s[1] }}</p>
        </div>
        @if(!$loop->last)
        <div class="w-px h-8 bg-aqua-200/10"></div>
        @endif
        @endforeach
    </div>

</div>
    </div>

    {{-- ============================================================
         KANAN — FORM LOGIN
    ============================================================ --}}
    <div class="flex-1 flex flex-col items-center justify-center p-10 md:p-16 bg-white relative overflow-y-auto">

        <div class="blob w-[55%] h-[45%] bg-aqua-200/20 -top-[5%] -right-[5%]"></div>
        <div class="blob w-[40%] h-[40%] bg-aqua-100/30 bottom-[5%] -left-[5%]"></div>

        <div class="relative z-10 w-full max-w-md animate-fadeUp">

            {{-- Mobile logo --}}
            <div class="lg:hidden flex items-center gap-3 mb-10">
                <div class="h-11 w-11 rounded-xl bg-aqua-800 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-8 object-contain"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <span class="text-aqua-200 font-black text-xs hidden">AE</span>
                </div>
                <div>
                    <p class="font-black text-aqua-800 text-sm">PBL Portal</p>
                    <p class="text-aqua-600/50 text-[10px] font-bold uppercase tracking-widest">AE Polman Bandung</p>
                </div>
            </div>

            {{-- Header --}}
            <div class="mb-10">
                <span class="inline-block text-[10px] font-bold tracking-[0.3em] uppercase text-aqua-600 bg-aqua-200/20 border border-aqua-200/50 px-3.5 py-1.5 rounded-full mb-5">
                    Portal Akademik
                </span>
                <h2 class="text-4xl font-black text-aqua-900 tracking-tight">Selamat Datang</h2>
                <p class="text-aqua-700/50 mt-2 text-base font-medium">Masuk ke akun akademik Anda</p>
            </div>

            {{-- Flash status --}}
            @if(session('status'))
            <div class="mb-6 flex items-center gap-3 p-4 bg-aqua-200/20 border border-aqua-200/50 rounded-2xl">
                <span class="material-symbols-outlined text-aqua-600 shrink-0" style="font-size:18px;">check_circle</span>
                <p class="text-aqua-700 text-sm font-semibold">{{ session('status') }}</p>
            </div>
            @endif

            {{-- Error --}}
            @if($errors->any())
            <div class="mb-6 flex items-start gap-3 p-4 bg-red-50 border border-red-200/60 rounded-2xl">
                <span class="material-symbols-outlined text-red-500 shrink-0 mt-0.5" style="font-size:18px;">error</span>
                <div>
                    @foreach($errors->all() as $error)
                    <p class="text-red-600 text-sm font-semibold">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-6" id="login-form">
                @csrf

                {{-- Email --}}
                <div>
                    <label class="block text-[11px] font-bold text-aqua-700/60 uppercase tracking-[0.2em] mb-2.5" for="email">
                        Email
                    </label>
                    <div class="input-group relative">
                        <input class="input-field pr-4" id="email" name="email" type="email"
                               placeholder="email@polman.ac.id"
                               value="{{ old('email') }}" required autofocus autocomplete="email">
                        <span class="material-symbols-outlined input-icon">person</span>
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-[11px] font-bold text-aqua-700/60 uppercase tracking-[0.2em] mb-2.5" for="password">
                        Kata Sandi
                    </label>
                    <div class="input-group relative">
                        <input class="input-field" id="password" name="password" type="password"
                               placeholder="••••••••" required autocomplete="current-password"
                               style="padding-right: 3.5rem;"
                               onkeyup="checkCapsLock(event)">
                        <span class="material-symbols-outlined input-icon">lock</span>
                        <button type="button"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-aqua-600/40 hover:text-aqua-700 transition-colors"
                                onclick="togglePassword()">
                            <span class="material-symbols-outlined" id="pw-eye" style="font-size:20px;">visibility</span>
                        </button>
                    </div>

                    {{-- Caps Lock warning --}}
                    <div id="caps-warning" class="hidden mt-2 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-amber-500" style="font-size:14px;">warning</span>
                        <p class="text-amber-600 text-xs font-semibold">Caps Lock aktif</p>
                    </div>
                </div>

                {{-- Remember & Forgot --}}
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="remember" id="remember_me" class="peer sr-only">
                            <div id="checkbox-box" class="w-5 h-5 rounded-md border-2 border-aqua-200/60 bg-aqua-200/10 flex items-center justify-center transition-all">
                                <span class="material-symbols-outlined text-white hidden" id="checkbox-check" style="font-size:13px;">check</span>
                            </div>
                        </div>
                        <span class="text-sm text-aqua-700/60 font-semibold group-hover:text-aqua-700 transition-colors">Ingat saya</span>
                    </label>

                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-sm font-bold text-aqua-600 hover:text-aqua-800 hover:underline underline-offset-4 decoration-2 transition-colors">
                        Lupa kata sandi?
                    </a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit" id="submit-btn"
                        class="w-full flex items-center justify-center gap-2.5 font-black text-base py-4 rounded-2xl transition-all duration-200 shadow-xl active:scale-[0.98] mt-2 text-white"
                        style="background: linear-gradient(135deg, #004d4d 0%, #008080 50%, #40e0d0 100%); box-shadow: 0 8px 32px -8px rgba(0,128,128,0.5);">
                    <span class="material-symbols-outlined" style="font-size:18px;">rocket_launch</span>
                    Masuk Sekarang
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-4 my-7">
                <div class="flex-1 h-px bg-aqua-200/40"></div>
                <span class="text-[10px] font-bold text-aqua-600/30 uppercase tracking-widest">atau</span>
                <div class="flex-1 h-px bg-aqua-200/40"></div>
            </div>

            {{-- Register --}}
            <p class="text-center text-sm text-aqua-700/50 font-medium">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-black text-aqua-700 hover:text-aqua-800 hover:underline underline-offset-4 decoration-2 transition-colors ml-1">
                    Daftar sekarang
                </a>
            </p>

            <p class="text-center text-[11px] text-aqua-600/30 font-semibold mt-3">
                Butuh akses? Hubungi Admin KPS / Kaprodi Anda.
            </p>
        </div>

        {{-- Footer --}}
        <div class="absolute bottom-6 left-0 right-0 text-center">
            <p class="text-[10px] font-bold text-aqua-600/20 uppercase tracking-widest">
                © {{ date('Y') }} · PBL Portal AE Polman Bandung
            </p>
        </div>
    </div>
</div>

{{-- ============================================================
     JAVASCRIPT
============================================================ --}}
<script>
// Toggle show/hide password
function togglePassword() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('pw-eye');
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    icon.textContent = isHidden ? 'visibility_off' : 'visibility';
}

// Caps Lock warning
function checkCapsLock(e) {
    const warning = document.getElementById('caps-warning');
    if (e.getModifierState && e.getModifierState('CapsLock')) {
        warning.classList.remove('hidden');
        warning.classList.add('flex');
    } else {
        warning.classList.add('hidden');
        warning.classList.remove('flex');
    }
}

// Custom checkbox
document.getElementById('remember_me').addEventListener('change', function() {
    const box   = document.getElementById('checkbox-box');
    const check = document.getElementById('checkbox-check');
    if (this.checked) {
        box.classList.add('bg-aqua-600','border-aqua-600');
        box.classList.remove('border-aqua-200/60','bg-aqua-200/10');
        check.classList.remove('hidden');
    } else {
        box.classList.remove('bg-aqua-600','border-aqua-600');
        box.classList.add('border-aqua-200/60','bg-aqua-200/10');
        check.classList.add('hidden');
    }
});

// Loading state saat submit
document.getElementById('login-form').addEventListener('submit', function() {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="animate-spin w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
        Memproses...
    `;
    btn.style.opacity = '0.8';
    btn.style.cursor  = 'not-allowed';
});

// Toast system
function showToast(msg, type = 'error') {
    const toast = document.getElementById('toast');
    const inner = document.getElementById('toast-inner');
    const icon  = document.getElementById('toast-icon');
    const text  = document.getElementById('toast-msg');

    text.textContent = msg;

    if (type === 'error') {
        inner.className = 'flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl border text-sm font-semibold min-w-[260px] bg-red-50 border-red-200 text-red-700';
        icon.textContent = 'error';
        icon.className = 'material-symbols-outlined shrink-0 text-red-500';
    } else {
        inner.className = 'flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl border text-sm font-semibold min-w-[260px] bg-aqua-200/30 border-aqua-200 text-aqua-800';
        icon.textContent = 'check_circle';
        icon.className = 'material-symbols-outlined shrink-0 text-aqua-600';
    }

    toast.classList.remove('hidden');
    toast.classList.add('animate-fadeDown');
    setTimeout(hideToast, 4000);
}

function hideToast() {
    document.getElementById('toast').classList.add('hidden');
}

// Auto-trigger toast jika ada error Laravel
@if($errors->any())
window.addEventListener('DOMContentLoaded', () => {
    showToast('{{ $errors->first() }}', 'error');
});
@endif

@if(session('status'))
window.addEventListener('DOMContentLoaded', () => {
    showToast('{{ session('status') }}', 'success');
});
@endif
</script>

</body>
</html>