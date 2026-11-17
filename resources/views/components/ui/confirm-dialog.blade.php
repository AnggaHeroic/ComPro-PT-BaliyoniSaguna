{{-- Props: tidak ada. Dialog konfirmasi global; dikendalikan oleh Alpine.store('confirm') (resources/js/confirm.js). Cukup dipasang sekali di layout admin. Ukuran dan susunannya sama dengan popup notifikasi (x-ui.flash-message); warna mengikuti tone: danger (merah, mis. hapus) atau default (hitam, peringatan biasa). --}}
<div x-data x-show="$store.confirm.open" x-cloak
     x-effect="$store.confirm.open && $nextTick(() => $refs.primary.focus())"
     @keydown.escape.window="$store.confirm.open && $store.confirm.answer(false)"
     class="fixed inset-0 z-[100] flex items-center justify-center p-4"
     role="alertdialog" aria-modal="true" aria-labelledby="confirm-title" aria-describedby="confirm-message">
    <div x-show="$store.confirm.open" x-transition.opacity @click="$store.confirm.answer(false)" class="absolute inset-0 bg-black-deep/60 backdrop-blur-[2px]" aria-hidden="true"></div>

    <div x-show="$store.confirm.open"
         x-transition:enter="transition duration-150 ease-out" x-transition:enter-start="translate-y-2 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
         :class="$store.confirm.tone === 'danger' ? 'border-brand' : 'border-black-soft'"
         class="relative w-full max-w-md overflow-hidden border-t-4 bg-white shadow-[0_24px_60px_-12px_rgb(0_0_0/0.5)]">
        <button type="button" x-show="$store.confirm.hideCancel" @click="$store.confirm.answer(false)" aria-label="Tutup"
                class="absolute right-3 top-3 inline-flex size-9 items-center justify-center text-muted transition-colors hover:text-black-soft">
            <x-ui.icon name="close" class="size-5" />
        </button>

        <div class="flex gap-4 p-6 sm:p-7">
            <span class="flex size-11 shrink-0 items-center justify-center text-white" :class="$store.confirm.tone === 'danger' ? 'bg-brand' : 'bg-black-soft'" aria-hidden="true">
                <x-ui.icon name="warning" class="size-5" />
            </span>
            <div class="min-w-0 pr-6 pt-0.5">
                <p class="text-[11px] font-bold uppercase tracking-[0.25em]" :class="$store.confirm.tone === 'danger' ? 'text-brand' : 'text-muted'">Konfirmasi</p>
                <h2 id="confirm-title" class="mt-1 font-display text-xl font-black leading-tight tracking-tight text-black-soft" x-text="$store.confirm.title"></h2>
                <p id="confirm-message" class="mt-2 text-sm leading-6 text-muted" x-text="$store.confirm.message"></p>
            </div>
        </div>

        <div class="flex flex-col-reverse gap-2 border-t border-black-soft/10 bg-gray-tint px-6 py-4 sm:flex-row sm:justify-end sm:px-7">
            <button type="button" x-show="! $store.confirm.hideCancel" @click="$store.confirm.answer(false)"
                    class="inline-flex min-h-10 items-center justify-center border border-black-soft/20 bg-white px-6 text-xs font-semibold uppercase tracking-wider text-black-soft transition-colors hover:bg-gray-tint">Batal</button>
            <button type="button" x-show="$store.confirm.secondaryLabel" x-text="$store.confirm.secondaryLabel" @click="$store.confirm.answer('secondary')"
                    class="inline-flex min-h-10 items-center justify-center border border-black-soft/20 bg-white px-6 text-xs font-semibold uppercase tracking-wider text-black-soft transition-colors hover:bg-gray-tint"></button>
            <button type="button" x-ref="primary" x-text="$store.confirm.label" @click="$store.confirm.answer(true)"
                    :class="$store.confirm.tone === 'danger' ? 'bg-brand hover:bg-brand-dark' : 'bg-black-soft hover:bg-brand'"
                    class="inline-flex min-h-10 items-center justify-center px-6 text-xs font-semibold uppercase tracking-wider text-white transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-black-soft focus-visible:ring-offset-2"></button>
        </div>
    </div>
</div>
