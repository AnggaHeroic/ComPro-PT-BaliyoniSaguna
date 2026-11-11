{{-- Props: href (opsional; jika ada dirender sebagai tautan), variant (primary|dark|light|outline|outline-light), type (untuk tombol, default button) --}}
@props(['href' => null, 'variant' => 'primary', 'type' => 'button'])
@php
    $classes = [
        'inline-flex min-h-11 items-center justify-center gap-2 rounded-sm px-6 py-3 text-sm font-semibold transition-colors',
        match ($variant) {
            'dark' => 'bg-black-soft text-white hover:bg-brand',
            'light' => 'bg-white text-black-soft hover:bg-gray-tint',
            'outline' => 'border border-black-soft/30 text-black-soft hover:bg-gray-tint',
            'outline-light' => 'border border-white/40 text-white hover:bg-white/10',
            default => 'bg-brand text-white hover:bg-brand-dark',
        },
    ];
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
