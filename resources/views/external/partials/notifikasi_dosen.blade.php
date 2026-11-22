<x-dropdown align="right" width="48">
    <x-slot name="trigger">
        <button class="relative w-8 h-8 flex items-center justify-center rounded-lg border border-[#e4e4e7] bg-white hover:bg-[#f4f4f5] transition-colors text-[#6a6f7c] hover:text-[#0b0b0c] focus:outline-none">
            <span class="material-symbols-outlined text-[18px]">notifications</span>
            @if(auth()->user()->unreadNotifications->count() > 0)
            <span class="absolute top-1.5 right-1.5 flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-[#b91c1c]"></span>
            </span>
            @endif
        </button>
    </x-slot>

    <x-slot name="content">
        <div class="w-[320px] sm:w-[360px] bg-white flex flex-col border border-[#e4e4e7] rounded-xl shadow-surface-lg overflow-hidden">
            <div class="bg-[#fafafa] border-b border-[#e4e4e7] px-4 py-3 flex justify-between items-center">
                <h3 class="font-semibold text-xs text-[#0b0b0c] m-0">Notifikasi TTD</h3>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="font-mono bg-[#013981] text-white text-[10px] font-medium px-2 py-0.5 rounded-full">
                        {{ auth()->user()->unreadNotifications->count() }} Baru
                    </span>
                @endif
            </div>

            <div class="max-h-[340px] overflow-y-auto w-full divide-y divide-[#f4f4f5]">
                @forelse(auth()->user()->notifications()->take(15)->get() as $notification)
                    @php
                        $isUnread = is_null($notification->read_at);
                        $statusPengajuan = $notification->data['status'] ?? 'pending';
                    @endphp
                    <div id="notif-item-{{ $notification->id }}" class="relative group transition-colors duration-150 {{ $isUnread ? 'bg-[#fafafa] hover:bg-[#f4f4f5]' : 'bg-white hover:bg-[#fafafa]' }}">
                        <div onclick="handleNotifClick('{{ $notification->id }}', '{{ $notification->data['url'] ?? '#' }}')" class="flex items-start px-4 py-3.5 w-full pr-10 cursor-pointer">
                            <div class="notif-icon flex-shrink-0 rounded-lg w-8 h-8 flex items-center justify-center mr-3 mt-0.5 border {{ $isUnread ? 'bg-[#013981] text-white border-[#013981]' : 'bg-[#f4f4f5] text-[#6a6f7c] border-[#e4e4e7]' }}">
                                <span class="material-symbols-outlined text-[16px]">description</span>
                            </div>
                            <div class="flex-1 min-w-0 pointer-events-none">
                                <p class="notif-title text-xs font-semibold truncate {{ $isUnread ? 'text-[#0b0b0c]' : 'text-[#6a6f7c]' }}">
                                    {{ $notification->data['title'] ?? 'Pemberitahuan' }}
                                </p>
                                <p class="notif-message text-xs mt-0.5 whitespace-normal leading-relaxed {{ $isUnread ? 'text-[#52525b]' : 'text-[#a1a1aa]' }}">
                                    {{ $notification->data['message'] ?? 'Ada pembaruan data.' }}
                                </p>
                                <p class="font-mono text-[10px] mt-1.5 text-[#6a6f7c]">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        @if($statusPengajuan !== 'pending')
                            <button type="button" onclick="deleteNotification(event, '{{ $notification->id }}')" class="absolute top-3.5 right-2.5 opacity-0 group-hover:opacity-100 transition-opacity duration-150 text-[#6a6f7c] hover:text-[#b91c1c] bg-white border border-[#e4e4e7] hover:bg-red-50 rounded-md p-1" title="Hapus Notifikasi Ini">
                                <span class="material-symbols-outlined text-[14px]">close</span>
                            </button>
                        @endif
                    </div>
                @empty
                    <div class="px-4 py-8 text-center">
                        <span class="material-symbols-outlined text-2xl text-[#d4d4d8] block mb-1.5">notifications_off</span>
                        <p class="text-xs text-[#6a6f7c]">Tidak ada notifikasi baru.</p>
                    </div>
                @endforelse
            </div>

            @if(auth()->user()->notifications->count() > 0)
            <div class="bg-[#fafafa] border-t border-[#e4e4e7] px-3 py-2 flex justify-between items-center w-full">
                <form method="POST" action="{{ route('external.notifikasi.read_all') }}" class="m-0">
                    @csrf
                    <button type="submit" class="text-[11px] font-medium text-[#0b0b0c] hover:underline py-1 px-2 rounded">
                        Tandai semua dibaca
                    </button>
                </form>
                <form method="POST" action="{{ route('external.notifikasi.clear_all') }}" class="m-0" onsubmit="return confirm('Bersihkan riwayat notifikasi? (Yang masih menunggu ACC/pending tidak akan dihapus)')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-[11px] font-medium text-[#b91c1c] hover:underline py-1 px-2 rounded">
                        Bersihkan Riwayat
                    </button>
                </form>
            </div>
            @endif
        </div>
    </x-slot>
</x-dropdown>

<script>
    function handleNotifClick(notifId, targetUrl) {
        let el = document.getElementById('notif-item-' + notifId);
        if (el) {
            el.classList.remove('bg-[#fafafa]');
            el.classList.add('bg-white');
        }
        fetch(`/external/notifikasi/${notifId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).catch(err => console.error("Gagal tandai dibaca", err));

        if (targetUrl && targetUrl !== '#') {
            window.location.href = targetUrl;
        }
    }

    async function deleteNotification(event, notifId) {
        event.stopPropagation();
        if (!confirm('Hapus notifikasi ini? (Data approval riwayat Anda akan tetap tersimpan)')) return;
        let el = document.getElementById('notif-item-' + notifId);
        try {
            const response = await fetch(`/external/notifikasi/hapus/${notifId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            if (response.ok) {
                el.style.transition = 'opacity 0.2s ease';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 200);
            } else {
                alert('Gagal menghapus notifikasi dari server.');
            }
        } catch (error) {
            console.error('Terjadi kesalahan koneksi:', error);
        }
    }
</script>
