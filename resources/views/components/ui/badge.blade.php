{{-- Props: variant (success|warning|neutral). Slot = teks label status. --}}
@props(['variant' => 'neutral'])
<span {{ $attributes->class([
    'inline-flex items-center px-2 py-0.5 text-[11px] font-bold uppercase tracking-wider',
    match ($variant) {
        'success' => 'bg-green-100 text-green-800',
        'warning' => 'bg-amber-100 text-amber-800',
        default => 'bg-gray-tint text-muted',
    },
]) }}>{{ $slot }}</span>
