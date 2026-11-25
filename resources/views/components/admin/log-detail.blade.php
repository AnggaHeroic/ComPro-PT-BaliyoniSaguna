{{-- Props: detail (teks rincian perubahan dari-menjadi). Tombol kecil "Detail" yang membuka dan menutup rincian di bawahnya. --}}
@props(['detail'])
<div x-data="{ open: false }" class="mt-1">
    <button type="button" @click="open = ! open" :aria-expanded="open"
            class="inline-flex items-center gap-1 text-xs font-semibold text-brand underline-offset-2 transition-colors hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-brand">
        Detail
        <x-ui.icon name="chevron-down" class="size-3 transition-transform" x-bind:class="open && 'rotate-180'" />
    </button>
    <p x-show="open" x-cloak class="mt-1.5 break-words text-xs leading-5 text-muted">{{ $detail }}</p>
</div>
