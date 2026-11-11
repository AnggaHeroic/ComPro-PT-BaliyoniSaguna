{{-- Props: name (wajib; dibuka lewat $dispatch('open-modal', 'nama')), title (judul di bagian atas), eyebrow (label kecil merah di atas judul), maxWidth (class lebar maksimum, default max-w-lg). Slot = isi. Ditutup lewat tombol silang, klik latar, atau tombol Esc. --}}
@props(['name', 'title' => null, 'eyebrow' => null, 'maxWidth' => 'max-w-lg'])
<div x-data="{ show: false }" x-show="show" x-cloak
     x-effect="document.body.classList.toggle('overflow-hidden', show)"
     @open-modal.window="if ($event.detail === '{{ $name }}') show = true"
     @keydown.escape.window="show = false"
     x-transition.opacity.duration.150ms
     role="dialog" aria-modal="true" @if ($title) aria-labelledby="modal-{{ $name }}-title" @endif
     class="fixed inset-0 z-[70] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black-deep/70" @click="show = false" aria-hidden="true"></div>

    <div {{ $attributes->class(['relative w-full border-t-4 border-brand bg-white p-8 text-black-soft shadow-2xl sm:p-10', $maxWidth]) }}>
        <button type="button" @click="show = false" aria-label="Tutup"
                class="absolute right-3 top-3 flex size-9 items-center justify-center text-muted transition-colors hover:text-brand">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
        </button>
        @if ($eyebrow)
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand">{{ $eyebrow }}</p>
        @endif
        @if ($title)
            <h2 id="modal-{{ $name }}-title" class="mt-2 pr-8 font-display text-2xl font-black leading-tight sm:text-3xl">{{ $title }}</h2>
            <span class="mt-5 block h-0.5 w-12 bg-brand" aria-hidden="true"></span>
        @endif
        <div class="mt-5 text-sm leading-7 text-muted sm:text-base">{{ $slot }}</div>
    </div>
</div>
