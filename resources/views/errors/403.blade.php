<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Akses Ditolak — PBL Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('layouts.partials.design-system')
</head>
<body class="min-h-screen bg-[#fafafa] font-sans text-[#0b0b0c] flex items-center justify-center p-6">
    <div class="bg-white border border-[#e4e4e7] rounded-xl p-8 max-w-md w-full text-center shadow-surface-md">
        <span class="font-mono text-xs font-medium text-[#b91c1c] bg-red-50 border border-red-200 px-2.5 py-1 rounded-full">HTTP 403</span>
        <h1 class="text-2xl font-semibold text-[#0b0b0c] tracking-tight mt-4 mb-2">Akses Ditolak</h1>
        <p class="text-xs text-[#6a6f7c] leading-relaxed mb-6">
            Anda tidak memiliki izin untuk mengakses halaman ini.
        </p>
        <a href="{{ url('/dashboard') }}" class="ps-btn-primary">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>
</body>
</html>
