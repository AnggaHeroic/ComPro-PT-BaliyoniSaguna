{{-- Props: name (system|efficiency|security|complete|support, sesuai config company.advantages), accent (class stroke untuk detail, default stroke-brand) --}}
@props(['name', 'accent' => 'stroke-brand'])
@php
    // Ikon garis kustom (viewBox 48). Warna utama: currentColor; detail memakai class stroke-brand (diganti sesuai $accent).
    $icons = [
        // Semua dalam satu sistem: roda gigi dikelilingi panah melingkar
        'system' => '<circle cx="24" cy="24" r="9"/><circle cx="24" cy="24" r="3.5"/><path d="M24 12v3M24 33v3M12 24h3M33 24h3M15.5 15.5l2 2M30.5 30.5l2 2M32.5 15.5l-2 2M17.5 30.5l-2 2"/><path d="M8 24A16 16 0 0 1 24 8m-4-3 4 3-4 3" class="stroke-brand"/><path d="M40 24A16 16 0 0 1 24 40m4 3-4-3 4-3" class="stroke-brand"/>',
        // Kecepatan dan efisiensi: kunci pas dan stopwatch
        'efficiency' => '<g transform="scale(.64)" stroke-width="2.5"><path d="M30 8a9 9 0 0 0-8 12L8 34a4 4 0 0 0 6 6l14-14a9 9 0 0 0 12-8l-6 3-5-2-2-5z"/></g><circle cx="31" cy="30" r="10" class="stroke-brand"/><path d="M31 30v-5M31 30l3.5 2M28 17.5h6M31 17.5V20" class="stroke-brand"/>',
        // Keamanan transaksi: perisai dengan tanda centang
        'security' => '<path d="M24 5 8 11v12c0 10 7 17 16 20 9-3 16-10 16-20V11z"/><path d="m16 24 6 6 11-12" class="stroke-brand"/>',
        // Layanan lengkap: papan daftar periksa dan kaca pembesar
        'complete' => '<rect x="8" y="8" width="25" height="32" rx="2"/><path d="M15 8V5h11v3"/><path d="M14 17h4M14 24h4M14 31h4"/><path d="M22 17h7M22 24h3"/><circle cx="32" cy="31" r="6.5" class="stroke-brand"/><path d="m36.5 35.5 6 6" class="stroke-brand"/>',
        // Pelayanan pelanggan: sekelompok orang di atas telapak tangan
        'support' => '<circle cx="24" cy="10" r="4" class="stroke-brand"/><circle cx="13" cy="14" r="3.5"/><circle cx="35" cy="14" r="3.5"/><path d="M17 25c0-4 3-6 7-6s7 2 7 6M7 24c0-3 2-5 5-5M41 24c0-3-2-5-5-5"/><path d="M5 33h6l9 3h9a2.5 2.5 0 0 0 0-5h-7"/><path d="m22 31 9-1 9-4a2 2 0 0 1 2 3.5l-9 8-13 3H11"/><path d="M5 31v12h6"/>',
    ];
@endphp
<svg {{ $attributes->class('shrink-0') }} viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! str_replace('stroke-brand', $accent, $icons[$name] ?? '') !!}</svg>
