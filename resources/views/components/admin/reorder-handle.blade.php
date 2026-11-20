{{-- Props: label (nama baris untuk teks aksesibilitas), overlay (true = tampilan transparan di atas foto, untuk kisi galeri). Ikon seret + tombol naik/turun; dipakai di dalam x-data="reorderList". --}}
@props(['label', 'overlay' => false])
@php
    $grip = $overlay ? 'text-white/90' : 'text-muted hover:bg-gray-tint hover:text-black-soft';
    $button = $overlay
        ? 'border border-white/40 bg-black-deep/35 text-white backdrop-blur-sm hover:bg-black-deep/60'
        : 'border border-black-soft/20 bg-white text-muted hover:border-black-soft hover:text-black-soft';
@endphp
<div class="inline-flex items-center gap-1">
    {{-- Pada galeri, seluruh foto bisa diseret; ikon ini hanya penanda. Pada tabel, ini pegangan seretnya. --}}
    <span @if (! $overlay) data-drag-handle @endif class="flex size-10 items-center justify-center transition-colors {{ $overlay ? '' : 'cursor-grab touch-none active:cursor-grabbing' }} {{ $grip }}" aria-hidden="true" title="Seret untuk memindah">
        <x-ui.icon name="grip" class="size-5" />
    </span>
    <button type="button" @click="move($event, -1)" aria-label="Naikkan {{ $label }}" title="Naikkan" class="flex size-8 items-center justify-center transition-colors {{ $button }}">
        <x-ui.icon name="chevron-up" class="size-4" />
    </button>
    <button type="button" @click="move($event, 1)" aria-label="Turunkan {{ $label }}" title="Turunkan" class="flex size-8 items-center justify-center transition-colors {{ $button }}">
        <x-ui.icon name="chevron-down" class="size-4" />
    </button>
</div>
