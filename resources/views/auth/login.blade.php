{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | PBL &amp; PPI Portal — Politeknik Manufaktur Bandung</title>
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
        @keyframes slideInUp {
            from { transform: translateY(18px); opacity: 0; }
            to   { transform: translateY(0); opacity: 1; }
        }
        .anim-card {
            animation: slideInUp 0.5s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 font-sans text-[#0b0b0c] antialiased">

{{-- TOAST NOTIFIKASI --}}
<div id="toast" class="fixed top-5 left-1/2 -translate-x-1/2 z-[999] hidden">
    <div id="toast-inner" class="flex items-center gap-2.5 px-4 py-3 rounded-xl shadow-surface-lg border text-xs font-semibold min-w-[260px]">
        <span class="material-symbols-outlined shrink-0" id="toast-icon" style="font-size:18px;"></span>
        <p id="toast-msg"></p>
        <button onclick="hideToast()" class="ml-auto opacity-50 hover:opacity-100 transition-opacity">
            <span class="material-symbols-outlined" style="font-size:15px;">close</span>
        </button>
    </div>
</div>

<div class="min-h-screen flex flex-col lg:flex-row w-full">

    {{-- ============================================================
         LEFT PANEL — DUAL SCREEN POLMAN BANDUNG BRANDING
    ============================================================ --}}
    <div class="hidden lg:flex lg:w-1/2 akasia-left-panel flex-col justify-center p-12 xl:p-16 text-white">
        {{-- Center Identity --}}
        <div class="relative z-10 space-y-5 max-w-lg">
            <div class="inline-flex items-center gap-2 text-xs font-bold text-emerald-300 bg-emerald-400/15 border border-emerald-400/30 px-3.5 py-1.5 rounded-full uppercase tracking-wide">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Jurusan Automation Engineering</span>
            </div>
            <h1 class="text-4xl font-extrabold text-white leading-tight tracking-tight drop-shadow-sm">
                Portal Akademik PBL &amp; Praktik Industri
            </h1>
            <p class="text-base text-white/85 leading-relaxed font-medium">
                Kelola logbook aktivitas harian, supervisi mata kuliah, evaluasi mitra industri, dan pengesahan transkrip nilai dalam satu platform terintegrasi.
            </p>
        </div>
    </div>

    {{-- ============================================================
         RIGHT PANEL — LOGIN CARD
    ============================================================ --}}
    <div class="flex-1 flex flex-col justify-center p-6 sm:p-12 lg:p-16 relative min-h-screen">

        {{-- Top-Right Official Logos --}}
        <div class="flex items-center justify-between lg:justify-end gap-3.5 mb-8 lg:mb-0 lg:absolute lg:top-8 lg:right-12">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3.5">
                <img src="{{ asset('images/logo-polman-teks.webp') }}" alt="Polman Bandung" class="h-8 md:h-9 w-auto object-contain">
                <img src="{{ asset('images/logo-blu-speed.png') }}" alt="BLU Speed" class="h-9 md:h-10 w-auto object-contain">
            </a>
        </div>

        {{-- Main Login Card --}}
        <div class="w-full max-w-md mx-auto anim-card">
            <div class="w-full border border-slate-200/80 shadow-2xl shadow-slate-200/50 bg-white/95 backdrop-blur-xl rounded-3xl p-7 sm:p-9">

                {{-- Card Header --}}
                <div class="space-y-1.5 pb-6">
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Selamat Datang</h2>
                    <p class="text-sm text-slate-500 font-medium">
                        Silakan masuk untuk mengakses dan mengelola portal akademik Anda.
                    </p>
                </div>

                {{-- Flash Status --}}
                @if(session('status'))
                <div class="mb-5 flex items-center gap-2.5 p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl">
                    <span class="material-symbols-outlined text-[#15803d] shrink-0 text-[18px]">check_circle</span>
                    <p class="text-[#15803d] text-xs font-semibold">{{ session('status') }}</p>
                </div>
                @endif

                {{-- Validation Errors --}}
                @if($errors->any())
                <div class="mb-5 flex items-start gap-2.5 p-3.5 bg-red-50 border border-red-200 rounded-xl">
                    <span class="material-symbols-outlined text-[#b91c1c] shrink-0 mt-0.5 text-[18px]">error</span>
                    <div>
                        @foreach($errors->all() as $error)
                        <p class="text-[#b91c1c] text-xs font-semibold">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5" id="login-form">
                    @csrf

                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-semibold {{ $errors->has('email') ? 'text-[#b91c1c]' : 'text-slate-700' }}">
                            Alamat Email
                        </label>
                        <input id="email" name="email" type="email"
                               placeholder="Masukkan Email"
                               value="{{ old('email') }}" required autofocus autocomplete="email"
                               class="w-full h-11 px-4 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 outline-none transition-all border {{ $errors->has('email') ? 'border-red-300 bg-red-50 focus:border-[#b91c1c]' : 'border-slate-200 bg-slate-50 focus:bg-white focus:border-[#013981] focus:ring-3 focus:ring-[#013981]/10' }}">
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-sm font-semibold {{ $errors->has('password') ? 'text-[#b91c1c]' : 'text-slate-700' }}">
                                Kata Sandi
                            </label>
                            @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-xs font-semibold text-[#013981] hover:underline">
                                Lupa kata sandi?
                            </a>
                            @endif
                        </div>
                        <div class="relative">
                            <input id="password" name="password" type="password"
                                   placeholder="Masukkan Password" required autocomplete="current-password"
                                   onkeyup="checkCapsLock(event)"
                                   class="w-full h-11 pl-4 pr-11 rounded-xl text-sm font-medium text-slate-900 placeholder:text-slate-400 outline-none transition-all border {{ $errors->has('password') ? 'border-red-300 bg-red-50 focus:border-[#b91c1c]' : 'border-slate-200 bg-slate-50 focus:bg-white focus:border-[#013981] focus:ring-3 focus:ring-[#013981]/10' }}">
                            <button type="button"
                                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 transition-colors"
                                    onclick="togglePassword()">
                                <span class="material-symbols-outlined" id="pw-eye" style="font-size:20px;">visibility_off</span>
                            </button>
                        </div>

                        <div id="caps-warning" class="hidden mt-1.5 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[#b45309]" style="font-size:14px;">warning</span>
                            <p class="text-[#b45309] text-xs font-semibold">Caps Lock aktif</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2.5 pt-1">
                        <input type="checkbox" name="remember" id="remember_me" class="w-4 h-4 rounded border-slate-300 text-[#013981] focus:ring-[#013981]">
                        <label for="remember_me" class="text-sm font-medium text-slate-600 cursor-pointer">
                            Ingat saya
                        </label>
                    </div>

                    <div class="pt-1">
                        <button type="submit" id="submit-btn"
                                class="w-full h-12 flex items-center justify-center gap-2 bg-[#013981] hover:bg-[#012b63] text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg transition-all active:scale-[0.98]">
                            <span class="material-symbols-outlined text-[18px]">login</span>
                            <span>Masuk ke Akun</span>
                        </button>
                    </div>
                </form>

                <div class="text-center text-sm text-slate-500 font-medium mt-6 pt-5 border-t border-slate-100">
                    Belum memiliki akun?
                    <a href="{{ route('register') }}" class="font-bold text-[#013981] hover:underline ml-1">
                        Daftar Sekarang
                    </a>
                </div>
            </div>
        </div>

        {{-- Footer Copyright --}}
        <div class="mt-8 lg:mt-0 lg:absolute lg:bottom-6 lg:inset-x-0 text-center text-xs text-slate-400 font-medium">
            &copy; {{ date('Y') }} Politeknik Manufaktur Bandung. All rights reserved.
        </div>

    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('pw-eye');
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    icon.textContent = isHidden ? 'visibility' : 'visibility_off';
}

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

document.getElementById('login-form').addEventListener('submit', function() {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<span>Memproses...</span>';
    btn.style.opacity = '0.8';
});

function showToast(msg, type = 'error') {
    const toast = document.getElementById('toast');
    const inner = document.getElementById('toast-inner');
    const icon  = document.getElementById('toast-icon');
    const text  = document.getElementById('toast-msg');
    text.textContent = msg;
    if (type === 'error') {
        inner.className = 'flex items-center gap-2.5 px-4 py-3 rounded-xl shadow-surface-lg border text-xs font-semibold min-w-[260px] bg-red-50 border-red-200 text-[#b91c1c]';
        icon.textContent = 'error';
    } else {
        inner.className = 'flex items-center gap-2.5 px-4 py-3 rounded-xl shadow-surface-lg border text-xs font-semibold min-w-[260px] bg-emerald-50 border-emerald-200 text-[#15803d]';
        icon.textContent = 'check_circle';
    }
    toast.classList.remove('hidden');
    setTimeout(hideToast, 4000);
}

function hideToast() {
    document.getElementById('toast').classList.add('hidden');
}

@if($errors->any())
window.addEventListener('DOMContentLoaded', () => {
    showToast(@json($errors->first()), 'error');
});
@endif

@if(session('status'))
window.addEventListener('DOMContentLoaded', () => {
    showToast(@json(session('status')), 'success');
});
@endif
</script>

</body>
</html>
