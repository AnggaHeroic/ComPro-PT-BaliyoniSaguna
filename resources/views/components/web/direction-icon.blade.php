{{-- Props: name (vision|mission|motto; ikon mengikuti brosur: bola lampu, target berpanah, tangan terkepal). Ikon solid berwarna merah merek dengan detail putih; ukuran diatur lewat class. --}}
@props(['name'])
<svg {{ $attributes->class('text-brand') }} viewBox="0 0 64 64" fill="none" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('vision')
            {{-- Bola lampu merah berisi roda gigi putih, dengan sinar --}}
            <path d="M32 12a17 17 0 0 0-9.6 31c2 1.5 2.8 3 2.8 5.6V51h13.6v-2.4c0-2.6.8-4.1 2.8-5.6A17 17 0 0 0 32 12Z" fill="currentColor"/>
            <path d="M26 55h12M28.5 59.5h7M32 2v4M9 9l3.5 3.5M55 9l-3.5 3.5M3 28h5M61 28h-5" stroke="currentColor" stroke-width="3"/>
            <circle cx="32" cy="28" r="6.5" stroke="#fff" stroke-width="2.5"/>
            <circle cx="32" cy="28" r="2" fill="#fff"/>
            <path d="M32 18v3M32 35v3M22 28h3M39 28h3" stroke="#fff" stroke-width="2.5"/>
            @break
        @case('mission')
            {{-- Target merah bercincin putih, anak panah bergaris tepi putih --}}
            <circle cx="27" cy="37" r="23" fill="currentColor"/>
            <circle cx="27" cy="37" r="15" stroke="#fff" stroke-width="2.5"/>
            <circle cx="27" cy="37" r="7" stroke="#fff" stroke-width="2.5"/>
            <path d="M28 36 47 17M47 17l.5-5M47 17l5-.5" stroke="#fff" stroke-width="7"/>
            <path d="M28 36 47 17M47 17l.5-5M47 17l5-.5" stroke="currentColor" stroke-width="3"/>
            @break
        @case('motto')
            {{-- Tangan terkepal merah: empat jari bergaris tepi putih, jempol menyilang, manset, dan sinar --}}
            <rect x="16" y="22" width="8" height="18" rx="4" fill="currentColor" stroke="#fff" stroke-width="1.5"/>
            <rect x="24.5" y="19" width="8" height="21" rx="4" fill="currentColor" stroke="#fff" stroke-width="1.5"/>
            <rect x="33" y="19" width="8" height="21" rx="4" fill="currentColor" stroke="#fff" stroke-width="1.5"/>
            <rect x="41.5" y="22" width="8" height="18" rx="4" fill="currentColor" stroke="#fff" stroke-width="1.5"/>
            <path d="M16 36v6a6 6 0 0 0 6 6h21.5a6 6 0 0 0 6-6v-6Z" fill="currentColor"/>
            <rect x="18" y="34" width="26" height="10" rx="5" fill="currentColor" stroke="#fff" stroke-width="2"/>
            <rect x="20" y="50" width="26" height="8" rx="1.5" fill="currentColor"/>
            <path d="M32 2v9M18 6l3 6M46 6l-3 6M6 16l6 3M58 16l-6 3" stroke="currentColor" stroke-width="3"/>
            @break
    @endswitch
</svg>
