{{-- Props: eyebrow (label kecil), title (wajib), invert (untuk latar gelap), center, width (class lebar maksimum, default max-w-2xl). Slot = deskripsi singkat. --}}
@props(['eyebrow' => null, 'title', 'invert' => false, 'center' => false, 'width' => 'max-w-2xl'])
<div {{ $attributes->class([$width, 'mx-auto text-center' => $center]) }}>
    @if ($eyebrow)
        <p class="text-xs font-bold uppercase tracking-[0.25em] {{ $invert ? 'text-brand-bright' : 'text-brand' }}">{{ $eyebrow }}</p>
    @endif
    <h2 class="mt-3 text-3xl font-black leading-tight tracking-tight sm:text-4xl {{ $invert ? 'text-white' : 'text-black-soft' }}">{{ $title }}</h2>
    <span class="mt-5 block h-0.5 w-10 bg-brand-bright {{ $center ? 'mx-auto' : '' }}" aria-hidden="true"></span>
    @if (trim($slot))
        <p class="mt-5 text-base leading-relaxed {{ $invert ? 'text-white/75' : 'text-muted' }}">{{ $slot }}</p>
    @endif
</div>
