{{-- Props: tidak ada. Peringatan sebelum admin keluar otomatis karena tidak aktif; logikanya di resources/js/idle-guard.js. Batas waktu di config/admin.php. --}}
<div x-data="idleGuard({
        timeout: {{ (int) config('admin.idle_timeout') }},
        warning: {{ (int) config('admin.idle_warning') }},
        pingUrl: @js(route('admin.keep-alive')),
        warningUrl: @js(route('admin.idle-warning')),
    })">
    {{-- Form keluar otomatis: dikirim oleh JS saat batas terlewati. --}}
    <form x-ref="logoutForm" method="POST" action="{{ route('admin.idle-logout') }}" class="hidden">@csrf</form>

    <div x-show="remaining !== null" x-cloak x-transition.opacity.duration.200ms role="alert" aria-live="assertive"
         class="fixed bottom-4 right-4 z-[90] w-80 max-w-[calc(100vw-2rem)] border border-black-soft/15 border-l-4 border-l-brand bg-white p-4 text-sm text-black-soft shadow-[0_8px_24px_-8px_rgb(0_0_0/0.35)]">
        <div class="flex gap-3">
            <x-ui.icon name="warning" class="mt-0.5 size-5 shrink-0 text-brand" />
            <div>
                <p class="font-bold">Sesi akan berakhir</p>
                <p class="mt-1 leading-5 text-muted">Anda tidak aktif. Anda akan keluar otomatis dalam <span class="font-bold tabular-nums text-black-soft" x-text="clock"></span>.</p>
                <x-ui.button type="button" @click="stay()" class="mt-3 !min-h-9 !px-4 !py-1.5 text-xs uppercase tracking-wider">Tetap masuk</x-ui.button>
            </div>
        </div>
    </div>
</div>
