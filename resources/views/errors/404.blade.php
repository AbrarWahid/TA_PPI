<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan — PBL Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
</head>
<body class="min-h-screen bg-[#f4f7f6] font-sans flex items-center justify-center p-6">
    <div class="text-center max-w-md">
        <div class="w-24 h-24 rounded-3xl bg-amber-100 flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-amber-500" style="font-size:48px;">search_off</span>
        </div>
        <h1 class="text-6xl font-black italic text-[#004d4d] tracking-tighter mb-2">404</h1>
        <h2 class="text-xl font-black text-gray-700 uppercase tracking-tight mb-3">
            Halaman <span class="text-amber-500">Tidak Ditemukan</span>
        </h2>
        <p class="text-sm text-gray-400 font-medium mb-8">
            Halaman yang Anda cari tidak ada atau sudah dipindahkan.
        </p>
        <a href="{{ url('/dashboard') }}"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl text-sm font-black text-white shadow-lg hover:scale-105 transition-all"
           style="background:#004d4d;">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Kembali ke Dashboard
        </a>
    </div>
</body>
</html>