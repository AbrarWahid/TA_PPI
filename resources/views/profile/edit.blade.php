<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <p class="font-mono text-[11px] uppercase tracking-wider text-[#6a6f7c] mb-1">PENGATURAN AKUN</p>
            <h1 class="text-2xl font-semibold text-[#0b0b0c] tracking-tight">Profil &amp; Keamanan</h1>
            <p class="text-xs text-[#6a6f7c] mt-1">Perbarui informasi profil, alamat email, dan kata sandi akun Anda.</p>
        </div>

        <div class="p-6 sm:p-7 bg-white border border-[#e4e4e7] rounded-xl shadow-surface">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-6 sm:p-7 bg-white border border-[#e4e4e7] rounded-xl shadow-surface">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-6 sm:p-7 bg-white border border-[#e4e4e7] rounded-xl shadow-surface">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
