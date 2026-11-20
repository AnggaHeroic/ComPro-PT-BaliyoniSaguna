{{-- Hanya untuk pratinjau di admin (Isi Halaman > Umum); tidak ada rute publiknya. Menampilkan kedua variasi ajakan menghubungi; penanda data-cms-section dipakai untuk menyisakan satu variasi. --}}
@extends('layouts.public')

@section('content')
    <div data-cms-section="cta_card">
        <x-web.cta :company="$company" />
    </div>
    <div data-cms-section="cta_banner">
        <x-web.cta :company="$company" variant="banner" />
    </div>
@endsection
