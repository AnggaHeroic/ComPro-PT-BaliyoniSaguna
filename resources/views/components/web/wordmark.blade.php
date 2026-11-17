{{-- Tulisan di samping logo, diatur admin lewat Isi Halaman > Umum ($brand disuplai view composer). Props: tone (dark|light|auto; auto = putih di atas hero transparan, hitam setelah scroll, butuh induk .group[data-scrolled]), size (md|lg) --}}
@props(['tone' => 'dark', 'size' => 'md'])
@php
    $classes = [
        'block whitespace-nowrap font-display font-black leading-none tracking-tight transition-colors duration-300',
        match ($tone) {
            'light' => 'text-white',
            'auto' => 'text-white group-data-[scrolled=true]:text-black-soft',
            default => 'text-black-soft',
        },
        $size === 'lg' ? 'text-2xl' : 'text-[1.05rem] max-[350px]:text-[0.95rem] sm:text-[1.2rem] lg:text-[1.45rem]',
    ];
@endphp
{{-- Satu baris huruf tebal seragam; kata yang diberi tanda * oleh admin (bawaan: huruf "i") berwarna merah. --}}
<span {{ $attributes->class($classes) }}>{{ $brand['wordmark'] }}</span>
