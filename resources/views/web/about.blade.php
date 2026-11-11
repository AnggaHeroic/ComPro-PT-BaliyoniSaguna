@extends('layouts.public', ['transparentNav' => true, 'title' => 'Tentang Kami', 'description' => 'Profil, visi, misi, dan nilai-nilai PT Baliyoni Saguna, perusahaan induk Baliyoni Group.'])

@section('content')
    <x-web.page-header eyebrow="Tentang Kami" title="Mengenal" highlight="PT. Baliyoni Saguna">
        Perusahaan induk dari berbagai holding dan outlet Baliyoni Group, menghadirkan beragam produk dan layanan dalam satu pintu.
    </x-web.page-header>
    <x-web.about-profile :about="$about" :company="$company" />
    <x-web.vision-mission :about="$about" />
    <x-web.company-values :values="$about['values']" />
    <x-web.cta :company="$company" variant="banner" />
@endsection
