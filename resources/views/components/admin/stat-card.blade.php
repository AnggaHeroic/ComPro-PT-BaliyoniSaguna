{{-- Props: label, value (angka), hint (keterangan kecil), icon (nama x-ui.icon), href (opsional: bila ada, kartu menjadi tautan ke menu pengelolaannya dengan animasi hover tipis) --}}
@props(['label', 'value', 'hint' => null, 'icon' => 'dashboard', 'href' => null])
@php($tag = $href ? 'a' : 'div')
<{{ $tag }} @if ($href) href="{{ $href }}" aria-label="Kelola {{ $label }}" @endif
    {{ $attributes->class([
        'group relative block overflow-hidden border border-black-soft/10 bg-white p-5 sm:p-6',
        // Kartu tautan: animasi hover dibuat tipis (garis tepi memerah, garis merah menebal, ikon dan panah), tanpa bayangan atau gerakan kartu.
        'transition-colors duration-200 hover:border-brand/40 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand' => $href,
    ]) }}>
    <span @class(['absolute left-0 top-0 h-full w-0.5 bg-brand', 'transition-all duration-200 group-hover:w-1' => $href]) aria-hidden="true"></span>
    <div class="flex items-start justify-between gap-4">
        <p class="text-xs font-bold uppercase tracking-[0.14em] text-muted">{{ $label }}</p>
        <x-ui.icon :name="$icon" class="size-5 text-black-soft/30 {{ $href ? 'transition-colors duration-200 group-hover:text-brand' : '' }}" />
    </div>
    <p class="mt-4 font-display text-4xl font-black leading-none tracking-tight text-black-soft">{{ $value }}</p>
    @if ($hint)
        <p class="mt-3 text-xs text-muted">{{ $hint }}</p>
    @endif
    @if ($href)
        {{-- Petunjuk panah: muncul dan bergeser ke kanan saat kartu disorot. --}}
        <x-ui.icon name="arrow-right" class="absolute bottom-5 right-5 size-4 -translate-x-2 text-brand opacity-0 transition-all duration-200 group-hover:translate-x-0 group-hover:opacity-100 group-focus-visible:translate-x-0 group-focus-visible:opacity-100 sm:bottom-6 sm:right-6" />
    @endif
</{{ $tag }}>
