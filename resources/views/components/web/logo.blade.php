{{-- Props: size (class Tailwind ukuran, default size-10). Gambar logo diatur admin lewat Isi Halaman > Umum ($brand disuplai view composer di AppServiceProvider). --}}
@props(['size' => 'size-10'])
<img src="{{ $brand['logo_url'] }}" alt="" width="40" height="40" {{ $attributes->class([$size, 'shrink-0 object-contain']) }}>
