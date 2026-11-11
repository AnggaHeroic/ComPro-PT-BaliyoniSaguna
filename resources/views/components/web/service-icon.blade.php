{{-- Props: name (nama ikon sesuai config company.services), accent (class stroke untuk detail, default stroke-brand) --}}
@props(['name', 'accent' => 'stroke-brand'])
@php
    // Ikon garis kustom (viewBox 48). Warna utama: currentColor; detail memakai class stroke-brand (diganti sesuai $accent).
    $icons = [
        'electronics' => '<rect x="6" y="10" width="36" height="24" rx="2"/><path d="M18 40h12M24 34v6"/><path d="m12 28 6-6 5 4 7-8 6 7" class="stroke-brand"/>',
        'stationery' => '<path d="M8 40l3-10L32 9a3 3 0 0 1 4 0l3 3a3 3 0 0 1 0 4L18 37z"/><path d="m28 13 7 7"/><path d="m11 30 7 7"/><rect x="22" y="31" width="20" height="9" rx="1" class="stroke-brand"/><path d="M27 31v4M32 31v5M37 31v4" class="stroke-brand"/>',
        'sports' => '<circle cx="24" cy="24" r="16"/><path d="M8 24h32"/><path d="M24 8c-6 5-6 27 0 32M24 8c6 5 6 27 0 32" class="stroke-brand"/>',
        'office' => '<rect x="8" y="16" width="32" height="22" rx="2"/><path d="M18 16v-4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v4"/><path d="M8 26h32"/><rect x="21" y="23" width="6" height="6" class="stroke-brand"/>',
        'education' => '<path d="M24 10 4 20l20 10 20-10z"/><path d="M12 25v9c3 3 8 5 12 5s9-2 12-5v-9"/><path d="M44 20v12" class="stroke-brand"/>',
        'gift' => '<rect x="8" y="20" width="32" height="20" rx="1"/><rect x="6" y="14" width="36" height="6" rx="1"/><path d="M24 14v26"/><path d="M24 14c-3-6-11-6-11-2s6 2 11 2zM24 14c3-6 11-6 11-2s-6 2-11 2z" class="stroke-brand"/>',
        'food' => '<path d="M10 14h18l-2 22a3 3 0 0 1-3 3h-8a3 3 0 0 1-3-3z"/><path d="M12 22h14"/><path d="M31 10v9M34 10v9M37 10v9M31 19a3 3 0 0 0 6 0M34 22v17" class="stroke-brand"/>',
        'tools' => '<path d="M30 8a9 9 0 0 0-8 12L8 34a4 4 0 0 0 6 6l14-14a9 9 0 0 0 12-8l-6 3-5-2-2-5z"/><circle cx="12" cy="37" r="1.5" class="stroke-brand"/>',
        'apparel' => '<path d="M16 8 6 14l5 9 5-3v20h16V20l5 3 5-9-10-6c-1 4-4 6-8 6s-7-2-8-6z"/><path d="M20 8c1 3 2.5 4 4 4s3-1 4-4" class="stroke-brand"/>',
        'ac-wall' => '<rect x="5" y="9" width="38" height="14" rx="2"/><path d="M9 19h30"/><path d="M12 28c0 4-3 4-3 8M24 28c0 4-3 4-3 8M36 28c0 4-3 4-3 8" class="stroke-brand"/>',
        'cassette' => '<rect x="8" y="8" width="32" height="32" rx="2"/><rect x="16" y="16" width="16" height="16" class="stroke-brand"/><path d="M24 8v8M24 32v8M8 24h8M32 24h8"/>',
        'rental' => '<circle cx="17" cy="31" r="7"/><circle cx="17" cy="31" r="2" class="stroke-brand"/><path d="M22 26 38 10"/><path d="m33 15 5 5"/><path d="m37 11 4 4" class="stroke-brand"/>',
        'vrf' => '<rect x="8" y="12" width="32" height="26" rx="2"/><path d="M12 38v4M36 38v4"/><circle cx="24" cy="25" r="8" class="stroke-brand"/><path d="M24 17v16M16 25h16" class="stroke-brand"/>',
        'lab' => '<path d="M19 8h10"/><path d="M21 8v12L9 38a3 3 0 0 0 3 5h24a3 3 0 0 0 3-5L27 20V8"/><path d="M14 32h20" class="stroke-brand"/>',
        'software' => '<rect x="6" y="9" width="36" height="30" rx="2"/><path d="M6 17h36"/><path d="m18 24-5 5 5 5M30 24l5 5-5 5" class="stroke-brand"/><path d="m26 22-4 14"/>',
        'parking' => '<path d="M6 40h12M12 40V22"/><rect x="9" y="14" width="6" height="8" rx="1"/><path d="M15 18h27"/><rect x="15" y="16" width="27" height="5" class="stroke-brand"/><path d="M22 16v5M29 16v5M36 16v5" class="stroke-brand"/><circle cx="34" cy="33" r="7"/><path d="M31 38V28h4a2.5 2.5 0 0 1 0 5h-4"/>',
    ];
@endphp
<svg {{ $attributes->class('shrink-0') }} viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! str_replace('stroke-brand', $accent, $icons[$name] ?? '') !!}</svg>
