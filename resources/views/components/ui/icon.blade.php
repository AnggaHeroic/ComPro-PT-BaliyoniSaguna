{{-- Props: name (arrow-right|arrow-left|close|zoom-in|zoom-out|mail|phone|whatsapp|map-pin) --}}
@props(['name'])
@php
    $paths = [
        'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'arrow-left' => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
        'close' => '<path d="M6 6l12 12M18 6 6 18"/>',
        'zoom-in' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5M11 8v6M8 11h6"/>',
        'zoom-out' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5M8 11h6"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'phone' => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z"/>',
        'whatsapp' => '<path d="m3 21 1.65-4.9A8.5 8.5 0 1 1 8 19.4L3 21Z"/><path d="M9 9.5c.3 2 2.3 4 4.5 4.5l1.2-1.2-1.8-1-.8.7a3 3 0 0 1-1.6-1.6l.7-.8-1-1.8L9 9.5Z"/>',
        'map-pin' => '<path d="M12 21s7-6.2 7-11.5A7 7 0 0 0 5 9.5C5 14.8 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/>',
    ];
@endphp
<svg {{ $attributes->class('shrink-0') }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $paths[$name] !!}</svg>
