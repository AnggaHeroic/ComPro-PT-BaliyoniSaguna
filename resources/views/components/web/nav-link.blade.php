{{-- Props: href (kosong = dirender sebagai tombol, mis. pemicu dropdown), active, transparent (putih di atas hero, hitam setelah scroll). Slot = label. --}}
@props(['href' => null, 'active' => false, 'transparent' => false])
@php
    $classes = [
        'relative py-2 text-sm font-semibold uppercase tracking-wider transition-colors after:absolute after:inset-x-0 after:-bottom-0.5 after:h-0.5 after:origin-left after:bg-brand-bright after:transition-transform',
        $active
            ? ($transparent ? 'text-white group-data-[scrolled=true]:text-black-soft' : 'text-black-soft').' after:scale-x-100'
            : ($transparent
                ? 'text-white/80 hover:text-white group-data-[scrolled=true]:text-black-soft/70 group-data-[scrolled=true]:hover:text-black-soft'
                : 'text-black-soft/70 hover:text-black-soft').' after:scale-x-0 hover:after:scale-x-100',
    ];
@endphp
@if ($href)
    <a href="{{ $href }}" @if ($active) aria-current="page" @endif {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="button" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
