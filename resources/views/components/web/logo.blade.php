{{-- Props: size (class Tailwind ukuran, default size-10) --}}
@props(['size' => 'size-10'])
<img src="{{ asset('images/logo.png') }}" alt="" width="40" height="40" {{ $attributes->class([$size, 'shrink-0 object-contain']) }}>
