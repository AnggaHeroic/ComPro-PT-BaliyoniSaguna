{{-- Props: tone (dark|light|auto; auto = putih di atas hero transparan, hitam setelah scroll, butuh induk .group[data-scrolled]), size (md|lg) --}}
@props(['tone' => 'dark', 'size' => 'md'])
@php
    $classes = [
        'block whitespace-nowrap font-display font-black leading-none tracking-tight transition-colors duration-300',
        match ($tone) {
            'light' => 'text-white',
            'auto' => 'text-white group-data-[scrolled=true]:text-black-soft',
            default => 'text-black-soft',
        },
        $size === 'lg' ? 'text-2xl' : 'text-[1.2rem] lg:text-[1.45rem]',
    ];
@endphp
{{-- Wordmark bergaya logo Baliyoni Group: satu baris huruf tebal seragam, hanya "i" yang merah. --}}
<span {{ $attributes->class($classes) }}>PT. Bal<span class="text-brand-bright">i</span>yoni Saguna</span>
